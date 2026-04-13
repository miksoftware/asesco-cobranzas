<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Asesco Cobranzas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: true, mobileSidebar: false }">

    {{-- Overlay mobile --}}
    <div x-show="mobileSidebar" x-transition.opacity @click="mobileSidebar = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" style="display:none;"></div>

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main wrapper --}}
    <div class="lg:ml-64 min-h-screen flex flex-col transition-all duration-300"
         :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

        {{-- Header --}}
        @include('layouts.partials.header')

        {{-- Page content --}}
        <main class="flex-1 p-6 overflow-auto">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
