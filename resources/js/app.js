import './bootstrap';

Alpine.data('chatApp', (chatId = null) => ({
    messages: [],
    message: '',
    loading: false,
    mode: 'echo',
    chatId: chatId,

    init() {
        if (this.chatId) {
            this.loadMessages();
        }
    },

    focusInput() {
        // Ensure the input exists and is not disabled
        if (this.$refs.input && !this.$refs.input.disabled) {
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        }
    },

    async loadMessages() {
        try {
            const { data } = await axios.get(`/chats/${this.chatId}/messages`);
            this.messages = data.messages.map(msg => ({
                id: msg.id,
                text: msg.content,
                sender: msg.sender === 'user' ? 'me' : 'assistant'
            }));
            this.scrollToBottom();
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    },

    async sendMessage() {
        if (!this.message.trim() || this.loading) return;

        const messageText = this.message;
        this.message = '';
        this.loading = true;

        try {
            const { data } = await axios.post(
                this.chatId ? `/chats/${this.chatId}/messages` : '/chats',
                {
                    message: messageText,
                    mode: this.mode
                }
            );

            if (!this.chatId && data.chat_id) {
                window.location.href = `/chats/${data.chat_id}`;
                return;
            }

            // Add user message
            this.messages.push({
                id: data.user_message.id,
                text: data.user_message.content,
                sender: 'me'
            });

            this.scrollToBottom();

            // Add assistant's response
            this.messages.push({
                id: data.assistant_message.id,
                text: data.assistant_message.content,
                sender: 'assistant'
            });

        } catch (error) {
            console.error('Failed to send message:', error);
            this.message = messageText;
        } finally {
            this.loading = false;
            setTimeout(() => this.focusInput(), 100);
            this.scrollToBottom();
        }
    },

    scrollToBottom() {
        this.$nextTick(() => {
            const chatBox = this.$refs.chatBox;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    }
}));

Alpine.start();
