@extends('layouts.app')

@section('title', 'Cargues — Asignación Terceros')
@section('page-title', 'Asignación Terceros')

@section('content')
<div x-data="cargueTelefonos()" class="space-y-5">

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total Registros</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_registros']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Titulares</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_titulares']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Terceros</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_terceros']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Empresas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_empresas']) }}</p>
        </div>
    </div>

    {{-- Upload + Search bar --}}
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Open Upload Modal Button --}}
            <button type="button" @click="abrirModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span>Subir XLSX</span>
            </button>

            {{-- Divider --}}
            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="buscar" @input.debounce.400ms="cargar(1)"
                       placeholder="Buscar por cédula, nombre, empresa, teléfono..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
            </div>

            {{-- Filter: Calidad --}}
            <select x-model="filtroCalidad" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todas las calidades</option>
                <option value="TT">Titular (TT)</option>
                <option value="CD">Codeudor (CD)</option>
            </select>

            {{-- Filter: Tipo dato --}}
            <select x-model="filtroTipo" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todos los datos</option>
                <option value="celular">Celular</option>
                <option value="fijo">Fijo</option>
                <option value="correo">Correo</option>
            </select>

            {{-- Filter: Tipo Cartera --}}
            <select x-model="filtroCartera" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todos los tipos de cartera</option>
                <option value="Vigente">Vigente</option>
                <option value="Castigada">Castigada</option>
            </select>
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
                <p class="text-sm text-gray-500">Cargando registros...</p>
            </div>
        </div>
    </template>

    {{-- Table --}}
    <template x-if="!cargando && registros.length > 0">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Referencia</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cédula Tercero</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre Tercero</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Calidad</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dato</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cartera</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in registros" :key="row.id">
                            <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors"
                                :class="i % 2 === 1 ? 'bg-gray-50/30' : ''">
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700" x-text="row.referencia"></td>
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700" x-text="row.cedula_tercero"></td>
                                <td class="px-4 py-2.5 text-sm text-gray-700 font-medium" x-text="row.nombre_tercero"></td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                          :class="row.calidad === 'TT' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-purple-50 text-purple-600 border border-purple-100'"
                                          x-text="row.calidad === 'TT' ? 'Titular' : 'Codeudor'"></span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-gray-600" x-text="row.empresa"></td>
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700" x-text="row.dato"></td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                          :class="{
                                              'bg-green-50 text-green-600 border border-green-100': row.tipo_dato === 'celular',
                                              'bg-amber-50 text-amber-600 border border-amber-100': row.tipo_dato === 'fijo',
                                              'bg-sky-50 text-sky-600 border border-sky-100': row.tipo_dato === 'correo',
                                          }"
                                          x-text="row.tipo_dato"></span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <template x-if="row.tipo_cartera">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                              :class="row.tipo_cartera === 'Vigente' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-purple-50 text-purple-700 border border-purple-200'"
                                              x-text="row.tipo_cartera"></span>
                                    </template>
                                    <template x-if="!row.tipo_cartera">
                                        <span class="text-gray-300 italic text-[10px]">—</span>
                                    </template>
                                </td>
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
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400 text-sm" x-text="buscar ? 'No se encontraron registros con ese criterio de búsqueda' : 'No hay registros cargados aún. Sube un archivo XLSX para comenzar.'"></p>
        </div>
    </template>

    {{-- UPLOAD MODAL WITH DRAG & DROP AND PRE-VALIDATION --}}
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
                            <h3 class="text-base font-bold text-gray-800">Cargar Archivo Excel de Terceros</h3>
                            <p class="text-xs text-gray-400">Inspección de estructura y validación de tipo de cartera</p>
                        </div>
                    </div>
                    <button @click="cerrarModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">

                    {{-- Instructions & Template Download --}}
                    <div class="bg-blue-50/60 border border-blue-200/80 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Instrucciones del Archivo</h4>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                El archivo Excel debe contener las columnas: <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">REFERENCIA_UNICA_CC_TT</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">CEDULA_TERCERO</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">NOMBRE_TERCERO</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">CALIDAD_DEL_TERCERO</code> (TT o CD), <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">EMPRESA</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">DATO</code>, <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">TIPO_DE_DATO</code> (celular/fijo/correo) y <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-900 font-mono">TIPO_CARTERA</code> (Vigente o Castigada).
                            </p>
                        </div>
                        <a href="{{ route('cargues.telefonos.plantilla') }}" target="_blank"
                           class="flex items-center gap-1.5 px-3.5 py-2 bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 rounded-lg text-xs font-bold shrink-0 shadow-xs transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Descargar Plantilla
                        </a>
                    </div>

                    {{-- Drag & Drop Area --}}
                    <div @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         :class="isDragging ? 'border-asesco-orange bg-orange-50/50 scale-[1.01]' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-50 hover:border-gray-400'"
                         class="border-2 border-dashed rounded-2xl p-8 text-center transition-all cursor-pointer relative">

                        <input type="file" ref="fileInput" accept=".xlsx,.xls" @change="handleFileSelect($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <template x-if="!selectedFile">
                            <div class="space-y-3 pointer-events-none">
                                <div class="w-14 h-14 mx-auto rounded-full bg-orange-100 text-asesco-orange flex items-center justify-center shadow-inner">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">Arrastra tu archivo Excel (.xlsx / .xls) aquí</p>
                                    <p class="text-xs text-gray-400 mt-1">o haz clic para examinar desde tu equipo (Máximo 10MB)</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedFile">
                            <div class="flex items-center justify-between bg-white border border-gray-200 p-3.5 rounded-xl shadow-xs relative z-20">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="p-2.5 bg-green-100 text-green-700 rounded-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="text-left min-w-0">
                                        <p class="text-xs font-bold text-gray-800 truncate" x-text="selectedFile.name"></p>
                                        <p class="text-[10px] text-gray-400" x-text="formatBytes(selectedFile.size)"></p>
                                    </div>
                                </div>
                                <button type="button" @click.stop="resetFile()" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Cambiar archivo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Pre-Validation Display --}}
                    <template x-if="validating">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
                            <div class="w-8 h-8 mx-auto border-3 border-asesco-orange border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-xs font-semibold text-gray-600 mt-3">Revisando estructura y validando registros del Excel...</p>
                        </div>
                    </template>

                    <template x-if="!validating && validationResult">
                        <div class="space-y-3">
                            {{-- Valid Success Box --}}
                            <template x-if="validationResult.valid">
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-2">
                                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        ¡Archivo verificado correctamente!
                                    </div>
                                    <p class="text-xs text-emerald-700">El sistema ha revisado los campos y todos los registros son válidos. Listo para procesar.</p>
                                    <div class="flex flex-wrap items-center gap-4 pt-1 text-xs">
                                        <span class="bg-white border border-emerald-200 text-emerald-800 font-bold px-2.5 py-1 rounded-md">Total Filas: <strong x-text="validationResult.total_filas"></strong></span>
                                        <span class="bg-emerald-100 text-emerald-900 font-bold px-2.5 py-1 rounded-md">Vigente: <strong x-text="validationResult.stats.vigente"></strong></span>
                                        <span class="bg-purple-100 text-purple-900 font-bold px-2.5 py-1 rounded-md">Castigada: <strong x-text="validationResult.stats.castigada"></strong></span>
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
                                                        <th class="px-3 py-2">REFERENCIA / CÉDULA</th>
                                                        <th class="px-3 py-2">CAMPO</th>
                                                        <th class="px-3 py-2">DESCRIPCIÓN DEL ERROR</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-rose-100 text-gray-700">
                                                    <template x-for="err in validationResult.errores" :key="err.fila + '_' + err.campo">
                                                        <tr class="hover:bg-rose-50/40 transition-colors">
                                                            <td class="px-3 py-1.5 font-bold text-center text-rose-600" x-text="'Fila ' + err.fila"></td>
                                                            <td class="px-3 py-1.5 font-mono text-[11px]" x-text="err.referencia + ' / ' + err.cedula"></td>
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

                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="button" @click="cerrarModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button" @click="procesarImportacion()"
                            :disabled="!selectedFile || validating || (validationResult && !validationResult.valid) || importing"
                            :class="(!selectedFile || validating || (validationResult && !validationResult.valid) || importing) ? 'opacity-40 cursor-not-allowed bg-gray-400 text-white' : 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20 hover:shadow-lg transition-all cursor-pointer'"
                            class="flex items-center gap-2 px-5 py-2 text-xs font-bold rounded-lg">
                        <template x-if="importing">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <span x-text="importing ? 'Procesando e Importando...' : 'Procesar e Importar'"></span>
                    </button>
                </div>
            </div>
        </div>

