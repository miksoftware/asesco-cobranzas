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
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Gestiones</span>
        </a>
        @endcan

        @can('cargues.ver')
        <div x-data="{ openCargues: {{ request()->routeIs('cargues.*') ? 'true' : 'false' }} }">
            {{-- Toggle button --}}
            <button @click="openCargues = !openCargues"
                    class="w-full group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 cursor-pointer
                           {{ request()->routeIs('cargues.*') ? 'text-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="whitespace-nowrap transition-all duration-300 flex-1 text-left" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Cargues</span>
                <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
                     :class="[openCargues ? 'rotate-180' : '', sidebarOpen ? 'opacity-100' : 'opacity-0']"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            {{-- Submenu --}}
            <div x-show="openCargues && sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 style="display: none;"
                 class="mt-0.5 space-y-0.5">
                <a href="{{ route('cargues.telefonos') }}"
                   class="flex items-center gap-3 pl-10 pr-3 py-2 rounded-lg text-[12px] font-medium transition-all duration-200
                          {{ request()->routeIs('cargues.telefonos*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/40 hover:bg-white/5 hover:text-white/80' }}">
                    <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="whitespace-nowrap">Asignación Terceros</span>
                </a>
                <a href="{{ route('cargues.comentarios') }}"
                   class="flex items-center gap-3 pl-10 pr-3 py-2 rounded-lg text-[12px] font-medium transition-all duration-200
                          {{ request()->routeIs('cargues.comentarios*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/40 hover:bg-white/5 hover:text-white/80' }}">
                    <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                    <span class="whitespace-nowrap">Reporte Comentarios</span>
                </a>
            </div>
        </div>
        @endcan

        @canany(['sistemas.ver', 'usuarios.ver'])
        <p class="px-3 mt-6 mb-3 text-[10px] font-semibold uppercase tracking-widest text-white/30"
           :class="sidebarOpen ? 'block' : 'hidden'">Administración</p>

        @if(auth()->user()->email === 'admin@asesco.com')
        <a href="{{ route('sistemas.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200
                  {{ request()->routeIs('sistemas.*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Sistemas EPS</span>
        </a>

        <a href="{{ route('roles.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200
                  {{ request()->routeIs('roles.*') ? 'bg-gradient-to-r from-asesco-orange/20 to-transparent text-asesco-orange border-l-2 border-asesco-orange' : 'text-white/50 hover:bg-white/5 hover:text-white/90' }}">
            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Roles y Permisos</span>
        </a>
        @endif

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
    </div>
</aside>
