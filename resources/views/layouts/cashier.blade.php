<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier Panel') - Ecoretech Printing Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false, userMenuOpen: false }">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-white shadow-lg w-64 transform transition-transform duration-300 ease-in-out" 
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0" 
             x-show="true">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-cashier-blue rounded-lg flex items-center justify-center">
                            <i class="fas fa-cash-register text-white text-lg"></i>
                        </div>
                        <div x-show="sidebarOpen" class="lg:block">
                            <h1 class="text-xl font-bold text-gray-900">Cashier Panel</h1>
                            <p class="text-sm text-gray-500">Ecoretech Printing</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('cashier.dashboard') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-maroon hover:text-white transition-colors {{ request()->routeIs('cashier.dashboard') ? 'bg-maroon text-white' : '' }}">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="lg:block">Dashboard</span>
                    </a>

                    <a href="{{ route('cashier.quotations.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-maroon hover:text-white transition-colors {{ request()->routeIs('cashier.quotations.*') ? 'bg-maroon text-white' : '' }}">
                        <i class="fas fa-file-invoice w-5"></i>
                        <span class="lg:block">Quotations</span>
                    </a>

                    <a href="{{ route('cashier.orders.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-maroon hover:text-white transition-colors {{ request()->routeIs('cashier.orders.*') ? 'bg-maroon text-white' : '' }}">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="lg:block">Job Orders</span>
                    </a>

                    <a href="{{ route('cashier.deliveries.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-maroon hover:text-white transition-colors {{ request()->routeIs('cashier.deliveries.*') ? 'bg-maroon text-white' : '' }}">
                        <i class="fas fa-truck w-5"></i>
                        <span class="lg:block">Deliveries</span>
                    </a>

                    
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-gray-200" x-data="{ userMenuOpen: false }">
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center w-full p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 bg-maroon rounded-full flex items-center justify-center text-white text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div x-show="sidebarOpen" class="ml-3 flex-1 text-left">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">Cashier</p>
                            </div>
                        </button>
                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                            <form method="POST" action="{{ route('cashier.logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <div x-show="sidebarOpen" class="mt-3 text-[11px] text-gray-400">v1.0.0</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
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
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('warning') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
