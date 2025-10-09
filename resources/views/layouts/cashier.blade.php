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
        .sidebar-link { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px; color:#374151; transition: all .2s; }
        .sidebar-link-collapsed { display:flex; align-items:center; justify-center; padding:10px; border-radius:10px; color:#374151; transition: all .2s; width:100%; }
        .sidebar-link:hover { background:#F3F4F6; color:#111827; }
        .sidebar-icon { width:20px; text-align:center; }
        .sidebar-tooltip { position:absolute; left:68px; background:#111827; color:#fff; padding:4px 8px; border-radius:6px; font-size:12px; white-space:nowrap; transform: translateY(-50%); top:50%; opacity:0; pointer-events:none; transition:opacity .15s; }
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
        sidebarOpen: true,
        mobileOpen: false,
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; localStorage.setItem('ecore_sidebar_open', this.sidebarOpen ? '1' : '0'); },
        init() { const v = localStorage.getItem('ecore_sidebar_open'); if (v !== null) { this.sidebarOpen = v === '1'; } }
    }">
        <!-- Sidebar -->
        <div class="sidebar-gradient shadow-lg transition-all duration-300 ease-in-out" 
             :class="sidebarOpen ? 'w-64' : 'w-16'">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div :class="sidebarOpen ? 'p-4' : 'p-3'" class="border-b border-gray-200">
                    <div class="flex items-center justify-center">
                        <div :class="sidebarOpen ? 'w-12 h-12' : 'w-10 h-10'" class="bg-maroon rounded-2xl flex items-center justify-center text-white font-bold transition-all duration-300"
                             :class="sidebarOpen ? 'text-xl' : 'text-lg'">
                            E
                        </div>
                        <div x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">
                            <h1 class="text-lg font-bold text-maroon">Ecoretech Printing Shop</h1>
                            <p class="text-xs text-gray-500">Cashier Panel</p>
                        </div>
                    </div>
                    <div :class="sidebarOpen ? 'mt-4 flex items-center gap-2' : 'mt-3 flex justify-center'" class="transition-all duration-300">
                        <button
                            @click="toggleSidebar()"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-maroon"
                            :aria-pressed="sidebarOpen.toString()"
                            :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
                            :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
                        >
                            <i class="fas text-gray-600 transition-transform duration-200"
                               :class="sidebarOpen ? 'fa-angles-left' : 'fa-angles-right'"></i>
                        </button>
                        <button x-show="sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" @click="mobileOpen = !mobileOpen" title="Open menu">
                            <i class="fas fa-ellipsis-vertical text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1">
                    <!-- Dashboard -->
                    <div class="sidebar-item relative">
                        <a href="{{ route('cashier.dashboard') }}" 
                           :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                           class="{{ request()->routeIs('cashier.dashboard') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-tachometer-alt sidebar-icon"></i>
                            <span x-show="sidebarOpen" class="transition-opacity duration-300">Dashboard</span>
                        </a>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Dashboard</div>
                    </div>

                    <!-- Quotations -->
                    <div class="sidebar-item relative">
                        <a href="{{ route('cashier.quotations.index') }}" 
                           :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                           class="{{ request()->routeIs('cashier.quotations.*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-file-invoice sidebar-icon"></i>
                            <span x-show="sidebarOpen" class="transition-opacity duration-300">Quotations</span>
                        </a>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Quotations</div>
                    </div>

                    <!-- Job Orders -->
                    <div class="sidebar-item relative">
                        <a href="{{ route('cashier.orders.index') }}" 
                           :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                           class="{{ request()->routeIs('cashier.orders.*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-shopping-cart sidebar-icon"></i>
                            <span x-show="sidebarOpen" class="transition-opacity duration-300">Job Orders</span>
                        </a>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Job Orders</div>
                    </div>

                    <!-- Deliveries -->
                    <div class="sidebar-item relative">
                        <a href="{{ route('cashier.deliveries.index') }}" 
                           :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                           class="{{ request()->routeIs('cashier.deliveries.*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-truck sidebar-icon"></i>
                            <span x-show="sidebarOpen" class="transition-opacity duration-300">Deliveries</span>
                        </a>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Deliveries</div>
                    </div>

                    <!-- Payments -->
                    <div class="sidebar-item relative">
                        <a href="{{ route('cashier.payments.index') }}" 
                           :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                           class="{{ request()->routeIs('cashier.payments.*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-credit-card sidebar-icon"></i>
                            <span x-show="sidebarOpen" class="transition-opacity duration-300">Payments</span>
                        </a>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Payments</div>
                    </div>
                </nav>

                <!-- User Section -->
                <div :class="sidebarOpen ? 'p-4' : 'p-2'" class="border-t border-gray-200 transition-all duration-300" x-data="{ userMenuOpen: false }">
                    <div class="relative">
                        <button @click.stop="userMenuOpen = !userMenuOpen" 
                                :class="sidebarOpen ? 'flex items-center w-full p-2' : 'flex items-center justify-center w-full p-2'"
                                class="rounded-lg hover:bg-gray-100 transition-colors">
                            <div :class="sidebarOpen ? 'w-8 h-8' : 'w-10 h-10'" class="bg-maroon rounded-full flex items-center justify-center text-white transition-all duration-300"
                                 :class="sidebarOpen ? 'text-sm' : 'text-base'">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div x-show="sidebarOpen" class="ml-3 flex-1 text-left">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">Cashier</p>
                            </div>
                        </button>
                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition 
                             :class="sidebarOpen ? 'absolute bottom-full left-0 right-0 mb-2' : 'absolute bottom-full left-0 mb-2'"
                             class="bg-white rounded-lg shadow-lg border border-gray-200 py-2"
                             :style="sidebarOpen ? '' : 'width: 200px;'">
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