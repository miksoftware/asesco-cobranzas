@extends('layouts.app')

@section('title', 'Cobro Jurídico')
@section('page-title', 'Cobro Jurídico')

@section('content')
<div x-data="cobroJuridicoData()" class="space-y-4 relative">

    {{-- Spinner/Overlay de carga moderno --}}
    <div x-show="loading" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4 border border-white/50 w-64 text-center transform transition-all">
            <svg class="animate-spin h-10 w-10 text-asesco-orange" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-sm font-bold text-gray-800 tracking-wide uppercase">Guardando...</span>
        </div>
    </div>

    {{-- Sección 1: Datos Generales del Proceso --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm relative">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3 border-b border-gray-100 pb-2.5">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-asesco-orange font-bold text-xs">1</span>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Datos Generales del Proceso</h3>
                </div>
                
                {{-- Cédula en el encabezado de forma moderna --}}
                <div class="flex items-center gap-2 bg-gradient-to-r from-orange-50 via-amber-50/50 to-orange-50 border border-orange-200/90 px-3 py-1 rounded-full shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-asesco-orange shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Cédula:</span>
                    <span class="text-xs font-black text-gray-900 font-mono tracking-tight" x-text="s1.cedula || '—'"></span>
                </div>
            </div>
            <div class="flex gap-2">
                <button x-show="!is_section1_locked" @click="saveSection1()" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors cursor-pointer">
                    Guardar Datos
                </button>
                <button x-show="is_section1_locked" @click="unlockSection(1)" class="bg-gray-500 hover:bg-gray-600 text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors cursor-pointer">
                    Editar Datos
                </button>
            </div>
        </div>

        {{-- Grid compacto de 7 columnas exactas en un solo renglón --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-x-3 gap-y-3">
            
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="No. Consecutivo">No. Consecutivo</label>
                <input type="text" :value="s1.no_consecutivo" disabled placeholder="CJ-000001" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-700 font-bold bg-gray-100 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="No. de Radicado">No. de Radicado</label>
                <input type="text" x-model="s1.no_radicado" :disabled="is_section1_locked" placeholder="No. de Radicado" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 font-medium bg-white focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Estado Proceso">Estado Proceso</label>
                <select x-model="s1.estado_proceso" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500 cursor-pointer disabled:cursor-not-allowed">
                    <option value="">Seleccione estado...</option>
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="SUSPENDIDO">SUSPENDIDO</option>
                    <option value="INADMITIDO">INADMITIDO</option>
                    <option value="RECHAZADO">RECHAZADO</option>
                    <option value="TERMINADO">TERMINADO</option>
                    <option value="INACTIVO">INACTIVO</option>
                    <option value="DESISTIMIENTO">DESISTIMIENTO</option>
                    <option value="INSOLVENCIA">INSOLVENCIA</option>
                    <option value="SIN PROCESO">SIN PROCESO</option>
                    <template x-if="s1.estado_proceso && !estadosProceso.includes(s1.estado_proceso)">
                        <option :value="s1.estado_proceso" x-text="s1.estado_proceso" selected></option>
                    </template>
                </select>
            </div>

            {{-- Departamento Select con buscador estilo DIAN --}}
            <div class="relative" @click.outside="deptOpen = false">
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Departamento">Departamento</label>
                <button type="button" 
                        @click="if (!is_section1_locked) { deptOpen = !deptOpen; munOpen = false; if (deptOpen) { deptSearch = ''; $nextTick(() => $refs.deptSearchInput?.focus()); } }" 
                        :disabled="is_section1_locked"
                        class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-left bg-white flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500 cursor-pointer disabled:cursor-not-allowed">
                    <span class="truncate" :class="s1.departamento ? 'text-gray-800 font-medium' : 'text-gray-400'" x-text="s1.departamento || 'Seleccione departamento...'"></span>
                    <div class="flex items-center ml-1 shrink-0">
                        <span x-show="s1.departamento && !is_section1_locked" @click.stop="selectDepartamento({name: '', dian_code: ''})" class="text-gray-400 hover:text-red-500 mr-1 text-xs cursor-pointer p-0.5 leading-none" title="Limpiar">×</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="deptOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                {{-- Dropdown popover con buscador --}}
                <div x-show="deptOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute z-50 left-0 mt-1 w-64 md:w-72 bg-white rounded-lg border border-gray-200 shadow-xl overflow-hidden"
                     style="display: none;">
                    
                    {{-- Buscador con icono de lupa --}}
                    <div class="p-2 border-b border-gray-100 bg-gray-50/70">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   x-ref="deptSearchInput" 
                                   x-model="deptSearch" 
                                   placeholder="Buscar por código o nombre..." 
                                   class="w-full pl-8 pr-2 py-1.5 rounded border border-gray-200 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white">
                        </div>
                    </div>

                    {{-- Lista scrollable con opciones estilo DIAN --}}
                    <div class="max-h-48 overflow-y-auto py-1 text-xs">
                        <template x-for="dept in filteredDepartamentos" :key="dept.id">
                            <button type="button"
                                    @click="selectDepartamento(dept)" 
                                    class="w-full text-left px-3 py-1.5 hover:bg-orange-50 flex items-center justify-between group transition-colors cursor-pointer"
                                    :class="s1.departamento === dept.name ? 'bg-orange-50/70 font-semibold text-asesco-orange' : 'text-gray-700'">
                                <span class="truncate">
                                    <span class="font-mono text-gray-500 group-hover:text-asesco-orange font-bold mr-1.5" x-text="dept.dian_code"></span>
                                    <span x-text="dept.name"></span>
                                </span>
                                <span x-show="s1.departamento === dept.name" class="text-asesco-orange text-xs font-bold ml-1.5">✓</span>
                            </button>
                        </template>
                        <div x-show="filteredDepartamentos.length === 0" class="px-3 py-3 text-center text-gray-400 italic text-[11px]">
                            No se encontraron departamentos
                        </div>
                    </div>
                </div>
            </div>

            {{-- Municipio Select con buscador dependiente de Departamento --}}
            <div class="relative" @click.outside="munOpen = false">
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Municipio">Municipio</label>
                <button type="button" 
                        @click="if (!is_section1_locked && selectedDepartment) { munOpen = !munOpen; deptOpen = false; if (munOpen) { munSearch = ''; $nextTick(() => $refs.munSearchInput?.focus()); } }" 
                        :disabled="is_section1_locked || !selectedDepartment"
                        class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-left bg-white flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-400 cursor-pointer disabled:cursor-not-allowed">
                    <span class="truncate" :class="s1.municipio ? 'text-gray-800 font-medium' : 'text-gray-400'" x-text="s1.municipio || (selectedDepartment ? 'Seleccione municipio...' : 'Primero seleccione dpto.')"></span>
                    <div class="flex items-center ml-1 shrink-0">
                        <span x-show="s1.municipio && !is_section1_locked" @click.stop="selectMunicipio({name: '', dian_code: ''})" class="text-gray-400 hover:text-red-500 mr-1 text-xs cursor-pointer p-0.5 leading-none" title="Limpiar">×</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="munOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                {{-- Dropdown popover con buscador --}}
                <div x-show="munOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute z-50 left-0 mt-1 w-64 md:w-72 bg-white rounded-lg border border-gray-200 shadow-xl overflow-hidden"
                     style="display: none;">
                    
                    {{-- Buscador con icono de lupa --}}
                    <div class="p-2 border-b border-gray-100 bg-gray-50/70">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   x-ref="munSearchInput" 
                                   x-model="munSearch" 
                                   placeholder="Buscar por código o nombre..." 
                                   class="w-full pl-8 pr-2 py-1.5 rounded border border-gray-200 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white">
                        </div>
                    </div>

                    {{-- Lista scrollable con opciones estilo DIAN --}}
                    <div class="max-h-48 overflow-y-auto py-1 text-xs">
                        <template x-for="mun in filteredMunicipios" :key="mun.id">
                            <button type="button"
                                    @click="selectMunicipio(mun)" 
                                    class="w-full text-left px-3 py-1.5 hover:bg-orange-50 flex items-center justify-between group transition-colors cursor-pointer"
                                    :class="s1.municipio === mun.name ? 'bg-orange-50/70 font-semibold text-asesco-orange' : 'text-gray-700'">
                                <span class="truncate">
                                    <span class="font-mono text-gray-500 group-hover:text-asesco-orange font-bold mr-1.5" x-text="mun.dian_code"></span>
                                    <span x-text="mun.name"></span>
                                </span>
                                <span x-show="s1.municipio === mun.name" class="text-asesco-orange text-xs font-bold ml-1.5">✓</span>
                            </button>
                        </template>
                        <div x-show="filteredMunicipios.length === 0" class="px-3 py-3 text-center text-gray-400 italic text-[11px]">
                            No se encontraron municipios
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Especialidad">Especialidad</label>
                <input type="text" x-model="s1.especialidad" :disabled="is_section1_locked" placeholder="Especialidad" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Juzgado Conocimiento">Juzgado Conocimiento</label>
                <input type="text" x-model="s1.juzgado_conocimiento" :disabled="is_section1_locked" placeholder="Juzgado de conocimiento" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

        </div>
    </div>

    {{-- Sección 2: Pestañas (Tabs) --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden" x-show="cobro_juridico_id">
        {{-- Pestañas Header --}}
        <div class="flex items-center gap-1.5 px-4 py-2 bg-gray-50 border-b border-gray-200 overflow-x-auto">
            <button @click="activeTab = 'datos2'"
                    :class="activeTab === 'datos2' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap">
                DATOS GENERALES
            </button>
            <button @click="activeTab = 'depositos'"
                    :class="activeTab === 'depositos' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap">
                RELACIÓN TÍTULO DEPÓSITO JUDICIAL
            </button>
            <button @click="activeTab = 'historial'"
                    :class="activeTab === 'historial' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                HISTORIAL DE MODIFICACIONES
            </button>
            <button @click="activeTab = 'consultas_judicial'; onJudicialTabClick()"
                    :class="activeTab === 'consultas_judicial' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                </svg>
                <span>HISTORIAL DE CONSULTAS JUDICIAL</span>
                <span x-show="consultasJudicial.length > 0" class="ml-1 bg-white/25 text-white text-[10px] px-1.5 py-0.2 rounded-full font-extrabold" x-text="consultasJudicial.length"></span>
            </button>
        </div>

        {{-- Contenido Pestañas --}}
        <div class="p-4 min-h-[260px]">
            
            {{-- Pestaña: DATOS GENERALES --}}
            <div x-show="activeTab === 'datos2'" style="display: none;">
                <div class="flex justify-end mb-3 pb-2 border-b border-gray-100">
                    <div class="flex gap-2">
                        <button x-show="!is_section2_locked" @click="saveSection2()" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors cursor-pointer">
                            Guardar Datos
                        </button>
                        <button x-show="is_section2_locked" @click="unlockSection(2)" class="bg-gray-500 hover:bg-gray-600 text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors cursor-pointer">
                            Editar Datos
                        </button>
                    </div>
                </div>

                {{-- Campos exactos de Datos Generales solicitados --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-x-4 gap-y-3">
                    
                    {{-- Fila 1: Demandados 1 al 4 --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Demandado 1">Demandado 1</label>
                        <input type="text" x-model="s2.demandado_1" :disabled="is_section2_locked" placeholder="Demandado 1" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Demandado 2">Demandado 2</label>
                        <input type="text" x-model="s2.demandado_2" :disabled="is_section2_locked" placeholder="Demandado 2" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Demandado 3">Demandado 3</label>
                        <input type="text" x-model="s2.demandado_3" :disabled="is_section2_locked" placeholder="Demandado 3" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Demandado 4">Demandado 4</label>
                        <input type="text" x-model="s2.demandado_4" :disabled="is_section2_locked" placeholder="Demandado 4" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    {{-- Fila 2: Etapa y Actividad (siempre bloqueados) --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Fecha Etapa">Fecha Etapa</label>
                        <input type="date" x-model="s2.fecha_etapa" disabled class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-gray-100 disabled:text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Etapa Procesal">Etapa Procesal</label>
                        <input type="text" x-model="s2.etapa_procesal" disabled placeholder="Etapa procesal" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-gray-100 disabled:text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Fecha Actividad">Fecha Actividad</label>
                        <input type="date" x-model="s2.fecha_actividad" disabled class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-gray-100 disabled:text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Actividad">Actividad</label>
                        <input type="text" x-model="s2.actividad" disabled placeholder="Actividad" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-gray-100 disabled:text-gray-500 cursor-not-allowed">
                    </div>

                    {{-- Fila 3: Concepto, Garantías y Anotación --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Concepto Viabilidad Jurídica">Concepto Viabilidad Jurídica</label>
                        <select x-model="s2.concepto_viabilidad_juridica" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="ALTA">ALTA</option>
                            <option value="MEDIA">MEDIA</option>
                            <option value="BAJA">BAJA</option>
                            <option value="PENDIENTE">PENDIENTE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Garantías Jurídica">Garantías Jurídica</label>
                        <select x-model="s2.garantias_juridica" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="Inmueble">Inmueble</option>
                            <option value="Salario">Salario</option>
                            <option value="Vehículos">Vehículos</option>
                            <option value="Est. de Comercio">Est. de Comercio</option>
                            <option value="Sin Garantías">Sin Garantías</option>
                            <option value="Cuentas Bancarias">Cuentas Bancarias</option>
                            <option value="Remanente">Remanente</option>
                            <option value="Muebles y Enseres">Muebles y Enseres</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 xl:col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Anotación Abogado">Anotación Abogado</label>
                        <input type="text" x-model="s2.anotacion_abogado" :disabled="is_section2_locked" placeholder="Anotaciones del abogado..." class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                </div>
            </div>

            {{-- Pestaña: RELACIÓN TÍTULO DEPÓSITO JUDICIAL --}}
            <div x-show="activeTab === 'depositos'" style="display: none;" class="flex flex-col justify-between pt-1 space-y-2 min-h-[220px]">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-1">
                    <div class="flex gap-2">
                        <button x-show="!is_depositos_locked" @click="saveDepositos()" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors cursor-pointer">
                            Guardar Títulos / Depósitos
                        </button>
                        <button x-show="is_depositos_locked" @click="unlockSection(3)" class="bg-gray-500 hover:bg-gray-600 text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors cursor-pointer">
                            Editar Títulos / Depósitos
                        </button>
                    </div>

                    {{-- Encabezados de Totales y Valor del Proceso --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Valor Proceso:</span>
                            <div class="relative w-32">
                                <span class="absolute left-2 top-1.5 text-gray-400 text-xs">$</span>
                                <input type="number" x-model.number="valor_total_proceso" :disabled="is_depositos_locked" placeholder="0" class="w-full pl-5 pr-2 py-1 rounded border border-gray-300 text-xs font-bold text-blue-800 bg-blue-50 focus:outline-none focus:border-asesco-orange disabled:bg-blue-50/50">
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Total Depósitos</span>
                            <div class="bg-green-100 text-green-800 px-3 py-1 font-bold text-xs rounded border border-green-200 min-w-[110px] text-center" x-text="formatMoney(totalDepositos)"></div>
                        </div>

                        <div class="flex flex-col items-center">
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Saldo Pendiente</span>
                            <div class="bg-red-100 text-red-800 px-3 py-1 font-bold text-xs rounded border border-red-200 min-w-[110px] text-center" x-text="formatMoney(saldoPendiente)"></div>
                        </div>
                    </div>
                </div>

                {{-- Tabla de Títulos de Depósito Judicial --}}
                <div class="overflow-x-auto overflow-y-auto flex-1 min-h-[125px] max-h-[180px] custom-scrollbar border border-gray-200 rounded-lg">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2 font-semibold">FECHA_DESCUENTO</th>
                                <th class="px-3 py-2 font-semibold">VALOR</th>
                                <th class="px-3 py-2 font-semibold">FECHA_CONSIGNACIÓN</th>
                                <th class="px-3 py-2 font-semibold text-center">REPORTADO_COOMULTRASAN</th>
                                <th class="px-3 py-2 font-semibold text-center">SOPORTE</th>
                                <th class="px-3 py-2 font-semibold text-center">APLICADO_CORE_CM</th>
                                <th class="px-3 py-2 font-semibold">SALDO</th>
                                <th class="px-3 py-2 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(deposito, index) in depositos" :key="index">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-2">
                                        <input type="date" x-model="deposito.fecha_descuento" :disabled="is_depositos_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    <td class="p-2">
                                        <div class="relative">
                                            <span class="absolute left-2 top-1.5 text-gray-500">$</span>
                                            <input type="number" x-model.number="deposito.valor" :disabled="is_depositos_locked" class="w-full pl-6 pr-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="date" x-model="deposito.fecha_consignacion" :disabled="is_depositos_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" x-model="deposito.reportado" :disabled="is_depositos_locked" class="w-4 h-4 text-asesco-orange border-gray-300 rounded focus:ring-asesco-orange cursor-pointer disabled:opacity-50">
                                    </td>
                                    <td class="p-2 text-center align-middle">
                                        <div class="flex items-center justify-center gap-1 relative">
                                            <template x-if="!deposito.soporte">
                                                <div class="relative">
                                                    <input type="file" @change="uploadSoporte($event, index)" :disabled="is_depositos_locked" accept=".pdf,image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer disabled:cursor-not-allowed z-10" title="Subir soporte">
                                                    <button type="button" :disabled="is_depositos_locked" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded disabled:opacity-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="deposito.soporte">
                                                <div class="flex items-center gap-1">
                                                    <button type="button" @click="openSoporteModal(deposito.soporte)" class="text-green-600 hover:bg-green-50 p-1.5 rounded transition-colors cursor-pointer" title="Ver soporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    </button>
                                                    <button type="button" @click="downloadSoporte(getSoporteUrl(deposito.soporte), getSoporteFileName(deposito.soporte))" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded transition-colors cursor-pointer" title="Descargar soporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    </button>
                                                    <button type="button" x-show="!is_depositos_locked" @click="deposito.soporte = null" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors cursor-pointer" title="Eliminar soporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" x-model="deposito.aplicado" :disabled="is_depositos_locked" class="w-4 h-4 text-asesco-orange border-gray-300 rounded focus:ring-asesco-orange cursor-pointer disabled:opacity-50">
                                    </td>
                                    <td class="p-2">
                                        <div class="px-2 py-1.5 bg-gray-100 rounded text-gray-700 font-mono font-medium border border-gray-200" x-text="formatMoney(calcularSaldoFila(index))"></div>
                                    </td>
                                    <td class="p-2 text-center">
                                        <button x-show="!is_depositos_locked" @click="removeDeposito(index)" class="text-red-400 hover:text-red-600 p-1 cursor-pointer" title="Eliminar fila">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="depositos.length === 0">
                                <td colspan="8" class="p-6 text-center text-gray-400 text-xs italic">
                                    No hay títulos de depósito judicial registrados. Haz clic en "Agregar Título / Depósito" para empezar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                {{-- Botón agregar fila --}}
                <div class="pt-1" x-show="!is_depositos_locked">
                    <button @click="addDeposito()" class="flex items-center gap-1 text-xs font-semibold text-asesco-orange hover:text-asesco-coral transition-colors cursor-pointer px-2 py-1 rounded hover:bg-orange-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar Título / Depósito
                    </button>
                </div>

            </div>

            {{-- Pestaña: HISTORIAL DE MODIFICACIONES --}}
            <div x-show="activeTab === 'historial'" style="display: none;" class="flex flex-col pt-1 min-h-[220px]">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Registro de Actividades
                        </h3>
                        
                        <div class="space-y-3 max-h-[160px] overflow-y-auto custom-scrollbar pr-2">
                            <template x-for="history in histories" :key="history.id">
                                <div class="flex gap-3 text-sm">
                                    <div class="flex flex-col items-center">
                                        <div class="w-2 h-2 rounded-full bg-asesco-orange mt-1.5"></div>
                                        <div class="w-px h-full bg-gray-300 my-1"></div>
                                    </div>
                                    <div class="flex-1 pb-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs text-gray-800">
                                                <span class="font-bold text-gray-900" x-text="history.user ? history.user.name : 'Sistema'"></span> 
                                                ha <span class="font-semibold text-asesco-orange" x-text="history.accion.toLowerCase()"></span> 
                                                en la sección <span class="font-semibold" x-text="history.seccion"></span>
                                            </p>
                                            <span class="text-[10px] text-gray-500" x-text="new Date(history.created_at).toLocaleString()"></span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-0.5 font-medium" x-text="history.campo"></p>
                                        
                                        <div class="mt-1" x-show="history.accion === 'EDITADO' || history.valor_anterior || history.valor_nuevo">
                                            <button @click="openHistoryModal(history)" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 transition-colors cursor-pointer">
                                                Ver detalle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="histories.length === 0" class="flex-1 flex items-center justify-center text-center text-gray-400 text-xs italic py-8">
                                No hay historial registrado para este cobro jurídico.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pestaña: HISTORIAL DE CONSULTAS JUDICIAL --}}
            <div x-show="activeTab === 'consultas_judicial'" style="display: none;" class="flex flex-col pt-1 min-h-[260px] space-y-3">
                {{-- Barra superior de controles --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gray-50/80 p-2.5 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5 text-gray-800 font-bold text-xs">
                            <span class="p-1 rounded bg-orange-100 text-asesco-orange">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                            </span>
                            <span>Procesos en Rama Judicial</span>
                        </div>

                        {{-- Badge de Cédula --}}
                        <div class="flex items-center gap-1 bg-white px-2 py-1 rounded border border-gray-200 text-xs">
                            <span class="text-gray-400 text-[10px] font-semibold uppercase">Cédula:</span>
                            <span class="font-bold text-gray-800 font-mono" x-text="s1.cedula || '(No asignada)'"></span>
                        </div>

                        {{-- Contador de procesos --}}
                        <template x-if="consultasJudicialLoading">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-asesco-orange border border-orange-200">
                                <svg class="animate-spin w-3 h-3 text-asesco-orange" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Consultando...
                            </span>
                        </template>
                        <template x-if="!consultasJudicialLoading && consultasJudicialLoaded && consultasJudicial.length > 0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="consultasJudicial.length + ' proceso(s) encontrado(s)'"></span>
                        </template>
                        <template x-if="!consultasJudicialLoading && consultasJudicialLoaded && consultasJudicial.length === 0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600 border border-gray-200">0 procesos</span>
                        </template>
                    </div>

                    {{-- Acciones y Filtro rápido --}}
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <template x-if="consultasJudicial.length > 0">
                            <div class="relative w-48 sm:w-56">
                                <input type="text" 
                                       x-model="consultasJudicialSearch" 
                                       placeholder="Buscar radicado, despacho..." 
                                       class="w-full pl-7 pr-2 py-1 text-xs rounded border border-gray-300 bg-white focus:outline-none focus:border-asesco-orange">
                                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </template>

                        <button type="button" 
                                @click="fetchConsultasJudicial(s1.cedula, true)" 
                                :disabled="consultasJudicialLoading || !s1.cedula" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-asesco-orange hover:bg-asesco-coral disabled:opacity-50 text-white text-xs font-bold rounded shadow-sm transition-colors cursor-pointer"
                                title="Actualizar consulta en Rama Judicial">
                            <svg class="w-3.5 h-3.5" :class="consultasJudicialLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Consultar Rama Judicial</span>
                        </button>
                    </div>
                </div>

                {{-- Mensaje de Error si la consulta falló --}}
                <div x-show="consultasJudicialError" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="consultasJudicialError"></span>
                    </div>
                    <button type="button" @click="fetchConsultasJudicial(s1.cedula, true)" class="text-xs font-bold underline hover:text-red-900 cursor-pointer">Reintentar</button>
                </div>

                {{-- Estado de Carga (Skeleton) --}}
                <div x-show="consultasJudicialLoading" class="py-8 flex flex-col items-center justify-center gap-3">
                    <div class="w-10 h-10 border-4 border-orange-200 border-t-asesco-orange rounded-full animate-spin"></div>
                    <p class="text-xs font-semibold text-gray-600">Consultando antecedentes y procesos en el sistema Rama Judicial...</p>
                    <span class="text-[11px] text-gray-400 font-mono" x-text="'Cédula consultada: ' + s1.cedula"></span>
                </div>

                {{-- Estado Vacío (Sin Resultados) --}}
                <div x-show="!consultasJudicialLoading && consultasJudicialLoaded && consultasJudicial.length === 0" 
                     class="py-12 px-4 flex flex-col items-center justify-center text-center bg-gray-50/60 rounded-xl border border-dashed border-gray-300">
                    <div class="w-14 h-14 rounded-full bg-orange-50 text-asesco-orange flex items-center justify-center mb-3 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">No se encontraron resultados</h4>
                    <p class="text-xs text-gray-500 max-w-md mb-4">
                        No se encontraron procesos judiciales asociados a la cédula 
                        <span class="font-bold text-gray-800 font-mono" x-text="s1.cedula || '(vacía)'"></span> 
                        en el sistema Rama Judicial.
                    </p>
                    <button type="button" 
                            @click="fetchConsultasJudicial(s1.cedula, true)" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Volver a consultar
                    </button>
                </div>

                {{-- Tabla de Resultados (Estilo Imagen 2) --}}
                <div x-show="!consultasJudicialLoading && filteredConsultasJudicial.length > 0" 
                     class="overflow-x-auto overflow-y-auto flex-1 min-h-[160px] max-h-[380px] custom-scrollbar border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[10px] sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2.5">RADICADO</th>
                                <th class="px-3 py-2.5">DEMANDANTE</th>
                                <th class="px-3 py-2.5">DEMANDADO</th>
                                <th class="px-3 py-2.5">CIUDAD / DESPACHO</th>
                                <th class="px-3 py-2.5">ÚLTIMA ACTUACIÓN</th>
                                <th class="px-3 py-2.5">ÚLTIMA CONSULTA</th>
                                <th class="px-3 py-2.5 text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-for="proceso in filteredConsultasJudicial" :key="proceso.id || proceso.numero_radicado">
                                <tr class="hover:bg-orange-50/40 transition-colors">
                                    {{-- Radicado --}}
                                    <td class="px-3 py-2.5 align-top">
                                        <div class="font-mono font-bold text-gray-900 text-[11.5px]" x-text="proceso.numero_radicado"></div>
                                        <div class="mt-1 flex items-center gap-1">
                                            <template x-if="proceso.metodo_consulta === 'radicado_unificada'">
                                                <span class="text-[9px] font-semibold bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded border border-purple-200">Radicado Unificado</span>
                                            </template>
                                            <template x-if="proceso.metodo_consulta !== 'radicado_unificada'">
                                                <span class="text-[9px] font-semibold bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded border border-blue-200">Consulta Procesos</span>
                                            </template>
                                            <span class="text-[9px] text-gray-500 uppercase font-medium" x-text="proceso.tipo || ''"></span>
                                        </div>
                                    </td>

                                    {{-- Demandante --}}
                                    <td class="px-3 py-2.5 align-top max-w-xs">
                                        <div class="text-[11px] font-semibold text-gray-800" 
                                             :title="Array.isArray(proceso.demandantes) ? proceso.demandantes.join(' / ') : proceso.demandantes"
                                             x-text="Array.isArray(proceso.demandantes) ? proceso.demandantes.join(', ') : (proceso.demandantes || '—')">
                                        </div>
                                    </td>

                                    {{-- Demandado --}}
                                    <td class="px-3 py-2.5 align-top max-w-xs">
                                        <div class="text-[11px] font-semibold text-gray-800"
                                             :title="Array.isArray(proceso.demandados) ? proceso.demandados.join(' / ') : proceso.demandados"
                                             x-text="Array.isArray(proceso.demandados) ? proceso.demandados.join(', ') : (proceso.demandados || '—')">
                                        </div>
                                    </td>

                                    {{-- Ciudad / Despacho --}}
                                    <td class="px-3 py-2.5 align-top max-w-sm">
                                        <div class="font-bold text-gray-900 text-[11px] uppercase tracking-wide" x-text="proceso.ciudad || '—'"></div>
                                        <div class="text-[10px] text-gray-500 mt-0.5 truncate max-w-xs" 
                                             :title="proceso.despacho" 
                                             x-text="proceso.despacho || '—'">
                                        </div>
                                    </td>

                                    {{-- Última Actuación --}}
                                    <td class="px-3 py-2.5 align-top max-w-xs">
                                        <template x-if="proceso.actuaciones && proceso.actuaciones.length > 0">
                                            <div>
                                                <div class="font-bold text-gray-800 text-[11px]" x-text="formatDateJudicial(proceso.actuaciones[0].fecha_actuacion)"></div>
                                                <div class="text-[10px] text-gray-600 uppercase font-medium mt-0.5 truncate max-w-[180px]" 
                                                     :title="proceso.actuaciones[0].actuacion"
                                                     x-text="proceso.actuaciones[0].actuacion">
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!proceso.actuaciones || proceso.actuaciones.length === 0">
                                            <span class="text-gray-400 italic text-[10.5px]">Sin actuaciones</span>
                                        </template>
                                    </td>

                                    {{-- Última Consulta --}}
                                    <td class="px-3 py-2.5 align-top whitespace-nowrap text-gray-600 text-[10.5px] font-mono" 
                                        x-text="formatDateTimeJudicial(proceso.ultima_actualizacion || proceso.fecha_registro)">
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-3 py-2.5 align-middle text-center">
                                        <button type="button" 
                                                @click="openProcesoModal(proceso)" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#E8611A] hover:bg-[#d05312] text-white text-[11px] font-bold rounded-md shadow-sm transition-all duration-150 cursor-pointer hover:shadow-md active:scale-95 whitespace-nowrap"
                                                title="Ver detalle completo del proceso">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Ver más</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Aviso cuando el filtro no coincide --}}
                <div x-show="!consultasJudicialLoading && consultasJudicial.length > 0 && filteredConsultasJudicial.length === 0" 
                     class="p-6 text-center text-xs text-gray-400 italic bg-gray-50 rounded-lg">
                    No se encontraron procesos que coincidan con el término de búsqueda "<span class="font-semibold" x-text="consultasJudicialSearch"></span>".
                </div>
            </div>
            
        </div>
    </div>

    {{-- Sección 3: Gestiones de Cobro Jurídico --}}
    <div class="mt-4 border border-green-500 rounded-lg overflow-hidden shadow-sm bg-white" x-show="cobro_juridico_id">
        <div class="bg-gradient-to-r from-green-600 to-green-500 px-4 py-2 flex justify-between items-center">
            <h3 class="text-xs font-bold text-white tracking-wide uppercase flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Gestiones de Cobro Jurídico
            </h3>
            <span class="text-[11px] font-bold text-green-100 bg-green-700/60 px-2 py-0.5 rounded-full" x-text="gestiones.length + ' gestión(es)'"></span>
        </div>
        
        {{-- Listado de gestiones en una sola línea (ordenadas del más reciente al más antiguo) --}}
        <div class="border-b border-gray-100 max-h-[130px] overflow-x-auto overflow-y-auto custom-scrollbar">
            <table class="w-full text-left text-[11px] whitespace-nowrap">
                <thead class="bg-green-50/80 border-b border-green-200 text-green-900 font-bold text-[9.5px] uppercase tracking-wider sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2 text-center w-12">#</th>
                        <th class="px-3 py-2">Usuario</th>
                        <th class="px-3 py-2">Fecha Gestión</th>
                        <th class="px-3 py-2">Fecha Etapa</th>
                        <th class="px-3 py-2">Etapa Procesal</th>
                        <th class="px-3 py-2">Fecha Actividad</th>
                        <th class="px-3 py-2">Actividad</th>
                        <th class="px-3 py-2 text-center">Soporte</th>
                        <th class="px-3 py-2 min-w-[260px]">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-gray-700">
                    <template x-for="(g, index) in gestiones" :key="g.id || index">
                        <tr class="hover:bg-green-50/40 transition-colors">
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <span class="font-bold text-gray-600 text-[10px]" x-text="'#' + (gestiones.length - index)"></span>
                                    <template x-if="index === 0">
                                        <span class="px-1.5 py-0.5 text-[8px] font-extrabold rounded bg-green-100 text-green-800 uppercase tracking-wider">Última</span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-semibold text-gray-800" x-text="g.user ? g.user.name : 'Usuario'"></td>
                            <td class="px-3 py-2 text-gray-600 text-[10.5px]" x-text="g.fecha_gestion ? new Date(g.fecha_gestion).toLocaleString('es-CO') : (g.created_at ? new Date(g.created_at).toLocaleString('es-CO') : 'Automática')"></td>
                            <td class="px-3 py-2 text-gray-600" x-text="g.fecha_etapa || '—'"></td>
                            <td class="px-3 py-2 font-medium text-gray-800" x-text="g.etapa_procesal || '—'"></td>
                            <td class="px-3 py-2 text-gray-600" x-text="g.fecha_actividad || '—'"></td>
                            <td class="px-3 py-2 text-gray-800" x-text="g.actividad || '—'"></td>
                            <td class="px-3 py-2 text-center align-middle">
                                <template x-if="g.soporte">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" @click="openSoporteModal(g.soporte)" class="text-green-600 hover:bg-green-100 p-1 rounded transition-colors cursor-pointer" title="Ver soporte">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <button type="button" @click="downloadSoporte(getSoporteUrl(g.soporte), getSoporteFileName(g.soporte))" class="text-blue-600 hover:bg-blue-100 p-1 rounded transition-colors cursor-pointer" title="Descargar soporte">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!g.soporte">
                                    <span class="text-gray-400 font-mono">—</span>
                                </template>
                            </td>
                            <td class="px-3 py-2 max-w-xs xl:max-w-md">
                                <div class="truncate cursor-help hover:text-green-800 transition-colors"
                                     :title="g.gestion || g.detalle"
                                     x-text="g.gestion || g.detalle">
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="gestiones.length === 0">
                        <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-xs italic">
                            No hay gestiones registradas aún.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Formulario para registrar nueva gestión --}}
        <div class="p-4 bg-green-50/50 space-y-3">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Registrar Nueva Gestión
            </h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-[10.5px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Fecha Etapa">Fecha Etapa</label>
                    <input type="date" x-model="nueva_gestion.fecha_etapa" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-[10.5px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Etapa Procesal">Etapa Procesal</label>
                    <select x-model="nueva_gestion.etapa_procesal" @change="nueva_gestion.actividad = ''" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <option value="">Seleccione etapa...</option>
                        <template x-for="etapa in etapasProcesales" :key="etapa">
                            <option :value="etapa" x-text="etapa"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-[10.5px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Fecha Actividad">Fecha Actividad</label>
                    <input type="date" x-model="nueva_gestion.fecha_actividad" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-[10.5px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Actividad">Actividad</label>
                    <select x-model="nueva_gestion.actividad" :disabled="!nueva_gestion.etapa_procesal" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="" x-text="nueva_gestion.etapa_procesal ? 'Seleccione actividad...' : 'Seleccione etapa primero...'"></option>
                        <template x-for="act in actividadesDisponibles" :key="act">
                            <option :value="act" x-text="act"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-[10.5px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Soporte (PDF o Imagen)">Soporte</label>
                    <div class="relative h-[31px]">
                        <template x-if="!nueva_gestion.soporte">
                            <div class="relative w-full h-full">
                                <input type="file" 
                                       @change="uploadGestionSoporte($event)" 
                                       accept=".pdf,image/*" 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                       title="Subir soporte (PDF o Imagen)">
                                <div class="w-full h-full flex items-center justify-center gap-1.5 px-2 rounded border border-dashed border-gray-300 bg-white hover:bg-green-50/50 hover:border-green-500 text-gray-600 hover:text-green-700 text-xs cursor-pointer transition-colors shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    <span class="truncate font-medium text-[11px]">Adjuntar soporte</span>
                                </div>
                            </div>
                        </template>
                        <template x-if="nueva_gestion.soporte">
                            <div class="w-full h-full flex items-center justify-between px-2 rounded border border-green-300 bg-green-50 text-xs text-green-800 shadow-sm">
                                <div class="flex items-center gap-1 min-w-0 flex-1 mr-1" :title="getSoporteFileName(nueva_gestion.soporte)">
                                    <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="truncate text-[11px] font-medium" x-text="getSoporteFileName(nueva_gestion.soporte)"></span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" @click="openSoporteModal(nueva_gestion.soporte)" class="text-green-700 hover:text-green-900 p-1 rounded hover:bg-green-100 transition-colors cursor-pointer" title="Ver soporte">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="nueva_gestion.soporte = null" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors cursor-pointer" title="Quitar soporte">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10.5px] font-semibold text-gray-600 uppercase mb-1">Gestión</label>
                <textarea x-model="nueva_gestion.gestion" rows="3" class="w-full p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-xs resize-y bg-white" placeholder="Escriba aquí los detalles de la gestión..."></textarea>
            </div>
            
            <div class="flex justify-end">
                <button @click="saveGestion()" :disabled="!nueva_gestion.gestion.trim()" class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-xs font-semibold px-5 py-2 rounded-lg transition-colors cursor-pointer shadow-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Guardar Gestión
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Historial Detalle --}}
    <div x-show="historyModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        
        <div x-show="historyModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.away="historyModalOpen = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[85vh]">
             
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-asesco-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Detalle del Cambio
                </h3>
                <button @click="historyModalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/50">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-4">Campos Modificados: <span class="text-gray-800 font-bold" x-text="selectedHistory?.campo"></span></p>
                
                <template x-if="parsedNewValues && Object.keys(parsedNewValues).length > 0">
                    <div class="space-y-4">
                        <template x-for="(val, key) in parsedNewValues" :key="key">
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm flex flex-col gap-3 transition-all hover:border-gray-200 hover:shadow-md">
                                <div class="text-[11px] font-bold text-gray-800 uppercase tracking-wider text-center bg-gray-50 py-1.5 rounded-lg border border-gray-100" x-text="key.replace(/_/g, ' ')"></div>
                                <div class="grid grid-cols-2 gap-6 relative mt-1">
                                    <div class="absolute inset-y-0 left-1/2 w-px bg-gradient-to-b from-transparent via-gray-200 to-transparent transform -translate-x-1/2"></div>
                                    
                                    {{-- Anterior --}}
                                    <div class="flex flex-col items-center text-center">
                                        <span class="text-[10px] text-gray-400 font-bold mb-1 uppercase tracking-wider">Anterior</span>
                                        <span class="text-[13px] text-gray-500 line-through decoration-red-300 decoration-2" x-text="(parsedOldValues && parsedOldValues[key]) ? parsedOldValues[key] : '(Ninguno)'"></span>
                                    </div>
                                    
                                    {{-- Nuevo --}}
                                    <div class="flex flex-col items-center text-center">
                                        <span class="text-[10px] text-gray-400 font-bold mb-1 uppercase tracking-wider">Nuevo</span>
                                        <span class="text-[13px] font-bold text-gray-900" x-text="val || '(Vacío)'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                
                <template x-if="!parsedNewValues || Object.keys(parsedNewValues).length === 0">
                    <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-medium">No hay detalles específicos registrados.</p>
                    </div>
                </template>
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end">
                <button type="button" @click="historyModalOpen = false" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer shadow-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Vista Previa de Soporte --}}
    <div x-show="soporteModalOpen" 
         x-transition.opacity 
         style="display: none;" 
         class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4"
         @keydown.escape.window="closeSoporteModal()">
        
        {{-- Modal Content --}}
        <div x-show="soporteModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.away="closeSoporteModal()"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[92vh] border border-gray-100">
             
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 text-asesco-orange rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Vista Previa de Soporte</h3>
                        <p class="text-xs text-gray-500 font-mono truncate max-w-md" x-text="soporteModalFileName"></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- Botón Descargar en Header --}}
                    <button type="button" 
                            @click="downloadSoporte(soporteModalUrl, soporteModalFileName)" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm cursor-pointer"
                            title="Descargar archivo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Descargar</span>
                    </button>
                    
                    {{-- Botón Cerrar --}}
                    <button @click="closeSoporteModal()" class="text-gray-400 hover:text-gray-600 transition-colors bg-white hover:bg-gray-100 rounded-full p-1.5 cursor-pointer border border-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-4 overflow-hidden flex-1 bg-gray-900/5 flex items-center justify-center min-h-[450px]">
                <template x-if="soporteModalType === 'image'">
                    <div class="w-full h-full flex items-center justify-center overflow-auto max-h-[70vh] p-2">
                        <img :src="soporteModalUrl" class="max-h-[68vh] max-w-full object-contain rounded-lg shadow-md" alt="Vista previa del soporte">
                    </div>
                </template>
                <template x-if="soporteModalType === 'pdf'">
                    <iframe :src="soporteModalUrl" class="w-full h-[70vh] rounded-lg border border-gray-200 bg-white shadow-inner" frameborder="0"></iframe>
                </template>
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-3 bg-white border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500 italic">Previsualización de documento adjunto</span>
                <div class="flex items-center gap-2">
                    <button type="button" @click="closeSoporteModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Cerrar
                    </button>
                    <button type="button" 
                            @click="downloadSoporte(soporteModalUrl, soporteModalFileName)" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Descargar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detalle de Proceso Judicial (Estilo Rama Judicial) --}}
    <div x-show="procesoModalOpen" 
         x-transition.opacity 
         style="display: none;" 
         class="fixed inset-0 z-[75] flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-3 sm:p-4"
         @keydown.escape.window="closeProcesoModal()">
        
        <div x-show="procesoModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.away="closeProcesoModal()"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[92vh] border border-gray-200">
             
            {{-- Modal Header (Estilo Imagen 3) --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/90 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-asesco-orange bg-orange-50 px-2 py-0.5 rounded border border-orange-200 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            </svg>
                            MÓDULO JUDICIAL
                        </span>
                        <span class="text-xs text-gray-500 font-medium">/</span>
                        <span class="text-xs text-gray-500 font-medium truncate">Detalle del Proceso</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="closeProcesoModal()" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Volver al listado</span>
                        </button>
                        <button @click="closeProcesoModal()" class="text-gray-400 hover:text-gray-600 transition-colors bg-white hover:bg-gray-100 rounded-full p-1.5 cursor-pointer border border-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Título Radicado y Subtítulo --}}
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <h2 class="text-lg md:text-xl font-bold font-mono text-gray-900 tracking-tight" x-text="selectedProceso?.numero_radicado"></h2>
                    <template x-if="selectedProceso?.metodo_consulta === 'radicado_unificada'">
                        <span class="bg-purple-100 text-purple-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-purple-200">Radicado Unificado</span>
                    </template>
                    <template x-if="selectedProceso?.metodo_consulta !== 'radicado_unificada'">
                        <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-blue-200">Consulta Procesos</span>
                    </template>
                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-emerald-200 uppercase" x-text="selectedProceso?.estado || 'completado'"></span>
                </div>
                <p class="text-xs text-gray-500 font-medium" x-text="(selectedProceso?.ciudad || '') + ' — ' + (selectedProceso?.despacho || '')"></p>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6 bg-slate-50/50">

                {{-- Bloque 1: Información del proceso (Estilo Imagen 3) --}}
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5 pb-2 border-b border-gray-100">
                        <svg class="w-4 h-4 text-asesco-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Información del proceso
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-3 text-xs">
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">DESPACHO</span>
                            <span class="font-semibold text-gray-800" x-text="selectedProceso?.despacho || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">PONENTE</span>
                            <span class="font-semibold text-gray-800" x-text="selectedProceso?.ponente || 'Sin asignar'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">TIPO</span>
                            <span class="font-semibold text-gray-800" x-text="selectedProceso?.tipo || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">CLASE</span>
                            <span class="font-semibold text-gray-800" x-text="selectedProceso?.clase || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">RECURSO</span>
                            <span class="font-semibold text-gray-800" x-text="selectedProceso?.recurso || 'Sin Tipo de Recurso'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">UBICACIÓN EXPEDIENTE</span>
                            <span class="font-semibold text-gray-800" x-text="selectedProceso?.ubicacion_expediente || 'Secretaría'"></span>
                        </div>
                        <div class="sm:col-span-2 md:col-span-3">
                            <span class="block text-[10px] font-bold uppercase text-gray-400 mb-0.5">CONTENIDO DE RADICACIÓN</span>
                            <span class="font-medium text-gray-700 bg-gray-50 p-2 rounded border border-gray-100 block" x-text="selectedProceso?.contenido_radicacion || '—'"></span>
                        </div>
                    </div>
                </div>

                {{-- Bloque 2: Sujetos Procesales (Demandantes y Demandados lado a lado, como Imagen 3) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Demandante(s) --}}
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col">
                        <div class="flex items-center gap-2 pb-2 mb-3 border-b border-blue-50">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wide">Demandante(s)</h4>
                        </div>
                        <div class="space-y-1.5 flex-1">
                            <template x-if="selectedDemandantes.length > 0">
                                <template x-for="(dem, i) in selectedDemandantes" :key="i">
                                    <div class="flex items-start gap-2 text-xs text-gray-800 bg-blue-50/40 px-2.5 py-1.5 rounded border border-blue-100/60">
                                        <span class="text-blue-500 font-bold">•</span>
                                        <span class="font-medium" x-text="dem"></span>
                                    </div>
                                </template>
                            </template>
                            <template x-if="selectedDemandantes.length === 0">
                                <span class="text-xs text-gray-400 italic">No hay demandantes registrados</span>
                            </template>
                        </div>
                    </div>

                    {{-- Demandado(s) --}}
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col">
                        <div class="flex items-center gap-2 pb-2 mb-3 border-b border-red-50">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wide">Demandado(s)</h4>
                        </div>
                        <div class="space-y-1.5 flex-1">
                            <template x-if="selectedDemandados.length > 0">
                                <template x-for="(dem, i) in selectedDemandados" :key="i">
                                    <div class="flex items-start gap-2 text-xs text-gray-800 bg-red-50/40 px-2.5 py-1.5 rounded border border-red-100/60">
                                        <span class="text-red-500 font-bold">•</span>
                                        <span class="font-medium" x-text="dem"></span>
                                    </div>
                                </template>
                            </template>
                            <template x-if="selectedDemandados.length === 0">
                                <span class="text-xs text-gray-400 italic">No hay demandados registrados</span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Bloque 3: Actuaciones (Estilo Imagen 3) --}}
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Actuaciones</h3>
                            <span class="text-xs text-gray-500 font-medium" x-text="'(' + (selectedProceso?.actuaciones?.length || 0) + ' actuación(es))'"></span>
                        </div>

                        {{-- Buscador interno de actuaciones --}}
                        <div class="relative w-full sm:w-64">
                            <input type="text" 
                                   x-model="actuacionSearch" 
                                   placeholder="Filtrar providencia, anotación..." 
                                   class="w-full pl-7 pr-2 py-1 text-xs rounded border border-gray-300 bg-white focus:outline-none focus:border-asesco-orange">
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Tabla de Actuaciones --}}
                    <div class="overflow-x-auto overflow-y-auto max-h-[300px] custom-scrollbar border border-gray-200 rounded-lg">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[10px] sticky top-0 z-10">
                                <tr>
                                    <th class="px-3 py-2 whitespace-nowrap">FECHA ACTUACIÓN</th>
                                    <th class="px-3 py-2 min-w-[160px]">ACTUACIÓN</th>
                                    <th class="px-3 py-2 min-w-[280px]">ANOTACIÓN</th>
                                    <th class="px-3 py-2 text-center whitespace-nowrap">INICIA TÉRMINO</th>
                                    <th class="px-3 py-2 text-center whitespace-nowrap">FINALIZA TÉRMINO</th>
                                    <th class="px-3 py-2 text-center whitespace-nowrap">FECHA REGISTRO</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="act in filteredActuaciones" :key="act.id || act.fecha_actuacion + act.actuacion">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-2 align-top font-bold text-gray-900 whitespace-nowrap" x-text="formatDateJudicial(act.fecha_actuacion)"></td>
                                        <td class="px-3 py-2 align-top font-semibold text-gray-800 uppercase text-[11px]" x-text="act.actuacion || '—'"></td>
                                        <td class="px-3 py-2 align-top text-gray-700 text-[11px] leading-relaxed" x-text="act.anotacion || '—'"></td>
                                        <td class="px-3 py-2 align-top text-center text-gray-600 whitespace-nowrap" x-text="formatDateJudicial(act.fecha_inicia_termino)"></td>
                                        <td class="px-3 py-2 align-top text-center text-gray-600 whitespace-nowrap" x-text="formatDateJudicial(act.fecha_finaliza_termino)"></td>
                                        <td class="px-3 py-2 align-top text-center text-gray-600 whitespace-nowrap" x-text="formatDateJudicial(act.fecha_registro)"></td>
                                    </tr>
                                </template>
                                <tr x-show="!selectedProceso?.actuaciones || selectedProceso.actuaciones.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs italic">
                                        No hay actuaciones registradas para este proceso.
                                    </td>
                                </tr>
                                <tr x-show="selectedProceso?.actuaciones && selectedProceso.actuaciones.length > 0 && filteredActuaciones.length === 0">
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400 text-xs italic">
                                        No se encontraron actuaciones que coincidan con "<span class="font-semibold" x-text="actuacionSearch"></span>".
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-500 italic">Información sincronizada desde el sistema Rama Judicial</span>
                <button type="button" @click="closeProcesoModal()" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
const existingCobro = @json($cobroJuridico ?? null);
const nextNoConsecutivo = @json($nextNoConsecutivo ?? '');
const initialCedula = @json($cedula ?? '');
const initialDepartamentos = @json($departamentos ?? []);

function cobroJuridicoData() {
    const initialGestiones = existingCobro?.gestiones || [];
    const latestGestion = initialGestiones.length > 0 ? initialGestiones[0] : null;
    const urlParams = new URLSearchParams(window.location.search);
    const cedulaParam = existingCobro?.cedula || initialCedula || urlParams.get('cedula') || '';
    const nombreParam = existingCobro?.demandado_1 || urlParams.get('nombre') || '';

    return {
        loading: false,
        activeTab: 'datos2',
        cobro_juridico_id: existingCobro ? existingCobro.id : null,
        
        // Historial de Consultas Judicial
        consultasJudicial: [],
        consultasJudicialLoading: false,
        consultasJudicialLoaded: false,
        consultasJudicialError: null,
        consultasJudicialSearch: '',
        consultasJudicialCedula: '',
        selectedProceso: null,
        procesoModalOpen: false,
        actuacionSearch: '',

        // Departamentos y Municipios
        departamentosList: initialDepartamentos,
        deptOpen: false,
        deptSearch: '',
        munOpen: false,
        munSearch: '',

        // Secciones locks
        is_section1_locked: existingCobro ? existingCobro.is_section1_locked : false,
        is_section2_locked: existingCobro ? existingCobro.is_section2_locked : false,
        is_depositos_locked: existingCobro ? existingCobro.is_depositos_locked : false,

        // Sección 1: Datos Generales del Proceso (7 campos)
        s1: {
            no_consecutivo: existingCobro?.no_consecutivo || nextNoConsecutivo,
            no_radicado: existingCobro?.no_radicado || '',
            cedula: cedulaParam,
            estado_proceso: existingCobro?.estado_proceso || '',
            departamento: existingCobro?.departamento || '',
            municipio: existingCobro?.municipio || '',
            especialidad: existingCobro?.especialidad || '',
            juzgado_conocimiento: existingCobro?.juzgado_conocimiento || '',
        },

        // Sección 2 - Pestaña 1: Datos Generales (con lo último puesto en gestiones)
        s2: {
            demandado_1: nombreParam,
            demandado_2: existingCobro?.demandado_2 || '',
            demandado_3: existingCobro?.demandado_3 || '',
            demandado_4: existingCobro?.demandado_4 || '',
            fecha_etapa: latestGestion?.fecha_etapa || existingCobro?.fecha_etapa || '',
            etapa_procesal: latestGestion?.etapa_procesal || existingCobro?.etapa_procesal || '',
            fecha_actividad: latestGestion?.fecha_actividad || existingCobro?.fecha_actividad || '',
            actividad: latestGestion?.actividad || existingCobro?.actividad || '',
            concepto_viabilidad_juridica: existingCobro?.concepto_viabilidad_juridica || '',
            garantias_juridica: existingCobro?.garantias_juridica || '',
            anotacion_abogado: existingCobro?.anotacion_abogado || '',
        },

        valor_total_proceso: existingCobro?.valor_total_proceso || null,

        // Relaciones
        depositos: existingCobro?.depositos || [],
        gestiones: initialGestiones,
        histories: existingCobro?.histories || [],

        // Formulario de nueva gestión
        nueva_gestion: {
            fecha_etapa: '',
            etapa_procesal: '',
            fecha_actividad: '',
            actividad: '',
            soporte: null,
            gestion: '',
        },

        // Catálogo de Estado del Proceso
        estadosProceso: [
            'ACTIVO',
            'SUSPENDIDO',
            'INADMITIDO',
            'RECHAZADO',
            'TERMINADO',
            'INACTIVO',
            'DESISTIMIENTO',
            'INSOLVENCIA',
            'SIN PROCESO'
        ],

        // Catálogos de Etapa y Actividad dependiente
        etapasProcesales: [
            'Elaboración Demanda',
            'Presentación Demanda',
            'Mandamiento de Pago',
            'Embargo',
            'Notificación',
            'Secuestro',
            'Sentencia',
            'Liquidación',
            'Sin Proceso'
        ],

        actividadesPorEtapa: {
            'Elaboración Demanda': [
                'Elaboración de demanda'
            ],
            'Presentación Demanda': [
                'Pendiente Auto de Mandamiento de Pago',
                'Presentar la Demanda y la Medida Cautelar En Oficina de Reparto'
            ],
            'Mandamiento de Pago': [
                'Demanda In Admitida',
                'Demanda Rechazada',
                'Memorial de Subsanación',
                'Retirar Mandamiento de Pago y Medidas'
            ],
            'Embargo': [
                'Aceptaron la Medida Cautelar',
                'Radicar Los Oficios de Medidas Cautelares',
                'Retirar Los Oficios de Las Medidas',
                'Terminar Proceso Por Pago',
                'Tramitar la Aceptación de la Medida'
            ],
            'Notificación': [
                'Elaboración de Notificación',
                'Elaborar Contestación de Excepciones',
                'Emplazamiento',
                'Emplazamiento y Curador ad-litem',
                'Envío de Notificación personal',
                'Envío de Notificación por aviso',
                'Memorial Allegando Citatorio de Notificación',
                'Notificación personal devuelta',
                'Notificación personal recibida',
                'Notificación por aviso devuelta',
                'Notificación por aviso recibida',
                'Notificación por Conducta Concluyente',
                'Radicar Contestación Excepciones',
                'Suspensión Por Acuerdo de Pago',
                'Terminar Proceso Por Pago'
            ],
            'Secuestro': [
                'Realizar la Diligencia de Secuestro'
            ],
            'Sentencia': [
                'Audiencia Art. 372 CGP.',
                'Elaborar Memorial de Solicitud de Sentencia',
                'Juez Emite Sentencia',
                'Solicitud de sentencia'
            ],
            'Liquidación': [
                'Elaboración Memorial de Liquidación de Crédito',
                'Juez Fija Liquidación de Crédito',
                'Presentar Liquidación del Crédito',
                'Terminar Proceso Por Pago'
            ],
            'Sin Proceso': [
                'Sin Proceso'
            ]
        },

        get actividadesDisponibles() {
            return this.actividadesPorEtapa[this.nueva_gestion.etapa_procesal] || [];
        },
        
        // Modal History
        historyModalOpen: false,
        selectedHistory: null,

        // Modal Soporte
        soporteModalOpen: false,
        soporteModalUrl: '',
        soporteModalFileName: '',
        soporteModalType: 'pdf',

        // Computed
        get totalDepositos() {
            return this.depositos.reduce((sum, item) => sum + (parseFloat(item.valor) || 0), 0);
        },
        get saldoPendiente() {
            return (parseFloat(this.valor_total_proceso) || 0) - this.totalDepositos;
        },
        get parsedOldValues() {
            if (!this.selectedHistory || !this.selectedHistory.valor_anterior) return null;
            try { 
                const parsed = JSON.parse(this.selectedHistory.valor_anterior); 
                return (typeof parsed === 'object' && parsed !== null) ? parsed : { "Detalle": parsed };
            } catch(e) { return { "Detalle": this.selectedHistory.valor_anterior }; }
        },
        get parsedNewValues() {
            if (!this.selectedHistory || !this.selectedHistory.valor_nuevo) return null;
            try { 
                const parsed = JSON.parse(this.selectedHistory.valor_nuevo); 
                return (typeof parsed === 'object' && parsed !== null) ? parsed : { "Detalle": parsed };
            } catch(e) { return { "Detalle": this.selectedHistory.valor_nuevo }; }
        },

        // Getters para Rama Judicial
        get filteredConsultasJudicial() {
            if (!this.consultasJudicialSearch.trim()) return this.consultasJudicial;
            const q = this.consultasJudicialSearch.toLowerCase().trim();
            return this.consultasJudicial.filter(p => {
                const rad = (p.numero_radicado || '').toLowerCase();
                const desp = (p.despacho || '').toLowerCase();
                const ciu = (p.ciudad || '').toLowerCase();
                const dem1 = Array.isArray(p.demandantes) ? p.demandantes.join(' ').toLowerCase() : (p.demandantes || '').toLowerCase();
                const dem2 = Array.isArray(p.demandados) ? p.demandados.join(' ').toLowerCase() : (p.demandados || '').toLowerCase();
                return rad.includes(q) || desp.includes(q) || ciu.includes(q) || dem1.includes(q) || dem2.includes(q);
            });
        },
        get filteredActuaciones() {
            const acts = this.selectedProceso?.actuaciones || [];
            if (!this.actuacionSearch.trim()) return acts;
            const q = this.actuacionSearch.toLowerCase().trim();
            return acts.filter(a => {
                const act = (a.actuacion || '').toLowerCase();
                const anot = (a.anotacion || '').toLowerCase();
                const fec = (a.fecha_actuacion || '').toLowerCase();
                return act.includes(q) || anot.includes(q) || fec.includes(q);
            });
        },
        get selectedDemandantes() {
            if (!this.selectedProceso || !this.selectedProceso.demandantes) return [];
            return Array.isArray(this.selectedProceso.demandantes) 
                ? this.selectedProceso.demandantes.filter(Boolean) 
                : [this.selectedProceso.demandantes].filter(Boolean);
        },
        get selectedDemandados() {
            if (!this.selectedProceso || !this.selectedProceso.demandados) return [];
            return Array.isArray(this.selectedProceso.demandados) 
                ? this.selectedProceso.demandados.filter(Boolean) 
                : [this.selectedProceso.demandados].filter(Boolean);
        },

        // Methods
        resetNuevaGestion() {
            this.nueva_gestion = {
                fecha_etapa: '',
                etapa_procesal: '',
                fecha_actividad: '',
                actividad: '',
                soporte: null,
                gestion: '',
            };
        },
        calcularSaldoFila(index) {
            let saldo = parseFloat(this.valor_total_proceso) || 0;
            for (let i = 0; i <= index; i++) {
                saldo -= (parseFloat(this.depositos[i].valor) || 0);
            }
            return saldo;
        },
        addDeposito() {
            this.depositos.push({ 
                fecha_descuento: '', 
                valor: null, 
                fecha_consignacion: '', 
                reportado: false, 
                aplicado: false,
                soporte: null
            });
        },
        removeDeposito(index) {
            this.depositos.splice(index, 1);
        },
        formatMoney(value) {
            if (value === null || isNaN(value)) return '$0';
            return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
        },
        openHistoryModal(history) {
            this.selectedHistory = history;
            this.historyModalOpen = true;
        },
        showSuccessAndReload(message) {
            this.reloadHistories();
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: message,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-2xl shadow-2xl' }
            });
        },
        showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Atención',
                text: message,
                showConfirmButton: true,
                confirmButtonColor: '#E8611A',
                customClass: { popup: 'rounded-2xl shadow-xl' }
            });
        },

        // AJAX Methods
        // Getters y Métodos para Departamentos y Municipios dependientes con buscador
        get filteredDepartamentos() {
            if (!this.deptSearch.trim()) return this.departamentosList;
            const q = this.deptSearch.toLowerCase().trim();
            return this.departamentosList.filter(d => 
                (d.name && d.name.toLowerCase().includes(q)) ||
                (d.dian_code && String(d.dian_code).toLowerCase().includes(q))
            );
        },

        get selectedDepartment() {
            if (!this.s1.departamento) return null;
            return this.departamentosList.find(d => 
                (d.name && d.name.toLowerCase() === this.s1.departamento.toLowerCase()) ||
                (d.dian_code && String(d.dian_code) === String(this.s1.departamento)) ||
                String(d.id) === String(this.s1.departamento)
            ) || null;
        },

        get availableMunicipios() {
            if (!this.selectedDepartment) return [];
            return this.selectedDepartment.municipios || [];
        },

        get filteredMunicipios() {
            if (!this.munSearch.trim()) return this.availableMunicipios;
            const q = this.munSearch.toLowerCase().trim();
            return this.availableMunicipios.filter(m => 
                (m.name && m.name.toLowerCase().includes(q)) ||
                (m.dian_code && String(m.dian_code).toLowerCase().includes(q))
            );
        },

        selectDepartamento(dept) {
            if (this.s1.departamento !== dept.name) {
                this.s1.departamento = dept.name;
                this.s1.municipio = '';
            }
            this.deptOpen = false;
            this.deptSearch = '';
        },

        selectMunicipio(mun) {
            this.s1.municipio = mun.name;
            this.munOpen = false;
            this.munSearch = '';
        },

        async postData(url, data) {
            this.loading = true;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Error en la petición');
                return result;
            } catch (error) {
                this.showError(error.message);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async saveSection1() {
            const data = { ...this.s1, cobro_juridico_id: this.cobro_juridico_id };
            const res = await this.postData('{{ route('cobros-juridicos.saveSection1') }}', data);
            if (res.success) {
                const isNew = !this.cobro_juridico_id;
                if (isNew) {
                    this.cobro_juridico_id = res.cobro_juridico_id;
                    this.s1.no_consecutivo = res.no_consecutivo;
                    this.s1.no_radicado = res.no_radicado;
                    window.history.pushState(null, '', `/cobros-juridicos/${this.cobro_juridico_id}`);
                }
                this.is_section1_locked = true;
                this.showSuccessAndReload('Datos Generales del Proceso guardados con éxito.');
            }
        },

        async saveSection2() {
            if (!this.cobro_juridico_id) return this.showError('Debe guardar la Sección 1 primero.');
            const data = { ...this.s2, cedula: this.s1.cedula, cobro_juridico_id: this.cobro_juridico_id };
            const res = await this.postData('{{ route('cobros-juridicos.saveSection2') }}', data);
            if (res.success) {
                this.is_section2_locked = true;
                this.showSuccessAndReload('Datos Generales guardados con éxito.');
            }
        },

        async saveDepositos() {
            if (!this.cobro_juridico_id) return this.showError('Debe guardar la Sección 1 primero.');
            const data = { 
                depositos: this.depositos, 
                valor_total_proceso: this.valor_total_proceso,
                cobro_juridico_id: this.cobro_juridico_id 
            };
            const res = await this.postData('{{ route('cobros-juridicos.saveDepositos') }}', data);
            if (res.success) {
                this.is_depositos_locked = true;
                this.showSuccessAndReload('Títulos de depósito judicial guardados con éxito.');
            }
        },

        async uploadSoporte(event, index) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                return this.showError('El archivo no debe pesar más de 5MB');
            }

            const formData = new FormData();
            formData.append('file', file);
            
            this.loading = true;
            try {
                const response = await fetch('{{ route('cobros-juridicos.uploadDepositoSoporte') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Error al subir el archivo');
                
                this.depositos[index].soporte = result.path;
            } catch (error) {
                this.showError(error.message);
            } finally {
                this.loading = false;
                event.target.value = '';
            }
        },

        async uploadGestionSoporte(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                return this.showError('El archivo no debe pesar más de 5MB');
            }

            const formData = new FormData();
            formData.append('file', file);
            
            this.loading = true;
            try {
                const response = await fetch('{{ route('cobros-juridicos.uploadGestionSoporte') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Error al subir el archivo');
                
                this.nueva_gestion.soporte = result.path;
            } catch (error) {
                this.showError(error.message);
            } finally {
                this.loading = false;
                event.target.value = '';
            }
        },

        async saveGestion() {
            if (!this.cobro_juridico_id) return this.showError('Debe guardar la Sección 1 primero.');
            if (!this.nueva_gestion.gestion.trim()) return this.showError('Debe escribir el detalle de la gestión.');
            
            const data = { 
                ...this.nueva_gestion, 
                cobro_juridico_id: this.cobro_juridico_id 
            };
            const res = await this.postData('{{ route('cobros-juridicos.saveGestion') }}', data);
            if (res.success) {
                // Agregar al inicio de la lista (más reciente a más antiguo)
                this.gestiones.unshift(res.gestion);
                
                // Actualizar automáticamente los campos en Datos Generales
                if (res.gestion.fecha_etapa) this.s2.fecha_etapa = res.gestion.fecha_etapa;
                if (res.gestion.etapa_procesal) this.s2.etapa_procesal = res.gestion.etapa_procesal;
                if (res.gestion.fecha_actividad) this.s2.fecha_actividad = res.gestion.fecha_actividad;
                if (res.gestion.actividad) this.s2.actividad = res.gestion.actividad;

                this.resetNuevaGestion();
                this.showSuccessAndReload('Gestión guardada con éxito.');
            }
        },

        async unlockSection(section) {
            const result = await Swal.fire({
                title: '¿Está seguro?',
                text: 'Los cambios quedarán registrados en el historial de auditoría.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8611A',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, editar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;
            
            const data = { section: section };
            const res = await this.postData(`/cobros-juridicos/${this.cobro_juridico_id}/unlock`, data);
            if (res.success) {
                if (section == 1) this.is_section1_locked = false;
                if (section == 2) this.is_section2_locked = false;
                if (section == 3) this.is_depositos_locked = false;
                this.reloadHistories();
            }
        },

        async reloadHistories() {
            if (!this.cobro_juridico_id) return;
            try {
                const response = await fetch(`/cobros-juridicos/${this.cobro_juridico_id}/historial`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (response.ok) {
                    const result = await response.json();
                    this.histories = result.histories;
                }
            } catch (e) {
                console.error('Error cargando historial:', e);
            }
        },

        // Métodos de soporte (Visualización y Descarga)
        getSoporteUrl(path) {
            if (!path) return '';
            return path.startsWith('http') ? path : '/storage/' + path;
        },
        getSoporteFileName(path) {
            if (!path) return 'soporte';
            const parts = path.split('/');
            return parts[parts.length - 1] || 'soporte';
        },
        openSoporteModal(path) {
            if (!path) return;
            const url = this.getSoporteUrl(path);
            this.soporteModalUrl = url;
            this.soporteModalFileName = this.getSoporteFileName(path);
            const lower = path.toLowerCase();
            const isImage = /\.(jpe?g|png|webp|gif|svg|bmp)($|\?)/i.test(lower);
            this.soporteModalType = isImage ? 'image' : 'pdf';
            this.soporteModalOpen = true;
        },
        closeSoporteModal() {
            this.soporteModalOpen = false;
            this.soporteModalUrl = '';
            this.soporteModalFileName = '';
        },
        async downloadSoporte(url, filename) {
            if (!url) return;
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Error al obtener el archivo');
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = filename || url.split('/').pop().split('?')[0] || 'soporte';
                document.body.appendChild(link);
                link.click();
                window.URL.revokeObjectURL(blobUrl);
                document.body.removeChild(link);
            } catch (e) {
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename || 'soporte');
                link.setAttribute('target', '_blank');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        },

        // Métodos para Historial de Consultas Judicial
        onJudicialTabClick() {
            if (!this.consultasJudicialLoaded && this.s1.cedula) {
                this.fetchConsultasJudicial(this.s1.cedula);
            }
        },
        async fetchConsultasJudicial(cedula, forceReload = false) {
            const doc = (cedula || this.s1.cedula || '').trim();
            if (!doc) {
                this.showError('Debe ingresar una cédula en Datos Generales para consultar en Rama Judicial.');
                return;
            }
            if (this.consultasJudicialLoading) return;
            if (this.consultasJudicialLoaded && !forceReload && this.consultasJudicialCedula === doc) return;

            this.consultasJudicialLoading = true;
            this.consultasJudicialError = null;
            this.consultasJudicialCedula = doc;

            try {
                const response = await fetch(`/cobros-juridicos/consulta-judicial/${encodeURIComponent(doc)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const res = await response.json();
                if (res.status === 'success') {
                    this.consultasJudicial = res.data || [];
                } else if (res.status === 'not_found') {
                    this.consultasJudicial = [];
                } else {
                    this.consultasJudicial = [];
                    this.consultasJudicialError = res.message || 'No fue posible consultar los procesos judiciales.';
                }
                this.consultasJudicialLoaded = true;
            } catch (err) {
                console.error('Error consultando rama judicial:', err);
                this.consultasJudicialError = 'Error de conexión con el servicio de Rama Judicial.';
                this.consultasJudicial = [];
                this.consultasJudicialLoaded = true;
            } finally {
                this.consultasJudicialLoading = false;
            }
        },
        openProcesoModal(proceso) {
            console.log('Abriendo modal de proceso:', proceso);
            this.selectedProceso = proceso;
            this.actuacionSearch = '';
            this.procesoModalOpen = true;
        },
        closeProcesoModal() {
            this.procesoModalOpen = false;
            this.selectedProceso = null;
            this.actuacionSearch = '';
        },
        formatDateJudicial(dateStr) {
            if (!dateStr) return '—';
            try {
                const parts = String(dateStr).split('T')[0].split('-');
                if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
                return dateStr;
            } catch (e) { return dateStr; }
        },
        formatDateTimeJudicial(dateStr) {
            if (!dateStr) return '—';
            try {
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                const hours = String(d.getHours()).padStart(2, '0');
                const minutes = String(d.getMinutes()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            } catch (e) { return dateStr; }
        }
    }
}
</script>
@endpush

@endsection
