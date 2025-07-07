<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          theme: localStorage.getItem('theme') || 'light',
          sidebarOpen: false
      }"
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
<body class="bg-base-100">
    <div class="relative min-h-screen w-screen">
        {{-- Sidebar --}}
        <aside class="fixed top-0 left-0 z-40 h-full transition-transform duration-300 ease-in-out"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'lg:translate-x-0': true}">
            <div class="w-80 h-full bg-base-200 p-4 flex flex-col">
                <form action="{{ route('chat.store') }}" method="POST" class="mb-4">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full gap-2">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        Новый чат
                    </button>
                </form>

                <div class="flex-1 overflow-y-auto">
                    @foreach($chats ?? [] as $chatItem)
                        <a href="{{ route('chat.show', $chatItem) }}"
                           class="block p-3 mb-2 rounded-lg hover:bg-base-300 {{ isset($chat) && $chat->id === $chatItem->id ? 'bg-base-300' : '' }}">
                            <div class="flex items-center gap-3">
                                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
                                <span class="text-sm truncate">{{ $chatItem->title ?: 'Новый чат' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300 lg:hidden"
             :class="{'opacity-100': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"
             @click="sidebarOpen = false">
        </div>

        {{-- Main Content --}}
        <main class="lg:pl-80">
            {{-- Navbar --}}
            <div class="w-full bg-base-100 sticky top-0 z-20 h-16">
                <div class="flex justify-center w-full h-full">
                    <nav class="navbar w-full max-w-[800px] px-4 border-b border-base-content/20">
                        <div class="navbar-start">
                            <button @click="sidebarOpen = !sidebarOpen" class="btn btn-ghost btn-circle lg:hidden">
                                <x-heroicon-o-bars-3 class="w-6 h-6" />
                            </button>
                            <h1 class="text-lg font-medium ml-2">{{ config('app.name') }}</h1>
                        </div>
                        <div class="navbar-end">
                            <button
                                type="button"
                                class="btn btn-ghost btn-circle btn-sm mr-2"
                                @click="theme = theme === 'light' ? 'dark' : 'light'"
                                :aria-label="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
                            >
                                <x-heroicon-o-sun x-show="theme === 'dark'" class="w-6 h-6"/>
                                <x-heroicon-o-moon x-show="theme === 'light'" class="w-6 h-6"/>
                            </button>

                            <details class="dropdown dropdown-end"
                                     x-data="{ open: false }"
                                     @click.outside="open = false"
                                     :open="open">
                                <summary class="btn btn-ghost btn-circle avatar"
                                         @click.prevent="open = !open">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ auth()->user()->avatar }}" alt="Profile"/>
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

            {{-- Chat Content --}}
            <div class="flex justify-center w-full">
                <div x-data="chatApp({{ $chat?->id }})"
                     class="flex flex-col h-[calc(100vh-4rem)] w-full max-w-[800px] px-4 relative">

                    {{-- Messages Container --}}
                    <div class="flex-1 overflow-y-auto mb-28">
                        <section x-ref="chatBox"
                                 class="flex-1 p-4 space-y-2 scroll-smooth"
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
                    </div>

                    {{-- Input Area (Fixed at bottom) --}}
                    <div class="absolute bottom-0 left-0 right-0 bg-base-100 border-t border-base-200 p-4">
                        {{-- Mode Controls --}}
                        <div class="flex justify-start gap-2">
                            <div class="tooltip" data-tip="Echo">
                                <button type="button"
                                        class="btn btn-sm btn-ghost btn-square"
                                        :class="{ 'btn-active': mode === 'echo' }"
                                        @click="mode = 'echo'; focusInput()"
                                        aria-label="Echo">
                                    <x-heroicon-o-signal class="w-6 h-6" />
                                </button>
                            </div>

                            <div class="tooltip" data-tip="Tasky">
                                <button type="button"
                                        class="btn btn-sm btn-ghost btn-square"
                                        :class="{ 'btn-active': mode === 'tasky' }"
                                        @click="mode = 'tasky'; focusInput()"
                                        aria-label="Tasky">
                                    <x-heroicon-o-check-badge class="w-6 h-6" />
                                </button>
                            </div>

                            <div class="tooltip" data-tip="Error">
                                <button type="button"
                                        class="btn btn-sm btn-ghost btn-square"
                                        :class="{ 'btn-active': mode === 'error' }"
                                        @click="mode = 'error'; focusInput()"
                                        aria-label="Error">
                                    <x-heroicon-o-no-symbol class="w-6 h-6" />
                                </button>
                            </div>
                        </div>

                        {{-- Message Input --}}
                        <div class="bg-base-100 pt-2">
                            <form @submit.prevent="sendMessage"
                                  class="flex gap-2"
                                  aria-label="Форма отправки сообщения">
                                <input type="text"
                                       x-model="message"
                                       x-ref="input"
                                       class="input input-bordered text-base flex-1 focus:outline-none focus:border-primary bg-base-100"
                                       placeholder="Введите сообщение..."
                                       :disabled="loading"
                                       aria-label="Текст сообщения"
                                       autofocus>
                                <button type="submit"
                                        class="btn btn-primary btn-square"
                                        :disabled="loading || !message.trim()"
                                        aria-label="Отправить сообщение">
                                    <x-heroicon-o-arrow-up-circle class="w-6 h-6" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
