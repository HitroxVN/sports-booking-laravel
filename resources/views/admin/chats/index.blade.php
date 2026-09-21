<x-admin-layout :title="'Livechat Hỗ trợ khách hàng'">

    <script>
        function adminChat() {
            return {
                conversations: @json($conversations->items()),
                activeConversationId: null,
                activeConversation: null,
                activeMessages: [],
                replyText: '',
                loadingMessages: false,
                sendingReply: false,
                filterStatus: 'all',
                searchTerm: '',
                activeChannel: null,
                csrfToken: '{{ csrf_token() }}',

                init() {
                    // Lắng nghe kênh chat.admin để nhận thông báo realtime khi có bất kỳ tin nhắn mới nào từ khách
                    if (window.Echo) {
                        window.Echo.private('chat.admin')
                            .listen('.message.sent', (e) => {
                                this.handleAdminBroadcast(e);
                            });
                    }

                    // Tự động chọn hội thoại đầu tiên nếu có
                    if (this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0].id);
                    }
                },

                handleAdminBroadcast(e) {
                    // 1. Cập nhật danh sách hội thoại bên trái
                    const idx = this.conversations.findIndex(c => c.id === e.conversation_id);
                    if (idx !== -1) {
                        const conv = this.conversations[idx];
                        conv.last_message_at = e.created_at;
                        conv.latest_message = {
                            message: e.message,
                            sender_type: e.sender_type,
                            created_at: e.created_at
                        };

                        if (this.activeConversationId !== e.conversation_id && e.sender_type === 'customer') {
                            conv.unread_customer_count = (conv.unread_customer_count || 0) + 1;
                        }

                        // Đưa hội thoại lên đầu danh sách
                        this.conversations.splice(idx, 1);
                        this.conversations.unshift(conv);
                    } else {
                        // Tải lại danh sách nếu là hội thoại mới
                        this.fetchConversations();
                    }

                    // 2. Nếu đang mở cuộc trò chuyện này và tin nhắn từ khách, thêm vào khung chat ngay
                    if (this.activeConversationId === e.conversation_id && e.sender_type === 'customer') {
                        if (!this.activeMessages.some(m => m.id === e.id)) {
                            this.activeMessages.push({
                                id: e.id,
                                conversation_id: e.conversation_id,
                                sender_type: e.sender_type,
                                sender_name: e.sender_name || 'Khách hàng',
                                message: e.message,
                                is_admin: false,
                                created_at: e.created_at,
                                created_at_time: e.created_at_time || 'Vừa xong'
                            });
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }
                },

                selectConversation(id) {
                    this.activeConversationId = id;
                    this.loadingMessages = true;
                    this.replyText = '';

                    // Reset badge unread ở danh sách bên trái
                    const target = this.conversations.find(c => c.id === id);
                    if (target) {
                        target.unread_customer_count = 0;
                    }

                    // Chuyển kênh Echo lắng nghe cuộc trò chuyện cụ thể
                    if (window.Echo) {
                        if (this.activeChannel && this.activeChannel !== id) {
                            window.Echo.leave('chat.conversation.' + this.activeChannel);
                        }
                        this.activeChannel = id;
                        window.Echo.private('chat.conversation.' + id)
                            .listen('.message.sent', (e) => {
                                if (e.sender_type === 'customer' && this.activeConversationId === id) {
                                    if (!this.activeMessages.some(m => m.id === e.id)) {
                                        this.activeMessages.push({
                                            id: e.id,
                                            conversation_id: e.conversation_id,
                                            sender_type: e.sender_type,
                                            sender_name: e.sender_name || 'Khách hàng',
                                            message: e.message,
                                            is_admin: false,
                                            created_at: e.created_at,
                                            created_at_time: e.created_at_time || 'Vừa xong'
                                        });
                                        this.$nextTick(() => this.scrollToBottom());
                                    }
                                }
                            });
                    }

                    fetch('/admin/chats/' + id)
                        .then(res => res.json())
                        .then(data => {
                            this.activeConversation = data;
                            this.activeMessages = data.messages || [];
                            this.loadingMessages = false;
                            this.$nextTick(() => {
                                this.scrollToBottom();
                                this.$refs.replyInput?.focus();
                            });
                        })
                        .catch(() => {
                            this.loadingMessages = false;
                        });
                },

                sendReply() {
                    const text = this.replyText.trim();
                    if (!text || this.sendingReply || !this.activeConversationId) return;

                    this.sendingReply = true;
                    this.replyText = '';

                    fetch(`/admin/chats/${this.activeConversationId}/reply`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(res => res.json())
                    .then(newMsg => {
                        this.sendingReply = false;
                        this.activeMessages.push(newMsg);

                        // Cập nhật lại snippet tin nhắn ở sidebar trái
                        const conv = this.conversations.find(c => c.id === this.activeConversationId);
                        if (conv) {
                            conv.latest_message = { message: newMsg.message, sender_type: 'admin' };
                        }

                        this.$nextTick(() => {
                            this.scrollToBottom();
                            this.$refs.replyInput?.focus();
                        });
                    })
                    .catch(() => {
                        this.sendingReply = false;
                    });
                },

                insertTemplate(text) {
                    this.replyText = text;
                    this.$refs.replyInput?.focus();
                },

                toggleStatus() {
                    if (!this.activeConversationId) return;

                    fetch(`/admin/chats/${this.activeConversationId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (this.activeConversation) {
                            this.activeConversation.status = data.status;
                        }
                        const conv = this.conversations.find(c => c.id === this.activeConversationId);
                        if (conv) {
                            conv.status = data.status;
                        }
                    });
                },

                scrollToBottom() {
                    const box = this.$refs.adminMessagesBox;
                    if (box) {
                        box.scrollTop = box.scrollHeight;
                    }
                },

                filteredConversations() {
                    return this.conversations.filter(c => {
                        const matchStatus = this.filterStatus === 'all' || c.status === this.filterStatus;
                        const matchSearch = !this.searchTerm ||
                            (c.customer_name && c.customer_name.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                            (c.customer_phone && c.customer_phone.includes(this.searchTerm)) ||
                            (c.customer_email && c.customer_email.toLowerCase().includes(this.searchTerm.toLowerCase()));
                        return matchStatus && matchSearch;
                    });
                },

                fetchConversations() {
                    fetch('{{ route('admin.chats.index') }}', {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.conversations) {
                            this.conversations = data.conversations;
                        }
                    });
                }
            };
        }
    </script>

    <div x-data="adminChat()" class="h-[calc(100vh-6rem)] flex flex-col">

        {{-- Top Bar Header --}}
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                    <span>Livechat Hỗ trợ Trực tuyến</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        WebSockets Active
                    </span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Trực tiếp trao đổi, giải đáp thắc mắc và hỗ trợ đặt sân thời gian thực với khách hàng
                </p>
            </div>
        </div>

        {{-- 2-Pane Chat Container --}}
        <div class="flex-1 flex flex-col md:flex-row bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden min-h-0">
            
            {{-- Left Column: Conversations List (w-80 or w-96) --}}
            <div class="w-full md:w-80 lg:w-96 border-r border-zinc-200 dark:border-zinc-800 flex flex-col shrink-0 bg-zinc-50/50 dark:bg-zinc-900/50">
                
                {{-- Search & Filters --}}
                <div class="p-3.5 border-b border-zinc-200 dark:border-zinc-800 space-y-2.5 shrink-0">
                    <div class="relative">
                        <input type="text"
                               x-model="searchTerm"
                               placeholder="Tìm khách hàng, SĐT..."
                               class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 pl-8 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    {{-- Tabs: Tất cả, Đang mở, Đã đóng --}}
                    <div class="flex p-1 rounded-lg bg-zinc-200/70 dark:bg-zinc-800 text-[11px] font-semibold">
                        <button @click="filterStatus = 'all'"
                                :class="filterStatus === 'all' ? 'bg-white dark:bg-zinc-900 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="flex-1 py-1 rounded-md transition-all">
                            Tất cả
                        </button>
                        <button @click="filterStatus = 'open'"
                                :class="filterStatus === 'open' ? 'bg-white dark:bg-zinc-900 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="flex-1 py-1 rounded-md transition-all">
                            Đang mở
                        </button>
                        <button @click="filterStatus = 'closed'"
                                :class="filterStatus === 'closed' ? 'bg-white dark:bg-zinc-900 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="flex-1 py-1 rounded-md transition-all">
                            Đã đóng
                        </button>
                    </div>
                </div>

                {{-- Conversations Stream --}}
                <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    <template x-for="conv in filteredConversations()" :key="conv.id">
                        <div @click="selectConversation(conv.id)"
                             :class="activeConversationId === conv.id
                                 ? 'bg-primary-50/70 dark:bg-primary-950/40 border-l-4 border-primary-600'
                                 : 'hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 border-l-4 border-transparent'"
                             class="p-3.5 cursor-pointer transition-colors flex items-start gap-3">
                            
                            {{-- Avatar --}}
                            <div class="relative shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-gradient-to-br from-primary-500 to-indigo-600 text-white shadow-sm">
                                    <span x-text="(conv.customer_name || 'K').charAt(0).toUpperCase()"></span>
                                </div>
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border border-white dark:border-zinc-900"
                                      :class="conv.status === 'open' ? 'bg-emerald-500' : 'bg-zinc-400'"></span>
                            </div>

                            {{-- Preview Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <h4 class="font-bold text-xs text-zinc-900 dark:text-zinc-100 truncate"
                                        x-text="conv.customer_name || 'Khách hàng'"></h4>
                                    <span class="text-[10px] text-zinc-400 shrink-0"
                                          x-text="conv.latest_message ? conv.latest_message.created_at.substring(11, 16) : ''"></span>
                                </div>

                                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate leading-relaxed"
                                   x-text="conv.latest_message ? ((conv.latest_message.sender_type === 'admin' ? 'Bạn: ' : '') + conv.latest_message.message) : 'Chưa có tin nhắn'"></p>

                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-[10px] text-zinc-400" x-text="conv.customer_phone || (conv.user ? 'Thành viên' : 'Khách vãng lai')"></span>
                                    
                                    {{-- Unread badge --}}
                                    <span x-show="conv.unread_customer_count > 0"
                                          x-text="conv.unread_customer_count"
                                          style="display: none;"
                                          class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-600 text-white"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredConversations().length === 0" class="p-8 text-center text-zinc-400 text-xs">
                        Không có cuộc trò chuyện nào phù hợp.
                    </div>
                </div>
            </div>

            {{-- Right Column: Active Chat Room --}}
            <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-zinc-900">
                
                {{-- If No Conversation Selected --}}
                <div x-show="!activeConversationId" class="flex-1 flex flex-col items-center justify-center p-8 text-center text-zinc-400">
                    <svg class="w-14 h-14 text-zinc-300 dark:text-zinc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="font-semibold text-sm text-zinc-600 dark:text-zinc-300">Chọn một cuộc trò chuyện để bắt đầu hỗ trợ</p>
                    <p class="text-xs text-zinc-400 mt-1">Tin nhắn của khách hàng sẽ được cập nhật realtime tức thời</p>
                </div>

                {{-- If Active Conversation --}}
                <div x-show="activeConversationId" class="flex-1 flex flex-col min-h-0" style="display: none;">
                    
                    {{-- Active Header Bar --}}
                    <div class="px-5 py-3.5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-4 shrink-0 bg-zinc-50/50 dark:bg-zinc-900/80">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-950 text-primary-700 dark:text-primary-300 flex items-center justify-center font-bold text-sm shrink-0">
                                <span x-text="(activeConversation?.customer_name || 'K').charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-50 truncate"
                                        x-text="activeConversation?.customer_name || 'Khách hàng'"></h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold"
                                          :class="activeConversation?.status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400'"
                                          x-text="activeConversation?.status === 'open' ? 'Đang mở' : 'Đã đóng'"></span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">
                                    <span x-text="'SĐT: ' + (activeConversation?.customer_phone || 'Chưa cung cấp')"></span>
                                    <span>&bull;</span>
                                    <span x-text="'Email: ' + (activeConversation?.customer_email || 'Chưa cung cấp')"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="toggleStatus()"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors"
                                    :class="activeConversation?.status === 'open'
                                        ? 'border-amber-300 text-amber-700 hover:bg-amber-50 dark:border-amber-800 dark:text-amber-400 dark:hover:bg-amber-950/40'
                                        : 'border-emerald-300 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-800 dark:text-emerald-400 dark:hover:bg-emerald-950/40'">
                                <span x-text="activeConversation?.status === 'open' ? 'Đóng hội thoại' : 'Mở lại hội thoại'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Messages Stream Box --}}
                    <div x-ref="adminMessagesBox" class="flex-1 p-5 overflow-y-auto space-y-3 bg-zinc-50/40 dark:bg-zinc-950/40 text-xs">
                        
                        <template x-if="loadingMessages">
                            <div class="py-20 flex justify-center items-center">
                                <svg class="animate-spin h-7 w-7 text-primary-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </template>

                        <template x-for="msg in activeMessages" :key="msg.id">
                            <div class="flex flex-col" :class="msg.is_admin ? 'items-end' : 'items-start'">
                                <div class="flex items-center gap-1.5 mb-1 px-1 text-[10px] text-zinc-400">
                                    <span class="font-semibold" :class="msg.is_admin ? 'text-primary-600 dark:text-primary-400' : 'text-zinc-600 dark:text-zinc-300'"
                                          x-text="msg.sender_name || (msg.is_admin ? 'Admin' : 'Khách')"></span>
                                    <span>&middot;</span>
                                    <span x-text="msg.created_at_time || msg.created_at"></span>
                                </div>

                                <div class="max-w-[70%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed break-words shadow-sm"
                                     :class="msg.is_admin
                                         ? 'bg-primary-600 text-white rounded-tr-xs'
                                         : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 rounded-tl-xs'">
                                    <p x-text="msg.message" class="whitespace-pre-line"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Quick Response Suggestions --}}
                    <div class="px-4 py-2 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center gap-2 overflow-x-auto text-[11px] shrink-0">
                        <span class="text-zinc-400 shrink-0 font-medium">Gợi ý nhanh:</span>
                        <button @click="insertTemplate('Xin chào bạn! Arena Sports có thể hỗ trợ gì cho bạn về việc đặt sân ạ?')"
                                class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-primary-50 dark:hover:bg-primary-950 text-zinc-700 dark:text-zinc-300 hover:text-primary-600 whitespace-nowrap transition-colors">
                            Chào khách
                        </button>
                        <button @click="insertTemplate('Dạ bạn có thể kiểm tra danh sách sân và khung giờ trống trực tiếp tại trang chi tiết sân ạ.')"
                                class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-primary-50 dark:hover:bg-primary-950 text-zinc-700 dark:text-zinc-300 hover:text-primary-600 whitespace-nowrap transition-colors">
                            Hướng dẫn xem giờ
                        </button>
                        <button @click="insertTemplate('Hệ thống hỗ trợ thanh toán qua chuyển khoản SePay QR tự động xác nhận tức thì bạn nhé.')"
                                class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-primary-50 dark:hover:bg-primary-950 text-zinc-700 dark:text-zinc-300 hover:text-primary-600 whitespace-nowrap transition-colors">
                            Tư vấn thanh toán SePay
                        </button>
                    </div>

                    {{-- Reply Input Bar --}}
                    <div class="p-3.5 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
                        <form @submit.prevent="sendReply()" class="flex items-center gap-2">
                            <input type="text"
                                   x-ref="replyInput"
                                   x-model="replyText"
                                   placeholder="Nhập tin nhắn phản hồi tới khách (Enter để gửi)..."
                                   :disabled="sendingReply"
                                   class="flex-1 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-4 py-2.5 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white dark:focus:bg-zinc-800 transition-all">

                            <button type="submit"
                                    :disabled="!replyText.trim() || sendingReply"
                                    :class="(!replyText.trim() || sendingReply) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700'"
                                    class="px-5 py-2.5 rounded-xl font-semibold text-xs bg-primary-600 text-white shadow transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 shrink-0 flex items-center gap-1.5">
                                <span>Gửi</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
