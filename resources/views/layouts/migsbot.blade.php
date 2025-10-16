    <!-- MigsBot Widget -->
    <style>
        #migsbot-button { 
            position: fixed; 
            bottom: 24px; 
            right: 24px; 
            z-index: 1000; 
            transition: all 0.3s ease;
        }
        
        #migsbot-panel { 
            position: fixed; 
            top: 80px; 
            right: 24px; 
            width: 380px; 
            height: 600px; 
            max-height: calc(100vh - 120px); 
            z-index: 1000;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            overflow: hidden;
        }
        
        @media (max-width: 1024px) { 
            #migsbot-panel { 
                right: 16px; 
                width: 360px; 
                height: 580px;
            } 
        }
        
        @media (max-width: 768px) { 
            #migsbot-panel { 
                top: 70px; 
                right: 12px; 
                width: calc(100% - 24px); 
                height: 70vh; 
                max-height: calc(100vh - 100px);
            } 
        }
        
        @media (max-width: 480px) { 
            #migsbot-panel { 
                top: 60px; 
                right: 8px; 
                width: calc(100% - 16px); 
                height: 65vh;
            } 
        }
        
        .migsbot-message {
            animation: fadeInUp 0.3s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .migsbot-typing {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #6B7280;
            animation: typing 1.4s infinite ease-in-out;
        }
        
        .migsbot-typing:nth-child(1) { animation-delay: -0.32s; }
        .migsbot-typing:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typing {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
    
    <!-- Floating button hidden when using header trigger -->
    <div id="migsbot-button" class="hidden">
        <button id="open-migsbot" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full w-16 h-16 shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <i class="fas fa-robot text-2xl"></i>
        </button>
    </div>
    
    <div id="migsbot-panel" class="hidden">
        <div class="bg-white flex flex-col h-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">MigsBot</h3>
                        <p class="text-xs text-blue-100">AI Assistant</p>
                    </div>
                </div>
                <button id="close-migsbot" class="text-white hover:text-blue-200 transition-colors duration-200 p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Messages Area -->
            <div id="migsbot-messages" class="flex-1 p-6 space-y-4 overflow-y-auto bg-gray-50">
                <!-- Welcome Message -->
                <div class="flex items-start space-x-3 migsbot-message">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                    <div class="bg-white p-4 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%]">
                        <p class="text-gray-800 text-sm leading-relaxed">
                            👋 Hi! I'm <strong>MigsBot</strong>, your AI assistant for 3Migs Barong. 
                        </p>
                        <p class="text-gray-600 text-xs mt-2">
                            I can help you find barong and gowns, check order info, or answer questions about shipping, returns, and more.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Find barong</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Shipping info</span>
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">Returns</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <form id="migsbot-form" class="p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center space-x-3">
                    <div class="flex-1 relative">
                        <input id="migsbot-input" 
                               class="w-full border border-gray-300 rounded-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="Type your message..." 
                               autocomplete="off" />
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-paper-plane text-gray-400 text-sm"></i>
                        </div>
                    </div>
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full w-12 h-12 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function(){
            // Prevent duplicate initialization
            if (window.migsbotInitialized) return;
            window.migsbotInitialized = true;
            
            const openBtn = document.getElementById('open-migsbot');
            const headerBtn = document.querySelector('[data-migsbot-trigger]') || document.getElementById('bot-btn');
            const closeBtn = document.getElementById('close-migsbot');
            const panel = document.getElementById('migsbot-panel');
            const form = document.getElementById('migsbot-form');
            const input = document.getElementById('migsbot-input');
            const messages = document.getElementById('migsbot-messages');
            
            function togglePanel(show){
                if(!panel) return;
                panel.classList[show ? 'remove' : 'add']('hidden');
                if(show) {
                    input && input.focus();
                    // Add entrance animation
                    panel.style.transform = 'translateY(-10px)';
                    panel.style.opacity = '0';
                    setTimeout(() => {
                        panel.style.transform = 'translateY(0)';
                        panel.style.opacity = '1';
                    }, 10);
                }
            }
            
            // Event listeners
            openBtn && openBtn.addEventListener('click', ()=> togglePanel(true));
            headerBtn && headerBtn.addEventListener('click', (e)=> { 
                e.preventDefault(); 
                togglePanel(true); 
            });
            closeBtn && closeBtn.addEventListener('click', ()=> togglePanel(false));
            
            // Enhanced message display
            function appendMessage(text, role='bot', isTyping=false){
                const wrap = document.createElement('div');
                wrap.className = 'flex items-start space-x-3 migsbot-message';
                
                if (role === 'user') {
                    wrap.className = 'flex items-start space-x-3 justify-end migsbot-message';
                    const bubble = document.createElement('div');
                    bubble.className = 'bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%]';
                    bubble.textContent = text;
                    wrap.appendChild(bubble);
                } else {
                    const avatar = document.createElement('div');
                    avatar.className = 'flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center';
                    avatar.innerHTML = '<i class="fas fa-robot text-white text-sm"></i>';
                    wrap.appendChild(avatar);
                    
                    const bubble = document.createElement('div');
                    bubble.className = 'bg-white p-4 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%]';
                    
                    if (isTyping) {
                        bubble.innerHTML = `
                            <div class="flex items-center space-x-1">
                                <span class="migsbot-typing"></span>
                                <span class="migsbot-typing"></span>
                                <span class="migsbot-typing"></span>
                            </div>
                        `;
                    } else {
                        bubble.innerHTML = `<p class="text-gray-800 text-sm leading-relaxed">${text}</p>`;
                    }
                    wrap.appendChild(bubble);
                }
                
                messages.appendChild(wrap);
                messages.scrollTop = messages.scrollHeight;
            }
            
            // Enhanced form handling
            form && form.addEventListener('submit', function(e){
                e.preventDefault();
                const text = (input.value || '').trim();
                if(!text) return;
                
                // Disable input while processing
                input.disabled = true;
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
                
                // Add user message
                appendMessage(text, 'user');
                input.value = '';
                
                // Show typing indicator
                const typingId = 'typing-' + Date.now();
                appendMessage('', 'bot', true);
                
                fetch('/api/v1/migsbot/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(r=>r.json())
                .then(res=>{
                    // Remove typing indicator
                    const typingElements = messages.querySelectorAll('.migsbot-typing');
                    typingElements.forEach(el => el.parentElement.parentElement.remove());
                    
                    if(res && res.success){
                        appendMessage(res.data.reply || 'I apologize, but I\'m having trouble processing your request. Please try again or contact our store directly.');
                        
                        // Handle product suggestions
                        if(res.data.products && res.data.products.length){
                            const productList = res.data.products.map(p => 
                                `<div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="font-medium text-blue-900">${p.name}</div>
                                    <div class="text-sm text-blue-700">₱${Number(p.price).toFixed(2)}</div>
                                    <a href="${p.url}" class="text-xs text-blue-600 hover:text-blue-800 underline">View Product</a>
                                </div>`
                            ).join('');
                            
                            const productWrap = document.createElement('div');
                            productWrap.className = 'flex items-start space-x-3 migsbot-message';
                            productWrap.innerHTML = `
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-robot text-white text-sm"></i>
                                </div>
                                <div class="bg-white p-4 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%]">
                                    <p class="text-gray-800 text-sm font-medium mb-2">Here are some products you might like:</p>
                                    ${productList}
                                </div>
                            `;
                            messages.appendChild(productWrap);
                            messages.scrollTop = messages.scrollHeight;
                        }
                    } else {
                        appendMessage('I apologize, but I\'m having trouble processing your request. Please try again or contact our store directly.');
                    }
                })
                .catch(()=> {
                    // Remove typing indicator
                    const typingElements = messages.querySelectorAll('.migsbot-typing');
                    typingElements.forEach(el => el.parentElement.parentElement.remove());
                    appendMessage('Network error, please try again.');
                })
                .finally(() => {
                    // Re-enable input
                    input.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    input.focus();
                });
            });
            
            // Auto-resize input
            input && input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });
            
            // Close panel when clicking outside
            document.addEventListener('click', function(e) {
                if (panel && !panel.classList.contains('hidden') && 
                    !panel.contains(e.target) && 
                    !headerBtn?.contains(e.target) && 
                    !openBtn?.contains(e.target)) {
                    togglePanel(false);
                }
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && panel && !panel.classList.contains('hidden')) {
                    togglePanel(false);
                }
            });
        })();
    </script>

