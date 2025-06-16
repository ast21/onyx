<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Простой чат</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen flex items-center justify-center p-6">

<div class="absolute top-4 right-4">
    <select class="select select-bordered" x-data x-on:change="document.documentElement.setAttribute('data-theme', $event.target.value)">
        <option value="light">Светлая</option>
        <option value="dark">Тёмная</option>
    </select>
</div>

<div x-data="chatApp()" x-init="$nextTick(() => $refs.input.focus())"
     class="w-full max-w-md bg-base-100 shadow-xl rounded-box p-6 space-y-4">

    <h1 class="text-2xl font-bold text-primary">Простой Чат</h1>

    <div class="h-64 overflow-y-auto border rounded-box p-2 space-y-2 bg-base-300" x-ref="chatBox">
        <template x-for="msg in messages" :key="msg.id">
            <div class="chat"
                 :class="msg.sender === 'me' ? 'chat-end' : 'chat-start'">
                <div class="chat-bubble" :class="msg.sender === 'me' ? 'chat-bubble-primary' : 'chat-bubble-accent'"
                     x-text="msg.text">
                </div>
            </div>
        </template>
    </div>

    <form @submit.prevent="sendMessage" class="flex gap-2">
        <input type="text" x-model="message" x-ref="input"
               class="input input-bordered focus:outline-none focus:ring-0 focus:border-base-content w-full"
               placeholder="Введите сообщение...">
        <button type="submit" class="btn btn-primary">
            Отправить
        </button>
    </form>
</div>

</body>
</html>
