import './bootstrap';

// Constants
const LOADING_DELAY = 0;
const MESSAGE_TYPES = {
    USER: 'me',
    BOT: 'bot'
};

// Helper functions
const generateMessageId = () => Date.now();
const scrollToBottom = (element) => element.scrollTop = element.scrollHeight;

// Chat application state and logic
Alpine.data('chatApp', () => ({
    message: '',
    messages: [],
    loading: false,

    // Lifecycle hooks
    init() {
        // Initial focus

        // Watch for messages changes
        this.$watch('messages', () => {
            this.$nextTick(() => this.scrollToLatestMessage());
        });
    },

    // Methods
    scrollToLatestMessage() {
        scrollToBottom(this.$refs.chatBox);
    },

    focusInput() {
        // Ensure the input exists and is not disabled
        if (this.$refs.input && !this.$refs.input.disabled) {
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        }
    },

    createMessage(text, sender) {
        return {
            id: generateMessageId(),
            text,
            sender
        };
    },

    async handleBotResponse(userMessage) {
        try {
            const response = await axios.post('/chat', { message: userMessage });
            await new Promise(resolve => setTimeout(resolve, LOADING_DELAY));
            return response.data.reply;
        } catch (error) {
            console.error('Error getting bot response:', error);
            return 'Ошибка при получении ответа';
        }
    },

    async sendMessage() {
        const text = this.message.trim();
        if (!text) return;

        // Add user message
        this.messages.push(this.createMessage(text, MESSAGE_TYPES.USER));
        this.message = '';
        this.loading = true;

        // Get and add bot response
        const botReply = await this.handleBotResponse(text);
        this.messages.push(this.createMessage(botReply, MESSAGE_TYPES.BOT));

        // Reset state and ensure focus after a short delay
        this.loading = false;
        setTimeout(() => this.focusInput(), 100);
    }
}));

Alpine.start();
