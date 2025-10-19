{{-- resources/views/layouts/cashier.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Ecoretech Printing Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-maroon: #800020;
            --secondary-gray: #6B7280;
            --light-gray: #F3F4F6;
            --dark-gray: #374151;
        }
        .bg-maroon { background-color: var(--primary-maroon); }
        .text-maroon { color: var(--primary-maroon); }
        .border-maroon { border-color: var(--primary-maroon); }
        .hover-maroon:hover { background-color: var(--primary-maroon); }
        .sidebar-gradient { background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%); }
        .sidebar-active { background-color: rgba(128, 0, 32, 0.08); border-right: 3px solid var(--primary-maroon); color: var(--primary-maroon); }
        .sidebar-link { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px; color:#374151; }
        .sidebar-link-collapsed { display:flex; align-items:center; justify-center; padding:10px; border-radius:10px; color:#374151; width:100%; }
        .sidebar-link:hover { background:#F3F4F6; color:#111827; }
        .sidebar-icon { width:20px; text-align:center; }
        .sidebar-tooltip { position:absolute; left:68px; background:#111827; color:#fff; padding:4px 8px; border-radius:6px; font-size:12px; white-space:nowrap; transform: translateY(-50%); top:50%; opacity:0; pointer-events:none; }
        .sidebar-item:hover .sidebar-tooltip { opacity:1; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: '#800020',
                        'maroon-dark': '#600018',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen" x-data="{
        sidebarOpen: false,
        mobileOpen: false,
        showSidebar() { this.sidebarOpen = true; },
        hideSidebar() { this.sidebarOpen = false; }
    }">
        <!-- Sidebar -->
        <div class="sidebar-gradient shadow-lg transition-all duration-300 ease-in-out" 
             :class="sidebarOpen ? 'w-64' : 'w-16'"
             @mouseenter="showSidebar()"
             @mouseleave="hideSidebar()">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="transition-all duration-300 ease-in-out border-b border-gray-200" :class="sidebarOpen ? 'p-4' : 'p-3'">
                    <div class="flex items-center transition-all duration-300 ease-in-out" :class="sidebarOpen ? 'justify-between' : 'flex-col'">
                        <a href="{{ route('cashier.dashboard') }}" class="flex items-center justify-center transition-all duration-300 ease-in-out" 
                           :class="sidebarOpen ? 'mb-0' : 'mb-3'">
                            <img src="{{ asset('images/logo/ecoretech.png') }}" 
                                 alt="Ecoretech Logo" 
                                 class="w-auto object-contain transition-all duration-300 ease-in-out"
                                 :class="sidebarOpen ? 'h-24' : 'h-12'">
                        </a>
                        <div class="flex items-center gap-2">
                            <button x-show="sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" @click="mobileOpen = !mobileOpen" title="Open menu">
                                <i class="fas fa-ellipsis-vertical text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 transition-all duration-300 ease-in-out" :class="sidebarOpen ? 'p-4' : 'p-2'">
                    <div class="space-y-4 transition-all duration-300 ease-in-out">
                        <div class="transition-all duration-300 ease-in-out">
                            <p x-show="sidebarOpen" class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2 transition-all duration-300 ease-in-out">Overview</p>
                            <ul class="space-y-1 transition-all duration-300 ease-in-out">
                                <li class="relative sidebar-item transition-all duration-300 ease-in-out">
                                    <a href="{{ route('cashier.dashboard') }}" 
                                       :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                                       class="{{ request()->routeIs('cashier.dashboard') ? 'sidebar-active' : '' }} transition-all duration-300 ease-in-out">
                                        <i class="fas fa-gauge sidebar-icon transition-all duration-300 ease-in-out"></i>
                                        <span x-show="sidebarOpen" class="transition-all duration-300 ease-in-out">Dashboard</span>
                                    </a>
                                    <span x-show="!sidebarOpen" class="sidebar-tooltip transition-all duration-300 ease-in-out">Dashboard</span>
                                </li>
                            </ul>
                        </div>

                        <div class="transition-all duration-300 ease-in-out">
                            <p x-show="sidebarOpen" class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2 transition-all duration-300 ease-in-out">Transactions</p>
                            <ul class="space-y-1 transition-all duration-300 ease-in-out">
                                <li class="relative sidebar-item transition-all duration-300 ease-in-out">
                                    <a href="{{ route('cashier.quotations.index') }}" 
                                       :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                                       class="{{ request()->routeIs('cashier.quotations.*') ? 'sidebar-active' : '' }} transition-all duration-300 ease-in-out">
                                        <i class="fas fa-file-invoice sidebar-icon transition-all duration-300 ease-in-out"></i>
                                        <span x-show="sidebarOpen" class="transition-all duration-300 ease-in-out">Quotations</span>
                                    </a>
                                    <span x-show="!sidebarOpen" class="sidebar-tooltip transition-all duration-300 ease-in-out">Quotations</span>
                                </li>

                                <li class="relative sidebar-item transition-all duration-300 ease-in-out">
                                    <a href="{{ route('cashier.orders.index') }}" 
                                       :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                                       class="{{ request()->routeIs('cashier.orders.*') ? 'sidebar-active' : '' }} transition-all duration-300 ease-in-out">
                                        <i class="fas fa-shopping-cart sidebar-icon transition-all duration-300 ease-in-out"></i>
                                        <span x-show="sidebarOpen" class="transition-all duration-300 ease-in-out">Job Orders</span>
                                    </a>
                                    <span x-show="!sidebarOpen" class="sidebar-tooltip transition-all duration-300 ease-in-out">Job Orders</span>
                                </li>

                                <li class="relative sidebar-item transition-all duration-300 ease-in-out">
                                    <a href="{{ route('cashier.deliveries.index') }}" 
                                       :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                                       class="{{ request()->routeIs('cashier.deliveries.*') ? 'sidebar-active' : '' }} transition-all duration-300 ease-in-out">
                                        <i class="fas fa-truck sidebar-icon transition-all duration-300 ease-in-out"></i>
                                        <span x-show="sidebarOpen" class="transition-all duration-300 ease-in-out">Deliveries</span>
                                    </a>
                                    <span x-show="!sidebarOpen" class="sidebar-tooltip transition-all duration-300 ease-in-out">Deliveries</span>
                                </li>

                                <li class="relative sidebar-item transition-all duration-300 ease-in-out">
                                    <a href="{{ route('cashier.payments.index') }}" 
                                       :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                                       class="{{ request()->routeIs('cashier.payments.*') ? 'sidebar-active' : '' }} transition-all duration-300 ease-in-out">
                                        <i class="fas fa-credit-card sidebar-icon transition-all duration-300 ease-in-out"></i>
                                        <span x-show="sidebarOpen" class="transition-all duration-300 ease-in-out">Payments</span>
                                    </a>
                                    <span x-show="!sidebarOpen" class="sidebar-tooltip transition-all duration-300 ease-in-out">Payments</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- User Section -->
                <div class="border-t border-gray-200 transition-all duration-300 ease-in-out" :class="sidebarOpen ? 'p-4' : 'p-2'" x-data="{ userMenuOpen: false }">
                    <div class="relative transition-all duration-300 ease-in-out">
                        <button x-show="sidebarOpen" @click.stop="userMenuOpen = !userMenuOpen" 
                                class="flex items-center w-full p-2 rounded-lg hover:bg-gray-100 transition-all duration-300 ease-in-out">
                            <div class="w-8 h-8 bg-maroon rounded-full flex items-center justify-center text-white text-sm transition-all duration-300 ease-in-out">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="ml-3 flex-1 text-left transition-all duration-300 ease-in-out">
                                <p class="text-sm font-medium text-gray-900 transition-all duration-300 ease-in-out">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 transition-all duration-300 ease-in-out">Cashier</p>
                            </div>
                        </button>
                        <div x-show="!sidebarOpen" class="flex items-center justify-center w-full p-2 transition-all duration-300 ease-in-out">
                            <div class="w-10 h-10 bg-maroon rounded-full flex items-center justify-center text-white text-base transition-all duration-300 ease-in-out">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition 
                             class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                            <form method="POST" action="{{ route('cashier.logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Cashier Panel')</h1>
                            <p class="text-sm text-gray-600">@yield('page-description', 'Manage quotations, orders, and deliveries')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @yield('header-actions')
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-6">
                    @if(session('success'))
                        <div 
                            x-data="{ show: true }"
                            x-init="setTimeout(() => show = false, 3000)"
                            x-show="show"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" @click="show = false" class="absolute top-0 right-0 p-2 text-green-700 hover:text-green-900">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div 
                            x-data="{ show: true }"
                            x-init="setTimeout(() => show = false, 3000)"
                            x-show="show"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" @click="show = false" class="absolute top-0 right-0 p-2 text-red-700 hover:text-red-900">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div 
                            x-data="{ show: true }"
                            x-init="setTimeout(() => show = false, 3000)"
                            x-show="show"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('warning') }}
                            <button type="button" @click="show = false" class="absolute top-0 right-0 p-2 text-yellow-700 hover:text-yellow-900">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>