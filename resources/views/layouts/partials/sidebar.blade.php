<aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#161e2d] text-white transition-all duration-300 shadow-2xl"
       :class="[
           sidebarOpen ? 'w-64' : 'w-20',
           mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
       ]">

    {{-- Logo --}}
    <div class="flex items-center gap-3 h-16 px-4 border-b border-white/5 shrink-0">
        <img src="{{ asset('images/logo_asesco.png') }}" alt="ASESCO Logo" class="h-9 w-auto max-w-[36px] object-contain shrink-0">
        <div class="overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
            <h1 class="font-bold text-sm whitespace-nowrap leading-tight tracking-wide">ASESCO</h1>
            <p class="text-[10px] text-slate-400 whitespace-nowrap leading-tight">BPO System</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-1 font-sans">

        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[14px] font-medium transition-all duration-200
                  {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-300 hover:bg-[#20293a] hover:text-white' }}">
            <svg class="w-[20px] h-[20px] shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Dashboard</span>
        </a>

        @can('consultas.ver')
        <a href="{{ route('consultas.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[14px] font-medium transition-all duration-200
                  {{ request()->routeIs('consultas.*') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-300 hover:bg-[#20293a] hover:text-white' }}">
            <svg class="w-[20px] h-[20px] shrink-0 {{ request()->routeIs('consultas.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
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
                    class="w-full group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[14px] font-medium transition-all duration-200 cursor-pointer
                           {{ request()->routeIs('cargues.*') ? 'text-white' : 'text-slate-300 hover:bg-[#20293a] hover:text-white' }}">
                <svg class="w-[20px] h-[20px] shrink-0 {{ request()->routeIs('cargues.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="whitespace-nowrap transition-all duration-300 flex-1 text-left" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Cargues</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-200"
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
                 class="mt-1 space-y-1">
                <a href="{{ route('cargues.telefonos') }}"
                   class="flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-200
                          {{ request()->routeIs('cargues.telefonos*') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-400 hover:bg-[#20293a] hover:text-white' }}">
                    <span class="whitespace-nowrap">Asignación Terceros</span>
                </a>
                <a href="{{ route('cargues.comentarios') }}"
                   class="flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-200
                          {{ request()->routeIs('cargues.comentarios*') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-400 hover:bg-[#20293a] hover:text-white' }}">
                    <span class="whitespace-nowrap">Reporte Comentarios</span>
                </a>
            </div>
        </div>
        @endcan

        @canany(['sistemas.ver', 'usuarios.ver'])
        
        <div class="h-4"></div>

        @if(auth()->id() === 1)
        <a href="{{ route('sistemas.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[14px] font-medium transition-all duration-200
                  {{ request()->routeIs('sistemas.*') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-300 hover:bg-[#20293a] hover:text-white' }}">
            <svg class="w-[20px] h-[20px] shrink-0 {{ request()->routeIs('sistemas.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Configuración</span>
        </a>

        <a href="{{ route('roles.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[14px] font-medium transition-all duration-200
                  {{ request()->routeIs('roles.*') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-300 hover:bg-[#20293a] hover:text-white' }}">
            <svg class="w-[20px] h-[20px] shrink-0 {{ request()->routeIs('roles.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Roles y Permisos</span>
        </a>
        @endif

        @can('usuarios.ver')
        <a href="{{ route('usuarios.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[14px] font-medium transition-all duration-200
                  {{ request()->routeIs('usuarios.*') ? 'bg-gradient-to-r from-[#E8611A]/20 to-[#C94477]/10 text-white shadow-[inset_3px_0_0_0_#E8611A]' : 'text-slate-300 hover:bg-[#20293a] hover:text-white' }}">
            <svg class="w-[20px] h-[20px] shrink-0 {{ request()->routeIs('usuarios.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Usuarios</span>
        </a>
        @endcan
        @endcanany

    </nav>

    {{-- User footer --}}
    <div class="p-4 shrink-0 mt-auto border-t border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#E8611A] flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-md">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="overflow-hidden transition-all duration-300 min-w-0" :class="sidebarOpen ? 'opacity-100 flex-1' : 'opacity-0 w-0'">
                <p class="text-[14px] font-medium truncate leading-tight text-white">{{ auth()->user()->name }}</p>
                <p class="text-[12px] text-slate-400 truncate leading-tight mt-0.5">{{ auth()->user()->email ?? 'admin@asesco.com' }}</p>
            </div>
        </div>
    </div>
</aside>
