<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name') }}</title>
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
                                    <!-- Currency Switcher and Logout -->
                                    <div class="flex justify-end space-x-2">
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
                                    
                                    <!-- Stats Grid -->
                                    <div class="grid grid-cols-2 gap-4 animate-bounce-in">
                                        <div class="glass-effect rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold" x-text="formatCurrency(getCurrentMonthTotal())"></div>
                                            <div class="text-xs text-blue-200">This Month</div>
                                        </div>
                                        <div class="glass-effect rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold" x-text="getMonthlyExpenses().length"></div>
                                            <div class="text-xs text-blue-200">Expenses</div>
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
                        <template x-for="expense in getMonthlyExpenses().sort((a, b) => new Date(b.date) - new Date(a.date))">
                            <div class="gradient-border animate-slide-up">
                                <div class="gradient-border-content">
                                    <div class="bg-white rounded-lg p-6 hover:shadow-lg transition-all duration-300 transform hover:scale-102">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center mr-4">
                                                    <span class="text-2xl" x-text="getCategoryIcon(expense.category)"></span>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900 text-lg" x-text="expense.description"></h4>
                                                    <p class="text-sm text-gray-500 flex items-center">
                                                        <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                                        <span x-text="expense.category + ' • ' + formatDate(expense.date)"></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-2xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent" x-text="formatCurrency(expense.amount)"></p>
                                                <p class="text-xs text-gray-400">Amount</p>
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
                </div>
            </div>

            <!-- Enhanced Summary Tab -->
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-8">
                <div class="text-center mb-8 animate-bounce-in">
                    <div class="text-6xl mb-4 animate-pulse-slow">📊</div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent mb-2">Monthly Summary</h2>
                    <p class="text-gray-600 text-lg">Your financial overview for <span x-text="getCurrentMonthName()"></span></p>
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
        </div>
    </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div
        x-show="showToast"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="transform translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-5 right-5 w-full max-w-xs bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50"
    >
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900" x-text="toastMessage"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="showToast = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
 <script>
        function expenseTracker() {
            return {
                activeTab: 'calendar',
                selectedDate: new Date(),
                
                // Currency system
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

                // Toast notification properties
                showToast: false,
                toastMessage: '',

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
                        alert('Please fill in all fields');
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
                            const errorData = await response.json();
                            console.error('Error response:', errorData);
                            throw new Error('Network response was not ok');
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
                        
                        this.showSuccessToast('Expense added successfully!');
                        this.activeTab = 'expenses';

                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        alert('Failed to add expense.');
                    }
                },

                applyQuickSuggestion(suggestion) {
                    this.newExpense.description = suggestion.desc;
                    this.newExpense.amount = suggestion.amount.toString();
                    this.newExpense.category = suggestion.cat;
                },

                // Currency functionality
                async init() {
                    await this.fetchExchangeRate();
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
                    this.currentCurrency = this.currentCurrency.code === 'USD' 
                        ? this.currencies.PHP 
                        : this.currencies.USD;
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
                        alert('Please fill in all fields');
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
                            const errorData = await response.json();
                            console.error('Error response:', errorData);
                            throw new Error('Network response was not ok');
                        }

                        const createdExpense = await response.json();
                        this.expenses.push(createdExpense);
                        
                        this.closeExpenseModal();
                        this.showSuccessToast('Expense added successfully!');

                    } catch (error) {
                        console.error('There has been a problem with your fetch operation:', error);
                        alert('Failed to add expense.');
                    }
                },

                // Toast notification method
                showSuccessToast(message) {
                    this.toastMessage = message;
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
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
                }
            }
        }
    </script>
</body>
</html>