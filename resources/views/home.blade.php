<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Monthly Expense Tracker</title>

    <!-- Basic PWA meta (safe on public page) -->
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="manifest" href="/manifest.json">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s infinite'
                    },
                    keyframes: {
                        fadeIn: { '0%': {opacity:0, transform:'translateY(6px)'}, '100%': {opacity:1, transform:'translateY(0)'} },
                        slideUp: { '0%': {opacity:0, transform:'translateY(12px)'}, '100%': {opacity:1, transform:'translateY(0)'} }
                    }
                }
            }
        };
    </script>
</head>
<body class="antialiased bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 text-gray-800">
    <!-- Nav -->
    <header class="sticky top-0 z-40 backdrop-blur bg-white/70 border-b border-white/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <span class="text-2xl">💰</span>
                <span class="font-extrabold text-lg sm:text-xl tracking-tight">Monthly Expense Tracker</span>
            </a>
            <nav class="hidden md:flex items-center space-x-6 text-sm">
                <a href="#features" class="hover:text-brand-700">Features</a>
                <a href="#how" class="hover:text-brand-700">How it works</a>
                <a href="#faq" class="hover:text-brand-700">FAQ</a>
            </nav>
            <div class="flex items-center space-x-2">
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-brand-700 hover:text-brand-800">Sign in</a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Create account</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-white/60 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-purple-200/40 blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div class="animate-slide-up">
                    <p class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full bg-white/70 border">NEW • PWA Offline Support</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold leading-tight bg-gradient-to-r from-gray-900 via-brand-700 to-purple-700 bg-clip-text text-transparent">Track spending with clarity. Stay on budget with confidence.</h1>
                    <p class="mt-4 text-gray-600 text-lg">A simple, beautiful expense tracker with calendar view, budgets, offline mode, and smart insights. Works great on mobile and desktop.</p>
                    <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-3">
                        <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-6 py-3 rounded-lg text-white font-semibold bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Start free</a>
                        <a href="#features" class="inline-flex justify-center items-center px-6 py-3 rounded-lg font-semibold bg-white/80 border hover:bg-white">Explore features</a>
                    </div>
                    <div class="mt-6 flex items-center space-x-6 text-sm text-gray-600">
                        <div class="flex items-center space-x-2"><span class="text-xl">📱</span><span>Installable app (PWA)</span></div>
                        <div class="flex items-center space-x-2"><span class="text-xl">🔒</span><span>Private & secure</span></div>
                    </div>
                </div>
                <div class="relative animate-fade-in">
                    <!-- Dashboard preview (closer to actual UI) -->
                    <div class="mx-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 overflow-hidden border">
                        <!-- Header like dashboard -->
                        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-lg flex items-center"><span class="mr-2">💰</span> Expense Tracker</h3>
                                    <p class="text-blue-100 text-xs">August 2025</p>
                                </div>
                                <div class="flex gap-2">
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs bg-white/10">
                                        <span class="font-semibold mr-1">$260.85</span> This Month
                                    </span>
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs bg-white/10">
                                        <span class="font-semibold mr-1">6</span> Expenses
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Tabs like dashboard -->
                        <div class="px-4 pt-3">
                            <div class="rounded-xl bg-gray-50 border overflow-hidden">
                                <div class="grid grid-cols-3 text-sm font-semibold">
                                    <div class="text-center py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white">Calendar</div>
                                    <div class="text-center py-2 text-gray-600">Expenses</div>
                                    <div class="text-center py-2 text-gray-600">Summary</div>
                                </div>
                            </div>
                        </div>
                        <!-- Mini calendar -->
                        <div class="p-4">
                            <div class="grid grid-cols-7 gap-1 text-[11px] text-gray-500 mb-1">
                                <div class="text-center">Sun</div><div class="text-center">Mon</div><div class="text-center">Tue</div><div class="text-center">Wed</div><div class="text-center">Thu</div><div class="text-center">Fri</div><div class="text-center">Sat</div>
                            </div>
                            <div class="grid grid-cols-7 gap-1">
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-blue-100 border-blue-300 shadow relative">
                                    <span class="absolute top-1 left-1 text-xs font-semibold text-blue-700">5</span>
                                    <span class="absolute bottom-1 left-1 text-[10px] font-bold text-red-600">$40.00</span>
                                </div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-green-50 border-green-300 relative">
                                    <span class="absolute top-1 left-1 text-xs font-semibold text-gray-700">2</span>
                                    <span class="absolute bottom-1 left-1 text-[10px] font-bold text-red-600">$12.50</span>
                                </div>
                                <!-- next row -->
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-green-50 border-green-300 relative">
                                    <span class="absolute top-1 left-1 text-xs font-semibold text-gray-700">9</span>
                                    <span class="absolute bottom-1 left-1 text-[10px] font-bold text-red-600">$78.25</span>
                                </div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <!-- row 3 -->
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-green-50 border-green-300 relative">
                                    <span class="absolute top-1 left-1 text-xs font-semibold text-gray-700">18</span>
                                    <span class="absolute bottom-1 left-1 text-[10px] font-bold text-red-600">$95.10</span>
                                </div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <!-- row 4 -->
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-green-50 border-green-300 relative">
                                    <span class="absolute top-1 left-1 text-xs font-semibold text-gray-700">23</span>
                                    <span class="absolute bottom-1 left-1 text-[10px] font-bold text-red-600">$25.00</span>
                                </div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                                <div class="h-12 rounded-lg border bg-gray-50"></div>
                            </div>
                            <div class="mt-3 flex items-center justify-center space-x-4 text-[11px] text-gray-500">
                                <div class="flex items-center"><div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded mr-1"></div><span>Today</span></div>
                                <div class="flex items-center"><div class="w-3 h-3 bg-green-50 border border-green-200 rounded mr-1"></div><span>Has Expenses</span></div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500 text-center">Preview of the dashboard you’ll use after sign up (mocked)</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social proof / quick metrics -->
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/80 rounded-lg p-4 shadow border text-center">
                <div class="text-2xl">📅</div>
                <p class="mt-1 font-semibold">Calendar-first</p>
                <p class="text-xs text-gray-500">Spot spending patterns quickly</p>
            </div>
            <div class="bg-white/80 rounded-lg p-4 shadow border text-center">
                <div class="text-2xl">📶</div>
                <p class="mt-1 font-semibold">Works offline</p>
                <p class="text-xs text-gray-500">Syncs when you’re back online</p>
            </div>
            <div class="bg-white/80 rounded-lg p-4 shadow border text-center">
                <div class="text-2xl">💡</div>
                <p class="mt-1 font-semibold">Smart insights</p>
                <p class="text-xs text-gray-500">Top categories, trends and more</p>
            </div>
            <div class="bg-white/80 rounded-lg p-4 shadow border text-center">
                <div class="text-2xl">💱</div>
                <p class="mt-1 font-semibold">USD/PHP</p>
                <p class="text-xs text-gray-500">Switch currency anytime</p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-center">Everything you need to stay on budget</h2>
            <p class="mt-3 text-center text-gray-600">Designed for speed, clarity and reliability—on any device.</p>
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">🗓️</div>
                    <h3 class="mt-2 font-semibold">Calendar view</h3>
                    <p class="mt-1 text-sm text-gray-600">See daily totals, click any day to review items and patterns.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">📊</div>
                    <h3 class="mt-2 font-semibold">Monthly summary</h3>
                    <p class="mt-1 text-sm text-gray-600">Totals, averages, largest spend and category breakdowns.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">🎯</div>
                    <h3 class="mt-2 font-semibold">Budgets that stick</h3>
                    <p class="mt-1 text-sm text-gray-600">Set a monthly budget and watch your progress live.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">📶</div>
                    <h3 class="mt-2 font-semibold">Offline first</h3>
                    <p class="mt-1 text-sm text-gray-600">Add expenses without internet. Syncs automatically.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">🔔</div>
                    <h3 class="mt-2 font-semibold">Quick actions</h3>
                    <p class="mt-1 text-sm text-gray-600">Duplicate common items, keyboard shortcuts, and more.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">🔒</div>
                    <h3 class="mt-2 font-semibold">Secure by default</h3>
                    <p class="mt-1 text-sm text-gray-600">Your data stays private. CSRF protection and auth built-in.</p>
                </div>
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 rounded-lg text-white font-semibold bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Create your free account</a>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how" class="py-20 bg-white/70 border-y">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-center">Get started in 3 steps</h2>
            <div class="mt-10 grid md:grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">1️⃣</div>
                    <h3 class="mt-2 font-semibold">Create your account</h3>
                    <p class="mt-1 text-sm text-gray-600">Sign up with email and set your preferred currency.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">2️⃣</div>
                    <h3 class="mt-2 font-semibold">Add your first expense</h3>
                    <p class="mt-1 text-sm text-gray-600">Use quick suggestions or fill in details—works offline too.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border">
                    <div class="text-2xl">3️⃣</div>
                    <h3 class="mt-2 font-semibold">Track and improve</h3>
                    <p class="mt-1 text-sm text-gray-600">Check the summary for insights and keep spending on track.</p>
                </div>
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 rounded-lg text-white font-semibold bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Start tracking now</a>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{open:null}">
            <h2 class="text-3xl font-extrabold text-center">Frequently asked questions</h2>
            <div class="mt-8 space-y-3">
                <div class="bg-white rounded-lg border shadow">
                    <button class="w-full text-left p-4 font-medium flex justify-between items-center" @click="open === 1 ? open = null : open = 1">
                        <span>Is it free?</span>
                        <span x-text="open===1 ? '−' : '+'" class="text-xl leading-none"></span>
                    </button>
                    <div x-show="open===1" x-collapse class="px-4 pb-4 text-sm text-gray-600">Yes. You can use all core features for free.</div>
                </div>
                <div class="bg-white rounded-lg border shadow">
                    <button class="w-full text-left p-4 font-medium flex justify-between items-center" @click="open === 2 ? open = null : open = 2">
                        <span>Does it work offline?</span>
                        <span x-text="open===2 ? '−' : '+'" class="text-xl leading-none"></span>
                    </button>
                    <div x-show="open===2" x-collapse class="px-4 pb-4 text-sm text-gray-600">Yes. Add expenses without internet; they sync when you’re back online.</div>
                </div>
                <div class="bg-white rounded-lg border shadow">
                    <button class="w-full text-left p-4 font-medium flex justify-between items-center" @click="open === 3 ? open = null : open = 3">
                        <span>Can I switch currency?</span>
                        <span x-text="open===3 ? '−' : '+'" class="text-xl leading-none"></span>
                    </button>
                    <div x-show="open===3" x-collapse class="px-4 pb-4 text-sm text-gray-600">Yes. Toggle USD/PHP and your budget adapts automatically.</div>
                </div>
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 rounded-lg text-white font-semibold bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 shadow">Create account</a>
                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 ml-3 rounded-lg font-semibold bg-white/80 border hover:bg-white">Sign in</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 border-t bg-white/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">💰</span>
                    <span class="font-bold">Monthly Expense Tracker</span>
                </div>
                <p class="text-sm text-gray-500">© <span x-data x-text="new Date().getFullYear()"></span> Monthly Expense Tracker. All rights reserved.</p>
                <div class="text-sm text-gray-500 flex items-center space-x-3">
                    <a href="#features" class="hover:text-gray-700">Features</a>
                    <a href="#how" class="hover:text-gray-700">How it works</a>
                    <a href="#faq" class="hover:text-gray-700">FAQ</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 