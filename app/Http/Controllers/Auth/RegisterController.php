<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Step 1: accept details, send OTP, show OTP form
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => 'required|accepted',
        ]);

        // Prepare pending registration data in session
        $otpCode = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $pending = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'otp'        => $otpCode,
            'expires_at' => $expiresAt->toIso8601String(),
            'attempts'   => 0,
        ];

        session(['pending_registration' => $pending]);

        // Send OTP via Brevo (Sendinblue) API
        $this->sendOtpEmail($request->email, $request->first_name, $otpCode);

        return redirect()->route('register.verify.show')
            ->with('status', 'We sent a 6-digit code to your email. Please enter it to continue.');
    }

    public function showVerifyOtp()
    {
        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->withErrors(['message' => 'No registration in progress.']);
        }
        
        // Extend session lifetime for OTP verification
        
        return view('auth.verify-otp');
    }

    // Step 2: verify OTP, create account
    public function verifyOtp(Request $request)
    {
        // Debug logging
        \Log::info('OTP Verification attempt', [
            'session_id' => session()->getId(),
            'has_pending' => session()->has('pending_registration'),
            'user_agent' => $request->userAgent(),
            'otp_provided' => $request->has('otp'),
            'is_api' => $request->expectsJson()
        ]);

        try {
            $request->validate([
                'otp' => 'required|digits:6',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid 6-digit code.'
                ], 422);
            }
            throw $e;
        }

        $pending = session('pending_registration');
        if (!$pending) {
            \Log::warning('No pending registration found in session');
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please register again.'
                ], 422);
            }
            return redirect()->route('register')->withErrors(['message' => 'Session expired. Please register again.']);
        }

        // Check expiry
        if (now()->greaterThanOrEqualTo($pending['expires_at'])) {
            session()->forget('pending_registration');
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification code expired. Please register again.'
                ], 422);
            }
            return redirect()->route('register')->withErrors(['message' => 'Verification code expired. Please register again.']);
        }

        // Check attempts (optional limit)
        $pending['attempts'] = ($pending['attempts'] ?? 0) + 1;
        session(['pending_registration' => $pending]);
        if ($pending['attempts'] > 5) {
            session()->forget('pending_registration');
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please register again.'
                ], 422);
            }
            return redirect()->route('register')->withErrors(['message' => 'Too many attempts. Please register again.']);
        }

        if ($request->otp !== $pending['otp']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid code. Please try again.'
                ], 422);
            }
            return back()->withErrors(['otp' => 'Invalid code.'])->withInput();
        }

        // Create user now
        $user = User::create([
            'first_name' => $pending['first_name'],
            'last_name'  => $pending['last_name'],
            'email'      => $pending['email'],
            'password'   => $pending['password'],
        ]);

        session()->forget('pending_registration');

        Auth::login($user);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Redirecting to dashboard...',
                'redirect' => '/dashboard'
            ]);
        }
        
        return redirect('/dashboard');
    }

    public function resendOtp(Request $request)
    {
        $pending = session('pending_registration');
        if (!$pending) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please register again.'
                ], 422);
            }
            return redirect()->route('register')->withErrors(['message' => 'Session expired. Please register again.']);
        }

        $newCode = (string) random_int(100000, 999999);
        $pending['otp'] = $newCode;
        $pending['expires_at'] = now()->addMinutes(10)->toIso8601String();
        session(['pending_registration' => $pending]);

        $this->sendOtpEmail($pending['email'], $pending['first_name'], $newCode);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'A new code was sent to your email.'
            ]);
        }
        
        return back()->with('status', 'A new code was sent to your email.');
    }

    private function sendOtpEmail(string $toEmail, string $toName, string $otp): void
    {
        $emailSent = false;
        
        // Try Laravel's built-in mail first
        $emailSent = $this->sendViaLaravelMail($toEmail, $toName, $otp);
        
        // If Laravel's mail fails, try Brevo
        if (!$emailSent) {
            $emailSent = $this->sendViaBrevo($toEmail, $toName, $otp);
        }
        
        // Always show OTP in debug mode or when email fails
        if (!$emailSent) {
            session()->flash('debug_otp', $otp);
            \Log::warning('OTP displayed in browser due to email failure', [
                'email' => $toEmail,
                'otp' => $otp
            ]);
        }
        
        // Always log the OTP for admin access
        \Log::info('OTP Generated', [
            'email' => $toEmail,
            'otp' => $otp,
            'timestamp' => now()->toISOString(),
            'email_sent' => $emailSent
        ]);
    }
    
    private function sendViaBrevo(string $toEmail, string $toName, string $otp): bool
    {
        $apiKey = env('BREVO_API_KEY');
        
        if (!$apiKey) {
            \Log::error('BREVO_API_KEY not found in environment');
            return false;
        }

        $endpoint = 'https://api.sendinblue.com/v3/smtp/email';
        $subject = 'Your One-Time Code (OTP)';
        $html = $this->buildOtpEmailHtml($toName, $otp);

        try {
            $response = Http::timeout(10)->withHeaders([
                'Accept' => 'application/json',
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'sender' => [
                    'name' => 'Monthly Expense Tracker',
                    'email' => 'enodd.coding.20@gmail.com',
                ],
                'to' => [[ 'email' => $toEmail, 'name' => $toName ]],
                'subject' => $subject,
                'htmlContent' => $html,
            ]);

            if ($response->successful()) {
                \Log::info('Brevo OTP email sent successfully', [
                    'email' => $toEmail,
                    'response_id' => $response->json()['messageId'] ?? 'unknown'
                ]);
                return true;
            } else {
                \Log::error('Brevo API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'email' => $toEmail,
                    'headers' => $response->headers()
                ]);
                return false;
            }
        } catch (\Throwable $e) {
            \Log::error('Brevo send OTP exception: '.$e->getMessage(), [
                'email' => $toEmail,
                'exception' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    private function sendViaLaravelMail(string $toEmail, string $toName, string $otp): bool
    {
        try {
            \Mail::send([], [], function ($message) use ($toEmail, $toName, $otp) {
                $message->to($toEmail, $toName)
                        ->subject('Your One-Time Code (OTP)')
                        ->html($this->buildOtpEmailHtml($toName, $otp));
            });
            
            \Log::info('Laravel Mail OTP sent successfully', ['email' => $toEmail]);
            return true;
        } catch (\Throwable $e) {
            \Log::error('Laravel Mail send OTP failed: '.$e->getMessage(), [
                'email' => $toEmail,
                'exception' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function buildOtpEmailHtml(string $name, string $otp): string
    {
        $safeName = e($name);
        $safeOtp = e($otp);
        return <<<HTML
<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your OTP Code</title>
  </head>
  <body style="margin:0;background:#f8fafc;font-family:Inter,Segoe UI,Roboto,Arial,sans-serif;color:#0f172a;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="center" style="padding:24px;">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="560" style="max-width:560px;background:#ffffff;border-radius:16px;box-shadow:0 10px 30px rgba(2,6,23,.08);overflow:hidden;border:1px solid #e2e8f0;">
            <tr>
              <td style="background:linear-gradient(90deg,#4f46e5,#8b5cf6);padding:20px 24px;color:#fff;">
                <div style="font-size:16px;font-weight:700;">Monthly Expense Tracker</div>
                <div style="opacity:.9;font-size:12px;">One-Time Verification Code</div>
              </td>
            </tr>
            <tr>
              <td style="padding:28px 24px;">
                <p style="margin:0 0 12px 0;font-size:16px;">Hi {$safeName},</p>
                <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#334155;">Use the 6‑digit code below to verify your email. This code expires in 10 minutes.</p>
                <div style="margin:20px 0;padding:16px 24px;border:1px dashed #c7d2fe;background:#eef2ff;border-radius:12px;text-align:center;">
                  <div style="font-size:28px;letter-spacing:8px;font-weight:800;color:#4f46e5;">{$safeOtp}</div>
                </div>
                <p style="margin:0 0 6px 0;font-size:12px;color:#64748b;">If you didn’t request this, you can safely ignore this email.</p>
              </td>
            </tr>
            <tr>
              <td style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:11px;">
                © Monthly Expense Tracker • ENODD
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;
    }
}