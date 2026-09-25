@auth
@if(auth()->user()->role === 'customer')
{{-- Chat chỉ dành cho khách hàng đã đăng nhập --}}
<div x-data="arenaChatWidget()"
     @open-owner-chat.window="openVenueChat($event.detail)"
     class="arena-chat-fab fixed bottom-6 right-6 z-50 transition-all duration-300">

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
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-accent-olive"></span>
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
                    <h4 class="font-bold text-sm leading-tight"
                        x-text="mode === 'owner' ? (venueName || 'Chủ sân') : 'Hỗ trợ Arena Sports'"></h4>
                    <p class="text-[11px] text-white/80 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-olive inline-block animate-pulse"></span>
                        <span x-text="mode === 'owner' ? 'Hỏi trực tiếp chủ sân' : 'Trực tuyến · Realtime chat'"></span>
                    </p>
                </div>
            </div>

            <button @click="closeChat()" aria-label="Đóng" class="p-1.5 rounded-lg hover:bg-white/15 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div x-ref="messageBox" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-zinc-50 dark:bg-zinc-900">
            <template x-if="loading">
                <div class="flex justify-center py-8">
                    <svg class="animate-spin w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </template>

            <template x-for="msg in messages" :key="msg.id">
                <div class="flex" :class="msg.is_me ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%]">
                        <div x-show="!msg.is_me && mode === 'owner'"
                             class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1 ml-1"
                             x-text="msg.sender_name"></div>
                        <div class="rounded-2xl px-3.5 py-2 text-sm shadow-sm"
                             :class="msg.is_me
                                ? 'bg-primary-600 text-white rounded-br-sm'
                                : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 rounded-bl-sm'">
                            <div class="whitespace-pre-wrap break-words" x-text="msg.message"></div>
                            <div class="text-[10px] mt-1 opacity-60" x-text="msg.created_at_time"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Composer --}}
        <form @submit.prevent="sendMessage()" class="shrink-0 p-3 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex items-end gap-2">
            <textarea x-model="newMessage" rows="1"
                      @keydown.enter.prevent="sendMessage()"
                      placeholder="Nhập tin nhắn..."
                      class="flex-1 resize-none px-3 py-2 text-sm rounded-xl border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            <button type="submit" :disabled="sending || !newMessage.trim()"
                    class="shrink-0 p-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    function arenaChatWidget() {
        return {
            open: false,
            loading: false,
            sending: false,
            // 'support' = hỗ trợ admin, 'owner' = hỏi chủ sân
            mode: 'support',
            // Khu sân đang hỏi chủ sân (định danh bằng slug — Venue bind theo slug)
            venueSlug: null,
            venueName: '',
            conversationId: null,
            messages: [],
            newMessage: '',
            unreadCount: 0,
            channel: null,
            pollTimer: null,
            csrfToken: '{{ csrf_token() }}',

            toggleChat() {
                if (this.open) {
                    this.closeChat();
                    return;
                }
                this.mode = 'support';
                this.venueSlug = null;
                this.venueName = '';
                this.openPanel();
            },

            // Mở hội thoại với chủ sân (gọi từ nút ở trang khu sân)
            openVenueChat(detail) {
                this.mode = 'owner';
                this.venueSlug = detail && detail.slug ? detail.slug : null;
                this.venueName = detail && detail.name ? detail.name : '';
                this.openPanel();
            },

            openPanel() {
                this.open = true;
                this.unreadCount = 0;
                this.loadConversation();
            },

            closeChat() {
                this.open = false;
                this.leaveChannel();
            },

            loadConversation() {
                this.loading = true;
                this.messages = [];
                this.leaveChannel();

                // Chưa biết hỏi chủ sân nào thì không gọi API
                if (this.mode === 'owner' && !this.venueSlug) {
                    this.loading = false;
                    this.showError('Không xác định được khu sân. Vui lòng tải lại trang.');
                    return;
                }

                const url = this.mode === 'owner'
                    ? '{{ route('customer.chat.venue', ['venue' => '__ID__']) }}'.replace('__ID__', this.venueSlug)
                    : '{{ route('customer.chat.initiate') }}';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;

                    // Không mở được hội thoại thì báo lỗi, KHÔNG subscribe kênh rỗng
                    if (!data || !data.conversation_id) {
                        this.showError('Không mở được hội thoại. Vui lòng thử lại.');
                        return;
                    }

                    this.conversationId = data.conversation_id;
                    this.messages = Array.isArray(data.messages) ? data.messages : [];
                    this.listenToChannel(this.conversationId);
                    this.$nextTick(() => this.scrollToBottom());
                })
                .catch(() => {
                    this.loading = false;
                    this.showError('Không kết nối được tới hệ thống chat. Vui lòng thử lại.');
                });
            },

            showError(text) {
                this.messages = [{
                    id: 'err-' + Date.now(),
                    message: text,
                    is_me: false,
                    sender_name: 'Hệ thống',
                    created_at_time: '',
                }];
            },

            listenToChannel(conversationId) {
                if (!conversationId) return;

                if (!window.Echo) {
                    // Không có WebSocket (Reverb chưa chạy) → hỏi lại định kỳ
                    this.startPolling();
                    return;
                }

                this.channel = window.Echo.private('chat.conversation.' + conversationId);
                this.channel.listen('.message.sent', (e) => this.receiveMessage(e));
            },

            leaveChannel() {
                if (this.channel && window.Echo) {
                    window.Echo.leave('chat.conversation.' + this.conversationId);
                }
                this.channel = null;

                if (this.pollTimer) {
                    clearInterval(this.pollTimer);
                    this.pollTimer = null;
                }
            },

            startPolling() {
                if (this.pollTimer || !this.conversationId) return;
                this.pollTimer = setInterval(() => {
                    if (!this.open || !this.conversationId) return;
                    fetch('{{ route('customer.chat.messages', ['conversation' => '__ID__']) }}'.replace('__ID__', this.conversationId), {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (!data || !Array.isArray(data.messages)) return;
                        this.mergeMessages(data.messages);
                    })
                    .catch(() => {});
                }, 8000);
            },

            // Gộp tin mới theo id, tránh trùng
            mergeMessages(incoming) {
                const known = new Set(this.messages.map(m => m.id));
                let added = false;
                incoming.forEach(m => {
                    if (!known.has(m.id)) {
                        this.messages.push(m);
                        added = true;
                    }
                });
                if (added) this.$nextTick(() => this.scrollToBottom());
            },

            receiveMessage(e) {
                if (!e || e.sender_type === 'customer') return;

                this.messages.push({
                    id: e.id,
                    sender_type: e.sender_type,
                    sender_name: e.sender_name,
                    message: e.message,
                    is_me: false,
                    created_at_time: e.created_at_time,
                });

                if (!this.open) {
                    this.unreadCount++;
                } else {
                    this.$nextTick(() => this.scrollToBottom());
                }
            },

            sendMessage() {
                const text = this.newMessage.trim();
                if (!text || this.sending || !this.conversationId) return;

                this.sending = true;
                this.newMessage = '';

                const tempId = 'tmp-' + Date.now();
                this.messages.push({
                    id: tempId,
                    message: text,
                    is_me: true,
                    sender_name: 'Bạn',
                    created_at_time: '',
                });
                this.$nextTick(() => this.scrollToBottom());

                fetch('{{ route('customer.chat.send', ['conversation' => '__ID__']) }}'.replace('__ID__', this.conversationId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(res => res.json())
                .then(data => {
                    this.sending = false;
                    // Gắn id thật cho tin vừa gửi
                    const idx = this.messages.findIndex(m => m.id === tempId);
                    if (idx !== -1) this.messages[idx].id = data.id;
                })
                .catch(() => {
                    this.sending = false;
                });
            },

            scrollToBottom() {
                const box = this.$refs.messageBox;
                if (box) box.scrollTop = box.scrollHeight;
            },
        }
    }
</script>
@endif
@endauth
