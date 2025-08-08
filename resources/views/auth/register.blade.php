<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account - Monthly Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 600: '#4f46e5', 700: '#4338ca' }
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased bg-gradient-to-br from-green-50 via-emerald-50 to-blue-50 text-gray-800">
    <header class="sticky top-0 z-40 backdrop-blur bg-white/70 border-b border-white/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <span class="text-2xl">💰</span>
                <span class="font-extrabold text-lg sm:text-xl tracking-tight">Monthly Expense Tracker</span>
            </a>
            <div class="flex items-center space-x-2">
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Sign in</a>
            </div>
        </div>
    </header>

    <main class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-xl">
            <div class="rounded-2xl shadow-xl ring-1 ring-black/5 overflow-hidden bg-white">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-6">
                    <h1 class="text-2xl font-bold">Create your account</h1>
                    <p class="text-emerald-100 text-sm">Join and start tracking in minutes</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First name</label>
                                <input id="first_name" name="first_name" type="text" required value="{{ old('first_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-transparent transition duration-200 @error('first_name') border-red-500 @enderror" placeholder="John" />
                                @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last name</label>
                                <input id="last_name" name="last_name" type="text" required value="{{ old('last_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-transparent transition duration-200 @error('last_name') border-red-500 @enderror" placeholder="Doe" />
                                @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror" placeholder="you@example.com" />
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input id="password" name="password" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-transparent transition duration-200 @error('password') border-red-500 @enderror" placeholder="Create a password" />
                                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-600 focus:border-transparent transition duration-200" placeholder="Repeat your password" />
                            </div>
                    </div>
                        <label class="flex items-center text-sm text-gray-700">
                            <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-brand-600 focus:ring-brand-600 border-gray-300 rounded mr-2" />
                            I agree to the <a href="#" class="text-brand-600 hover:text-brand-700 mx-1">Terms</a> and <a href="#" class="text-brand-600 hover:text-brand-700 ml-1">Privacy</a>
                        </label>
                        <button type="submit" class="w-full inline-flex justify-center items-center py-3 px-4 rounded-lg text-white font-semibold bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">
                            Continue
                    </button>
                </form>

                    <div class="mt-6 text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Sign in</a>
                    </div>
                </div>
            </div>
            <p class="text-center text-xs text-gray-500 mt-4">© <span x-data x-text="new Date().getFullYear()"></span> Monthly Expense Tracker</p>
        </div>
    </main>
</body>
</html>