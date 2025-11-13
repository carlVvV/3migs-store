<div x-data="toastNotification()" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed top-4 right-4 z-50 max-w-sm w-full"
     style="display: none;">
    <div :class="typeClasses" 
         class="rounded-lg shadow-lg p-4 flex items-start gap-3 border-l-4">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <i :class="iconClass" class="text-xl"></i>
        </div>
        
        <!-- Message -->
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium" x-text="message"></p>
        </div>
        
        <!-- Close Button -->
        <button @click="close()" 
                class="flex-shrink-0 text-current opacity-70 hover:opacity-100 transition-opacity">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
function toastNotification() {
    return {
        show: false,
        message: '',
        type: 'success',
        
        init() {
            // Listen for custom 'notify' event
            const self = this;
            window.addEventListener('notify', function(e) {
                self.message = e.detail.message || 'Notification';
                self.type = e.detail.type || 'success';
                self.show = true;
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    self.close();
                }, 5000);
            });
        },
        
        close() {
            this.show = false;
            // Reset after transition
            setTimeout(() => {
                this.message = '';
                this.type = 'success';
            }, 200);
        },
        
        get typeClasses() {
            const baseClasses = 'bg-white';
            const typeMap = {
                'success': 'border-green-500 text-green-800',
                'error': 'border-red-500 text-red-800',
                'warning': 'border-yellow-500 text-yellow-800',
                'info': 'border-blue-500 text-blue-800',
            };
            return `${baseClasses} ${typeMap[this.type] || typeMap.success}`;
        },
        
        get iconClass() {
            const iconMap = {
                'success': 'fas fa-check-circle text-green-500',
                'error': 'fas fa-exclamation-circle text-red-500',
                'warning': 'fas fa-exclamation-triangle text-yellow-500',
                'info': 'fas fa-info-circle text-blue-500',
            };
            return iconMap[this.type] || iconMap.success;
        }
    }
}

// Helper function to dispatch notify event
window.notify = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { message, type }
    }));
};
</script>

