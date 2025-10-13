<!-- Reusable Delete Confirmation Modal -->
<div id="delete-confirmation-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full mx-4 p-4 max-w-xs">
        <!-- Header -->
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-2">
                <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900">Warning</h3>
        </div>
        
        <!-- Content -->
        <p id="delete-modal-message" class="text-gray-600 mb-4 text-sm">
            Do you want to delete this product?
        </p>
        
        <!-- Actions -->
        <div class="flex space-x-2">
            <button id="delete-confirm-btn" class="flex-1 bg-red-600 text-white py-2 px-3 rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
                Yes
            </button>
            <button id="delete-cancel-btn" class="flex-1 bg-gray-200 text-gray-800 py-2 px-3 rounded-md hover:bg-gray-300 transition-colors font-medium text-sm">
                No
            </button>
        </div>
    </div>
</div>

<script>
// Global Delete Confirmation Modal Handler
class DeleteConfirmationModal {
    constructor() {
        this.modal = document.getElementById('delete-confirmation-modal');
        this.messageElement = document.getElementById('delete-modal-message');
        this.confirmBtn = document.getElementById('delete-confirm-btn');
        this.cancelBtn = document.getElementById('delete-cancel-btn');
        this.currentCallback = null;
        
        this.init();
    }
    
    init() {
        // Event listeners
        this.confirmBtn.addEventListener('click', () => this.confirm());
        this.cancelBtn.addEventListener('click', () => this.cancel());
        
        // Close on backdrop click
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.cancel();
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                this.cancel();
            }
        });
    }
    
    show(message, onConfirm) {
        this.messageElement.textContent = message;
        this.currentCallback = onConfirm;
        this.modal.classList.remove('hidden');
        
        // Focus the cancel button for accessibility
        this.cancelBtn.focus();
    }
    
    confirm() {
        if (this.currentCallback) {
            this.currentCallback();
        }
        this.hide();
    }
    
    cancel() {
        this.hide();
    }
    
    hide() {
        this.modal.classList.add('hidden');
        this.currentCallback = null;
    }
}

// Initialize the modal when DOM is loaded
let deleteModal;
document.addEventListener('DOMContentLoaded', function() {
    deleteModal = new DeleteConfirmationModal();
});

// Global function to show delete confirmation
function showDeleteConfirmation(message, onConfirm) {
    if (deleteModal) {
        deleteModal.show(message, onConfirm);
    } else {
        // Fallback to browser confirm if modal not initialized
        if (confirm(message)) {
            onConfirm();
        }
    }
}
</script>
