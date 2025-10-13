@extends('layouts.app')

@section('title', 'My Account - 3Migs Gowns & Barong')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Account</h1>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <!-- Sidebar Navigation -->
            <div class="md:w-1/4 bg-gray-50 border-r border-gray-200 p-6">
                <nav class="space-y-2">
                    <a href="#profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md font-medium active-link" data-tab="profile">
                        <i class="fas fa-user mr-2"></i> Profile Information
                    </a>
                    <a href="#password" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md font-medium" data-tab="password">
                        <i class="fas fa-lock mr-2"></i> Change Password
                    </a>
                    <a href="#notifications" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md font-medium" data-tab="notifications">
                        <i class="fas fa-bell mr-2"></i> Notification Settings
                    </a>
                </nav>
            </div>

            <!-- Main Content Area -->
            <div class="md:w-3/4 p-8">
                <!-- Profile Information Tab -->
                <div id="profile-tab" class="tab-content active">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Profile Information</h2>
                    <form method="POST" action="{{ route('account.profile.update') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-6 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Save Changes
                            </button>
                        </div>
                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-green-600 mt-4">Profile updated successfully!</p>
                        @endif
                    </form>
                </div>

                <!-- Change Password Tab -->
                <div id="password-tab" class="tab-content hidden">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Change Password</h2>
                    <form method="POST" action="{{ route('account.password.update') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" id="password" name="password" required autocomplete="new-password"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-6 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Change Password
                            </button>
                        </div>
                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-green-600 mt-4">Password updated successfully!</p>
                        @endif
                    </form>
                </div>

                <!-- Notification Settings Tab -->
                <div id="notifications-tab" class="tab-content hidden">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Notification Settings</h2>
                    <form method="POST" action="{{ route('account.notifications.update') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="email_notifications" value="1" class="form-checkbox h-5 w-5 text-gray-600" {{ $user->email_notifications ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Email Notifications</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">Receive updates about your orders and account via email.</p>
                        </div>
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sms_notifications" value="1" class="form-checkbox h-5 w-5 text-gray-600" {{ $user->sms_notifications ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">SMS Notifications</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">Receive important updates and tracking information via SMS.</p>
                        </div>
                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="marketing_emails" value="1" class="form-checkbox h-5 w-5 text-gray-600" {{ $user->marketing_emails ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Marketing Emails</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">Receive promotional offers and news from 3Migs Gowns & Barong.</p>
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-6 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Save Preferences
                            </button>
                        </div>
                        @if (session('status') === 'notifications-updated')
                            <p class="text-sm text-green-600 mt-4">Notification preferences updated successfully!</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tab]');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const targetTab = this.dataset.tab;

                tabs.forEach(item => item.classList.remove('active-link', 'bg-gray-100'));
                tabContents.forEach(content => content.classList.add('hidden'));

                this.classList.add('active-link', 'bg-gray-100');
                document.getElementById(`${targetTab}-tab`).classList.remove('hidden');
            });

            // Set initial active tab based on URL hash or default to profile
            if (window.location.hash === `#${tab.dataset.tab}`) {
                tab.click();
            }
        });

        // Default to profile tab if no hash is present
        if (!window.location.hash) {
            document.querySelector('[data-tab="profile"]').click();
        }
    });
</script>
@endsection