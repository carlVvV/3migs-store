@extends('layouts.admin')

@section('title', 'Users - Admin Dashboard')
@section('page-title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Users Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $users->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-shield text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Admin Users</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $users->where('role', 'admin')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Regular Users</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $users->where('role', '!=', 'admin')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Filter Users</h3>
            <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Search by name or email..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">All Users</h3>
            
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($user->role === 'admin') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($user->role ?? 'user') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $latestDoc = $user->latestIdDocument;
                                        $approvedDoc = $user->approvedIdDocument;
                                        $statusLabel = 'No ID Submitted';
                                        $statusClasses = 'bg-gray-100 text-gray-700';
                                        $statusIcon = 'fas fa-id-card';

                                        if ($approvedDoc) {
                                            $statusLabel = 'Verified';
                                            $statusClasses = 'bg-green-100 text-green-800';
                                            $statusIcon = 'fas fa-check-circle';
                                        } elseif ($latestDoc) {
                                            if ($latestDoc->status === 'pending') {
                                                $statusLabel = 'Pending Review';
                                                $statusClasses = 'bg-yellow-100 text-yellow-800';
                                                $statusIcon = 'fas fa-hourglass-half';
                                            } elseif ($latestDoc->status === 'rejected') {
                                                $statusLabel = 'Rejected';
                                                $statusClasses = 'bg-red-100 text-red-800';
                                                $statusIcon = 'fas fa-times-circle';
                                            }
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex items-center gap-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        <i class="{{ $statusIcon }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button"
                                            class="text-blue-600 hover:text-blue-900 view-user-btn"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}">
                                        View
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Found</h3>
                    <p class="text-gray-500">There are no users matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div id="user-detail-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-40 transition-opacity" data-modal-overlay></div>
    <div class="relative mx-auto my-10 w-11/12 max-w-5xl bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 id="user-modal-name" class="text-xl font-semibold text-gray-900">User Details</h3>
                <p id="user-modal-email" class="text-sm text-gray-500"></p>
            </div>
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-modal-close>
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
            <div id="user-modal-loading" class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-circle-notch fa-spin"></i>
                <span>Loading user information...</span>
            </div>
            <div id="user-modal-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-100 rounded-md px-4 py-3 mt-3"></div>
            <div id="user-modal-content" class="space-y-6 hidden">
                <section>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">General Information</h4>
                    <div id="user-modal-info" class="mt-3"></div>
                </section>
                <section>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Saved Addresses</h4>
                    <div id="user-modal-addresses" class="mt-3 space-y-3"></div>
                </section>
                <section>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">ID Documents</h4>
                    <div id="user-modal-id-documents" class="mt-3 space-y-3"></div>
                </section>
                <section>
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Recent Orders</h4>
                        <span id="user-modal-order-summary" class="text-xs text-gray-500"></span>
                    </div>
                    <div id="user-modal-orders" class="mt-3 space-y-3"></div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('user-detail-modal');
    if (!modal) {
        return;
    }

    const overlay = modal.querySelector('[data-modal-overlay]');
    const closeButtons = modal.querySelectorAll('[data-modal-close]');
    const loadingEl = document.getElementById('user-modal-loading');
    const contentEl = document.getElementById('user-modal-content');
    const errorEl = document.getElementById('user-modal-error');
    const nameEl = document.getElementById('user-modal-name');
    const emailEl = document.getElementById('user-modal-email');
    const infoEl = document.getElementById('user-modal-info');
    const addressesEl = document.getElementById('user-modal-addresses');
    const idDocsEl = document.getElementById('user-modal-id-documents');
    const ordersEl = document.getElementById('user-modal-orders');
    const orderSummaryEl = document.getElementById('user-modal-order-summary');
    let currentUserId = null;

    document.querySelectorAll('.view-user-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const userId = button.dataset.userId;
            const fallbackName = button.dataset.userName || 'User Details';
            const fallbackEmail = button.dataset.userEmail || '';
            openModal(userId, fallbackName, fallbackEmail);
        });
    });

    // Event delegation for sync Veriff status buttons (dynamically created)
    modal.addEventListener('click', (event) => {
        if (event.target.closest('.sync-veriff-btn')) {
            const button = event.target.closest('.sync-veriff-btn');
            const sessionId = button.dataset.sessionId;
            if (sessionId) {
                syncVeriffStatus(sessionId, button);
            }
        }
    });

    if (overlay) {
        overlay.addEventListener('click', closeModal);
    }

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    function openModal(userId, fallbackName, fallbackEmail) {
        if (!userId) {
            return;
        }
        currentUserId = userId;

        showModal();
        setLoadingState();

        nameEl.textContent = fallbackName;
        emailEl.textContent = fallbackEmail;

        fetch(`/admin/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(handleFetchResponse)
            .then((payload) => {
                if (!payload.success || !payload.data) {
                    throw new Error(payload.message || 'Unable to load user details.');
                }
                renderModal(payload.data);
            })
            .catch((error) => {
                showError(error.message || 'Failed to load user information.');
                console.error(error);
            });
    }

    function showModal() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function setLoadingState() {
        loadingEl.classList.remove('hidden');
        contentEl.classList.add('hidden');
        errorEl.classList.add('hidden');
        infoEl.innerHTML = '';
        addressesEl.innerHTML = '';
        idDocsEl.innerHTML = '';
        ordersEl.innerHTML = '';
        orderSummaryEl.textContent = '';
    }

    function showError(message) {
        loadingEl.classList.add('hidden');
        contentEl.classList.add('hidden');
        errorEl.classList.remove('hidden');
        errorEl.textContent = message;
    }

    function renderModal(data) {
        loadingEl.classList.add('hidden');
        errorEl.classList.add('hidden');
        contentEl.classList.remove('hidden');

        const user = data.user || {};
        const addresses = Array.isArray(data.addresses) ? data.addresses : [];
        const idDocuments = Array.isArray(data.id_documents) ? data.id_documents : [];
        const orders = Array.isArray(data.orders) ? data.orders : [];
        const orderSummary = data.order_summary || {};

        nameEl.textContent = user.name || 'User Details';
        emailEl.textContent = user.email || '';

        infoEl.innerHTML = renderGeneralInfo(user, idDocuments, orderSummary);
        addressesEl.innerHTML = renderAddresses(addresses);
        idDocsEl.innerHTML = renderIdDocuments(idDocuments);
        ordersEl.innerHTML = renderOrders(orders);
        orderSummaryEl.textContent = renderOrderSummary(orderSummary);
    }

    function handleFetchResponse(response) {
        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }
        return response.json();
    }

    function renderGeneralInfo(user, idDocuments, orderSummary) {
        const idStatus = getIdStatus(idDocuments);
        return `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div>
                    <p class="text-xs uppercase text-gray-500">Name</p>
                    <p class="font-medium">${escapeHtml(user.name) || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Email</p>
                    <p class="font-medium">${escapeHtml(user.email) || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Phone</p>
                    <p class="font-medium">${escapeHtml(user.phone) || 'Not set'}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Role</p>
                    <p class="font-medium capitalize">${escapeHtml(user.role) || 'customer'}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Joined</p>
                    <p class="font-medium">${formatDate(user.created_at)}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Email Verified</p>
                    <p class="font-medium">${user.email_verified_at ? formatDate(user.email_verified_at) : 'Not verified'}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Total Orders</p>
                    <p class="font-medium">${orderSummary.total_orders ?? 0}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500">Total Spent</p>
                    <p class="font-medium">${formatCurrency(orderSummary.total_spent ?? 0)}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase text-gray-500">ID Status</p>
                    <span class="inline-flex items-center gap-2 px-3 py-1 mt-1 text-xs font-semibold rounded-full ${idStatus.classes}">
                        <i class="${idStatus.icon}"></i>
                        ${idStatus.label}
                    </span>
                </div>
            </div>
        `;
    }

    function renderAddresses(addresses) {
        if (!addresses.length) {
            return '<p class="text-sm text-gray-500">No saved addresses.</p>';
        }

        return addresses.map((address) => `
            <div class="border border-gray-200 rounded-md p-4 ${address.is_default ? 'bg-gray-50' : ''}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900">${escapeHtml(address.full_name) || 'Unnamed Address'}</p>
                        <p class="text-sm text-gray-600">${escapeHtml(address.street_address)}</p>
                        ${address.apartment ? `<p class="text-sm text-gray-600">${escapeHtml(address.apartment)}</p>` : ''}
                        <p class="text-sm text-gray-600">${[address.barangay, address.city, address.province].filter(Boolean).map(escapeHtml).join(', ')}</p>
                        <p class="text-sm text-gray-600">${escapeHtml(address.postal_code) || ''}</p>
                        <p class="text-sm text-gray-600 mt-2">
                            <span class="font-medium">Contact:</span> ${escapeHtml(address.phone) || 'N/A'}
                            ${address.email ? ` • ${escapeHtml(address.email)}` : ''}
                        </p>
                    </div>
                    ${address.is_default ? '<span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Default</span>' : ''}
                </div>
            </div>
        `).join('');
    }

    function renderIdDocuments(documents) {
        if (!documents.length) {
            return '<p class="text-sm text-gray-500">No ID documents uploaded yet.</p>';
        }

        return documents.map((doc) => {
            const status = getIdStatus([doc]);

            return `
                <div class="border border-gray-200 rounded-md p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${escapeHtml(doc.type) || 'ID Document'}</p>
                            <p class="text-xs text-gray-500">Uploaded ${formatDate(doc.created_at || doc.uploaded_at)}</p>
                            <div class="mt-2 text-xs text-gray-500 font-mono">Veriff Session: ${escapeHtml(doc.veriff_session_id) || 'N/A'}</div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold rounded-full ${status.classes}">
                                <i class="${status.icon}"></i>
                                ${status.label}
                            </span>
                            ${doc.veriff_session_id ? `
                                <button 
                                    type="button"
                                    class="sync-veriff-btn text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                                    data-session-id="${escapeHtml(doc.veriff_session_id)}"
                                    title="Sync status from Veriff API">
                                    <i class="fas fa-sync-alt mr-1"></i> Sync from Veriff
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderOrders(orders) {
        if (!orders.length) {
            return '<p class="text-sm text-gray-500">No recent orders found.</p>';
        }

        return orders.map((order) => `
            <div class="border border-gray-200 rounded-md p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <p class="font-semibold text-gray-900">Order #${escapeHtml(order.order_number) || order.id}</p>
                        <p class="text-xs text-gray-500">Placed ${formatDate(order.created_at)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 capitalize">${escapeHtml(order.status) || 'pending'}</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 capitalize">${escapeHtml(order.payment_status) || 'pending'}</span>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-700">
                    <p class="font-medium">Total: ${formatCurrency(order.total_amount ?? 0)}</p>
                    <p class="text-xs text-gray-500 uppercase mt-1">Payment Method: ${escapeHtml(order.payment_method) || 'N/A'}</p>
                </div>
                <div class="mt-3">
                    <h5 class="text-xs font-semibold text-gray-500 uppercase">Items</h5>
                    <ul class="mt-2 space-y-1 text-sm text-gray-700">
                        ${(Array.isArray(order.items) ? order.items : []).map((item) => `
                            <li class="flex justify-between">
                                <span>${escapeHtml(item.product_name) || 'Product'} × ${item.quantity}</span>
                                <span>${formatCurrency(item.total_price ?? ((item.unit_price ?? 0) * (item.quantity ?? 0)))}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            </div>
        `).join('');
    }

    function renderOrderSummary(summary) {
        const totalOrders = summary.total_orders ?? 0;
        const totalSpent = formatCurrency(summary.total_spent ?? 0);
        return `Showing recent orders • Total Orders: ${totalOrders} • Total Spent: ${totalSpent}`;
    }

    function getIdStatus(documents) {
        if (!documents || !documents.length) {
            return {
                label: 'No ID on file',
                classes: 'bg-gray-100 text-gray-700',
                icon: 'fas fa-id-card',
            };
        }

        const priority = ['approved', 'pending', 'rejected'];
        let status = documents[0].status || 'pending';

        for (const state of priority) {
            const doc = documents.find((item) => item.status === state);
            if (doc) {
                status = doc.status;
                break;
            }
        }

        switch (status) {
            case 'approved':
                return { label: 'Verified', classes: 'bg-green-100 text-green-800', icon: 'fas fa-check-circle' };
            case 'pending':
                return { label: 'Pending Review', classes: 'bg-yellow-100 text-yellow-800', icon: 'fas fa-hourglass-half' };
            case 'rejected':
                return { label: 'Rejected', classes: 'bg-red-100 text-red-800', icon: 'fas fa-times-circle' };
            default:
                return { label: 'Unknown', classes: 'bg-gray-100 text-gray-700', icon: 'fas fa-question-circle' };
        }
    }

    function formatDate(dateString) {
        if (!dateString) {
            return 'Not available';
        }

        const date = new Date(dateString);
        if (Number.isNaN(date.getTime())) {
            return dateString;
        }

        return new Intl.DateTimeFormat('en-PH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(date);
    }

    function formatCurrency(value) {
        const number = Number(value) || 0;
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
        }).format(number);
    }

    async function syncVeriffStatus(sessionId, button) {
        if (!sessionId) {
            alert('Session ID is missing');
            return;
        }

        // Disable button and show loading state
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...';
        button.classList.add('opacity-50', 'cursor-not-allowed');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const response = await fetch('/admin/veriff/sync-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ session_id: sessionId }),
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                alert(`Status synced successfully!\n\nVeriff Status: ${data.veriff_status}\nDatabase Status: ${data.document.status}\n\n${data.message}`);
                
                // Reload the user details to show updated status
                if (currentUserId) {
                    const fallbackName = nameEl?.textContent || 'User Details';
                    const fallbackEmail = emailEl?.textContent || '';
                    openModal(currentUserId, fallbackName, fallbackEmail);
                }
            } else {
                alert(`Failed to sync status: ${data.error || data.message || 'Unknown error'}`);
                button.innerHTML = originalHTML;
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } catch (error) {
            console.error('Error syncing Veriff status:', error);
            alert('Failed to sync status. Please check the console for details.');
            button.innerHTML = originalHTML;
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttribute(value) {
        return escapeHtml(value).replace(/"/g, '&quot;');
    }
});
</script>
@endpush
