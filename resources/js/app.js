import './bootstrap';

Alpine.data('chatApp', () => ({
    message: '',
    messages: [],
    async sendMessage() {
        const text = this.message.trim()
        if (!text) return

        this.messages.push({ id: Date.now(), text, sender: 'me' })

        try {
            const res = await axios.post('/chat', { message: text })
            this.messages.push({ id: Date.now() + 1, text: res.data.reply, sender: 'bot' })
        } catch (e) {
            this.messages.push({ id: Date.now() + 2, text: 'Ошибка при отправке', sender: 'bot' })
        }

        this.message = ''
        this.$nextTick(() => {
            this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight
        })
    }
}))

Alpine.start();
