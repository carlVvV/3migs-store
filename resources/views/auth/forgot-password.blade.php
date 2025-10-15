@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16">
    <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-md">
        <h1 class="text-xl font-semibold text-gray-900 mb-4">Forgot your password?</h1>
        <p class="text-gray-600 mb-6">Enter your account email and we will send a 6‑digit code to verify your identity.</p>

        @if(session('status'))
            <div class="mb-4 text-green-600 text-sm">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-600">
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button class="w-full bg-black text-white py-2 rounded-md hover:bg-gray-800">Send Code</button>
        </form>
    </div>
    </div>
@endsection

<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
