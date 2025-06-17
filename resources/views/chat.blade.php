<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ theme: localStorage.getItem('theme') || 'light' }"
      x-init="$watch('theme', val => {
          localStorage.setItem('theme', val);
          document.documentElement.setAttribute('data-theme', val);
      })"
      :data-theme="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 interactive-widget=resizes-content">
    <meta name="description" content="Simple chat application">
    <title>{{ config('app.name') }} - Чат</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-100 antialiased flex flex-col">

{{-- Navbar Container --}}
<div class="w-full bg-base-100 sticky top-0 z-50 h-16 flex-none">
    <div class="flex justify-center w-full h-full">
        <nav class="navbar w-full max-w-[800px] px-4 border-b" role="navigation">
            <div class="navbar-start">
                <h1 class="text-lg font-medium">{{ config('app.name') }}</h1>
            </div>
            <div class="navbar-end">
                <button type="button"
                        class="btn btn-ghost btn-circle btn-sm"
                        @click="theme = theme === 'light' ? 'dark' : 'light'"
                        :aria-label="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'">
                    {{-- Sun icon --}}
                    <svg x-show="theme === 'dark'"
                         class="w-5 h-5 fill-current"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24">
                        <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/>
                    </svg>
                    {{-- Moon icon --}}
                    <svg x-show="theme === 'light'"
                         class="w-5 h-5 fill-current"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24">
                        <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
                    </svg>
                </button>

                <details class="dropdown dropdown-end"
                         x-data="{ open: false }"
                         @click.outside="open = false"
                         :open="open">
                    <summary class="btn btn-ghost btn-circle avatar"
                             @click.prevent="open = !open">
                        <div class="w-10 rounded-full">
                            <img src="{{ auth()->user()->avatar }}" alt="Profile" />
                        </div>
                    </summary>
                    <div class="dropdown-content z-[1] menu p-2 shadow bg-base-200 rounded-box w-52 mt-3">
                        <div class="px-4 py-2">
                            <span class="text-sm font-medium block">{{ auth()->user()->name }}</span>
                            <span class="text-xs opacity-50 block">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="divider my-0"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-error hover:bg-base-300 rounded-lg cursor-pointer">
                                Выйти
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        </nav>
    </div>
</div>

{{-- Chat Container --}}
<main class="flex justify-center w-full flex-auto">
    <div x-data="chatApp()"
         x-init="$nextTick(() => $refs.input.focus())"
         class="flex flex-col h-full w-full max-w-[800px] px-4">

        {{-- Messages Container --}}
        <section x-ref="chatBox"
                 class="flex-1 p-4 space-y-2 scroll-smooth"
                 :class="{ 'overflow-y-auto': messages.length > 0 }"
                 aria-live="polite">

            {{-- Empty State --}}
            <template x-if="messages.length === 0">
                <div class="h-full flex items-center justify-center text-base-content/50">
                    <span class="text-sm">Начните общение...</span>
                </div>
            </template>

            {{-- Message List --}}
            <template x-for="msg in messages" :key="msg.id">
                <article class="flex"
                         :class="msg.sender === 'me' ? 'justify-end' : 'justify-start'"
                         :aria-label="msg.sender === 'me' ? 'Ваше сообщение' : 'Сообщение бота'">
                    <div class="max-w-[80%] break-words"
                         :class="msg.sender === 'me' ? 'text-right' : 'text-left'">
                        <p class="inline-block px-4 py-2 rounded-lg"
                           :class="msg.sender === 'me' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content'"
                           x-text="msg.text">
                        </p>
                    </div>
                </article>
            </template>

            {{-- Loading State --}}
            <template x-if="loading">
                <div class="flex justify-start">
                    <div class="inline-block px-4 py-2 rounded-lg bg-base-200">
                        <span class="loading loading-dots loading-sm" aria-label="Загрузка..."></span>
                    </div>
                </div>
            </template>
        </section>

        {{-- Input Form --}}
        <footer class="border-t border-base-200 bg-base-100 p-4">
            <form @submit.prevent="sendMessage"
                  class="flex gap-2"
                  aria-label="Форма отправки сообщения">
                <input type="text"
                       x-model="message"
                       x-ref="input"
                       x-on:blur="focusInput()"
                       class="input input-bordered text-base flex-1 focus:outline-none focus:border-primary bg-base-100"
                       placeholder="Введите сообщение..."
                       :disabled="loading"
                       aria-label="Текст сообщения">
                <button type="submit"
                        class="btn btn-primary btn-square"
                        :disabled="loading || !message.trim()"
                        aria-label="Отправить сообщение">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 20 20"
                         fill="currentColor"
                         aria-hidden="true">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>
        </footer>
    </div>
</main>

</body>
</html>
