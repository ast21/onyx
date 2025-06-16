<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Простой чат</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen flex items-center justify-center p-0 transition-all duration-500">

<div class="absolute top-4 right-4" x-data="{ theme: 'light' }" x-init="theme = localStorage.getItem('theme') || 'light'; document.documentElement.setAttribute('data-theme', theme)">
    <label class="swap swap-rotate">
        <input type="checkbox"
               :checked="theme === 'dark'"
               @change="theme = theme === 'light' ? 'dark' : 'light'; document.documentElement.setAttribute('data-theme', theme); localStorage.setItem('theme', theme)">

        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffae00"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="swap-on w-10 h-10 text-yellow-400 transition-all duration-300">
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2"/>
            <path d="M12 20v2"/>
            <path d="m4.93 4.93 1.41 1.41"/>
            <path d="m17.66 17.66 1.41 1.41"/>
            <path d="M2 12h2"/>
            <path d="M20 12h2"/>
            <path d="m6.34 17.66-1.41 1.41"/>
            <path d="m19.07 4.93-1.41 1.41"/>
        </svg>

        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#870099"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="swap-off w-10 h-10 text-violet-900 transition-all duration-300">
            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
        </svg>
    </label>
</div>

<div x-data="chatApp()" x-init="$nextTick(() => $refs.input.focus())"
     :class="messages.length > 0 ? 'h-screen' : 'h-auto'"
     class="w-full md:w-1/2 bg-base-100 shadow-xl rounded-none md:rounded-box p-6 flex flex-col space-y-4 transition-all duration-500">

    <div :class="messages.length === 0 ? 'hidden' : 'flex-1'" class="overflow-y-auto border rounded-box p-2 space-y-2 bg-base-300 transition-all duration-500" x-ref="chatBox">
        <template x-for="msg in messages" :key="msg.id">
            <div class="chat" :class="msg.sender === 'me' ? 'chat-end' : 'chat-start'">
                <div class="chat-bubble" :class="msg.sender === 'me' ? 'chat-bubble-primary' : 'chat-bubble-accent'" x-text="msg.text">
                </div>
            </div>
        </template>

        <template x-if="loading">
            <div class="chat chat-start">
                <div class="chat-bubble chat-bubble-accent animate-pulse">
                    печатает...
                </div>
            </div>
        </template>
    </div>

    <form @submit.prevent="sendMessage" class="flex gap-2">
        <input type="text" x-model="message" x-ref="input"
               class="input input-bordered focus:outline-none focus:ring-0 focus:border-base-content w-full transition-all duration-300"
               placeholder="Введите сообщение...">
        <button type="submit" class="btn btn-primary transition-all duration-300">
            Отправить
        </button>
    </form>
</div>

</body>
</html>
