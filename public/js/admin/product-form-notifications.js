// Product Form JavaScript - Notification System
// File: resources/js/admin/product-form-notifications.js

// Position notification container below header dynamically
document.addEventListener('DOMContentLoaded', function() {
    const notificationContainer = document.getElementById('notification-container');
    if (notificationContainer && typeof positionNotificationBelowHeader === 'function') {
        positionNotificationBelowHeader(notificationContainer, 16);
    }
    
    // Recalculate position on window resize
    window.addEventListener('resize', () => {
        if (notificationContainer && typeof positionNotificationBelowHeader === 'function') {
            positionNotificationBelowHeader(notificationContainer, 16);
        }
    });
});

// Simple notification system
function showSuccess(title, message, duration = 5000) {
    showNotification('success', title, message, duration);
}

function showError(title, message, duration = 7000) {
    showNotification('error', title, message, duration);
}

function positionNotificationBelowHeader() {
    const container = document.getElementById('notification-container');
    const header = document.querySelector('header');
    
    if (container && header) {
        const headerHeight = header.getBoundingClientRect().height;
        container.style.top = `${headerHeight + 16}px`;
    }
}

function showNotification(type, title, message, duration) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    // Position notification below header
    positionNotificationBelowHeader();
    
    const notification = document.createElement('div');
    notification.className = `bg-white border-l-4 shadow-lg rounded-lg p-4 max-w-sm transform transition-all duration-300 ease-in-out translate-x-full opacity-0 ${
        type === 'success' ? 'border-green-500' : 
        type === 'error' ? 'border-red-500' : 
        'border-blue-500'
    }`;
    
    const iconColor = type === 'success' ? 'text-green-600' : 
                     type === 'error' ? 'text-red-600' : 'text-blue-600';
    const iconBg = type === 'success' ? 'bg-green-100' : 
                  type === 'error' ? 'bg-red-100' : 'bg-blue-100';
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center ${iconBg}">
                    <svg class="w-5 h-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' ? 
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
                        }
                    </svg>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">${title}</p>
                <p class="text-sm text-gray-500">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Auto remove
    if (duration > 0) {
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
}
