<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify email - Monthly Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { 600: '#4f46e5', 700: '#4338ca' } } } } }
    </script>
</head>
<body class="antialiased bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 text-gray-800">
    <header class="sticky top-0 z-40 backdrop-blur bg-white/70 border-b border-white/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <span class="text-2xl">💰</span>
                <span class="font-extrabold text-lg sm:text-xl tracking-tight">Monthly Expense Tracker</span>
            </a>
        </div>
    </header>

    <main class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="rounded-2xl shadow-xl ring-1 ring-black/5 overflow-hidden bg-white">
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white p-6">
                    <h1 class="text-2xl font-bold">Verify your email</h1>
                    <p class="text-indigo-100 text-sm">Enter the 6‑digit code we sent to your email</p>
                </div>
                <div class="p-6">
                    @if (session('status'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded px-3 py-2">{{ session('status') }}</div>
                    @endif
                    @if (session('debug_otp'))
                        <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded px-3 py-2">
                            <strong>⚠️ Email delivery issue!</strong> Your OTP code is: <span class="font-mono text-lg font-bold bg-yellow-100 px-2 py-1 rounded">{{ session('debug_otp') }}</span>
                            <br><small class="text-yellow-600">Please check your email spam folder or use the code above.</small>
                        </div>
                    @endif
                    @error('csrf')
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded px-3 py-2">
                            <strong>🔒 {{ $message }}</strong>
                        </div>
                    @enderror
                    <form method="POST" action="{{ route('register.verify') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">6‑digit code</label>
                            <input id="otp" name="otp" inputmode="numeric" pattern="[0-9]*" maxlength="6" minlength="6" required class="w-full tracking-[0.6em] text-center text-xl px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-transparent @error('otp') border-red-500 @enderror" placeholder="••••••" />
                            @error('otp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center items-center py-3 px-4 rounded-lg text-white font-semibold bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Verify</button>
                    </form>
                    <form method="POST" action="{{ route('register.resend') }}" class="mt-4 text-center">
                        @csrf
                        <button type="submit" class="text-sm text-brand-600 hover:text-brand-700">Resend code</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-xs text-gray-500 mt-4">© <span x-data x-text="new Date().getFullYear()"></span> Monthly Expense Tracker</p>
        </div>
    </main>
</body>
</html>