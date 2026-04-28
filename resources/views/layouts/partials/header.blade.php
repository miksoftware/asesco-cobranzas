<header class="sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0">
    <div class="flex items-center gap-3">
        {{-- Sidebar toggle desktop --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors cursor-pointer"
                aria-label="Toggle sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Sidebar toggle mobile --}}
        <button @click="mobileSidebar = !mobileSidebar"
                class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors cursor-pointer"
                aria-label="Toggle mobile sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page title --}}
        <h2 class="text-base font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
    </div>

    {{-- Right side: user info + logout button --}}
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-3 px-3 py-1.5">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-asesco-orange to-asesco-coral flex items-center justify-center text-white text-xs font-bold shadow-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="hidden sm:block text-right">
                <p class="text-sm font-medium text-gray-700 leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-gray-400 leading-tight">{{ ucfirst(auth()->user()->roles->first()?->name ?? 'Sin rol') }}</p>
            </div>
        </div>
        <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors cursor-pointer"
                    title="Cerrar sesión">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Cerrar sesión</span>
            </button>
        </form>
    </div>
</header>
