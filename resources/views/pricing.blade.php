<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans - {{ config('app.name') }}</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="manifest" href="/manifest.json">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'bounce-in': 'bounceIn 0.8s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': {opacity:0, transform:'translateY(10px)'}, '100%': {opacity:1, transform:'translateY(0)'} },
                        slideUp: { '0%': {opacity:0, transform:'translateY(20px)'}, '100%': {opacity:1, transform:'translateY(0)'} },
                        bounceIn: { '0%': {opacity:0, transform:'scale(0.3)'}, '50%': {opacity:1, transform:'scale(1.05)'}, '70%': {transform:'scale(0.9)'}, '100%': {opacity:1, transform:'scale(1)'} },
                        float: { '0%, 100%': {transform:'translateY(0px)'}, '50%': {transform:'translateY(-10px)'} }
                    }
                }
            }
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .gradient-border {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #06b6d4);
            padding: 2px;
            border-radius: 16px;
        }
        .gradient-border-content {
            background: white;
            border-radius: 14px;
        }
        .popular-badge {
            background: linear-gradient(45deg, #f59e0b, #f97316);
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-40 backdrop-blur bg-white/70 border-b border-white/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <span class="text-2xl">💰</span>
                <span class="font-extrabold text-xl tracking-tight">Monthly Expense Tracker</span>
            </a>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-indigo-700 hover:text-indigo-800">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-indigo-700 hover:text-indigo-800">Sign in</a>
                @endauth
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow">Get Started</a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="pricingPage()">
        <!-- Hero Section -->
        <div class="text-center mb-16 animate-fade-in">
            <h1 class="text-4xl sm:text-5xl font-extrabold bg-gradient-to-r from-gray-900 via-indigo-700 to-purple-700 bg-clip-text text-transparent mb-4">
                Choose Your Perfect Plan
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Start free and upgrade when you need more powerful features. All plans include our core expense tracking with calendar view.
            </p>
            
            <!-- Billing Toggle -->
            <div class="flex items-center justify-center space-x-4 mb-8">
                <span class="text-sm font-medium" :class="billingPeriod === 'monthly' ? 'text-indigo-600' : 'text-gray-500'">Monthly</span>
                <button 
                    @click="toggleBilling()"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    :class="billingPeriod === 'yearly' ? 'bg-indigo-600' : 'bg-gray-200'"
                >
                    <span 
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                        :class="billingPeriod === 'yearly' ? 'translate-x-6' : 'translate-x-1'"
                    ></span>
                </button>
                <span class="text-sm font-medium" :class="billingPeriod === 'yearly' ? 'text-indigo-600' : 'text-gray-500'">
                    Yearly 
                    <span class="text-green-600 font-bold">(Save 20%)</span>
                </span>
            </div>
        </div>

        <!-- Pricing Cards -->
        <div class="grid lg:grid-cols-4 gap-8 mb-16">
            <!-- Free Plan -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 animate-slide-up">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">🆓</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Free</h3>
                    <div class="text-4xl font-extrabold text-gray-900 mb-2">$0</div>
                    <p class="text-gray-500">Perfect for getting started</p>
                </div>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Up to 50 expenses/month</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Calendar view</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Basic categories</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">1 monthly budget</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Offline mode</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">USD/PHP currency</span>
                    </li>
                </ul>
                
                <button class="w-full py-3 px-4 rounded-lg font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                    Current Plan
                </button>
            </div>

            <!-- Premium Plan -->
            <div class="gradient-border animate-slide-up" style="animation-delay: 0.1s">
                <div class="gradient-border-content p-8 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="popular-badge text-white text-xs font-bold px-3 py-1 rounded-full">
                            MOST POPULAR
                        </span>
                    </div>
                    
                    <div class="text-center mb-8">
                        <div class="text-4xl mb-4">⭐</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium</h3>
                        <div class="text-4xl font-extrabold text-indigo-600 mb-2">
                            <span x-text="billingPeriod === 'monthly' ? '$4.99' : '$47.90'"></span>
                            <span class="text-lg text-gray-500" x-text="billingPeriod === 'monthly' ? '/month' : '/year'"></span>
                        </div>
                        <p class="text-gray-500">For serious expense trackers</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Unlimited expenses</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Multiple budgets (category, weekly)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">📸 Receipt scanning & OCR</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">🔄 Recurring expenses</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">📊 Advanced analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">📄 Export (PDF, CSV, Excel)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">🔔 Spending alerts</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">🎨 Custom categories</span>
                        </li>
                    </ul>
                    
                    <button class="w-full py-3 px-4 rounded-lg font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg">
                        Upgrade to Premium
                    </button>
                </div>
            </div>

            <!-- Pro Plan -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 animate-slide-up" style="animation-delay: 0.2s">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro</h3>
                    <div class="text-4xl font-extrabold text-gray-900 mb-2">
                        <span x-text="billingPeriod === 'monthly' ? '$9.99' : '$95.90'"></span>
                        <span class="text-lg text-gray-500" x-text="billingPeriod === 'monthly' ? '/month' : '/year'"></span>
                    </div>
                    <p class="text-gray-500">For power users & families</p>
                </div>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700"><strong>Everything in Premium</strong></span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">🏦 Bank account integration</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">📈 Investment tracking</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">🎯 Savings goals & debt tracking</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">📅 Bill reminders</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">👨‍👩‍👧‍👦 Family sharing (3 users)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">🎧 Priority support</span>
                    </li>
                </ul>
                
                <button class="w-full py-3 px-4 rounded-lg font-semibold text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg">
                    Upgrade to Pro
                </button>
            </div>

            <!-- Business Plan -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 animate-slide-up" style="animation-delay: 0.3s">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">🏢</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Business</h3>
                    <div class="text-4xl font-extrabold text-gray-900 mb-2">
                        <span x-text="billingPeriod === 'monthly' ? '$19.99' : '$191.90'"></span>
                        <span class="text-lg text-gray-500" x-text="billingPeriod === 'monthly' ? '/month' : '/year'"></span>
                    </div>
                    <p class="text-gray-500">For teams & businesses</p>
                </div>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700"><strong>Everything in Pro</strong></span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">💼 Business expense categories</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">🏗️ Multiple businesses/projects</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">👥 Team collaboration (10 users)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">📋 Advanced tax reporting</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">🔌 API access</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">🏷️ White-label options</span>
                    </li>
                </ul>
                
                <button class="w-full py-3 px-4 rounded-lg font-semibold text-white bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-900 hover:to-black transition-all transform hover:scale-105 shadow-lg">
                    Contact Sales
                </button>
            </div>
        </div>

        <!-- Feature Comparison Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-16 animate-bounce-in">
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <h3 class="text-2xl font-bold text-center">Feature Comparison</h3>
                <p class="text-center text-indigo-100 mt-2">See what's included in each plan</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Features</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Free</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Premium</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Pro</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Business</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">Monthly expenses limit</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">50</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">Unlimited</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">Unlimited</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">Unlimited</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">Budget tracking</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">1 monthly</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">Multiple types</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">Multiple types</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-semibold">Multiple types</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">Receipt scanning</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">✓</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">Bank integration</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">Team collaboration</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">3 users</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">10 users</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">API access</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-400">✗</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600">✓</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-16 animate-fade-in">
            <h3 class="text-2xl font-bold text-center mb-8">Frequently Asked Questions</h3>
            
            <div class="space-y-6" x-data="{ openFaq: null }">
                <div class="border-b border-gray-200 pb-6">
                    <button 
                        @click="openFaq = openFaq === 1 ? null : 1"
                        class="flex justify-between items-center w-full text-left"
                    >
                        <span class="text-lg font-semibold text-gray-900">Can I change plans anytime?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" x-transition class="mt-4 text-gray-600">
                        Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate the billing accordingly.
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-6">
                    <button 
                        @click="openFaq = openFaq === 2 ? null : 2"
                        class="flex justify-between items-center w-full text-left"
                    >
                        <span class="text-lg font-semibold text-gray-900">Is there a free trial?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" x-transition class="mt-4 text-gray-600">
                        Our Free plan is generous and permanent - no trial needed! You can track up to 50 expenses per month with full access to core features. Upgrade when you need more.
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-6">
                    <button 
                        @click="openFaq = openFaq === 3 ? null : 3"
                        class="flex justify-between items-center w-full text-left"
                    >
                        <span class="text-lg font-semibold text-gray-900">How secure is my financial data?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" x-transition class="mt-4 text-gray-600">
                        We use bank-level encryption (256-bit SSL) and never store your banking credentials. Bank connections use read-only access through trusted partners like Plaid. Your data is encrypted at rest and in transit.
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-6">
                    <button 
                        @click="openFaq = openFaq === 4 ? null : 4"
                        class="flex justify-between items-center w-full text-left"
                    >
                        <span class="text-lg font-semibold text-gray-900">Can I export my data?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" x-transition class="mt-4 text-gray-600">
                        Premium and higher plans include full data export in multiple formats (CSV, Excel, PDF reports). You own your data and can export it anytime.
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-12 text-white animate-bounce-in">
            <h3 class="text-3xl font-bold mb-4">Ready to take control of your expenses?</h3>
            <p class="text-xl text-indigo-100 mb-8">Join thousands of users who've simplified their financial tracking</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors transform hover:scale-105">
                    Start Free Today
                </a>
                <button class="px-8 py-4 bg-indigo-500 text-white rounded-lg font-semibold hover:bg-indigo-400 transition-colors transform hover:scale-105">
                    View Demo
                </button>
            </div>
        </div>
    </div>

    <script>
        function pricingPage() {
            return {
                billingPeriod: 'monthly',
                
                toggleBilling() {
                    this.billingPeriod = this.billingPeriod === 'monthly' ? 'yearly' : 'monthly';
                }
            }
        }
    </script>
</body>
</html>