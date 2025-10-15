s<!-- Notification Container -->
<div id="notification-container" class="fixed right-4 z-50 space-y-2" style="top: 0px;">
    <!-- Notifications will be dynamically inserted here -->
</div>

<!-- Notification Template -->
<template id="notification-template">
    <div class="notification-item bg-white border-l-4 shadow-lg rounded-lg p-4 max-w-sm transform transition-all duration-300 ease-in-out translate-x-full opacity-0">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="notification-icon w-8 h-8 rounded-full flex items-center justify-center">
                    <!-- Icon will be inserted here -->
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="notification-title text-sm font-medium text-gray-900"></p>
                <p class="notification-message text-sm text-gray-500"></p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button class="notification-close inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notification-container');
        this.template = document.getElementById('notification-template');
        this.notifications = new Map();
        this.init();
    }

    init() {
        // Ensure container exists
        if (!this.container) {
            this.createContainer();
        }
        
        // Position notifications below header
        this.positionNotifications();
        
        // Recalculate position on window resize
        window.addEventListener('resize', () => {
            this.positionNotifications();
        });
    }

    positionNotifications() {
        if (!this.container) return;
        
        const header = document.querySelector('header');
        if (header) {
            const headerRect = header.getBoundingClientRect();
            const headerHeight = headerRect.height;
            const topPosition = headerHeight + 16; // 16px gap below header
            
            this.container.style.top = `${topPosition}px`;
        } else {
            // Fallback to fixed position if header not found
            this.container.style.top = '80px';
        }
    }

    createContainer() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed right-4 z-50 space-y-2';
        container.style.top = '0px';
        document.body.appendChild(container);
        this.container = container;
        
        // Position notifications below header
        this.positionNotifications();
    }

    show(type, title, message, duration = 5000) {
        const id = Date.now() + Math.random();
        const notification = this.createNotification(id, type, title, message);
        
        this.container.appendChild(notification);
        this.notifications.set(id, notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 10);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                this.remove(id);
            }, duration);
        }

        return id;
    }

    createNotification(id, type, title, message) {
        const notification = this.template.content.cloneNode(true);
        const notificationElement = notification.querySelector('.notification-item');
        
        // Set unique ID
        notificationElement.setAttribute('data-notification-id', id);

        // Configure based on type
        this.configureNotification(notificationElement, type, title, message);

        // Add close functionality
        const closeBtn = notificationElement.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => this.remove(id));

        return notificationElement;
    }

    configureNotification(element, type, title, message) {
        const iconContainer = element.querySelector('.notification-icon');
        const titleElement = element.querySelector('.notification-title');
        const messageElement = element.querySelector('.notification-message');

        // Set content
        titleElement.textContent = title;
        messageElement.textContent = message;

        // Configure based on type
        switch (type) {
            case 'success':
                element.classList.add('border-green-500');
                iconContainer.classList.add('bg-green-100');
                iconContainer.innerHTML = `
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                `;
                break;

            case 'error':
                element.classList.add('border-red-500');
                iconContainer.classList.add('bg-red-100');
                iconContainer.innerHTML = `
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                `;
                break;

            case 'warning':
                element.classList.add('border-yellow-500');
                iconContainer.classList.add('bg-yellow-100');
                iconContainer.innerHTML = `
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                `;
                break;

            case 'info':
                element.classList.add('border-blue-500');
                iconContainer.classList.add('bg-blue-100');
                iconContainer.innerHTML = `
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                `;
                break;

            default:
                element.classList.add('border-gray-500');
                iconContainer.classList.add('bg-gray-100');
                iconContainer.innerHTML = `
                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                `;
        }
    }

    remove(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        // Animate out
        notification.classList.add('translate-x-full', 'opacity-0');
        notification.classList.remove('translate-x-0', 'opacity-100');

        // Remove from DOM after animation
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 300);
    }

    clear() {
        this.notifications.forEach((notification, id) => {
            this.remove(id);
        });
    }
}

// Initialize notification system
window.notificationSystem = new NotificationSystem();

// Convenience functions
window.showNotification = (type, title, message, duration) => {
    return window.notificationSystem.show(type, title, message, duration);
};

window.showSuccess = (title, message, duration) => {
    return window.notificationSystem.show('success', title, message, duration);
};

window.showError = (title, message, duration) => {
    return window.notificationSystem.show('error', title, message, duration);
};

window.showWarning = (title, message, duration) => {
    return window.notificationSystem.show('warning', title, message, duration);
};

window.showInfo = (title, message, duration) => {
    return window.notificationSystem.show('info', title, message, duration);
};
</script>
