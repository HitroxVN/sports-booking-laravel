<div x-data="{
    open: false,
    initialized: false,
    loading: false,
    sending: false,
    sessionToken: localStorage.getItem('arena_chat_token') || '',
    conversationId: localStorage.getItem('arena_chat_conv_id') || null,
    customerName: '{{ Auth::user()->name ?? '' }}',
    customerPhone: '{{ Auth::user()->phone ?? '' }}',
    messages: [],
    newMessage: '',
    unreadCount: 0,
    channel: null,

    init() {
        if (this.sessionToken && this.conversationId) {
            if ('{{ Auth::check() ? 'yes' : 'no' }}' === 'yes') {
                this.listenToChannel(this.conversationId);
            } else {
                // Guest không auth được private channel — polling thay thế
                this.startPolling();
            }
        }
    },

    toggleChat() {
        this.open = !this.open;
        if (this.open) {
            this.unreadCount = 0;
            if (!this.initialized) {
                this.initiateChat();
            } else {
                this.$nextTick(() => this.scrollToBottom());
            }
        }
    },

    initiateChat() {
        this.loading = true;
        fetch('{{ route('chat.initiate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                session_token: this.sessionToken,
                customer_name: this.customerName || 'Khách hàng',
                customer_phone: this.customerPhone
            })
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            this.initialized = true;
            this.sessionToken = data.session_token;
            this.conversationId = data.conversation_id;
            localStorage.setItem('arena_chat_token', data.session_token);
            localStorage.setItem('arena_chat_conv_id', data.conversation_id);
            this.messages = data.messages || [];

            @if (Auth::check())
            this.listenToChannel(data.conversation_id);
            @else
            this.startPolling();
            @endif

            this.listenToChannel(data.conversation_id);
            this.$nextTick(() => this.scrollToBottom());
        })
        .catch(() => {
            this.loading = false;
        });
    },

    listenToChannel(convId) {
        if (!convId || !window.Echo) return;

        // Tránh subscribe trùng lặp
        if (this.channel) {
            window.Echo.leave('chat.conversation.' + convId);
        }

        this.channel = window.Echo.private('chat.conversation.' + convId)
            .listen('.message.sent', (e) => {
                // Nếu tin nhắn từ admin gửi tới khách
                if (e.sender_type === 'admin') {
                    this.messages.push({
                        id: e.id,
                        sender_type: e.sender_type,
                        sender_name: e.sender_name || 'Hỗ trợ viên',
                        message: e.message,
                        is_me: false,
                        created_at_time: e.created_at_time || 'Vừa xong'
                    });

                    if (!this.open) {
                        this.unreadCount++;
                    }

                    this.$nextTick(() => this.scrollToBottom());
                }
            });
    },

    sendMessage() {
        const text = this.newMessage.trim();
        if (!text || this.sending || !this.conversationId) return;

        this.sending = true;
        this.newMessage = '';

        // Tạm thời hiển thị optimistic
        const tempId = 'temp_' + Date.now();
        const nowTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

        this.messages.push({
            id: tempId,
            sender_type: 'customer',
            sender_name: this.customerName || 'Bạn',
            message: text,
            is_me: true,
            created_at_time: nowTime
        });
        this.$nextTick(() => this.scrollToBottom());

        fetch(`/chat/${this.conversationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Chat-Session': this.sessionToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: text,
                session_token: this.sessionToken
            })
        })
        .then(res => res.json())
        .then(data => {
            this.sending = false;
            // Thay thế temp message bằng ID chính thức
            const idx = this.messages.findIndex(m => m.id === tempId);
            if (idx !== -1) {
                this.messages[idx].id = data.id;
            }
            this.$nextTick(() => this.scrollToBottom());
        })
        .catch(() => {
            this.sending = false;
        });
    },

    startPolling() {
        if (this.pollTimer || !this.conversationId) return;
        this.pollTimer = setInterval(() => {
            if (!this.open) return; // tab đóng thì bỏ qua, đỡ request
            fetch(`/chat/${this.conversationId}/messages`, {
                headers: {
                    'X-Chat-Session': this.sessionToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.ok ? res.json() : null)
            .then(data => {
                if (!data || !Array.isArray(data.messages)) return;
                // Chỉ append tin nhắn mới (so theo id)
                const knownIds = new Set(this.messages.map(m => m.id));
                for (const m of data.messages) {
                    if (!knownIds.has(m.id)) {
                        this.messages.push({
                            id: m.id,
                            sender_type: m.sender_type,
                            sender_name: m.sender_name || 'Hỗ trợ viên',
                            message: m.message,
                            is_me: m.sender_type === 'customer',
                            created_at_time: m.created_at_time || 'Vừa xong'
                        });
                        if (m.sender_type === 'admin' && !this.open) {
                            this.unreadCount++;
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    }
                }
            })
            .catch(() => {});
        }, 5000);
    },

    scrollToBottom() {
        const box = this.$refs.messagesBox;
        if (box) {
            box.scrollTop = box.scrollHeight;
        }
    }
}" class="arena-chat-fab fixed bottom-6 right-6 z-50 transition-all duration-300">

    {{-- Floating Toggle Button --}}
    <button @click="toggleChat()"
            aria-label="Hỗ trợ trực tuyến"
            class="relative flex items-center justify-center w-14 h-14 rounded-full bg-primary-600 hover:bg-primary-700 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-primary-500/40">
        
        {{-- Chat Icon when closed --}}
        <svg x-show="!open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        {{-- Close Icon when open --}}
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>

        {{-- Unread Badge Counter --}}
        <span x-show="unreadCount > 0 && !open"
              x-text="unreadCount"
              style="display: none;"
              class="absolute -top-1 -right-1 flex items-center justify-center min-w-5 h-5 px-1.5 text-[11px] font-bold text-white bg-red-500 border-2 border-white dark:border-zinc-900 rounded-full animate-bounce">
        </span>

        {{-- Online Pulse Ping --}}
        <span class="absolute top-0 right-0 -mt-0.5 -mr-0.5 flex h-3.5 w-3.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent-olive opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-accent-moss border-2 border-white dark:border-zinc-900"></span>
        </span>
    </button>

    {{-- Chat Window Modal --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         style="display: none;"
         class="absolute bottom-16 right-0 w-[92vw] sm:w-96 h-[540px] max-h-[80vh] flex flex-col bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white flex items-center justify-between shadow-sm shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center text-white font-bold text-sm">
                        AS
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-accent-olive border border-white"></span>
                </div>
                <div>
                    <h4 class="font-bold text-sm leading-tight">Hỗ trợ Arena Sports</h4>
                    <p class="text-[11px] text-white/80 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-olive inline-block animate-pulse"></span>
                        Trực tuyến &middot; Realtime chat
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <button @click="open = false" class="p-1 rounded-lg hover:bg-white/10 text-white/90 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body: Messages stream --}}
        <div x-ref="messagesBox" class="flex-1 p-4 overflow-y-auto space-y-3 bg-zinc-50/50 dark:bg-zinc-950/40 text-xs">
            
            {{-- Loading skeleton --}}
            <template x-if="loading">
                <div class="py-16 flex flex-col items-center justify-center text-zinc-400 gap-2">
                    <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs">Đang kết nối hỗ trợ...</span>
                </div>
            </template>

            {{-- Welcome note if messages loaded --}}
            <template x-if="!loading && messages.length > 0">
                <div class="text-center my-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-medium bg-zinc-200/80 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                        Phiên hỗ trợ trực tuyến
                    </span>
                </div>
            </template>

            {{-- Message Bubbles --}}
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex flex-col" :class="msg.is_me ? 'items-end' : 'items-start'">
                    {{-- Sender name --}}
                    <span class="text-[10px] text-zinc-400 mb-0.5 px-1" x-text="msg.is_me ? 'Bạn' : (msg.sender_name || 'Hỗ trợ viên')"></span>
                    
                    {{-- Bubble --}}
                    <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-xs leading-relaxed break-words shadow-sm"
                         :class="msg.is_me
                             ? 'bg-primary-600 text-white rounded-tr-xs'
                             : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700/80 rounded-tl-xs'">
                        <p x-text="msg.message" class="whitespace-pre-line"></p>
                    </div>

                    {{-- Timestamp --}}
                    <span class="text-[10px] text-zinc-400 mt-0.5 px-1" x-text="msg.created_at_time"></span>
                </div>
            </template>
        </div>

        {{-- Footer: Input box --}}
        <div class="p-3 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text"
                       x-model="newMessage"
                       placeholder="Nhập tin nhắn..."
                       :disabled="loading"
                       class="flex-1 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3.5 py-2.5 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white dark:focus:bg-zinc-800 transition-all">
                
                <button type="submit"
                        :disabled="!newMessage.trim() || sending || loading"
                        :class="(!newMessage.trim() || sending || loading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700'"
                        class="p-2.5 rounded-xl bg-primary-600 text-white shadow transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <div class="mt-1.5 flex items-center justify-between text-[10px] text-zinc-400 px-1">
                <span>Nhấn Enter để gửi</span>
                <span class="text-accent-moss font-medium">&bull; Realtime WebSockets</span>
            </div>
        </div>
    </div>
</div>
