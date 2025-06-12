<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Простой чат</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

<div x-data="chatApp()" x-init="$nextTick(() => $refs.input.focus())"
     class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 space-y-4">

    <h1 class="text-2xl font-semibold text-gray-700">Простой Чат</h1>

    <div class="h-64 overflow-y-auto border rounded p-2 space-y-2 bg-gray-50" x-ref="chatBox">
        <template x-for="msg in messages" :key="msg.id">
            <div class="p-2 rounded"
                 :class="msg.sender === 'me' ? 'bg-blue-100 text-right' : 'bg-green-100 text-left'"
                 x-text="msg.text">
            </div>
        </template>
    </div>

    <form @submit.prevent="sendMessage" class="flex space-x-2">
        <input type="text" x-model="message" x-ref="input"
               class="flex-1 border p-2 rounded focus:outline-none focus:ring"
               placeholder="Введите сообщение...">
        <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Отправить
        </button>
    </form>
</div>

</body>
</html>
