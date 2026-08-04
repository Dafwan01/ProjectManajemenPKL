<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/37b670cdf2.js" crossorigin="anonymous"></script>
        <script>
    (function() {
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

        @livewireStyles
    </head>
    <body>
        {{ $slot }}

        <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3"></div>
        <div id="flash-data" class="hidden"
             data-message="{{ session('message') }}"
             data-error="{{ session('error') }}"
             data-warning="{{ session('warning') }}"
             data-info="{{ session('info') }}"></div>

        @livewireScripts

    <script>
        function createToast(message, type = 'success') {
            const colors = {
                success: 'bg-emerald-500 text-white border-emerald-600',
                error: 'bg-rose-500 text-white border-rose-600',
                warning: 'bg-amber-500 text-slate-950 border-amber-600',
                info: 'bg-sky-500 text-white border-sky-600'
            };
            const toast = document.createElement('div');
            toast.className = `max-w-sm rounded-2xl border px-4 py-3 shadow-lg shadow-slate-900/10 flex items-center gap-3 ${colors[type] || colors.info}`;
            toast.innerHTML = `<div class="flex-1 text-sm font-medium">${message}</div><button type="button" class="text-lg font-bold leading-none opacity-90 hover:opacity-100">&times;</button>`;
            document.getElementById('toast-container').appendChild(toast);
            toast.querySelector('button').addEventListener('click', () => toast.remove());
            setTimeout(() => toast.remove(), 4500);
        }

        document.addEventListener('show-toast', event => {
            const { message, type } = event.detail || {};
            if (message) createToast(message, type);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const flashData = document.getElementById('flash-data');
            if (!flashData) return;

            const message = flashData.dataset.message;
            const error = flashData.dataset.error;
            const warning = flashData.dataset.warning;
            const info = flashData.dataset.info;

            if (message) createToast(message, 'success');
            if (error) createToast(error, 'error');
            if (warning) createToast(warning, 'warning');
            if (info) createToast(info, 'info');
        });
    </script>
    </body>
</html>
