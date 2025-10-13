    <!-- MigsBot Widget -->
    <style>
        #migsbot-button { position: fixed; bottom: 24px; right: 24px; z-index: 60; }
        /* Dock panel to header right */
        #migsbot-panel { position: fixed; top: 72px; right: 24px; width: 360px; height: 520px; max-height: calc(100vh - 96px); z-index: 60; }
        @media (max-width: 1024px) { #migsbot-panel { right: 16px; width: 340px; } }
        @media (max-width: 480px) { #migsbot-panel { top: 64px; right: 12px; width: calc(100% - 24px); height: 70vh; } }
    </style>
    <!-- Floating button hidden when using header trigger -->
    <div id="migsbot-button" class="hidden">
        <button id="open-migsbot" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 shadow-lg flex items-center justify-center">
            <i class="fas fa-robot text-xl"></i>
        </button>
    </div>
    <div id="migsbot-panel" class="hidden">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col h-full">
            <div class="bg-blue-600 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-robot"></i>
                    <span class="font-semibold">MigsBot</span>
                </div>
                <button id="close-migsbot" class="text-white opacity-80 hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="migsbot-messages" class="flex-1 p-4 space-y-3 overflow-y-auto bg-gray-50">
                <div class="text-xs text-gray-500 text-center">Ask about products, shipping, returns, or type a keyword like "barong".</div>
            </div>
            <form id="migsbot-form" class="p-3 border-t border-gray-200 flex items-center space-x-2">
                <input id="migsbot-input" class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none" placeholder="Type a message..." />
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-sm">Send</button>
            </form>
        </div>
    </div>
    <script>
        (function(){
            if (document.getElementById('open-migsbot')) {
                // already injected
            }
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
                if(show) input && input.focus();
            }
            openBtn && openBtn.addEventListener('click', ()=> togglePanel(true));
            headerBtn && headerBtn.addEventListener('click', (e)=> { e.preventDefault(); togglePanel(true); });
            closeBtn && closeBtn.addEventListener('click', ()=> togglePanel(false));
            
            function appendMessage(text, role='bot'){
                const wrap = document.createElement('div');
                wrap.className = 'flex';
                const bubble = document.createElement('div');
                bubble.className = role === 'user' ? 'ml-auto bg-blue-600 text-white px-3 py-2 rounded-2xl text-sm max-w-[80%]' : 'bg-white border px-3 py-2 rounded-2xl text-sm max-w-[80%]';
                bubble.textContent = text;
                wrap.appendChild(bubble);
                messages.appendChild(wrap);
                messages.scrollTop = messages.scrollHeight;
            }
            
            form && form.addEventListener('submit', function(e){
                e.preventDefault();
                const text = (input.value || '').trim();
                if(!text) return;
                appendMessage(text, 'user');
                input.value = '';
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
                    if(res && res.success){
                        appendMessage(res.data.reply || '');
                        if(res.data.products && res.data.products.length){
                            const list = res.data.products.map(p=>`• ${p.name} – ₱${Number(p.price).toFixed(2)} (${p.url})`).join('\n');
                            appendMessage(list);
                        }
                    } else {
                        appendMessage('Sorry, I could not process that.');
                    }
                })
                .catch(()=> appendMessage('Network error, please try again.'))
            });
        })();
    </script>

