@extends('layouts.cashier')

@section('title', 'Change Password')
@section('page-title', 'Change Password')
@section('page-description', 'Update your account password')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
            <p class="text-sm text-gray-600">Update your account password for security</p>
        </div>

        <div class="px-6 py-4">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cashier.change-password') }}" class="space-y-6">
                @csrf

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password *
                    </label>
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        required 
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors duration-200 @error('current_password') border-red-500 @enderror"
                        placeholder="Enter your current password"
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password *
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autocomplete="new-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors duration-200 @error('password') border-red-500 @enderror"
                        placeholder="Enter your new password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Password Strength Indicator -->
                    <div class="mt-2">
                        <div class="text-xs text-gray-600 mb-1">Password Requirements:</div>
                        <div class="space-y-1">
                            <div class="flex items-center text-xs" id="length-check">
                                <span class="w-4 h-4 mr-2 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden"></i>
                                </span>
                                <span class="text-gray-600">At least 8 characters</span>
                            </div>
                            <div class="flex items-center text-xs" id="uppercase-check">
                                <span class="w-4 h-4 mr-2 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden"></i>
                                </span>
                                <span class="text-gray-600">One uppercase letter (A-Z)</span>
                            </div>
                            <div class="flex items-center text-xs" id="lowercase-check">
                                <span class="w-4 h-4 mr-2 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden"></i>
                                </span>
                                <span class="text-gray-600">One lowercase letter (a-z)</span>
                            </div>
                            <div class="flex items-center text-xs" id="number-check">
                                <span class="w-4 h-4 mr-2 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden"></i>
                                </span>
                                <span class="text-gray-600">One number (0-9)</span>
                            </div>
                            <div class="flex items-center text-xs" id="special-check">
                                <span class="w-4 h-4 mr-2 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden"></i>
                                </span>
                                <span class="text-gray-600">One special character (@$!%*?&)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password *
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors duration-200 @error('password_confirmation') border-red-500 @enderror"
                        placeholder="Confirm your new password"
                    >
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('cashier.dashboard') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </a>
                    
                    <button 
                        type="submit" 
                        class="bg-maroon hover:bg-maroon-dark text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-maroon focus:ring-offset-2"
                    >
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div>
</div>

<script>
// Real-time password validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    validatePasswordRequirements(password);
});

