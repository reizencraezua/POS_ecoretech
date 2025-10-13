{{-- resources/views/layouts/admin.blade.php --}}
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
    }" x-init="init()">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'w-64' : 'w-16'" class="sidebar-gradient shadow-lg flex-shrink-0 relative" style="transition: width 0.3s ease;">
            <div class="flex flex-col h-full">
                <!-- Brand / Toggle -->
                <div :class="sidebarOpen ? 'p-4' : 'p-3'" class="border-b border-gray-200">
                    <div class="flex items-center justify-center">
                        <div :class="sidebarOpen ? 'w-16 h-16' : 'w-12 h-12'" class="bg-maroon flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/logo/ecoretech.png') }}" 
                                 alt="Ecoretech Logo" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div x-show="sidebarOpen" class="ml-3">
                            <h1 class="text-lg font-bold text-maroon">Ecoretech</h1>
                            <p class="text-xs text-gray-500">Admin Panel</p>
                        </div>
                    </div>
                    <div :class="sidebarOpen ? 'mt-4 flex items-center gap-2' : 'mt-3 flex justify-center'" >
                        <button
                            @click="toggleSidebar()"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-maroon"
                            :aria-pressed="sidebarOpen.toString()"
                            :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
                            :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
                        >
                            <i class="fas text-gray-600"
                               :class="sidebarOpen ? 'fa-angles-left' : 'fa-angles-right'"></i>
                        </button>
                        <button x-show="sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" @click="mobileOpen = !mobileOpen" title="Open menu">
                            <i class="fas fa-ellipsis-vertical text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav :class="sidebarOpen ? 'p-4' : 'p-2'" class="flex-1 overflow-y-auto">
                    <div class="space-y-6">
                        <div>
                            <p x-show="sidebarOpen" class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Overview</p>
                            <ul class="space-y-1">
                                <li class="relative sidebar-item">
                                    <a href="{{ route('admin.dashboard') }}" 
                                       :class="sidebarOpen ? 'sidebar-link' : 'sidebar-link-collapsed'"
                                       class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                                        <i class="fas fa-gauge sidebar-icon"></i>
                                        <span x-show="sidebarOpen">Dashboard</span>
                                    </a>
                                    <span x-show="!sidebarOpen" class="sidebar-tooltip">Dashboard</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Transactions Section -->
                        <div x-data="{ transactionsOpen: {{ request()->routeIs('admin.quotations.*') || request()->routeIs('admin.orders.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.deliveries.*') ? 'true' : 'false' }} }">
                            <button @click.stop="transactionsOpen = !transactionsOpen" class="w-full flex items-center justify-between px-2 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 transition-colors">
                                <span x-show="sidebarOpen">Transactions</span>
                                <i x-show="sidebarOpen" class="fas fa-chevron-down text-xs" :class="transactionsOpen ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="transactionsOpen" x-transition class="mt-2">
                                <ul class="space-y-1">
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.quotations.index') }}" class="sidebar-link {{ request()->routeIs('admin.quotations.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-file-lines sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Quotations</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Quotations</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-clipboard-check sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Job Orders</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Job Orders</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-credit-card sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Payments</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Payments</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.deliveries.index') }}" class="sidebar-link {{ request()->routeIs('admin.deliveries.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-truck sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Deliveries</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Deliveries</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Inventory & Services Section -->
                        <div x-data="{ inventoryOpen: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.services.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.inventories.*') ? 'true' : 'false' }} }">
                            <button @click.stop="inventoryOpen = !inventoryOpen" class="w-full flex items-center justify-between px-2 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 transition-colors">
                                <span x-show="sidebarOpen">Inventory & Services</span>
                                <i x-show="sidebarOpen" class="fas fa-chevron-down text-xs" :class="inventoryOpen ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="inventoryOpen" x-transition class="mt-2">
                                <ul class="space-y-1">
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-box sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Products</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Products</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-cogs sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Services</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Services</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-tags sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Categories</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Categories</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.inventories.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventories.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-warehouse sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Inventory</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Inventory</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- People & Resources Section -->
                        <div x-data="{ peopleOpen: {{ request()->routeIs('admin.customers.*') || request()->routeIs('admin.employees.*') || request()->routeIs('admin.jobs.*') || request()->routeIs('admin.suppliers.*') ? 'true' : 'false' }} }">
                            <button @click.stop="peopleOpen = !peopleOpen" class="w-full flex items-center justify-between px-2 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 transition-colors">
                                <span x-show="sidebarOpen">People & Resources</span>
                                <i x-show="sidebarOpen" class="fas fa-chevron-down text-xs" :class="peopleOpen ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="peopleOpen" x-transition class="mt-2">
                                <ul class="space-y-1">
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-users sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Customers</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Customers</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.employees.index') }}" class="sidebar-link {{ request()->routeIs('admin.employees.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-user-tie sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Employees</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Employees</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.jobs.index') }}" class="sidebar-link {{ request()->routeIs('admin.jobs.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-briefcase sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Job Positions</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Job Positions</span>
                                    </li>
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.suppliers.index') }}" class="sidebar-link {{ request()->routeIs('admin.suppliers.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-handshake sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Suppliers</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Suppliers</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Settings & Reports Section -->
                        <div x-data="{ settingsOpen: {{ request()->routeIs('admin.discount-rules.*') ? 'true' : 'false' }} }">
                            <button @click.stop="settingsOpen = !settingsOpen" class="w-full flex items-center justify-between px-2 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 transition-colors">
                                <span x-show="sidebarOpen">Settings</span>
                                <i x-show="sidebarOpen" class="fas fa-chevron-down text-xs" :class="settingsOpen ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="settingsOpen" x-transition class="mt-2">
                                <ul class="space-y-1">
                                    <li class="relative sidebar-item">
                                        <a href="{{ route('admin.discount-rules.index') }}" class="sidebar-link {{ request()->routeIs('admin.discount-rules.*') ? 'sidebar-active' : '' }}">
                                            <i class="fas fa-percent sidebar-icon"></i>
                                            <span x-show="sidebarOpen">Discount Rules</span>
                                        </a>
                                        <span x-show="!sidebarOpen" class="sidebar-tooltip">Discount Rules</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- User Section -->
                <div :class="sidebarOpen ? 'p-4' : 'p-2'" class="border-t border-gray-200" x-data="{ userMenuOpen: false }">
                    <div class="relative">
                        <button @click.stop="userMenuOpen = !userMenuOpen" 
                                :class="sidebarOpen ? 'flex items-center w-full p-2' : 'flex items-center justify-center w-full p-2'"
                                class="rounded-lg hover:bg-gray-100 transition-colors">
                            <div :class="sidebarOpen ? 'w-8 h-8' : 'w-10 h-10'" class="bg-maroon rounded-full flex items-center justify-center text-white"
                                 :class="sidebarOpen ? 'text-sm' : 'text-base'">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div x-show="sidebarOpen" class="ml-3 flex-1 text-left">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                        </button>
                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition 
                             :class="sidebarOpen ? 'absolute bottom-full left-0 right-0 mb-2' : 'absolute bottom-full left-0 mb-2'"
                             class="bg-white rounded-lg shadow-lg border border-gray-200 py-2"
                             :style="sidebarOpen ? '' : 'width: 200px;'">
                            <form method="POST" action="{{ route('admin.logout') }}">
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
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-600">@yield('page-description', 'Welcome to Ecoretech Printing Shop Admin Panel')</p>
                    </div>

                    
                    <div class="flex items-center space-x-4">
                        @hasSection('header-actions')
                            <div class="flex items-end">
                                @yield('header-actions')
                            </div>
                        @endif
                        <div class="text-sm text-gray-600">
                            {{ now()->format('M d, Y - h:i A') }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @php
                    $flashType = session('success') ? 'success' : (session('error') ? 'error' : null);
                    $flashMessage = session('success') ?? session('error');
                @endphp
                @if ($flashMessage)
                    <div
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 3000)"
                        x-show="show"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="mb-4 rounded-lg relative px-4 py-3 border shadow-lg {{ $flashType==='success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' }}"
                        role="alert"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas {{ $flashType==='success' ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-red-400' }}"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <span class="block sm:inline font-medium">{{ $flashMessage }}</span>
                            </div>
                            <button type="button" @click="show = false" class="ml-4 text-sm opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <style>
        .nav-link {
            @apply flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200;
        }
        .nav-link i {
            @apply w-5 text-center;
        }
        .nav-link span {
            @apply ml-3 font-medium;
        }
        .sidebar-active {
            @apply bg-maroon bg-opacity-10 text-maroon border-r-4 border-maroon;
        }
    </style>
</body>
</html>