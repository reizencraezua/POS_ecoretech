<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Ecoretech Printing Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <div class="mx-auto w-20 h-20 bg-maroon rounded-full flex items-center justify-center mb-4">
                    <span class="text-white text-3xl font-bold">E</span>
                </div>
                <h1 class="text-2xl font-bold text-maroon mb-2">Ecoretech Printing Shop</h1>
                <p class="text-gray-600 text-sm">Admin & Cashier Login Panel</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        required 
                        autocomplete="email" 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors duration-200 @error('email') border-red-500 @enderror"
                        placeholder="Enter your email address"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors duration-200 @error('password') border-red-500 @enderror"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="h-4 w-4 text-maroon border-gray-300 rounded focus:ring-maroon"
                        >
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-maroon text-white py-3 px-4 rounded-lg font-medium hover:bg-maroon-dark focus:outline-none focus:ring-2 focus:ring-maroon focus:ring-offset-2 transition-colors duration-200"
                >
                    Sign In to POS System
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    © 2024 Ecoretech Printing Shop. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
