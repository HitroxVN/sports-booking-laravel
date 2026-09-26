<x-owner-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
            {{ __('Chat Với Khách Hàng') }}
        </h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Trả lời câu hỏi khách gửi tới khu sân của bạn</p>
    </x-slot>

    <script>
        function ownerChat() {
            return {
                conversations: @json($conversations->items()),
                activeConversationId: null,
                activeConversation: null,
                activeMessages: [],
                replyText: '',
                sendingReply: false,
                csrfToken: '{{ csrf_token() }}',

                init() {
                    // Nghe thông báo tin nhắn mới của khách gửi tới các sân của mình
                    if (window.Echo) {
                        window.Echo.private('chat.owner.{{ auth()->id() }}')
                            .listen('.message.sent', (e) => this.onBroadcast(e));
                    }

                    if (this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0].id);
                    }
                },

                onBroadcast(e) {
                    const conv = this.conversations.find(c => c.id === e.conversation_id);
                    if (conv) {
                        conv.latest_message = { message: e.message, sender_type: e.sender_type };
                        if (this.activeConversationId !== e.conversation_id && e.sender_type === 'customer') {
                            conv.unread_customer_count = (conv.unread_customer_count || 0) + 1;
                        }
                    }

                    // Đang mở đúng hội thoại đó thì chèn tin mới vào khung chat
                    if (this.activeConversationId === e.conversation_id && e.sender_type === 'customer') {
                        this.activeMessages.push({ ...e, is_owner: false });
                        this.$nextTick(() => this.scrollBottom());
                    }
                },

                selectConversation(id) {
                    this.activeConversationId = id;

                    fetch('{{ route('owner.chats.show', ['conversation' => '__ID__']) }}'.replace('__ID__', id), {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.activeConversation = data;
                        this.activeMessages = data.messages;
                        const conv = this.conversations.find(c => c.id === id);
                        if (conv) conv.unread_customer_count = 0;
                        this.$nextTick(() => this.scrollBottom());
                    });
                },

                sendReply() {
                    const text = this.replyText.trim();
                    if (!text || this.sendingReply || !this.activeConversationId) return;

                    this.sendingReply = true;
                    this.replyText = '';

                    fetch('{{ route('owner.chats.reply', ['conversation' => '__ID__']) }}'.replace('__ID__', this.activeConversationId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(res => res.json())
                    .then(msg => {
                        this.sendingReply = false;
                        this.activeMessages.push(msg);
                        const conv = this.conversations.find(c => c.id === this.activeConversationId);
                        if (conv) conv.latest_message = { message: msg.message, sender_type: 'owner' };
                        this.$nextTick(() => this.scrollBottom());
                    })
                    .catch(() => { this.sendingReply = false; });
                },

                toggleStatus() {
                    if (!this.activeConversationId) return;

                    fetch('{{ route('owner.chats.status', ['conversation' => '__ID__']) }}'.replace('__ID__', this.activeConversationId), {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (this.activeConversation) this.activeConversation.status = data.status;
                    });
                },

                scrollBottom() {
                    const el = this.$refs.thread;
                    if (el) el.scrollTop = el.scrollHeight;
                }
            }
        }
    </script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card-base overflow-hidden" x-data="ownerChat()">
            <div class="grid grid-cols-1 md:grid-cols-3 h-[calc(100vh-14rem)]">

                {{-- Danh sách hội thoại --}}
                <div class="border-r border-zinc-200 dark:border-zinc-700 overflow-y-auto">
                    <template x-for="c in conversations" :key="c.id">
                        <button type="button" @click="selectConversation(c.id)"
                            class="w-full text-left px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/60"
                            :class="activeConversationId === c.id ? 'bg-primary-50 dark:bg-primary-900/20' : ''">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 truncate" x-text="c.customer_name"></span>
                                <span x-show="c.unread_customer_count > 0"
                                      class="shrink-0 min-w-[1.25rem] text-center text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-red-500 text-white"
                                      x-text="c.unread_customer_count"></span>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate"
                                 x-text="c.venue ? ('Sân: ' + c.venue.name) : ''"></div>
                            <div class="text-xs text-zinc-400 dark:text-zinc-500 truncate"
                                 x-text="c.latest_message ? c.latest_message.message : ''"></div>
                        </button>
                    </template>

                    <p x-show="conversations.length === 0" class="p-6 text-sm text-zinc-500 dark:text-zinc-400">
                        Chưa có khách nào nhắn tin cho khu sân của bạn.
                    </p>
                </div>

                {{-- Khung hội thoại --}}
                <div class="md:col-span-2 flex flex-col">
                    <template x-if="activeConversation">
                        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate" x-text="activeConversation.customer_name"></div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate" x-text="activeConversation.venue_name || ''"></div>
                            </div>
                            <button type="button" @click="toggleStatus()"
                                class="btn-secondary text-xs shrink-0"
                                x-text="activeConversation.status === 'open' ? 'Đóng hội thoại' : 'Mở lại'"></button>
                        </div>
                    </template>

                    <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="thread">
                        <template x-for="m in activeMessages" :key="m.id">
                            <div class="flex" :class="m.is_owner ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[75%] rounded-2xl px-3.5 py-2 text-sm"
                                     :class="m.is_owner
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100'">
                                    <div class="whitespace-pre-wrap break-words" x-text="m.message"></div>
                                    <div class="text-[10px] opacity-70 mt-1" x-text="m.created_at_time"></div>
                                </div>
                            </div>
                        </template>

                        <p x-show="!activeConversation" class="text-sm text-zinc-500 dark:text-zinc-400 text-center py-10">
                            Chọn một hội thoại để xem tin nhắn.
                        </p>
                    </div>

                    <form @submit.prevent="sendReply()" class="border-t border-zinc-200 dark:border-zinc-700 p-3 flex gap-2"
                          x-show="activeConversation">
                        <input type="text" x-model="replyText" placeholder="Nhập tin nhắn trả lời..."
                            class="flex-1 px-3 py-2 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <button type="submit" class="btn-primary text-sm shrink-0" :disabled="sendingReply">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-owner-layout>
