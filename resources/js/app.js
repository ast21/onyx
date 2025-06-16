import './bootstrap';

Alpine.data('chatApp', () => ({
    message: '',
    messages: [],
    loading: false,

    async sendMessage() {
        const text = this.message.trim();
        if (!text) return;

        this.messages.push({ id: Date.now(), text, sender: 'me' });
        this.message = '';
        this.loading = true;

        this.$nextTick(() => {
            this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
        });

        try {
            const res = await axios.post('/chat', { message: text });
            await new Promise(resolve => setTimeout(resolve, 800)); // симуляция задержки
            this.messages.push({ id: Date.now() + 1, text: res.data.reply, sender: 'bot' });
        } catch (e) {
            this.messages.push({ id: Date.now() + 2, text: 'Ошибка при получении ответа', sender: 'bot' });
        } finally {
            this.loading = false;
            this.$nextTick(() => {
                this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
            });
        }
    }
}));

Alpine.start();
