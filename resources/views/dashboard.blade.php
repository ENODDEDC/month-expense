<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name') }}</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Expense Tracker">
    <meta name="description" content="Track your expenses with budget management and analytics">
    <meta name="format-detection" content="telephone=no">
    <meta name="msapplication-TileColor" content="#4f46e5">
    <meta name="msapplication-tap-highlight" content="no">
    
    <!-- PWA Icons -->
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#4f46e5">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        bounceIn: {
                            '0%': { opacity: '0', transform: 'scale(0.3)' },
                            '50%': { opacity: '1', transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }
        .gradient-border {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #06b6d4);
            padding: 2px;
            border-radius: 12px;
        }
        .gradient-border-content {
            background: white;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <div class="min-h-screen py-4 px-4">
        <div class="max-w-6xl mx-auto" x-data="expenseTracker()">
            <!-- Enhanced Header with Stats -->
            <div class="gradient-border mb-4 animate-fade-in">
                <div class="gradient-border-content">
                    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white p-4 rounded-lg relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
                            <div class="absolute bottom-0 right-0 w-24 h-24 bg-white rounded-full translate-x-12 translate-y-12"></div>
                        </div>
                        
                        <div class="relative z-10">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-6 md:mb-0">
                                    <h1 class="text-4xl font-bold mb-2 animate-slide-up">💰 Expense Tracker</h1>
                                    <p class="text-blue-100 text-lg animate-slide-up" x-text="getCurrentMonthName()"></p>
                                    @auth
                                        <p class="text-blue-200 text-sm mt-1">Welcome, {{ Auth::user()->first_name }}!</p>
                                    @endauth
                                </div>
                                
                                <!-- Quick Stats with Currency Switcher and Logout -->
                                <div class="flex flex-col space-y-4">
                                    <!-- PWA Controls, Currency Switcher and Logout -->
                                    <div class="flex justify-end space-x-2">
                                        <!-- PWA Install Button -->
                                        <button 
                                            x-show="showInstallButton"
                                            @click="installPWA()"
                                            class="glass-effect rounded-lg px-4 py-2 text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center space-x-2"
                                            title="Install App"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Install</span>
                                        </button>

                                        <!-- Offline Status Indicator -->
                                        <div 
                                            x-show="!isOnline()"
                                            class="glass-effect rounded-lg px-4 py-2 text-sm font-medium text-white flex items-center space-x-2"
                                            title="You're offline - changes will sync when back online"
                                        >
                                            <div class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></div>
                                            <span>Offline</span>
                                            <span x-show="offlineDataCount > 0" x-text="'(' + offlineDataCount + ')'"></span>
                                        </div>

                                        <button 
                                            @click="switchCurrency()"
                                            class="glass-effect rounded-lg px-4 py-2 text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center space-x-2"
                                        >
                                            <span x-text="currentCurrency.symbol"></span>
                                            <span x-text="currentCurrency.code"></span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                        </button>
                                        @auth
                                        <form method="POST" action="{{ route('logout') }}" class="inline">
                                            @csrf
                                            <button 
                                                type="submit"
                                                class="glass-effect rounded-lg px-4 py-2 text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center space-x-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                        @endauth
                                    </div>
                                    
                                    <!-- Stats Grid with Budget -->
                                    <div class="grid grid-cols-2 gap-4 animate-bounce-in">
                                        <div class="glass-effect rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold" x-text="formatCurrency(getCurrentMonthTotal())"></div>
                                            <div class="text-xs text-blue-200">This Month</div>
                                            <!-- Budget Progress -->
                                            <div x-show="monthlyBudget > 0" class="mt-2">
                                                <div class="w-full bg-white/20 rounded-full h-1">
                                                    <div 
                                                        class="h-1 rounded-full transition-all duration-500"
                                                        :class="getBudgetProgress().isOverBudget ? 'bg-red-400' : 'bg-green-400'"
                                                        :style="'width: ' + Math.min(getBudgetProgress().percentage, 100) + '%'"
                                                    ></div>
                                                </div>
                                                <div class="text-xs mt-1" :class="getBudgetProgress().isOverBudget ? 'text-red-200' : 'text-green-200'">
                                                    <span x-text="getBudgetProgress().percentage.toFixed(0) + '% of budget'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="glass-effect rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold" x-text="getMonthlyExpenses().length"></div>
                                            <div class="text-xs text-blue-200">Expenses</div>
                                            <!-- Budget Button -->
                                            <button 
                                                @click="setBudget()"
                                                class="mt-2 text-xs text-blue-200 hover:text-white transition-colors"
                                            >
                                                <span x-show="!monthlyBudget || monthlyBudget <= 0">Set Budget</span>
                                                <span x-show="monthlyBudget && monthlyBudget > 0" x-text="'Budget: ' + formatBudgetAmount(monthlyBudget)"></span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Exchange Rate Info -->
                                    <div x-show="currentCurrency.code === 'PHP'" class="text-center">
                                        <div class="text-xs text-blue-200" x-text="'1 USD = ₱' + exchangeRate.toFixed(2)"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offline Banner - Shows when user is offline -->
            <div 
                x-show="!isOnline()"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="bg-gradient-to-r from-orange-500 to-red-500 text-white p-4 rounded-xl shadow-lg mb-4 animate-pulse"
            >
                <div class="flex items-center justify-center space-x-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                        <span class="font-bold text-lg">📱 OFFLINE MODE</span>
                    </div>
                    <div class="hidden md:block text-sm opacity-90">
                        You can still add expenses - they'll sync when you're back online
                    </div>
                    <div x-show="offlineDataCount > 0" class="bg-white/20 rounded-full px-3 py-1 text-sm font-medium">
                        <span x-text="offlineDataCount"></span> pending
                    </div>
                </div>
            </div>

            <!-- Enhanced Navigation Tabs -->
            <div class="bg-white rounded-xl shadow-lg mb-4 overflow-hidden animate-slide-up">
                <nav class="flex">
                    <button
                        @click="activeTab = 'calendar'"
                        :class="activeTab === 'calendar' ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center py-4 px-6 font-semibold text-sm transition-all duration-300 transform hover:scale-105"
                    >
                        <span class="text-xl mr-3">📅</span>
                        <span>Calendar</span>
                    </button>
                    <button
                        @click="activeTab = 'expenses'"
                        :class="activeTab === 'expenses' ? 'bg-gradient-to-r from-red-500 to-pink-500 text-white shadow-lg' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center py-4 px-6 font-semibold text-sm transition-all duration-300 transform hover:scale-105"
                    >
                        <span class="text-xl mr-3">💸</span>
                        <span>Expenses</span>
                    </button>
                    <button
                        @click="activeTab = 'add'"
                        :class="activeTab === 'add' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center py-4 px-6 font-semibold text-sm transition-all duration-300 transform hover:scale-105"
                    >
                        <span class="text-xl mr-3">➕</span>
                        <span>Add Expense</span>
                    </button>
                    <!-- Offline Data Tab - Only show when there's offline data -->
                    <button
                        x-show="offlineDataCount > 0"
                        @click="activeTab = 'offline'"
                        :class="activeTab === 'offline' ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center py-4 px-6 font-semibold text-sm transition-all duration-300 transform hover:scale-105"
                    >
                        <span class="text-xl mr-3">📱</span>
                        <span>Offline</span>
                        <span class="ml-1 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full" x-text="offlineDataCount"></span>
                    </button>
                    <button
                        @click="activeTab = 'summary'"
                        :class="activeTab === 'summary' ? 'bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow-lg' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center py-4 px-6 font-semibold text-sm transition-all duration-300 transform hover:scale-105"
                    >
                        <span class="text-xl mr-3">📊</span>
                        <span>Summary</span>
                    </button>
                </nav>
            </div>

            <!-- Enhanced Tab Content -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-fade-in">
                <!-- Calendar Tab -->
                <div x-show="activeTab === 'calendar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-4">
                <!-- Compact Calendar Header -->
                <div class="flex items-center justify-between mb-4">
                    <button
                        @click="navigateMonth(-1)"
                        class="p-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 text-white hover:from-blue-600 hover:to-purple-600 transition-all duration-300 transform hover:scale-110 shadow-lg"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent" x-text="getCurrentMonthName()"></h2>
                    <button
                        @click="navigateMonth(1)"
                        class="p-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 text-white hover:from-blue-600 hover:to-purple-600 transition-all duration-300 transform hover:scale-110 shadow-lg"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Compact Weekday Headers -->
                <div class="grid grid-cols-7 gap-1 mb-1">
                    <template x-for="day in weekdays">
                        <div class="p-1 text-center font-semibold text-gray-600 text-xs" x-text="day"></div>
                    </template>
                </div>

                <!-- Compact Calendar Grid - Fits Screen -->
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="(day, index) in getCalendarDays()" :key="index">
                        <div
                            @click="day && openExpenseModal(day)"
                            :class="{
                                'bg-gradient-to-br from-blue-100 to-blue-200 border-blue-400 shadow-md': isToday(day),
                                'bg-gradient-to-br from-green-50 to-green-100 border-green-300': day && getExpensesForDate(day).length > 0 && !isToday(day),
                                'hover:shadow-lg hover:scale-102': day
                            }"
                            class="p-2 h-16 border border-gray-200 rounded-lg cursor-pointer transition-all duration-300 hover:bg-gray-50 relative overflow-hidden"
                        >
                            <div class="flex flex-col h-full justify-between" x-show="day">
                                <div class="flex justify-between items-start">
                                    <span
                                        :class="isToday(day) ? 'text-blue-700 font-bold' : 'text-gray-700'"
                                        class="text-xs sm:text-sm font-medium"
                                        x-text="day"
                                    ></span>
                                    <!-- Expense indicator dot -->
                                    <div x-show="day && getExpensesForDate(day).length > 0" class="w-2 h-2 bg-red-500 rounded-full animate-pulse flex-shrink-0"></div>
                                </div>
                                <div x-show="day && getExpensesForDate(day).length > 0" class="mt-1">
                                    <div class="text-xs font-bold text-red-600 truncate" x-text="formatCurrency(getTotalForDate(day))"></div>
                                    <div class="text-xs text-gray-500 truncate" x-text="getExpensesForDate(day).length + ' item' + (getExpensesForDate(day).length !== 1 ? 's' : '')"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Compact Legend -->
                <div class="mt-3 flex items-center justify-center space-x-4 text-xs text-gray-600">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded mr-1"></div>
                        <span>Today</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-50 border border-green-200 rounded mr-1"></div>
                        <span>Has Expenses</span>
                    </div>
                </div>

                <!-- Expense Modal -->
                <div x-show="showExpenseModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeExpenseModal()">
                    <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-hidden">
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-2xl font-bold">Add Expense</h2>
                                    <p class="text-green-100 mt-1" x-text="getSelectedDateFormatted()"></p>
                                </div>
                                <button @click="closeExpenseModal()" class="text-white hover:text-green-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-6">
                            <form @submit.prevent="addExpenseFromModal()" class="space-y-4">
                                <!-- Category Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Category</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <template x-for="category in categories">
                                            <label
                                                :class="modalExpense.category === category.value ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                                                class="relative flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                            >
                                                <input
                                                    type="radio"
                                                    x-model="modalExpense.category"
                                                    :value="category.value"
                                                    class="sr-only"
                                                />
                                                <span class="text-2xl mb-1" x-text="category.icon"></span>
                                                <span class="text-xs font-medium text-gray-700 text-center" x-text="category.value"></span>
                                                <div x-show="modalExpense.category === category.value" class="absolute top-1 right-1">
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Description Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <input
                                        type="text"
                                        x-model="modalExpense.description"
                                        placeholder="e.g., Lunch at restaurant, Gas for car..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                        required
                                    />
                                </div>

                                <!-- Amount Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="'Amount (' + currentCurrency.symbol + ')'"></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-lg" x-text="currentCurrency.symbol"></span>
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            x-model="modalExpense.amount"
                                            placeholder="0.00"
                                            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                            required
                                        />
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="flex space-x-3 pt-4">
                                    <button
                                        type="button"
                                        @click="closeExpenseModal()"
                                        class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 px-4 rounded-lg font-medium hover:from-green-600 hover:to-emerald-600 transition-all duration-200"
                                    >
                                        Add Expense
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Expenses Tab -->
            <div x-show="activeTab === 'expenses'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-8">
                <!-- Search and Filter Bar -->
                <div x-show="getMonthlyExpenses().length > 0" class="mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Search Input -->
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        x-model="searchQuery"
                                        placeholder="Search expenses..."
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="md:w-48">
                                <select x-model="selectedCategory" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all">All Categories</option>
                                    <template x-for="category in categories">
                                        <option :value="category.value" x-text="category.icon + ' ' + category.value"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <!-- Filter Toggle -->
                            <button
                                @click="showFilters = !showFilters"
                                :class="showFilters ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'"
                                class="px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z" />
                                </svg>
                                <span>Filters</span>
                            </button>
                        </div>
                        
                        <!-- Advanced Filters -->
                        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Date Range -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                                    <div class="flex space-x-2">
                                        <input
                                            type="date"
                                            x-model="dateRange.start"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Start date"
                                        />
                                        <input
                                            type="date"
                                            x-model="dateRange.end"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="End date"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Clear Filters -->
                                <div class="flex items-end">
                                    <button
                                        @click="clearFilters()"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                                    >
                                        Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="getMonthlyExpenses().length === 0">
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">💸</div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No expenses yet</h3>
                        <p class="text-gray-500">Add your first expense to get started!</p>
                    </div>
                </div>

                <div x-show="getMonthlyExpenses().length > 0">
                    <!-- Summary Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-lg mb-6 border border-blue-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800" x-text="getCurrentMonthName() + ' Summary'"></h3>
                                <p class="text-gray-600" x-text="getMonthlyExpenses().length + ' total expenses'"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-blue-600" x-text="formatCurrency(getCurrentMonthTotal())"></p>
                                <p class="text-sm text-gray-600">Total spent</p>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Expenses List -->
                    <div class="space-y-4">
                        <template x-for="expense in getFilteredExpenses()"
                            <div class="gradient-border animate-slide-up">
                                <div class="gradient-border-content">
                                    <div class="bg-white rounded-lg p-6 hover:shadow-lg transition-all duration-300 transform hover:scale-102">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center flex-1">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center mr-4">
                                                    <span class="text-2xl" x-text="getCategoryIcon(expense.category)"></span>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-bold text-gray-900 text-lg flex items-center">
                                                        <span x-text="expense.description"></span>
                                                        <span x-show="expense.offline" class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Offline</span>
                                                    </h4>
                                                    <p class="text-sm text-gray-500 flex items-center">
                                                        <span class="inline-block w-2 h-2 rounded-full mr-2" :class="expense.offline ? 'bg-orange-500' : 'bg-blue-500'"></span>
                                                        <span x-text="expense.category + ' • ' + formatDate(expense.date)"></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent" x-text="formatCurrency(expense.amount)"></p>
                                                    <p class="text-xs text-gray-400">Amount</p>
                                                </div>
                                                <!-- Action Buttons -->
                                                <div class="flex space-x-2">
                                                    <button
                                                        @click="duplicateExpense(expense)"
                                                        class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-all duration-200"
                                                        title="Duplicate expense"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        @click="openEditModal(expense)"
                                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                                        title="Edit expense"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        @click="confirmDelete(expense)"
                                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-200"
                                                        title="Delete expense"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Add Expense Tab -->
            <div x-show="activeTab === 'add'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-8">
                <div class="max-w-2xl mx-auto">
                    <!-- Enhanced Header -->
                    <div class="text-center mb-8 animate-bounce-in">
                        <div class="text-6xl mb-4 animate-pulse-slow">💰</div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">Add New Expense</h2>
                        <p class="text-gray-600 text-lg">Track your spending by adding a new expense</p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="addExpense()" class="space-y-6">
                        <!-- Date Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input
                                type="date"
                                x-model="newExpense.date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                required
                            />
                        </div>

                        <!-- Category Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Category</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <template x-for="category in categories">
                                    <label
                                        :class="newExpense.category === category.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                                        class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                    >
                                        <input
                                            type="radio"
                                            x-model="newExpense.category"
                                            :value="category.value"
                                            class="sr-only"
                                        />
                                        <span class="text-2xl mr-3" x-text="category.icon"></span>
                                        <span class="font-medium text-gray-700" x-text="category.value"></span>
                                        <div x-show="newExpense.category === category.value" class="absolute top-2 right-2">
                                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Description Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input
                                type="text"
                                x-model="newExpense.description"
                                placeholder="e.g., Lunch at restaurant, Gas for car..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                required
                            />
                        </div>

                        <!-- Amount Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="'Amount (' + currentCurrency.symbol + ')'"></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-lg" x-text="currentCurrency.symbol"></span>
                                </div>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    x-model="newExpense.amount"
                                    placeholder="0.00"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105"
                            >
                                Add Expense
                            </button>
                        </div>
                    </form>

                    <!-- Quick Add Suggestions -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Quick Add Suggestions</h3>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="suggestion in quickSuggestions">
                                <button
                                    @click="applyQuickSuggestion(suggestion)"
                                    class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-full hover:bg-gray-100 transition-colors"
                                    x-text="suggestion.desc + ' ($' + suggestion.amount + ')'"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- Keyboard Shortcuts Help -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="text-sm font-medium text-blue-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Keyboard Shortcuts
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-700">
                            <div class="flex justify-between">
                                <span>New Expense:</span>
                                <kbd class="px-2 py-1 bg-blue-100 rounded text-xs">Ctrl + N</kbd>
                            </div>
                            <div class="flex justify-between">
                                <span>Search Expenses:</span>
                                <kbd class="px-2 py-1 bg-blue-100 rounded text-xs">Ctrl + F</kbd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offline Data Tab -->
            <div x-show="activeTab === 'offline'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-8">
                <div class="text-center mb-8 animate-bounce-in">
                    <div class="text-6xl mb-4">📱</div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-2">Offline Data</h2>
                    <p class="text-gray-600 text-lg">Expenses saved while offline</p>
                </div>

                <div x-show="getOfflineExpenses().length === 0" class="text-center py-12">
                    <div class="text-6xl mb-4">✨</div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No offline data</h3>
                    <p class="text-gray-500">Add expenses while offline and they'll appear here</p>
                </div>

                <div x-show="getOfflineExpenses().length > 0">
                    <!-- Offline Summary Card -->
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 p-6 rounded-lg mb-6 border border-orange-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Pending Sync</h3>
                                <p class="text-gray-600" x-text="getOfflineExpenses().length + ' expenses waiting to sync'"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-orange-600" x-text="formatCurrency(getOfflineExpensesTotal())"></p>
                                <p class="text-sm text-gray-600">Total amount</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button @click="syncOfflineData()" class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                                Sync Now
                            </button>
                        </div>
                    </div>

                    <!-- Offline Expenses List -->
                    <div class="space-y-4">
                        <template x-for="expense in getOfflineExpenses()">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 animate-slide-up">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center flex-1">
                                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center mr-4">
                                            <span class="text-2xl" x-text="getCategoryIcon(expense.category)"></span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 text-lg flex items-center">
                                                <span x-text="expense.description"></span>
                                                <span class="ml-2 px-2 py-1 bg-orange-200 text-orange-800 text-xs rounded-full">Pending Sync</span>
                                            </h4>
                                            <p class="text-sm text-gray-600" x-text="expense.category + ' • ' + formatDate(expense.date)"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="'Saved: ' + formatDate(expense.timestamp)"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-orange-600" x-text="formatCurrency(expense.amount)"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Enhanced Summary Tab -->
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-8">
                <div class="text-center mb-8 animate-bounce-in">
                    <div class="text-6xl mb-4 animate-pulse-slow">📊</div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent mb-2">Monthly Summary</h2>
                    <p class="text-gray-600 text-lg">Your financial overview for <span x-text="getCurrentMonthName()"></span></p>
                </div>

                <!-- Smart Insights -->
                <div x-show="getMonthlyExpenses().length > 0" class="mb-8">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6 border border-purple-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">🧠</span>
                            Smart Insights
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <!-- Top Category -->
                            <div class="bg-white rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <span class="text-lg mr-2">🏆</span>
                                    <span class="font-medium text-gray-700">Top Category</span>
                                </div>
                                <p class="text-gray-600" x-text="getTopCategory()"></p>
                            </div>
                            
                            <!-- Average Transaction -->
                            <div class="bg-white rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <span class="text-lg mr-2">💰</span>
                                    <span class="font-medium text-gray-700">Avg Transaction</span>
                                </div>
                                <p class="text-gray-600" x-text="formatCurrency(getAverageTransaction())"></p>
                            </div>
                            
                            <!-- Budget Status -->
                            <div x-show="monthlyBudget > 0" class="bg-white rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <span class="text-lg mr-2" x-text="getBudgetProgress().isOverBudget ? '⚠️' : '✅'"></span>
                                    <span class="font-medium text-gray-700">Budget Status</span>
                                </div>
                                <p class="text-gray-600" x-text="getBudgetProgress().isOverBudget ? 'Over budget by ' + formatBudgetAmount(getBudgetProgress().spent - monthlyBudget) : 'Under budget by ' + formatBudgetAmount(getBudgetProgress().remaining)"></p>
                            </div>
                            
                            <!-- Spending Trend -->
                            <div class="bg-white rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <span class="text-lg mr-2">📈</span>
                                    <span class="font-medium text-gray-700">This Week</span>
                                </div>
                                <p class="text-gray-600" x-text="formatCurrency(getWeeklySpending()) + ' spent'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Summary Cards -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Total Expenses Card -->
                    <div class="gradient-border animate-slide-up">
                        <div class="gradient-border-content">
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 p-6 rounded-lg text-center">
                                <div class="text-4xl mb-3">💸</div>
                                <p class="text-3xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent mb-2" x-text="formatCurrency(getCurrentMonthTotal())"></p>
                                <p class="text-gray-600 font-medium">Total Expenses</p>
                                <p class="text-sm text-gray-500 mt-1" x-text="getMonthlyExpenses().length + ' transactions'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Average Daily Spending -->
                    <div class="gradient-border animate-slide-up" style="animation-delay: 0.1s">
                        <div class="gradient-border-content">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-lg text-center">
                                <div class="text-4xl mb-3">📈</div>
                                <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2" x-text="formatCurrency(dailyAverage())"></p>
                                <p class="text-gray-600 font-medium">Daily Average</p>
                                <p class="text-sm text-gray-500 mt-1">Based on current month</p>
                            </div>
                        </div>
                    </div>

                    <!-- Largest Expense -->
                    <div class="gradient-border animate-slide-up" style="animation-delay: 0.2s">
                        <div class="gradient-border-content">
                            <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-6 rounded-lg text-center">
                                <div class="text-4xl mb-3">🎯</div>
                                <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-violet-600 bg-clip-text text-transparent mb-2" x-text="formatCurrency(largestExpense())"></p>
                                <p class="text-gray-600 font-medium">Largest Expense</p>
                                <p class="text-sm text-gray-500 mt-1">Single transaction</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="gradient-border animate-fade-in" style="animation-delay: 0.3s">
                    <div class="gradient-border-content">
                        <div class="bg-white p-6 rounded-lg">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <span class="text-2xl mr-3">📋</span>
                                Category Breakdown
                            </h3>
                            <div class="space-y-4">
                                <template x-for="category in categories">
                                    <div x-show="getMonthlyExpenses().filter(e => e.category === category.value).length > 0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <span class="text-xl mr-3" x-text="category.icon"></span>
                                                <span class="font-medium text-gray-700" x-text="category.value"></span>
                                            </div>
                                            <div class="text-right">
                                                <span class="font-bold text-gray-900" x-text="formatCurrency(getMonthlyExpenses().filter(e => e.category === category.value).reduce((sum, e) => sum + e.amount, 0))"></span>
                                                <span class="text-sm text-gray-500 ml-2" x-text="'(' + getMonthlyExpenses().filter(e => e.category === category.value).length + ')'"></span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div 
                                                class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-500"
                                                :style="'width: ' + (getMonthlyExpenses().filter(e => e.category === category.value).reduce((sum, e) => sum + e.amount, 0) / getCurrentMonthTotal() * 100) + '%'"
                                            ></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Notification Toast - Inside Alpine.js scope -->
            <div
                x-show="toastVisible"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="transform translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="toastType === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'"
                class="fixed bottom-5 right-5 w-full max-w-xs shadow-lg rounded-lg pointer-events-auto border-2 overflow-hidden z-[9999]"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Success Icon -->
                            <svg x-show="toastType === 'success'" class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Error Icon -->
                            <svg x-show="toastType === 'error'" class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p :class="toastType === 'success' ? 'text-green-800' : 'text-red-800'" class="text-sm font-medium" x-text="toastMessage"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="toastVisible = false" :class="toastType === 'success' ? 'text-green-400 hover:text-green-500' : 'text-red-400 hover:text-red-500'" class="rounded-md inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Expense Modal -->
            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9998]" @click="closeEditModal()">
                <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold">Edit Expense</h2>
                                <p class="text-blue-100 mt-1">Update your expense details</p>
                            </div>
                            <button @click="closeEditModal()" class="text-white hover:text-blue-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        <form @submit.prevent="updateExpense()" class="space-y-4">
                            <!-- Date Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input
                                    type="date"
                                    x-model="editExpense.date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    required
                                />
                            </div>

                            <!-- Category Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Category</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="category in categories">
                                        <label
                                            :class="editExpense.category === category.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                                            class="relative flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                        >
                                            <input
                                                type="radio"
                                                x-model="editExpense.category"
                                                :value="category.value"
                                                class="sr-only"
                                            />
                                            <span class="text-2xl mb-1" x-text="category.icon"></span>
                                            <span class="text-xs font-medium text-gray-700 text-center" x-text="category.value"></span>
                                            <div x-show="editExpense.category === category.value" class="absolute top-1 right-1">
                                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Description Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <input
                                    type="text"
                                    x-model="editExpense.description"
                                    placeholder="e.g., Lunch at restaurant, Gas for car..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    required
                                />
                            </div>

                            <!-- Amount Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" x-text="'Amount (' + currentCurrency.symbol + ')'"></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-lg" x-text="currentCurrency.symbol"></span>
                                    </div>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        x-model="editExpense.amount"
                                        placeholder="0.00"
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex space-x-3 pt-4">
                                <button
                                    type="button"
                                    @click="closeEditModal()"
                                    class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-200"
                                >
                                    Update Expense
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9998]" @click="closeDeleteModal()">
                <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold">Delete Expense</h2>
                                <p class="text-red-100 mt-1">This action cannot be undone</p>
                            </div>
                            <button @click="closeDeleteModal()" class="text-white hover:text-red-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Are you sure you want to delete this expense?</h3>
                            <div x-show="expenseToDelete" class="bg-gray-50 rounded-lg p-4 mb-4">
                                <p class="font-semibold text-gray-800" x-text="expenseToDelete?.description"></p>
                                <p class="text-sm text-gray-600" x-text="expenseToDelete?.category + ' • ' + formatDate(expenseToDelete?.date)"></p>
                                <p class="text-lg font-bold text-red-600" x-text="formatCurrency(expenseToDelete?.amount)"></p>
                            </div>
                            <p class="text-sm text-gray-500">This expense will be permanently removed from your records.</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-3">
                            <button
                                @click="closeDeleteModal()"
                                class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteExpense()"
                                class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 text-white py-3 px-4 rounded-lg font-medium hover:from-red-600 hover:to-pink-600 transition-all duration-200"
                            >
                                Delete Expense
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Modal -->
            <div x-show="showBudgetModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9998]" @click="showBudgetModal = false">
                <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold">Set Monthly Budget</h2>
                                <p class="text-green-100 mt-1">Track your spending goals</p>
                            </div>
                            <button @click="showBudgetModal = false" class="text-white hover:text-green-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        <form @submit.prevent="saveBudget()" class="space-y-4">
                            <!-- Budget Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" x-text="'Monthly Budget (' + currentCurrency.symbol + ')'"></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-lg" x-text="currentCurrency.symbol"></span>
                                    </div>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        x-model="monthlyBudget"
                                        placeholder="0.00"
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                        required
                                    />
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Set your monthly spending limit to track your budget progress.</p>
                            </div>

                            <!-- Current Progress -->
                            <div x-show="monthlyBudget > 0" class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">Current Month Progress</h4>
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>Spent: <span x-text="formatBudgetAmount(getBudgetProgress().spent)"></span></span>
                                    <span>Remaining: <span x-text="formatBudgetAmount(getBudgetProgress().remaining)"></span></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        class="h-2 rounded-full transition-all duration-500"
                                        :class="(getCurrentMonthTotal() / monthlyBudget * 100) > 100 ? 'bg-red-500' : 'bg-green-500'"
                                        :style="'width: ' + Math.min((getCurrentMonthTotal() / monthlyBudget * 100), 100) + '%'"
                                    ></div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex space-x-3 pt-4">
                                <button
                                    type="button"
                                    @click="showBudgetModal = false"
                                    class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 px-4 rounded-lg font-medium hover:from-green-600 hover:to-emerald-600 transition-all duration-200"
                                >
                                    Save Budget
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <script>
        function expenseTracker() {
            return {
                activeTab: 'calendar',
                selectedDate: new Date(),
                
                // Currency system with localStorage persistence
                currentCurrency: { code: 'USD', symbol: '$', name: 'US Dollar' },
                exchangeRate: 58.013569, // Default PHP rate
                currencies: {
                    USD: { code: 'USD', symbol: '$', name: 'US Dollar' },
                    PHP: { code: 'PHP', symbol: '₱', name: 'Philippine Peso' }
                },
                
                expenses: @json($expenses),
                newExpense: {
                    date: new Date().toISOString().split('T')[0],
                    category: 'Food',
                    description: '',
                    amount: ''
                },
                categories: [
                    { value: 'Food', icon: '🍽️', color: 'bg-orange-100 text-orange-800' },
                    { value: 'Transport', icon: '🚗', color: 'bg-blue-100 text-blue-800' },
                    { value: 'Shopping', icon: '🛒', color: 'bg-green-100 text-green-800' },
                    { value: 'Entertainment', icon: '🎬', color: 'bg-purple-100 text-purple-800' },
                    { value: 'Bills', icon: '📄', color: 'bg-red-100 text-red-800' },
                    { value: 'Other', icon: '📝', color: 'bg-gray-100 text-gray-800' }
                ],
                quickSuggestions: [
                    { desc: 'Coffee', amount: 5.00, cat: 'Food' },
                    { desc: 'Lunch', amount: 15.00, cat: 'Food' },
                    { desc: 'Gas', amount: 40.00, cat: 'Transport' },
                    { desc: 'Groceries', amount: 80.00, cat: 'Shopping' }
                ],
                weekdays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'],
                
                // Modal properties
                showExpenseModal: false,
                selectedModalDate: null,
                modalExpense: {
                    category: 'Food',
                    description: '',
                    amount: ''
                },

                // Edit/Delete modal properties
                showEditModal: false,
                showDeleteModal: false,
                editExpense: {
                    id: null,
                    date: '',
                    category: 'Food',
                    description: '',
                    amount: ''
                },
                expenseToDelete: null,

                // Search & Filter properties
                searchQuery: '',
                selectedCategory: 'all',
                dateRange: { start: '', end: '' },
                showFilters: false,

                // Budget Management properties
                monthlyBudget: 0,
                categoryBudgets: {},
                showBudgetModal: false,

                // Theme properties
                currentTheme: 'light',
                
                // Custom categories
                customCategories: [],
                showCategoryModal: false,
                newCategory: { name: '', icon: '📝', color: 'bg-gray-100' },

                // Toast notification properties
                toastVisible: false,
                toastMessage: '',
                toastType: 'success', // 'success' or 'error'

                // PWA properties
                showInstallButton: false,
                isOffline: false,
                offlineDataCount: 0,
                onlineStatus: navigator.onLine, // Track online status reactively
                syncInProgress: false, // Prevent multiple sync calls
                
                // Initialize offline data count on load
                initOfflineDataCount() {
                    const offlineData = JSON.parse(localStorage.getItem('expense_tracker_offline_data') || '[]');
                    this.offlineDataCount = offlineData.length;
                },

                // Update offline data count
                updateOfflineDataCount() {
                    const offlineData = JSON.parse(localStorage.getItem('expense_tracker_offline_data') || '[]');
                    this.offlineDataCount = offlineData.length;
                },

                getCurrentMonthName() {
                    return this.monthNames[this.selectedDate.getMonth()] + ' ' + this.selectedDate.getFullYear();
                },

                navigateMonth(direction) {
                    const newDate = new Date(this.selectedDate);
                    newDate.setMonth(newDate.getMonth() + direction);
                    this.selectedDate = newDate;
                },

                getCalendarDays() {
                    const year = this.selectedDate.getFullYear();
                    const month = this.selectedDate.getMonth();
                    const firstDayOfMonth = new Date(year, month, 1);
                    const lastDayOfMonth = new Date(year, month + 1, 0);
                    const firstDayWeekday = firstDayOfMonth.getDay();
                    const daysInMonth = lastDayOfMonth.getDate();

                    const calendarDays = [];
                    
                    // Add empty cells for days before the first day of the month
                    for (let i = 0; i < firstDayWeekday; i++) {
                        calendarDays.push(null);
                    }
                    
                    // Add days of the month
                    for (let day = 1; day <= daysInMonth; day++) {
                        calendarDays.push(day);
                    }
                    
                    return calendarDays;
                },

                isToday(day) {
                    if (!day) return false;
                    const today = new Date();
                    return today.getDate() === day &&
                           today.getMonth() === this.selectedDate.getMonth() &&
                           today.getFullYear() === this.selectedDate.getFullYear();
                },

                getExpensesForDate(day) {
                    if (!day) return [];
                    const year = this.selectedDate.getFullYear();
                    const month = this.selectedDate.getMonth();
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    return this.expenses.filter(expense => {
                        // Handle cases where expense.date might include time
                        const expenseDate = expense.date.split('T')[0];
                        return expenseDate === dateStr;
                    });
                },

                getTotalForDate(day) {
                    const dayExpenses = this.getExpensesForDate(day);
                    return dayExpenses.reduce((total, expense) => total + parseFloat(expense.amount || 0), 0);
                },

                getMonthlyExpenses() {
                    const currentMonth = this.selectedDate.getMonth();
                    const currentYear = this.selectedDate.getFullYear();
                    return this.expenses.filter(expense => {
                        const expenseDate = new Date(expense.date);
                        return expenseDate.getMonth() === currentMonth && expenseDate.getFullYear() === currentYear;
                    });
                },

                getCurrentMonthTotal() {
                    return this.getMonthlyExpenses().reduce((total, expense) => total + parseFloat(expense.amount || 0), 0);
                },

                getCategoryIcon(category) {
                    const icons = {
                        'Food': '🍽️',
                        'Transport': '🚗',
                        'Shopping': '🛒',
                        'Entertainment': '🎬',
                        'Bills': '📄',
                        'Other': '📝'
                    };
                    return icons[category] || '📝';
                },

                formatDate(dateStr) {
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                async addExpense() {
                    if (!this.newExpense.description.trim() || !this.newExpense.amount) {
                        this.showToast('Please fill in all fields', 'error');
                        return;
                    }

                    let amountInUSD = parseFloat(this.newExpense.amount);
                    if (this.currentCurrency.code === 'PHP') {
                        amountInUSD = amountInUSD / this.exchangeRate;
                    }

                    const newExpenseData = {
                        date: this.newExpense.date,
                        category: this.newExpense.category,
                        description: this.newExpense.description.trim(),
                        amount: amountInUSD
                    };

                    // Check if online with debugging
                    console.log('=== ADD EXPENSE DEBUG ===');
                    console.log('Navigator online status:', navigator.onLine);
                    console.log('Our online status:', this.onlineStatus);
                    console.log('Offline banner showing:', !this.isOnline());
                    
                    if (!this.onlineStatus) {
                        console.log('OFFLINE MODE: Saving expense locally only (will NOT hit database)');
                        // Handle offline - add to main array for immediate display AND store for sync
                        const offlineExpense = {
                            id: 'offline_' + Date.now(), // Unique offline ID
                            ...newExpenseData,
                            offline: true,
                            created_at: new Date().toISOString()
                        };
                        
                        // Add to main array for immediate calendar display
                        this.expenses.push(offlineExpense);
                        
                        // Store for later sync
                        await this.storeOfflineExpense(newExpenseData);
                        
                        // Update offline data count
                        this.updateOfflineDataCount();
                        
                        // Reset form
                        this.newExpense = {
                            date: new Date().toISOString().split('T')[0],
                            category: 'Food',
                            description: '',
                            amount: ''
                        };
                        
                        this.showToast('Expense saved offline! Will sync when back online.', 'success');
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('expenses.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(newExpenseData)
                        });

                        if (!response.ok) {
                            // Handle 419 Page Expired error
                            if (this.handle419Error(response)) return;
                            
                            const errorData = await response.json();
                            console.error('Error response:', errorData);
                            this.showToast('Failed to add expense. Please try again.', 'error');
                            return;
                        }

                        const createdExpense = await response.json();
                        this.expenses.push(createdExpense);

                        // Reset form
                        this.newExpense = {
                            date: new Date().toISOString().split('T')[0],
                            category: 'Food',
                            description: '',
                            amount: ''
                        };
                        
                        // Show success toast notification
                        this.showToast('Expense added successfully!', 'success');

                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        
                        // If network error, treat as offline - only store in localStorage
                        await this.storeOfflineExpense(newExpenseData);
                        
                        // Update offline data count
                        this.updateOfflineDataCount();
                        
                        // Reset form
                        this.newExpense = {
                            date: new Date().toISOString().split('T')[0],
                            category: 'Food',
                            description: '',
                            amount: ''
                        };
                        
                        this.showToast('Network error - expense saved offline!', 'success');
                    }
                },

                // Store expense for offline sync
                async storeOfflineExpense(expenseData) {
                    const offlineData = JSON.parse(localStorage.getItem('expense_tracker_offline_data') || '[]');
                    
                    const offlineItem = {
                        id: Date.now(),
                        method: 'POST',
                        endpoint: '{{ route('expenses.store') }}',
                        data: expenseData,
                        timestamp: new Date().toISOString()
                    };
                    
                    offlineData.push(offlineItem);
                    localStorage.setItem('expense_tracker_offline_data', JSON.stringify(offlineData));
                    
                    // Update offline data count using the new function
                    this.updateOfflineDataCount();
                },

                applyQuickSuggestion(suggestion) {
                    this.newExpense.description = suggestion.desc;
                    this.newExpense.amount = suggestion.amount.toString();
                    this.newExpense.category = suggestion.cat;
                },

                // Currency functionality with localStorage persistence
                async init() {
                    await this.fetchExchangeRate();
                    this.loadCurrencyPreference();
                    // Load budget after currency and exchange rate are set
                    this.loadBudget();
                    this.loadTheme();
                    
                    // Initialize offline data count
                    this.initOfflineDataCount();
                    
                    // Add keyboard shortcuts
                    document.addEventListener('keydown', this.handleKeyboardShortcuts.bind(this));
                    
                    // Initialize PWA functionality
                    this.initPWA();
                    
                    // Refresh CSRF token periodically to prevent 419 errors
                    this.initCSRFRefresh();
                    
                    // Add real-time online/offline detection
                    this.initOnlineOfflineDetection();
                },

                // Initialize real-time online/offline detection
                initOnlineOfflineDetection() {
                    // Listen for online/offline events
                    window.addEventListener('online', () => {
                        console.log('Back online!');
                        this.onlineStatus = true; // Update reactive property
                        this.showToast('Back online! Syncing data...', 'success');
                        this.syncOfflineData();
                        this.updateOfflineDataCount();
                    });

                    window.addEventListener('offline', () => {
                        console.log('Gone offline!');
                        this.onlineStatus = false; // Update reactive property
                        this.showToast('You\'re offline. Changes will sync when back online.', 'error');
                        this.updateOfflineDataCount();
                    });

                    // Check connection status periodically and update reactive property
                    setInterval(() => {
                        const currentStatus = navigator.onLine;
                        if (this.onlineStatus !== currentStatus) {
                            this.onlineStatus = currentStatus;
                            console.log('Connection status changed:', currentStatus ? 'online' : 'offline');
                        }
                    }, 1000); // Check every second
                },

                // Initialize CSRF token refresh to prevent 419 errors
                initCSRFRefresh() {
                    // Refresh CSRF token every 30 minutes
                    setInterval(async () => {
                        try {
                            const response = await fetch('/refresh-csrf', {
                                method: 'GET',
                                credentials: 'same-origin'
                            });
                            if (response.ok) {
                                const data = await response.json();
                                if (data.csrf_token) {
                                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                                    console.log('CSRF token refreshed');
                                }
                            }
                        } catch (error) {
                            console.log('Failed to refresh CSRF token:', error);
                        }
                    }, 10 * 60 * 1000); // 10 minutes
                },

                // Helper function to handle 419 Page Expired errors
                handle419Error(response) {
                    if (response.status === 419) {
                        this.showToast('Session expired. Refreshing page...', 'error');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        return true;
                    }
                    return false;
                },

                // PWA Functionality
                initPWA() {
                    
                    // Register service worker
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.register('/sw.js')
                            .then((registration) => {
                                console.log('Service Worker registered successfully:', registration);
                                
                                // Force immediate activation
                                if (registration.waiting) {
                                    registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                                }
                                
                                // Listen for updates
                                registration.addEventListener('updatefound', () => {
                                    const newWorker = registration.installing;
                                    newWorker.addEventListener('statechange', () => {
                                        if (newWorker.state === 'installed') {
                                            if (navigator.serviceWorker.controller) {
                                                this.showToast('App updated! Refresh to see changes.', 'success');
                                            } else {
                                                this.showToast('App ready for offline use!', 'success');
                                            }
                                        }
                                    });
                                });
                            })
                            .catch((error) => {
                                console.log('Service Worker registration failed:', error);
                            });

                        // Listen for messages from service worker
                        navigator.serviceWorker.addEventListener('message', (event) => {
                            if (event.data.type === 'SYNC_COMPLETE') {
                                this.showToast(`Synced ${event.data.syncedCount} offline expenses!`, 'success');
                                // Refresh expenses from server
                                this.refreshExpenses();
                            }
                        });
                    }

                    // Handle online/offline status
                    window.addEventListener('online', () => {
                        this.showToast('Back online! Syncing data...', 'success');
                        this.syncOfflineData();
                    });

                    window.addEventListener('offline', () => {
                        this.showToast('You\'re offline. Changes will sync when back online.', 'error');
                    });

                    // Show install prompt
                    this.handleInstallPrompt();
                },

                // Handle PWA install prompt
                handleInstallPrompt() {
                    let deferredPrompt;
                    
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        deferredPrompt = e;
                        
                        // Show install button
                        this.showInstallButton = true;
                    });

                    // Install button click handler
                    this.installPWA = async () => {
                        if (deferredPrompt) {
                            deferredPrompt.prompt();
                            const { outcome } = await deferredPrompt.userChoice;
                            
                            if (outcome === 'accepted') {
                                this.showToast('App installed successfully!', 'success');
                            }
                            
                            deferredPrompt = null;
                            this.showInstallButton = false;
                        }
                    };
                },

                // Sync offline data - SAVE TO DATABASE BUT PREVENT DUPLICATES
                async syncOfflineData() {
                    const offlineData = JSON.parse(localStorage.getItem('expense_tracker_offline_data') || '[]');
                    
                    if (offlineData.length === 0) return;
                    
                    console.log('Syncing', offlineData.length, 'offline items to database');
                    
                    let syncedCount = 0;
                    const remainingData = [];
                    
                    // DEBUG: Check if sync is being called multiple times
                    console.log('🔍 SYNC DEBUG: Starting sync process');
                    console.log('🔍 Number of offline items to sync:', offlineData.length);
                    console.log('🔍 Current expenses in array:', this.expenses.length);
                    
                    // Check if sync is already running
                    if (this.syncInProgress) {
                        console.log('⚠️ SYNC ALREADY IN PROGRESS - SKIPPING');
                        return;
                    }
                    this.syncInProgress = true;
                    
                    for (const item of offlineData) {
                        try {
                            console.log('🔄 Syncing to database:', item.data.description, item.data.amount);
                            console.log('🔄 API endpoint:', item.endpoint);
                            console.log('🔄 Request data:', JSON.stringify(item.data));
                            
                            // Make ONE API call to save to database
                            const response = await fetch(item.endpoint, {
                                method: item.method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(item.data)
                            });
                            
                            console.log('🔄 Response status:', response.status);
                            
                            if (response.ok) {
                                const savedExpense = await response.json();
                                console.log('✅ SAVED TO DATABASE:', savedExpense.description, 'ID:', savedExpense.id);
                                
                                // Find and REPLACE the offline expense with the database version
                                const offlineExpenseIndex = this.expenses.findIndex(e => 
                                    e.offline === true &&
                                    e.description === item.data.description &&
                                    Math.abs(parseFloat(e.amount) - parseFloat(item.data.amount)) < 0.01 &&
                                    e.date.split('T')[0] === item.data.date &&
                                    e.category === item.data.category
                                );
                                
                                console.log('🔍 Looking for offline expense to replace...');
                                console.log('🔍 Found at index:', offlineExpenseIndex);
                                
                                if (offlineExpenseIndex !== -1) {
                                    console.log('🔄 BEFORE REPLACE - Expenses count:', this.expenses.length);
                                    // REPLACE offline expense with database version (same position)
                                    this.expenses[offlineExpenseIndex] = savedExpense;
                                    console.log('✅ REPLACED offline expense with database version');
                                    console.log('🔄 AFTER REPLACE - Expenses count:', this.expenses.length);
                                } else {
                                    console.log('⚠️ Could not find offline expense to replace - adding new one');
                                    this.expenses.push(savedExpense);
                                }
                                
                                syncedCount++;
                            } else {
                                console.log('❌ Failed to save to database:', response.status);
                                remainingData.push(item);
                            }
                        } catch (error) {
                            console.log('❌ Error saving to database:', error);
                            remainingData.push(item);
                        }
                    }
                    
                    this.syncInProgress = false;
                    
                    // Clear synced items from localStorage, keep failed ones
                    localStorage.setItem('expense_tracker_offline_data', JSON.stringify(remainingData));
                    console.log('Updated localStorage. Remaining items:', remainingData.length);
                    
                    // Update offline data count
                    this.updateOfflineDataCount();
                    
                    if (syncedCount > 0) {
                        this.showToast(`Synced ${syncedCount} offline expenses to database!`, 'success');
                    }
                },

                // Refresh expenses from server
                async refreshExpenses() {
                    try {
                        const response = await fetch('/api/expenses');
                        if (response.ok) {
                            const expenses = await response.json();
                            this.expenses = expenses;
                        }
                    } catch (error) {
                        console.log('Failed to refresh expenses:', error);
                    }
                },

                // Check if app is running as PWA
                isPWA() {
                    return window.matchMedia('(display-mode: standalone)').matches ||
                           window.navigator.standalone ||
                           document.referrer.includes('android-app://');
                },

                // Get offline status
                isOnline() {
                    return this.onlineStatus;
                },

                // Load saved currency preference from localStorage
                loadCurrencyPreference() {
                    const savedCurrency = localStorage.getItem('expense_tracker_currency');
                    if (savedCurrency && this.currencies[savedCurrency]) {
                        this.currentCurrency = this.currencies[savedCurrency];
                        console.log(`Loaded saved currency preference: ${savedCurrency}`);
                    }
                },

                // Save currency preference to localStorage
                saveCurrencyPreference() {
                    localStorage.setItem('expense_tracker_currency', this.currentCurrency.code);
                    console.log(`Saved currency preference: ${this.currentCurrency.code}`);
                },

                async fetchExchangeRate() {
                    try {
                        console.log('Fetching exchange rate...');
                        const response = await fetch('https://open.er-api.com/v6/latest/USD');
                        const data = await response.json();
                        if (data.rates && data.rates.PHP) {
                            this.exchangeRate = data.rates.PHP;
                            console.log(`Exchange rate updated: 1 USD = ${this.exchangeRate} PHP`);
                        }
                    } catch (error) {
                        console.error('Failed to fetch exchange rate:', error);
                        console.log('Using default exchange rate:', this.exchangeRate);
                    }
                },

                switchCurrency() {
                    const oldCurrency = this.currentCurrency.code;
                    this.currentCurrency = this.currentCurrency.code === 'USD' 
                        ? this.currencies.PHP 
                        : this.currencies.USD;
                    
                    // Convert existing budget to new currency
                    if (this.monthlyBudget > 0) {
                        if (oldCurrency === 'USD' && this.currentCurrency.code === 'PHP') {
                            this.monthlyBudget = this.monthlyBudget * this.exchangeRate;
                        } else if (oldCurrency === 'PHP' && this.currentCurrency.code === 'USD') {
                            this.monthlyBudget = this.monthlyBudget / this.exchangeRate;
                        }
                        
                        // Save the converted budget
                        const budgetData = {
                            amount: this.monthlyBudget,
                            currency: this.currentCurrency.code
                        };
                        localStorage.setItem('expense_tracker_budget', JSON.stringify(budgetData));
                    }
                    
                    // Save the new currency preference
                    this.saveCurrencyPreference();
                    
                    // Show toast notification
                    this.showToast(`Currency switched to ${this.currentCurrency.name}`, 'success');
                },

                convertCurrency(usdAmount) {
                    if (this.currentCurrency.code === 'USD') {
                        return usdAmount;
                    } else if (this.currentCurrency.code === 'PHP') {
                        return usdAmount * this.exchangeRate;
                    }
                    return usdAmount;
                },

                formatCurrency(usdAmount) {
                    if (typeof usdAmount !== 'number') {
                        usdAmount = parseFloat(usdAmount) || 0;
                    }
                    const convertedAmount = this.convertCurrency(usdAmount);
                    const formattedAmount = convertedAmount.toFixed(2);
                    return `${this.currentCurrency.symbol}${formattedAmount}`;
                },

                // Format budget amounts (already in current currency, no conversion needed)
                formatBudgetAmount(amount) {
                    if (typeof amount !== 'number') {
                        amount = parseFloat(amount) || 0;
                    }
                    const formattedAmount = amount.toFixed(2);
                    return `${this.currentCurrency.symbol}${formattedAmount}`;
                },

                // Debug function to check budget values
                debugBudget() {
                    console.log('Current Currency:', this.currentCurrency.code);
                    console.log('Monthly Budget:', this.monthlyBudget);
                    console.log('Exchange Rate:', this.exchangeRate);
                    const savedBudget = localStorage.getItem('expense_tracker_budget');
                    console.log('Saved Budget:', savedBudget);
                },

                // Modal functionality
                openExpenseModal(day) {
                    this.selectedModalDate = day;
                    this.showExpenseModal = true;
                    // Reset modal form
                    this.modalExpense = {
                        category: 'Food',
                        description: '',
                        amount: ''
                    };
                },

                closeExpenseModal() {
                    this.showExpenseModal = false;
                    this.selectedModalDate = null;
                },

                getSelectedDateFormatted() {
                    if (!this.selectedModalDate) return '';
                    const year = this.selectedDate.getFullYear();
                    const month = this.selectedDate.getMonth();
                    const date = new Date(year, month, this.selectedModalDate);
                    return date.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                },

                async addExpenseFromModal() {
                    if (!this.modalExpense.description.trim() || !this.modalExpense.amount) {
                        this.showToast('Please fill in all fields', 'error');
                        return;
                    }

                    const year = this.selectedDate.getFullYear();
                    const month = this.selectedDate.getMonth();
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(this.selectedModalDate).padStart(2, '0')}`;

                    let amountInUSD = parseFloat(this.modalExpense.amount);
                    if (this.currentCurrency.code === 'PHP') {
                        amountInUSD = amountInUSD / this.exchangeRate;
                    }

                    const newExpenseData = {
                        date: dateStr,
                        category: this.modalExpense.category,
                        description: this.modalExpense.description.trim(),
                        amount: amountInUSD
                    };

                    // Check if online with debugging
                    console.log('=== EXPENSE MODAL DEBUG ===');
                    console.log('Navigator online status:', navigator.onLine);
                    console.log('Our online status:', this.onlineStatus);
                    console.log('Offline banner showing:', !this.isOnline());
                    
                    if (!this.onlineStatus) {
                        console.log('OFFLINE MODE: Saving expense locally only (will NOT hit database)');
                        // Handle offline - add to main array for immediate display AND store for sync
                        const offlineExpense = {
                            id: 'offline_' + Date.now(), // Unique offline ID
                            ...newExpenseData,
                            offline: true,
                            created_at: new Date().toISOString()
                        };
                        
                        // Add to main array for immediate calendar display
                        this.expenses.push(offlineExpense);
                        
                        // Store for later sync (but mark it to prevent duplicate sync)
                        await this.storeOfflineExpense(newExpenseData);
                        
                        // Update offline data count
                        this.updateOfflineDataCount();
                        
                        this.closeExpenseModal();
                        this.showToast('Expense saved offline! Will sync when back online.', 'success');
                        return;
                    }
                    
                    console.log('ONLINE MODE: Saving expense to database immediately');

                    try {
                        const response = await fetch('{{ route('expenses.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(newExpenseData)
                        });

                        if (!response.ok) {
                            // Handle 419 Page Expired error
                            if (this.handle419Error(response)) return;
                            
                            const errorData = await response.json();
                            console.error('Error response:', errorData);
                            this.showToast('Failed to add expense. Please try again.', 'error');
                            return;
                        }

                        const createdExpense = await response.json();
                        this.expenses.push(createdExpense);
                        
                        this.closeExpenseModal();
                        this.showToast('Expense added successfully!', 'success');

                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        
                        // If network error, treat as offline - only store in localStorage
                        await this.storeOfflineExpense(newExpenseData);
                        
                        // Update offline data count
                        this.updateOfflineDataCount();
                        
                        this.closeExpenseModal();
                        this.showToast('Network error - expense saved offline!', 'success');
                    }
                },

                // Enhanced Toast notification method
                showToast(message, type = 'success') {
                    this.$nextTick(() => {
                        this.toastMessage = message;
                        this.toastType = type;
                        this.toastVisible = true;
                        setTimeout(() => {
                            this.toastVisible = false;
                        }, 4000); // Show for 4 seconds
                    });
                },

                // Search & Filter functionality
                getFilteredExpenses() {
                    let filtered = this.getMonthlyExpenses();
                    
                    // Search filter
                    if (this.searchQuery.trim()) {
                        filtered = filtered.filter(expense => 
                            expense.description.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            expense.category.toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }
                    
                    // Category filter
                    if (this.selectedCategory !== 'all') {
                        filtered = filtered.filter(expense => expense.category === this.selectedCategory);
                    }
                    
                    // Date range filter
                    if (this.dateRange.start) {
                        filtered = filtered.filter(expense => expense.date >= this.dateRange.start);
                    }
                    if (this.dateRange.end) {
                        filtered = filtered.filter(expense => expense.date <= this.dateRange.end);
                    }
                    
                    return filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
                },

                clearFilters() {
                    this.searchQuery = '';
                    this.selectedCategory = 'all';
                    this.dateRange = { start: '', end: '' };
                    this.showFilters = false;
                },

                // Budget Management functionality - SIMPLIFIED VERSION
                getBudgetProgress() {
                    const spentInUSD = this.getCurrentMonthTotal();
                    const spentInCurrentCurrency = this.convertCurrency(spentInUSD);
                    
                    const percentage = this.monthlyBudget > 0 ? (spentInCurrentCurrency / this.monthlyBudget) * 100 : 0;
                    return { 
                        spent: spentInCurrentCurrency, 
                        percentage: Math.min(percentage, 100), 
                        remaining: Math.max(this.monthlyBudget - spentInCurrentCurrency, 0),
                        isOverBudget: spentInCurrentCurrency > this.monthlyBudget && this.monthlyBudget > 0
                    };
                },

                setBudget() {
                    this.showBudgetModal = true;
                },

                saveBudget() {
                    // Convert to number to ensure proper comparison
                    const budgetAmount = parseFloat(this.monthlyBudget) || 0;
                    
                    console.log('=== SAVE BUDGET DEBUG ===');
                    console.log('Original monthlyBudget:', this.monthlyBudget);
                    console.log('Parsed budgetAmount:', budgetAmount);
                    console.log('Current Currency:', this.currentCurrency.code);
                    
                    if (budgetAmount >= 0) {
                        // Save budget with currency information
                        const budgetData = {
                            amount: budgetAmount,
                            currency: this.currentCurrency.code
                        };
                        localStorage.setItem('expense_tracker_budget', JSON.stringify(budgetData));
                        
                        // Update the local variable with the parsed number
                        this.monthlyBudget = budgetAmount;
                        
                        console.log('Budget saved:', budgetData);
                        console.log('Updated monthlyBudget:', this.monthlyBudget);
                        console.log('========================');
                        
                        this.showBudgetModal = false;
                        this.showToast('Budget updated successfully!', 'success');
                    }
                },

                loadBudget() {
                    const savedBudget = localStorage.getItem('expense_tracker_budget');
                    if (savedBudget) {
                        try {
                            const budgetData = JSON.parse(savedBudget);
                            if (budgetData.currency && budgetData.amount !== undefined) {
                                // Check if the saved budget currency matches current currency
                                if (budgetData.currency === this.currentCurrency.code) {
                                    // Same currency, use as-is - NO CONVERSION
                                    this.monthlyBudget = budgetData.amount;
                                } else {
                                    // Different currency, convert it
                                    if (budgetData.currency === 'USD' && this.currentCurrency.code === 'PHP') {
                                        this.monthlyBudget = budgetData.amount * this.exchangeRate;
                                    } else if (budgetData.currency === 'PHP' && this.currentCurrency.code === 'USD') {
                                        this.monthlyBudget = budgetData.amount / this.exchangeRate;
                                    } else {
                                        this.monthlyBudget = budgetData.amount;
                                    }
                                }
                            } else {
                                // Legacy format - treat as current currency, NO CONVERSION
                                this.monthlyBudget = parseFloat(savedBudget);
                            }
                        } catch (e) {
                            // Legacy format - treat as current currency, NO CONVERSION
                            this.monthlyBudget = parseFloat(savedBudget);
                        }
                    }
                },

                // Theme functionality
                toggleTheme() {
                    this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('expense_tracker_theme', this.currentTheme);
                    this.applyTheme();
                },

                applyTheme() {
                    if (this.currentTheme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                loadTheme() {
                    const savedTheme = localStorage.getItem('expense_tracker_theme');
                    if (savedTheme) {
                        this.currentTheme = savedTheme;
                        this.applyTheme();
                    }
                },

                // Recurring expenses functionality
                addRecurringExpense(expense) {
                    const recurring = JSON.parse(localStorage.getItem('expense_tracker_recurring') || '[]');
                    recurring.push({
                        ...expense,
                        id: Date.now(),
                        nextDate: this.getNextRecurringDate(expense.frequency)
                    });
                    localStorage.setItem('expense_tracker_recurring', JSON.stringify(recurring));
                },

                getNextRecurringDate(frequency) {
                    const today = new Date();
                    switch(frequency) {
                        case 'daily': return new Date(today.setDate(today.getDate() + 1));
                        case 'weekly': return new Date(today.setDate(today.getDate() + 7));
                        case 'monthly': return new Date(today.setMonth(today.getMonth() + 1));
                        default: return new Date(today.setDate(today.getDate() + 1));
                    }
                },

                // Quick actions
                duplicateExpense(expense) {
                    this.newExpense = {
                        date: new Date().toISOString().split('T')[0],
                        category: expense.category,
                        description: expense.description + ' (Copy)',
                        amount: this.convertCurrency(expense.amount).toFixed(2)
                    };
                    this.activeTab = 'add';
                    this.showToast('Expense duplicated! Edit and save.', 'success');
                },

                // Keyboard shortcuts
                handleKeyboardShortcuts(event) {
                    if (event.ctrlKey || event.metaKey) {
                        switch(event.key) {
                            case 'n':
                                event.preventDefault();
                                this.activeTab = 'add';
                                break;
                            case 'f':
                                event.preventDefault();
                                this.activeTab = 'expenses';
                                this.showFilters = true;
                                break;
                        }
                    }
                },

                // Edit expense functionality
                openEditModal(expense) {
                    this.editExpense = {
                        id: expense.id,
                        date: expense.date.split('T')[0], // Format date for input
                        category: expense.category,
                        description: expense.description,
                        amount: this.convertCurrency(expense.amount).toFixed(2) // Convert to current currency
                    };
                    this.showEditModal = true;
                },

                closeEditModal() {
                    this.showEditModal = false;
                    this.editExpense = {
                        id: null,
                        date: '',
                        category: 'Food',
                        description: '',
                        amount: ''
                    };
                },

                async updateExpense() {
                    if (!this.editExpense.description.trim() || !this.editExpense.amount) {
                        this.showToast('Please fill in all fields', 'error');
                        return;
                    }

                    let amountInUSD = parseFloat(this.editExpense.amount);
                    if (this.currentCurrency.code === 'PHP') {
                        amountInUSD = amountInUSD / this.exchangeRate;
                    }

                    const updatedExpenseData = {
                        date: this.editExpense.date,
                        category: this.editExpense.category,
                        description: this.editExpense.description.trim(),
                        amount: amountInUSD
                    };

                    try {
                        const response = await fetch(`/expenses/${this.editExpense.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(updatedExpenseData)
                        });

                        if (!response.ok) {
                            // Handle 419 Page Expired error
                            if (this.handle419Error(response)) return;
                            
                            const errorData = await response.json();
                            console.error('Error response:', errorData);
                            this.showToast('Failed to update expense. Please try again.', 'error');
                            return;
                        }

                        const updatedExpense = await response.json();
                        
                        // Update the expense in the local array
                        const index = this.expenses.findIndex(e => e.id === this.editExpense.id);
                        if (index !== -1) {
                            this.expenses[index] = updatedExpense;
                        }

                        this.closeEditModal();
                        this.showToast('Expense updated successfully!', 'success');

                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        this.showToast('Failed to update expense. Please check your connection.', 'error');
                    }
                },

                // Delete expense functionality
                confirmDelete(expense) {
                    this.expenseToDelete = expense;
                    this.showDeleteModal = true;
                },

                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.expenseToDelete = null;
                },

                async deleteExpense() {
                    if (!this.expenseToDelete) return;

                    try {
                        const response = await fetch(`/expenses/${this.expenseToDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (!response.ok) {
                            // Handle 419 Page Expired error
                            if (this.handle419Error(response)) return;
                            
                            const errorData = await response.json();
                            console.error('Error response:', errorData);
                            this.showToast('Failed to delete expense. Please try again.', 'error');
                            return;
                        }

                        // Remove the expense from the local array
                        this.expenses = this.expenses.filter(e => e.id !== this.expenseToDelete.id);

                        this.closeDeleteModal();
                        this.showToast('Expense deleted successfully!', 'success');

                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        this.showToast('Failed to delete expense. Please check your connection.', 'error');
                    }
                },

                // Summary card calculations
                dailyAverage() {
                    const total = this.getCurrentMonthTotal();
                    const expenses = this.getMonthlyExpenses();
                    if (expenses.length === 0) return 0;

                    const today = new Date();
                    const year = this.selectedDate.getFullYear();
                    const month = this.selectedDate.getMonth();
                    
                    let daysInMonthSoFar = new Date(year, month + 1, 0).getDate();
                    
                    if (today.getFullYear() === year && today.getMonth() === month) {
                        daysInMonthSoFar = today.getDate();
                    }
                    
                    return daysInMonthSoFar > 0 ? total / daysInMonthSoFar : 0;
                },
                
                largestExpense() {
                    const amounts = this.getMonthlyExpenses().map(e => parseFloat(e.amount || 0));
                    return amounts.length > 0 ? Math.max(...amounts) : 0;
                },

                // Smart Insights functions
                getTopCategory() {
                    const expenses = this.getMonthlyExpenses();
                    if (expenses.length === 0) return 'No expenses yet';
                    
                    const categoryTotals = {};
                    expenses.forEach(expense => {
                        categoryTotals[expense.category] = (categoryTotals[expense.category] || 0) + parseFloat(expense.amount);
                    });
                    
                    const topCategory = Object.keys(categoryTotals).reduce((a, b) => 
                        categoryTotals[a] > categoryTotals[b] ? a : b
                    );
                    
                    const categoryIcon = this.getCategoryIcon(topCategory);
                    const amount = this.formatCurrency(categoryTotals[topCategory]);
                    return `${categoryIcon} ${topCategory} (${amount})`;
                },

                getAverageTransaction() {
                    const expenses = this.getMonthlyExpenses();
                    if (expenses.length === 0) return 0;
                    
                    const total = expenses.reduce((sum, expense) => sum + parseFloat(expense.amount), 0);
                    return total / expenses.length;
                },

                getWeeklySpending() {
                    const oneWeekAgo = new Date();
                    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                    
                    const weeklyExpenses = this.expenses.filter(expense => {
                        const expenseDate = new Date(expense.date);
                        return expenseDate >= oneWeekAgo;
                    });
                    
                    return weeklyExpenses.reduce((total, expense) => total + parseFloat(expense.amount), 0);
                },

                // Offline data functions
                getOfflineExpenses() {
                    // Get expenses from localStorage (these are the pending sync items)
                    const offlineData = JSON.parse(localStorage.getItem('expense_tracker_offline_data') || '[]');
                    return offlineData.map(item => ({
                        ...item.data,
                        id: item.id,
                        timestamp: item.timestamp,
                        offline: true
                    }));
                },

                getOfflineExpensesTotal() {
                    return this.getOfflineExpenses().reduce((total, expense) => {
                        const amount = parseFloat(expense.amount || 0);
                        return total + (this.currentCurrency.code === 'USD' ? amount : amount * this.exchangeRate);
                    }, 0);
                },

                // Additional utility functions
                getLastMonthTotal() {
                    const lastMonth = new Date(this.selectedDate);
                    lastMonth.setMonth(lastMonth.getMonth() - 1);
                    
                    const lastMonthExpenses = this.expenses.filter(expense => {
                        const expenseDate = new Date(expense.date);
                        return expenseDate.getMonth() === lastMonth.getMonth() && 
                               expenseDate.getFullYear() === lastMonth.getFullYear();
                    });
                    
                    return lastMonthExpenses.reduce((total, expense) => total + parseFloat(expense.amount), 0);
                },

                // Quick expense templates
                getQuickExpenseTemplates() {
                    return [
                        { description: 'Morning Coffee', category: 'Food', amount: 5.00 },
                        { description: 'Lunch', category: 'Food', amount: 15.00 },
                        { description: 'Gas Fill-up', category: 'Transport', amount: 50.00 },
                        { description: 'Grocery Shopping', category: 'Shopping', amount: 80.00 },
                        { description: 'Movie Ticket', category: 'Entertainment', amount: 12.00 },
                        { description: 'Utility Bill', category: 'Bills', amount: 100.00 }
                    ];
                },

                // Expense analytics
                getSpendingTrend() {
                    const thisMonth = this.getCurrentMonthTotal();
                    const lastMonth = this.getLastMonthTotal();
                    
                    if (lastMonth === 0) return { trend: 'neutral', message: 'First month tracking' };
                    
                    const percentageChange = ((thisMonth - lastMonth) / lastMonth) * 100;
                    
                    if (percentageChange > 10) {
                        return { trend: 'up', message: `Spending up ${percentageChange.toFixed(1)}% from last month` };
                    } else if (percentageChange < -10) {
                        return { trend: 'down', message: `Spending down ${Math.abs(percentageChange).toFixed(1)}% from last month` };
                    } else {
                        return { trend: 'stable', message: 'Spending similar to last month' };
                    }
                },

                // Category budget tracking
                getCategorySpending(category) {
                    const expenses = this.getMonthlyExpenses().filter(e => e.category === category);
                    return expenses.reduce((total, expense) => total + parseFloat(expense.amount), 0);
                },

                // Expense frequency analysis
                getMostFrequentExpenseType() {
                    const expenses = this.getMonthlyExpenses();
                    if (expenses.length === 0) return 'No expenses';
                    
                    const descriptions = {};
                    expenses.forEach(expense => {
                        const key = expense.description.toLowerCase();
                        descriptions[key] = (descriptions[key] || 0) + 1;
                    });
                    
                    const mostFrequent = Object.keys(descriptions).reduce((a, b) => 
                        descriptions[a] > descriptions[b] ? a : b
                    );
                    
                    return `"${mostFrequent}" (${descriptions[mostFrequent]} times)`;
                }
            }
        }
    </script>
</body>
</html>