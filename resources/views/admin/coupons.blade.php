@extends('layouts.admin')

@section('title', 'Coupons - Admin Dashboard')
@section('page-title', 'Coupons')

@section('content')
<div class="space-y-6">
    <!-- Coupons Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-ticket-alt text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Coupons</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $coupons->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Coupons</dt>
                            <dd class="text-lg font-medium text-gray-900">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Expired Coupons</dt>
                            <dd class="text-lg font-medium text-gray-900">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-800">Coupon System Coming Soon</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>The coupon management system is currently under development. This feature will include:</p>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        <li>Create and manage discount coupons</li>
                        <li>Set expiration dates and usage limits</li>
                        <li>Track coupon usage and performance</li>
                        <li>Generate coupon codes automatically</li>
                        <li>Apply coupons to specific products or categories</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder Content -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Coupon Management</h3>
                <button disabled 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                    <i class="fas fa-plus mr-2"></i>
                    Create Coupon
                </button>
            </div>
            
            <div class="text-center py-12">
                <i class="fas fa-ticket-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Coupons Available</h3>
                <p class="text-gray-500 mb-4">The coupon system is being developed and will be available soon.</p>
                <div class="text-sm text-gray-400">
                    <p>Expected features:</p>
                    <ul class="mt-2 space-y-1">
                        <li>• Percentage and fixed amount discounts</li>
                        <li>• Minimum order requirements</li>
                        <li>• Usage tracking and analytics</li>
                        <li>• Bulk coupon generation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