</div>

@push('scripts')
<script>
function cargueTelefonos() {
    return {
        registros: [],
        paginacion: {},
        buscar: '',
        filtroCalidad: '',
        filtroTipo: '',
        filtroCartera: '',
        cargando: false,
        cargado: false,
        uploadResult: null,

        // Modal Drag & Drop state
        modalOpen: false,
        isDragging: false,
        selectedFile: null,
        validating: false,
        validationResult: null,
        importing: false,

        init() {
            this.cargar(1);
        },

        abrirModal() {
            this.modalOpen = true;
            this.resetFile();
        },

        cerrarModal() {
            this.modalOpen = false;
            this.resetFile();
        },

        resetFile() {
            this.selectedFile = null;
            this.validationResult = null;
            this.validating = false;
            this.isDragging = false;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
                this.validarArchivo();
            }
        },

        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls'))) {
                this.selectedFile = file;
                this.validarArchivo();
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

        async validarArchivo() {
            if (!this.selectedFile) return;
            this.validating = true;
            this.validationResult = null;

            const formData = new FormData();
            formData.append('archivo', this.selectedFile);

            try {
                const res = await fetch('{{ route("cargues.telefonos.validar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await res.json();
                this.validationResult = data;
            } catch (e) {
                this.validationResult = {
                    valid: false,
                    message: 'Error al comunicarse con el servidor: ' + e.message,
                    errores: [{ fila: 1, referencia: '-', cedula: '-', campo: 'Conexión', error: e.message }]
                };
            } finally {
                this.validating = false;
            }
        },

        async procesarImportacion() {
            if (!this.selectedFile || !this.validationResult || !this.validationResult.valid || this.importing) return;

            this.importing = true;
            this.uploadResult = null;

            const formData = new FormData();
            formData.append('archivo', this.selectedFile);

            try {
                const res = await fetch('{{ route("cargues.telefonos.importar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await res.json();
                this.uploadResult = data;

                if (data.success) {
                    this.cerrarModal();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Importación Completada!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        customClass: { popup: 'rounded-2xl shadow-2xl' }
                    });
                    this.cargar(1);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la importación',
                        text: data.message || 'Ocurrió un error al procesar el archivo.',
                        confirmButtonColor: '#E8611A',
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
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
                if (this.filtroCalidad) params.set('calidad', this.filtroCalidad);
                if (this.filtroTipo) params.set('tipo_dato', this.filtroTipo);
                if (this.filtroCartera) params.set('tipo_cartera', this.filtroCartera);

                const res = await fetch(`{{ route('cargues.telefonos.listar') }}?${params}`, {
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
                console.error('Error cargando registros:', e);
            } finally {
                this.cargando = false;
                this.cargado = true;
            }
        },
    };
}
</script>
@endpush
@endsection