function validatePasswordRequirements(password) {
    // Length check
    const lengthCheck = document.getElementById('length-check');
    const lengthIcon = lengthCheck.querySelector('i');
    const lengthSpan = lengthCheck.querySelector('span:last-child');
    
    if (password.length >= 8) {
        lengthIcon.classList.remove('hidden');
        lengthCheck.querySelector('span:first-child').classList.remove('border-gray-300');
        lengthCheck.querySelector('span:first-child').classList.add('border-green-500', 'bg-green-500');
        lengthSpan.classList.remove('text-gray-600');
        lengthSpan.classList.add('text-green-600');
    } else {
        lengthIcon.classList.add('hidden');
        lengthCheck.querySelector('span:first-child').classList.add('border-gray-300');
        lengthCheck.querySelector('span:first-child').classList.remove('border-green-500', 'bg-green-500');
        lengthSpan.classList.add('text-gray-600');
        lengthSpan.classList.remove('text-green-600');
    }

    // Uppercase check
    const uppercaseCheck = document.getElementById('uppercase-check');
    const uppercaseIcon = uppercaseCheck.querySelector('i');
    const uppercaseSpan = uppercaseCheck.querySelector('span:last-child');
    
    if (/[A-Z]/.test(password)) {
        uppercaseIcon.classList.remove('hidden');
        uppercaseCheck.querySelector('span:first-child').classList.remove('border-gray-300');
        uppercaseCheck.querySelector('span:first-child').classList.add('border-green-500', 'bg-green-500');
        uppercaseSpan.classList.remove('text-gray-600');
        uppercaseSpan.classList.add('text-green-600');
    } else {
        uppercaseIcon.classList.add('hidden');
        uppercaseCheck.querySelector('span:first-child').classList.add('border-gray-300');
        uppercaseCheck.querySelector('span:first-child').classList.remove('border-green-500', 'bg-green-500');
        uppercaseSpan.classList.add('text-gray-600');
        uppercaseSpan.classList.remove('text-green-600');
    }

    // Lowercase check
    const lowercaseCheck = document.getElementById('lowercase-check');
    const lowercaseIcon = lowercaseCheck.querySelector('i');
    const lowercaseSpan = lowercaseCheck.querySelector('span:last-child');
    
    if (/[a-z]/.test(password)) {
        lowercaseIcon.classList.remove('hidden');
        lowercaseCheck.querySelector('span:first-child').classList.remove('border-gray-300');
        lowercaseCheck.querySelector('span:first-child').classList.add('border-green-500', 'bg-green-500');
        lowercaseSpan.classList.remove('text-gray-600');
        lowercaseSpan.classList.add('text-green-600');
    } else {
        lowercaseIcon.classList.add('hidden');
        lowercaseCheck.querySelector('span:first-child').classList.add('border-gray-300');
        lowercaseCheck.querySelector('span:first-child').classList.remove('border-green-500', 'bg-green-500');
        lowercaseSpan.classList.add('text-gray-600');
        lowercaseSpan.classList.remove('text-green-600');
    }

    // Number check
    const numberCheck = document.getElementById('number-check');
    const numberIcon = numberCheck.querySelector('i');
    const numberSpan = numberCheck.querySelector('span:last-child');
    
    if (/[0-9]/.test(password)) {
        numberIcon.classList.remove('hidden');
        numberCheck.querySelector('span:first-child').classList.remove('border-gray-300');
        numberCheck.querySelector('span:first-child').classList.add('border-green-500', 'bg-green-500');
        numberSpan.classList.remove('text-gray-600');
        numberSpan.classList.add('text-green-600');
    } else {
        numberIcon.classList.add('hidden');
        numberCheck.querySelector('span:first-child').classList.add('border-gray-300');
        numberCheck.querySelector('span:first-child').classList.remove('border-green-500', 'bg-green-500');
        numberSpan.classList.add('text-gray-600');
        numberSpan.classList.remove('text-green-600');
    }

    // Special character check
    const specialCheck = document.getElementById('special-check');
    const specialIcon = specialCheck.querySelector('i');
    const specialSpan = specialCheck.querySelector('span:last-child');
    
    if (/[@$!%*?&]/.test(password)) {
        specialIcon.classList.remove('hidden');
        specialCheck.querySelector('span:first-child').classList.remove('border-gray-300');
        specialCheck.querySelector('span:first-child').classList.add('border-green-500', 'bg-green-500');
        specialSpan.classList.remove('text-gray-600');
        specialSpan.classList.add('text-green-600');
    } else {
        specialIcon.classList.add('hidden');
        specialCheck.querySelector('span:first-child').classList.add('border-gray-300');
        specialCheck.querySelector('span:first-child').classList.remove('border-green-500', 'bg-green-500');
        specialSpan.classList.add('text-gray-600');
        specialSpan.classList.remove('text-green-600');
    }
}

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    // Check if all requirements are met
    const allChecks = document.querySelectorAll('#password + div .flex.items-center');
    let allValid = true;
    
    allChecks.forEach(check => {
        const icon = check.querySelector('i');
        if (icon.classList.contains('hidden')) {
            allValid = false;
        }
    });
    
    if (!allValid) {
        e.preventDefault();
        alert('Please ensure all password requirements are met before submitting.');
        return false;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password confirmation does not match.');
        return false;
    }
    
    // Check for weak patterns
    const weakPatterns = ['password', '123456', 'qwerty', 'abc123', 'admin', 'user', 'welcome', 'login', 'pass', 'secret', 'test'];
    const lowerPassword = password.toLowerCase();
    
    for (let pattern of weakPatterns) {
        if (lowerPassword.includes(pattern)) {
            e.preventDefault();
            alert('Password contains common weak patterns. Please choose a stronger password.');
            return false;
        }
    }
    
    // Check for repeated characters
    if (/(.)\1{2,}/.test(password)) {
        e.preventDefault();
        alert('Password should not contain repeated characters (e.g., aaa, 111).');
        return false;
    }
    
    // Check for sequential characters
    if (/(012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i.test(password)) {
        e.preventDefault();
        alert('Password should not contain sequential characters (e.g., 123, abc).');
        return false;
    }
});
</script>
@endsection
