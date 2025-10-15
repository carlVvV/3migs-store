@extends('layouts.app')

@section('title', 'Verify Code')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16">
    <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-md">
        <h1 class="text-xl font-semibold text-gray-900 mb-4">Enter Verification Code</h1>
        <p class="text-gray-600 mb-6">We sent a 6‑digit code to <span class="font-medium">{{ $email }}</span>.</p>

        <form method="POST" action="{{ route('password.verify.submit') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">6‑digit code</label>
                <input type="text" name="otp" maxlength="6" pattern="\d{6}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-600">
                @error('otp')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button class="w-full bg-black text-white py-2 rounded-md hover:bg-gray-800">Verify</button>
        </form>
    </div>
    </div>
@endsection


