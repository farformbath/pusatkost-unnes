<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Pusat Kost UNNES') }}</title>

        <link rel="icon" type="image/jpeg" href="/images/logo.jpg">

        @viteReactRefresh
        @vite(['resources/js/app.tsx', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="h-full font-sans antialiased text-slate-900 bg-slate-50 selection:bg-[#FF6565] selection:text-white">
        @inertia
    </body>
</html>
