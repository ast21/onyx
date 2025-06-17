<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Вход</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-100 flex items-center justify-center">

<div class="w-full max-w-sm p-6">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Вход в систему</h1>
        <p class="text-base-content/60 mt-2">Войдите чтобы продолжить</p>
    </div>

    <div class="card bg-base-200">
        <div class="card-body">
            <a href="{{ route('auth.google') }}"
               class="btn btn-outline gap-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M21.35,11.1H12.18V13.83H18.69C18.36,17.64 15.19,19.27 12.19,19.27C8.36,19.27 5,16.25 5,12C5,7.9 8.2,4.73 12.2,4.73C15.29,4.73 17.1,6.7 17.1,6.7L19,4.72C19,4.72 16.56,2 12.1,2C6.42,2 2.03,6.8 2.03,12C2.03,17.05 6.16,22 12.25,22C17.6,22 21.5,18.33 21.5,12.91C21.5,11.76 21.35,11.1 21.35,11.1V11.1Z" />
                </svg>
                Войти через Google
            </a>
        </div>
    </div>
</div>

</body>
</html>
