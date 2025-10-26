@extends('layouts.app')

@section('title', 'Processing Order - 3Migs Gowns & Barong')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16">
    <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-md text-center">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-red-600 mb-4"></div>
        <h1 class="text-xl font-semibold text-gray-900 mb-2">Processing your order</h1>
        <p class="text-gray-600 mb-6">Please wait while we place your order and connect to the payment gateway.</p>
        <p id="processing-status" class="text-sm text-gray-500">Initializing…</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const statusEl = document.getElementById('processing-status');
    try {
        const raw = sessionStorage.getItem('checkoutOrderData');
        if (!raw) {
            statusEl.textContent = 'Missing order details. Redirecting…';
            setTimeout(() => window.location.href = '/checkout', 1500);
            return;
        }

        const orderData = JSON.parse(raw);
        statusEl.textContent = 'Placing order…';

        const res = await fetch('/api/v1/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            statusEl.textContent = (data && data.message) ? data.message : 'Failed to place order.';
            setTimeout(() => window.location.href = '/checkout', 2000);
            return;
        }

        const order = data.data?.order || data.data;
        if (orderData.payment_method === 'ewallet') {
            statusEl.textContent = 'Opening payment gateway…';
            const buxRes = await fetch(`/api/v1/orders/${order.id}/bux-checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const bux = await buxRes.json();
            const url = bux?.data?.checkout_url || bux?.data?.url || bux?.data?.redirect_url;
            if (buxRes.ok && bux.success && url) {
                window.location.href = url;
                return;
            }
            statusEl.textContent = 'Payment service unavailable. Redirecting to orders…';
            setTimeout(() => window.location.href = '/orders', 1500);
            return;
        }

        statusEl.textContent = 'Order placed. Redirecting…';
        setTimeout(() => window.location.href = '/checkout', 1200);
    } catch (e) {
        console.error(e);
        statusEl.textContent = 'An error occurred. Redirecting…';
        setTimeout(() => window.location.href = '/checkout', 1500);
    }
});
</script>
@endsection


