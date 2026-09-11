@extends('layouts.app')

@section('title', 'Cargues — Reporte Comentarios')
@section('page-title', 'Reporte Comentarios')

@section('content')
<div x-data="cargueComentarios()" class="space-y-5">

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total Comentarios</p>
            <p class="text-2xl font-bold text-gray-800 mt-1" x-text="stats.total_comentarios ?? '{{ number_format($stats['total_comentarios']) }}'"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Cédulas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1" x-text="stats.total_cedulas ?? '{{ number_format($stats['total_cedulas']) }}'"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Gestores</p>
            <p class="text-2xl font-bold text-gray-800 mt-1" x-text="stats.total_gestores ?? '{{ number_format($stats['total_gestores']) }}'"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Empresas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1" x-text="stats.total_empresas ?? '{{ number_format($stats['total_empresas']) }}'"></p>
        </div>
    </div>

    {{-- Upload + Filtros --}}
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 space-y-3">

        {{-- Fila 1: Upload button + búsqueda + efecto --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- Open Upload Modal Button --}}
            <button type="button" @click="abrirModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span>Subir XLSX</span>
            </button>

            @if(auth()->user()->email === 'admin@asesco.com')
            {{-- Botón de vaciar comentarios solo para superadmin --}}
            <button @click="borrarCargue()"
                    :disabled="borrando"
                    title="Eliminar todos los comentarios cargados (solo superadmin)"
                    class="flex items-center gap-1.5 px-3 py-2 bg-red-50 border border-red-200 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 hover:border-red-300 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="!borrando">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </template>
                <template x-if="borrando">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <span x-text="borrando ? 'Vaciando...' : 'Vaciar'"></span>
            </button>
            @endif

            {{-- Divider --}}
            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="buscar" @input.debounce.400ms="cargar(1)"
                       placeholder="Buscar por cédula, nombre, gestor, comentario, empresa..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
            </div>

            {{-- Filter: Efecto --}}
            <select x-model="filtroEfecto" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todos los efectos</option>
                <option value="EN GESTIÓN">En Gestión</option>
                <option value="EN MENSAJE">En Mensaje</option>
                <option value="INTENCIÓN DE PAGO">Intención de Pago</option>
                <option value="NO CONTESTA">No Contesta</option>
                <option value="PROMESA DE PAGO">Promesa de Pago</option>
                <option value="PROMESA ROTA">Promesa Rota</option>
                <option value="RENUENTE">Renuente</option>
            </select>
        </div>

        {{-- Fila 2: Rango de fechas + Gestor + Empresa + Limpiar --}}
        <div class="flex flex-wrap items-center gap-3 pt-1 border-t border-gray-100">
            {{-- Fecha inicio --}}
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 whitespace-nowrap">Desde</label>
                <input type="date" x-model="fechaInicio" @change="cargar(1)"
                       class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
            </div>
            {{-- Fecha fin --}}
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 whitespace-nowrap">Hasta</label>
                <input type="date" x-model="fechaFin" @change="cargar(1)"
                       class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
            </div>

            {{-- Divider --}}
            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

            {{-- Filter: Gestor --}}
            <select x-model="filtroGestor" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todos los gestores</option>
                @foreach($gestores as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>

            {{-- Filter: Empresa --}}
            <select x-model="filtroEmpresa" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todas las empresas</option>
                @foreach($empresas as $e)
                    <option value="{{ $e }}">{{ $e }}</option>
                @endforeach
            </select>

            {{-- Limpiar filtros --}}
            <template x-if="hayFiltros">
                <button @click="limpiarFiltros()"
                        class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar filtros
                </button>
            </template>
        </div>
    </div>

    {{-- Upload result message --}}
    <template x-if="uploadResult">
        <div class="rounded-xl border px-4 py-3 flex items-center gap-3"
             :class="uploadResult.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
            <template x-if="uploadResult.success">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </template>
            <template x-if="!uploadResult.success">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            </template>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium" :class="uploadResult.success ? 'text-green-700' : 'text-red-700'" x-text="uploadResult.message"></p>
                <template x-if="uploadResult.success">
                    <div class="flex items-center gap-4 mt-1">
                        <span class="text-xs text-green-600"><strong x-text="uploadResult.nuevos"></strong> nuevos</span>
                        <span class="text-xs text-gray-500"><strong x-text="uploadResult.duplicados"></strong> duplicados</span>
                        <span class="text-xs text-red-500" x-show="uploadResult.errores > 0"><strong x-text="uploadResult.errores"></strong> errores</span>
                    </div>
                </template>
            </div>
            <button @click="uploadResult = null" class="p-1 rounded hover:bg-black/5 transition-colors cursor-pointer">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>

    {{-- Loading --}}
    <template x-if="cargando">
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="flex flex-col items-center justify-center">
                <div class="relative w-12 h-12 mb-3">
                    <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-asesco-orange border-t-transparent animate-spin"></div>
                </div>
                <p class="text-sm text-gray-500">Cargando comentarios...</p>
            </div>
        </div>
    </template>

    {{-- Table --}}
    <template x-if="!cargando && registros.length > 0">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[1100px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestor</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[300px]">Comentario</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Canal</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Efecto</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acción</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cédula</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in registros" :key="row.id">
                            <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors"
                                :class="i % 2 === 1 ? 'bg-gray-50/30' : ''">
                                <td class="px-4 py-2.5 text-sm text-gray-600 whitespace-nowrap">
                                    <span x-text="formatFecha(row.fecha)"></span>
                                    <span class="text-gray-400 text-xs ml-1" x-text="row.hora || ''"></span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-gray-700 font-medium whitespace-nowrap" x-text="row.gestor"></td>
                                <td class="px-4 py-2.5 text-sm text-gray-600">
                                    <p class="line-clamp-2 leading-relaxed" x-text="row.comentario" :title="row.comentario"></p>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100"
                                          x-text="row.canal || '—'"></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                          :class="{
                                              'bg-amber-50 text-amber-600 border border-amber-100': row.efecto_gestion === 'EN GESTIÓN',
                                              'bg-blue-50 text-blue-600 border border-blue-100': row.efecto_gestion === 'EN MENSAJE',
                                              'bg-emerald-50 text-emerald-600 border border-emerald-100': row.efecto_gestion === 'PROMESA DE PAGO' || row.efecto_gestion === 'INTENCIÓN DE PAGO',
                                              'bg-red-50 text-red-600 border border-red-100': row.efecto_gestion === 'NO CONTESTA' || row.efecto_gestion === 'RENUENTE' || row.efecto_gestion === 'PROMESA ROTA',
                                              'bg-gray-50 text-gray-500 border border-gray-200': !row.efecto_gestion,
                                          }"
                                          x-text="row.efecto_gestion || '—'"></span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-gray-600 whitespace-nowrap" x-text="row.accion_cobro || '—'"></td>
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700 whitespace-nowrap" x-text="row.cedula"></td>
                                <td class="px-4 py-2.5 text-sm text-gray-700 font-medium whitespace-nowrap" x-text="row.nombre"></td>
                                <td class="px-4 py-2.5 text-sm text-gray-600 whitespace-nowrap" x-text="row.empresa"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    Mostrando <strong x-text="paginacion.from || 0"></strong> a <strong x-text="paginacion.to || 0"></strong> de <strong x-text="paginacion.total || 0"></strong> registros
                </p>
                <div class="flex items-center gap-1">
                    <button @click="cargar(paginacion.current_page - 1)" :disabled="!paginacion.prev_page_url"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <span class="px-3 py-1.5 text-xs font-semibold text-asesco-orange">
                        Pág. <span x-text="paginacion.current_page"></span> / <span x-text="paginacion.last_page"></span>
                    </span>
                    <button @click="cargar(paginacion.current_page + 1)" :disabled="!paginacion.next_page_url"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="!cargando && registros.length === 0 && cargado">
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
            </svg>
            <p class="text-gray-400 text-sm" x-text="buscar ? 'No se encontraron comentarios con ese criterio' : 'No hay comentarios cargados aún. Haz clic en Subir XLSX para comenzar.'"></p>
        </div>
    </template>

    {{-- UPLOAD MODAL WITH DRAG & DROP AND PRE-VALIDATION (ESTILO TERCEROS) --}}
    <div x-show="modalOpen"
         x-transition.opacity
         @click.self="cerrarModal()"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs overflow-y-auto"
         style="display: none;">

        <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden max-h-[90vh] flex flex-col my-auto"
             @click.stop>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-orange-100 text-asesco-orange rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Cargar Archivo Excel de Comentarios</h3>
                        <p class="text-xs text-gray-400">Inspección de estructura y validación de campos</p>
                    </div>
                </div>
                <button @click="cerrarModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">

                {{-- Instructions & Template Download --}}
                <div class="bg-blue-50/60 border border-blue-200/80 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Instrucciones del Archivo</h4>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            El archivo Excel debe contener las columnas: <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">FECHA</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">HORA</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">GESTOR</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">COMENTARIO</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">CANAL</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">TIPO_DE_CONTACTO</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">EFECTO_DE_GESTION</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">ACCION_DE_COBRO</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">CEDULA</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">NOMBRE</code> y <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">EMPRESA</code>.
                        </p>
                    </div>
                    <a href="{{ route('cargues.comentarios.plantilla') }}" target="_blank"
                       class="flex items-center gap-1.5 px-3.5 py-2 bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 rounded-lg text-xs font-bold shrink-0 shadow-xs transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar Plantilla
                    </a>
                </div>

                {{-- Drag & Drop Area (Soporta 1 o múltiples archivos) --}}
                <div @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)"
                     :class="isDragging ? 'border-asesco-orange bg-orange-50/50 scale-[1.01]' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-50 hover:border-gray-400'"
                     class="border-2 border-dashed rounded-2xl p-8 text-center transition-all cursor-pointer relative">

                    <input type="file" ref="fileInput" accept=".xlsx,.xls" multiple @change="handleFileSelect($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <template x-if="selectedFiles.length === 0">
                        <div class="space-y-3 pointer-events-none">
                            <div class="w-14 h-14 mx-auto rounded-full bg-orange-100 text-asesco-orange flex items-center justify-center shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-700">Arrastra uno o varios archivos Excel (.xlsx / .xls) aquí</p>
                                <p class="text-xs text-gray-400 mt-1">o haz clic para examinar desde tu equipo (Máximo 30MB por archivo)</p>
                            </div>
                        </div>
                    </template>

                    {{-- Vista si se seleccionó 1 archivo --}}
                    <template x-if="selectedFiles.length === 1">
                        <div class="flex items-center justify-between bg-white border border-gray-200 p-3.5 rounded-xl shadow-xs relative z-20">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="p-2.5 bg-green-100 text-green-700 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="text-left min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="selectedFiles[0].name"></p>
                                    <p class="text-[10px] text-gray-400" x-text="formatBytes(selectedFiles[0].size)"></p>
                                </div>
                            </div>
                            <button type="button" @click.stop="resetFiles()" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Quitar archivo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>

                    {{-- Vista si se seleccionaron múltiples archivos --}}
                    <template x-if="selectedFiles.length > 1">
                        <div class="space-y-2 relative z-20 text-left">
                            <div class="flex items-center justify-between pb-1 border-b border-gray-200">
                                <span class="text-xs font-bold text-gray-700" x-text="selectedFiles.length + ' archivos seleccionados'"></span>
                                <button type="button" @click.stop="resetFiles()" class="text-xs font-semibold text-red-500 hover:text-red-700 cursor-pointer">
                                    Limpiar todos
                                </button>
                            </div>
                            <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1">
                                <template x-for="(file, idx) in selectedFiles" :key="file.name + '_' + idx">
                                    <div class="flex items-center justify-between bg-white border border-gray-200 px-3 py-2 rounded-lg text-xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-medium text-gray-800 truncate" x-text="file.name"></span>
                                            <span class="text-gray-400 text-[10px]" x-text="'(' + formatBytes(file.size) + ')'"></span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <template x-if="validations[idx] && validations[idx].valid">
                                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="validations[idx].validas + ' filas ✓'"></span>
                                            </template>
                                            <template x-if="validations[idx] && !validations[idx].valid">
                                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100" x-text="validations[idx].errores.length + ' errores ✗'"></span>
                                            </template>
                                            <button type="button" @click.stop="quitarArchivo(idx)" class="text-gray-400 hover:text-red-500 p-1 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Pre-Validation Display (Spinner) --}}
                <template x-if="validating">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
                        <div class="w-8 h-8 mx-auto border-3 border-asesco-orange border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-xs font-semibold text-gray-600 mt-3" x-text="validatingMessage || 'Revisando estructura y validando registros del Excel...'"></p>
                    </div>
                </template>

                {{-- Pre-Validation Display (Resultados para 1 archivo) --}}
                <template x-if="!validating && selectedFiles.length === 1 && validationResult">
                    <div class="space-y-3">
                        {{-- Valid Success Box --}}
                        <template x-if="validationResult.valid">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-2">
                                <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    ¡Archivo verificado correctamente!
                                </div>
                                <p class="text-xs text-emerald-700">El sistema ha revisado los campos y todos los registros son válidos. Listo para procesar.</p>
                                <div class="flex flex-wrap items-center gap-3 pt-1 text-xs">
                                    <span class="bg-white border border-emerald-200 text-emerald-800 font-bold px-2.5 py-1 rounded-md">Total Comentarios: <strong x-text="validationResult.total_filas"></strong></span>
                                    <span class="bg-emerald-100 text-emerald-900 font-bold px-2.5 py-1 rounded-md">Cédulas: <strong x-text="validationResult.stats.cedulas"></strong></span>
                                    <span class="bg-indigo-100 text-indigo-900 font-bold px-2.5 py-1 rounded-md">Gestores: <strong x-text="validationResult.stats.gestores"></strong></span>
                                    <span class="bg-purple-100 text-purple-900 font-bold px-2.5 py-1 rounded-md">Empresas: <strong x-text="validationResult.stats.empresas"></strong></span>
                                </div>
                            </div>
                        </template>

                        {{-- Invalid Error Box --}}
                        <template x-if="!validationResult.valid">
                            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-rose-800 font-bold text-xs">
                                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Se encontraron errores en el archivo Excel
                                    </div>
                                    <span class="bg-rose-100 text-rose-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full" x-text="validationResult.errores.length + ' error(es)'"></span>
                                </div>
                                <p class="text-xs text-rose-700" x-text="validationResult.message || 'Corrige los siguientes errores en el archivo Excel para poder procesar la importación.'"></p>

                                {{-- Errors List Table --}}
                                <template x-if="validationResult.errores && validationResult.errores.length > 0">
                                    <div class="max-h-48 overflow-y-auto border border-rose-200 rounded-lg bg-white">
                                        <table class="w-full text-xs text-left">
                                            <thead class="bg-rose-100/50 text-rose-900 font-bold uppercase text-[10px] sticky top-0">
                                                <tr>
                                                    <th class="px-3 py-2 w-16 text-center">FILA #</th>
                                                    <th class="px-3 py-2">CÉDULA</th>
                                                    <th class="px-3 py-2">CAMPO</th>
                                                    <th class="px-3 py-2">DESCRIPCIÓN DEL ERROR</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-rose-100 text-gray-700">
                                                <template x-for="err in validationResult.errores" :key="err.fila + '_' + err.campo">
                                                    <tr class="hover:bg-rose-50/40 transition-colors">
                                                        <td class="px-3 py-1.5 font-bold text-center text-rose-600" x-text="'Fila ' + err.fila"></td>
                                                        <td class="px-3 py-1.5 font-mono text-[11px]" x-text="err.cedula"></td>
                                                        <td class="px-3 py-1.5 font-semibold text-gray-800" x-text="err.campo"></td>
                                                        <td class="px-3 py-1.5 text-rose-600 font-medium" x-text="err.error"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Pre-Validation Display (Resumen para múltiples archivos) --}}
                <template x-if="!validating && selectedFiles.length > 1 && totalMultiStats">
                    <div class="space-y-3">
                        <template x-if="totalMultiStats.allValid">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-2">
                                <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    ¡Todos los archivos fueron verificados exitosamente!
                                </div>
                                <p class="text-xs text-emerald-700">Se procesarán <strong x-text="selectedFiles.length"></strong> archivos con un total estimado de <strong x-text="totalMultiStats.totalFilas"></strong> comentarios.</p>
                            </div>
                        </template>
                        <template x-if="!totalMultiStats.allValid">
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-800">
                                <p class="font-bold">⚠️ Algunos archivos contienen errores.</p>
                                <p class="mt-1">Revisa el indicador de cada archivo arriba. Puedes quitar con la "x" los archivos que tengan errores o corregirlos en tu equipo para procesar.</p>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Progreso de importación multi-archivo --}}
                <template x-if="importing">
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center space-y-2">
                        <div class="w-7 h-7 mx-auto border-3 border-asesco-orange border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-xs font-bold text-gray-800" x-text="importProgressText || 'Importando comentarios...'"></p>
                        <p class="text-[11px] text-gray-500">Por favor, espera un momento mientras se insertan y verifican los duplicados.</p>
                    </div>
                </template>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button type="button" @click="cerrarModal()" :disabled="importing" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold rounded-lg transition-colors cursor-pointer disabled:opacity-50">
                    Cancelar
                </button>
                <button type="button" @click="procesarImportacion()"
                        :disabled="!puedeProcesar"
                        :class="(!puedeProcesar) ? 'opacity-40 cursor-not-allowed bg-gray-400 text-white' : 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20 hover:shadow-lg transition-all cursor-pointer'"
                        class="flex items-center gap-2 px-5 py-2 text-xs font-bold rounded-lg">
                    <template x-if="importing">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="importing ? 'Procesando e Importando...' : (selectedFiles.length > 1 ? 'Procesar ' + selectedFiles.length + ' Archivos' : 'Procesar e Importar')"></span>
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function cargueComentarios() {
    return {
        registros: [],
        paginacion: {},
        buscar: '',
        filtroEfecto: '',
        filtroGestor: '',
        filtroEmpresa: '',
        fechaInicio: '',
        fechaFin: '',
        cargando: false,
        cargado: false,
        uploadResult: null,
        borrando: false,

        // Estadísticas reactivas
        stats: {
            total_comentarios: '{{ number_format($stats['total_comentarios']) }}',
            total_cedulas: '{{ number_format($stats['total_cedulas']) }}',
            total_gestores: '{{ number_format($stats['total_gestores']) }}',
            total_empresas: '{{ number_format($stats['total_empresas']) }}',
        },

        // Modal Drag & Drop state
        modalOpen: false,
        isDragging: false,
        selectedFiles: [],
        validating: false,
        validatingMessage: '',
        validationResult: null, // Para 1 archivo
        validations: {},       // Para múltiples archivos: index => data
        importing: false,
        importProgressText: '',

        get hayFiltros() {
            return this.buscar || this.filtroEfecto || this.filtroGestor || this.filtroEmpresa || this.fechaInicio || this.fechaFin;
        },

        get puedeProcesar() {
            if (this.selectedFiles.length === 0 || this.validating || this.importing) return false;
            if (this.selectedFiles.length === 1) {
                return this.validationResult && this.validationResult.valid;
            }
            // Para múltiples archivos: que todos los archivos tengan validación y sean válidos
            if (Object.keys(this.validations).length !== this.selectedFiles.length) return false;
            return Object.values(this.validations).every(v => v && v.valid);
        },

        get totalMultiStats() {
            if (this.selectedFiles.length <= 1) return null;
            const vals = Object.values(this.validations);
            if (vals.length === 0) return null;
            const allValid = vals.length === this.selectedFiles.length && vals.every(v => v && v.valid);
            let totalFilas = 0;
            vals.forEach(v => { if (v && v.validas) totalFilas += v.validas; });
            return { allValid, totalFilas };
        },

        init() {
            this.cargar(1);
        },

        abrirModal() {
            this.modalOpen = true;
            this.resetFiles();
        },

        cerrarModal() {
            if (this.importing) return;
            this.modalOpen = false;
            this.resetFiles();
        },

        resetFiles() {
            this.selectedFiles = [];
            this.validationResult = null;
            this.validations = {};
            this.validating = false;
            this.validatingMessage = '';
            this.isDragging = false;
            this.importing = false;
            this.importProgressText = '';
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        quitarArchivo(idx) {
            this.selectedFiles.splice(idx, 1);
            delete this.validations[idx];
            // Re-indexar validaciones
            const newValidations = {};
            this.selectedFiles.forEach((file, i) => {
                if (this.validations[i]) newValidations[i] = this.validations[i];
            });
            this.validations = newValidations;
            if (this.selectedFiles.length === 1) {
                this.validationResult = this.validations[0] || null;
            }
        },

        handleFileSelect(event) {
            const files = Array.from(event.target.files || []);
            if (files.length > 0) {
                this.establecerArchivos(files);
            }
        },

        handleDrop(event) {
            this.isDragging = false;
            const files = Array.from(event.dataTransfer.files || []);
            const validFiles = files.filter(f => f.name.endsWith('.xlsx') || f.name.endsWith('.xls'));
            if (validFiles.length > 0) {
                this.establecerArchivos(validFiles);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formato inválido',
                    text: 'Solo se permiten archivos de hojas de cálculo Excel (.xlsx o .xls).',
                    confirmButtonColor: '#E8611A',
                });
            }
        },

        formatBytes(bytes, decimals = 2) {
            if (!bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },

        async establecerArchivos(files) {
            this.selectedFiles = files;
            this.validationResult = null;
            this.validations = {};

            if (files.length === 1) {
                this.validating = true;
                this.validatingMessage = 'Revisando estructura y validando registros del Excel...';
                try {
                    const res = await this.validarArchivo(files[0]);
                    this.validationResult = res;
                    this.validations[0] = res;
                } finally {
                    this.validating = false;
                }
            } else {
                this.validating = true;
                for (let i = 0; i < files.length; i++) {
                    this.validatingMessage = `Validando archivo ${i + 1} de ${files.length} (${files[i].name})...`;
                    const res = await this.validarArchivo(files[i]);
                    this.validations[i] = res;
                }
                this.validating = false;
            }
        },

        async validarArchivo(file) {
            const formData = new FormData();
            formData.append('archivo', file);

            try {
                const res = await fetch('{{ route("cargues.comentarios.validar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                return await res.json();
            } catch (e) {
                return {
                    valid: false,
                    message: 'Error de conexión: ' + e.message,
                    errores: [{ fila: 1, cedula: '-', campo: 'Conexión', error: e.message }]
                };
            }
        },

        async procesarImportacion() {
            if (!this.puedeProcesar) return;

            this.importing = true;
            this.uploadResult = null;

            let totalNuevos = 0;
            let totalDuplicados = 0;
            let totalErrores = 0;
            let erroresOcurridos = [];

            try {
                for (let i = 0; i < this.selectedFiles.length; i++) {
                    const file = this.selectedFiles[i];
                    this.importProgressText = `Procesando archivo ${i + 1} de ${this.selectedFiles.length} (${file.name})...`;

                    const formData = new FormData();
                    formData.append('archivo', file);

                    const res = await fetch('{{ route("cargues.comentarios.importar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await res.json();
                    if (data.success) {
                        totalNuevos += (data.nuevos || 0);
                        totalDuplicados += (data.duplicados || 0);
                        totalErrores += (data.errores || 0);
                        if (data.stats) {
                            this.stats = data.stats;
                        }
                    } else {
                        erroresOcurridos.push(`${file.name}: ${data.message || 'Error desconocido'}`);
                    }
                }

                this.cerrarModal();

                const mensajeFinal = `Importación completada: ${totalNuevos} nuevos comentarios importados, ${totalDuplicados} duplicados omitidos, ${totalErrores} con errores.`;

                this.uploadResult = {
                    success: erroresOcurridos.length === 0,
                    message: mensajeFinal,
                    nuevos: totalNuevos,
                    duplicados: totalDuplicados,
                    errores: totalErrores,
                };

                Swal.fire({
                    icon: erroresOcurridos.length === 0 ? 'success' : 'warning',
                    title: '¡Importación Finalizada!',
                    text: mensajeFinal,
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    customClass: { popup: 'rounded-2xl shadow-2xl' }
                });

                this.cargar(1);
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error durante la importación',
                    text: e.message,
                    confirmButtonColor: '#E8611A',
                });
            } finally {
                this.importing = false;
            }
        },

        async cargar(page = 1) {
            this.cargando = true;
            try {
                const params = new URLSearchParams();
                params.set('page', page);
                if (this.buscar) params.set('buscar', this.buscar);
                if (this.filtroEfecto) params.set('efecto', this.filtroEfecto);
                if (this.filtroGestor) params.set('gestor', this.filtroGestor);
                if (this.filtroEmpresa) params.set('empresa', this.filtroEmpresa);
                if (this.fechaInicio) params.set('fecha_inicio', this.fechaInicio);
                if (this.fechaFin) params.set('fecha_fin', this.fechaFin);

                const res = await fetch(`{{ route('cargues.comentarios.listar') }}?${params}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.registros = data.data;
                this.paginacion = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    from: data.from,
                    to: data.to,
                    total: data.total,
                    prev_page_url: data.prev_page_url,
                    next_page_url: data.next_page_url,
                };
            } catch (e) {
                console.error('Error cargando comentarios:', e);
            } finally {
                this.cargando = false;
                this.cargado = true;
            }
        },

        limpiarFiltros() {
            this.buscar = '';
            this.filtroEfecto = '';
            this.filtroGestor = '';
            this.filtroEmpresa = '';
            this.fechaInicio = '';
            this.fechaFin = '';
            this.cargar(1);
        },

        async borrarCargue() {
            if (!confirm('⚠️ ¿Estás seguro de que deseas eliminar TODOS los comentarios cargados?\n\nEsta acción no se puede deshacer. Podrás volver a subir comentarios cuando desees.')) return;

            this.borrando = true;
            this.uploadResult = null;
            try {
                const res = await fetch('{{ route('cargues.comentarios.borrar') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                this.uploadResult = data;
                if (data.success) {
                    this.registros = [];
                    this.paginacion = {};
                    this.stats = {
                        total_comentarios: '0',
                        total_cedulas: '0',
                        total_gestores: '0',
                        total_empresas: '0',
                    };
                    this.cargado = true;
                }
            } catch (e) {
                this.uploadResult = { success: false, message: 'Error de conexión: ' + e.message };
            } finally {
                this.borrando = false;
            }
        },

        formatFecha(fecha) {
            if (!fecha) return '—';
            const d = new Date(fecha);
            if (isNaN(d.getTime())) return '—';
            return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC' });
        },
    };
}
</script>
@endpush
@endsection
