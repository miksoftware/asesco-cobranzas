<aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#1a1a2e] text-white transition-all duration-300 shadow-xl"
       :class="[
           sidebarOpen ? 'w-64' : 'w-20',
           mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
       ]">

    {{-- Logo --}}
    <div class="flex items-center gap-3 h-16 px-5 border-b border-white/10 shrink-0">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-asesco-orange to-asesco-magenta flex items-center justify-center shrink-0 shadow-lg shadow-asesco-orange/20">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
            <h1 class="font-bold text-sm whitespace-nowrap leading-tight">ASESCO <span class="text-asesco-orange">BPO</span></h1>
            <p class="text-[10px] text-white/40 whitespace-nowrap leading-tight">Cobranzas</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-1">

        <p class="px-3 mb-3 text-[10px] font-semibold uppercase tracking-widest text-white/30"
           :class="sidebarOpen ? 'block' : 'hidden'">Principal</p>

        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200
                  {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Dashboard</span>
        </a>

        @can('consultas.ver')
        <p class="px-3 mt-6 mb-3 text-[10px] font-semibold uppercase tracking-widest text-white/30"
           :class="sidebarOpen ? 'block' : 'hidden'">Gestión</p>

        <a href="{{ route('consultas.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200
                  {{ request()->routeIs('consultas.*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Consultas</span>
        </a>
        @endcan

        @canany(['sistemas.ver', 'usuarios.ver'])
        <p class="px-3 mt-6 mb-3 text-[10px] font-semibold uppercase tracking-widest text-white/30"
           :class="sidebarOpen ? 'block' : 'hidden'">Administración</p>

        @can('sistemas.ver')
        <a href="{{ route('sistemas.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200
                  {{ request()->routeIs('sistemas.*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Sistemas EPS</span>
        </a>
        @endcan

        @can('usuarios.ver')
        <a href="{{ route('usuarios.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200
                  {{ request()->routeIs('usuarios.*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Usuarios</span>
        </a>
        @endcan
        @endcanany

    </nav>

    {{-- User footer --}}
    <div class="border-t border-white/10 p-4 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-asesco-orange to-asesco-coral flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-md">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden transition-all duration-300 min-w-0" :class="sidebarOpen ? 'opacity-100 flex-1' : 'opacity-0 w-0'">
                <p class="text-sm font-medium truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-white/40 truncate leading-tight">{{ auth()->user()->roles->first()?->name ?? 'Sin rol' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-xs text-red-400/70 hover:bg-red-500/10 hover:text-red-400 transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>
