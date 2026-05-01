@extends('layouts.app')

@section('title', 'Gestiones')
@section('page-title', 'Gestiones')

@section('content')
<div x-data="consultaPage()" class="space-y-5">

    {{-- Compact search bar --}}
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
        <div class="flex items-center gap-3">
            {{-- Search input --}}
            <div class="relative w-64 shrink-0">
                <input id="cedula" type="text" x-model="cedula" @keydown.enter.prevent="consultar()"
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                       :disabled="loading" inputmode="numeric" pattern="[0-9]*">
            </div>

            {{-- Search button (icon only) --}}
            <button @click="consultar()" :disabled="loading || !cedula.trim()"
                    class="flex items-center justify-center w-9 h-9 shrink-0 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    title="Consultar">
                <template x-if="!loading">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
            </button>

            {{-- Person name + empresa (inline, from terceros) --}}
            <div x-show="personName" x-transition class="flex items-center gap-2 min-w-0" style="display:none">
                <div class="w-px h-6 bg-gray-200 shrink-0"></div>
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="text-xs font-medium text-gray-400 shrink-0">Nombre:</span>
                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="personName"></p>
                </div>
                <template x-if="personEmpresa">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <div class="w-px h-6 bg-gray-200 shrink-0"></div>
                        <span class="text-xs font-medium text-gray-400 shrink-0">Empresa:</span>
                        <p class="text-sm font-semibold text-gray-800 truncate" x-text="personEmpresa"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Loading state --}}
    <template x-if="loading">
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="flex flex-col items-center justify-center">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-asesco-orange border-t-transparent animate-spin"></div>
                </div>
                <p class="text-sm font-medium text-gray-700">Consultando sistemas EPS...</p>
                <p class="text-xs text-gray-400 mt-1">Esto puede tomar unos segundos</p>
            </div>
        </div>
    </template>

    {{-- Results with tabs --}}
    <template x-if="results && !loading">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            {{-- Pill-style tab bar --}}
            <div class="flex items-center gap-1.5 px-4 py-3 bg-gray-50/80 border-b border-gray-200">
                <button @click="activeTab = 'localizacion'"
                        :class="activeTab === 'localizacion'
                            ? 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Actualización
                </button>
                <button @click="activeTab = 'comentarios'; cargarComentarios()"
                        :class="activeTab === 'comentarios'
                            ? 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Comentarios
                </button>
                <button @click="activeTab = 'telefonos'; cargarTelefonos()"
                        :class="activeTab === 'telefonos'
                            ? 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Teléfonos
                </button>
            </div>

            {{-- Tab: Localización --}}
            <div x-show="activeTab === 'localizacion'">

                {{-- No results --}}
                <template x-if="results.found === 0">
                    <div class="p-10 text-center">
                        <svg class="w-14 h-14 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No se encontraron registros para esta cédula en ningún sistema</p>
                    </div>
                </template>

                {{-- Table --}}
                <template x-if="results.found > 0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[920px]">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Departamento</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Municipio</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Correo</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de Consulta</th>
                                    <th class="w-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, i) in flatRecords" :key="row._systemSlug + '-' + row._recordIdx">
                                    <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors"
                                        :class="i % 2 === 1 ? 'bg-gray-50/30' : ''">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-700" x-text="row._systemName"></span>
                                                <span x-show="row._totalInSystem > 1"
                                                      class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono bg-orange-50 text-orange-500 border border-orange-100">
                                                    #<span x-text="row._recordIdx + 1"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getDepartamento(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getMunicipio(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getTelefono(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getCorreo(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-500" x-text="row._consultedAt || '—'"></td>
                                        <td class="px-4 py-3">
                                            <button @click="openModal(row)"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-asesco-orange hover:bg-orange-50 transition-colors"
                                                    title="Ver detalle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.58-3.007-9.964-7.178z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            {{-- Tab: Comentarios --}}
            <div x-show="activeTab === 'comentarios'">

                {{-- Loading comentarios --}}
                <template x-if="loadingComentarios">
                    <div class="p-8 flex flex-col items-center justify-center">
                        <div class="relative w-10 h-10 mb-3">
                            <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-asesco-orange border-t-transparent animate-spin"></div>
                        </div>
                        <p class="text-xs text-gray-400">Cargando comentarios...</p>
                    </div>
                </template>

                {{-- Comentarios content --}}
                <template x-if="!loadingComentarios">
                    <div class="flex flex-col" style="height: calc(100vh - 310px); min-height: 400px;">

                        {{-- Lista de comentarios (scrollable, ocupa todo el espacio) --}}
                        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-2" x-ref="comentariosList">
                            <template x-if="comentarios.length === 0">
                                <div class="h-full flex flex-col items-center justify-center py-12">
                                    <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                                    </svg>
                                    <p class="text-sm text-gray-400">No hay comentarios para esta cédula</p>
                                    <p class="text-xs text-gray-300 mt-1">Agrega el primer comentario de gestión abajo</p>
                                </div>
                            </template>

                            <template x-for="(c, i) in comentarios" :key="c.id">
                                <div class="rounded-lg border border-gray-100 bg-gray-50/50 px-3.5 py-2.5 hover:bg-orange-50/20 transition-colors">
                                    <div class="flex items-center justify-between gap-3 mb-1.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-asesco-orange to-asesco-coral flex items-center justify-center text-white text-[9px] font-bold shrink-0">
                                                <span x-text="c.gestor ? c.gestor.charAt(0) : '?'"></span>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700 truncate" x-text="c.gestor"></span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-[10px] text-gray-400" x-text="formatFechaComentario(c.fecha) + ' ' + (c.hora || '')"></span>
                                        </div>
                                    </div>
                                    <p class="text-[12.5px] text-gray-600 leading-relaxed mb-1.5" x-text="c.comentario"></p>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span x-show="c.efecto_gestion"
                                              class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase"
                                              :class="{
                                                  'bg-amber-50 text-amber-600 border border-amber-100': c.efecto_gestion && c.efecto_gestion.includes('GESTI'),
                                                  'bg-blue-50 text-blue-600 border border-blue-100': c.efecto_gestion === 'EN MENSAJE',
                                                  'bg-emerald-50 text-emerald-600 border border-emerald-100': c.efecto_gestion && (c.efecto_gestion.includes('PROMESA DE PAGO') || c.efecto_gestion.includes('INTENCI')),
                                                  'bg-red-50 text-red-600 border border-red-100': c.efecto_gestion && (c.efecto_gestion === 'NO CONTESTA' || c.efecto_gestion === 'RENUENTE' || c.efecto_gestion === 'PROMESA ROTA'),
                                              }"
                                              x-text="c.efecto_gestion"></span>
                                        <span x-show="c.accion_cobro"
                                              class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-gray-100 text-gray-500 border border-gray-200"
                                              x-text="c.accion_cobro"></span>
                                        <span x-show="c.tipo_contacto"
                                              class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-indigo-50 text-indigo-500 border border-indigo-100"
                                              x-text="c.tipo_contacto"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Formulario nuevo comentario (siempre abajo) --}}
                        <div class="border-t border-gray-200 bg-gray-50/80 px-4 py-3 shrink-0">
                            <div class="flex items-center gap-1.5 mb-2.5">
                                <svg class="w-3.5 h-3.5 text-asesco-orange shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="text-xs font-semibold text-gray-600">Nuevo comentario</span>
                            </div>

                            {{-- Selects en fila --}}
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-2.5">
                                <select x-model="nuevoComentario.canal"
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all cursor-pointer">
                                    <option value="">Canal...</option>
                                    <option value="GESTOR">Gestor</option>
                                    <option value="CLIENTE">Cliente</option>
                                    <option value="WHATSAPP">WhatsApp</option>
                                    <option value="CORREO">Correo</option>
                                </select>
                                <select x-model="nuevoComentario.tipo_contacto"
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all cursor-pointer">
                                    <option value="">Tipo contacto...</option>
                                    <option value="CONTACTO DIRECTO">Contacto Directo</option>
                                    <option value="CONTACTO INDIRECTO">Contacto Indirecto</option>
                                    <option value="NO CONTACTADO">No Contactado</option>
                                </select>
                                <select x-model="nuevoComentario.efecto_gestion"
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all cursor-pointer">
                                    <option value="">Efecto...</option>
                                    <option value="EN GESTIÓN">En Gestión</option>
                                    <option value="EN MENSAJE">En Mensaje</option>
                                    <option value="INTENCIÓN DE PAGO">Intención de Pago</option>
                                    <option value="NO CONTESTA">No Contesta</option>
                                    <option value="PROMESA DE PAGO">Promesa de Pago</option>
                                    <option value="PROMESA ROTA">Promesa Rota</option>
                                    <option value="RENUENTE">Renuente</option>
                                </select>
                                <select x-model="nuevoComentario.accion_cobro"
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all cursor-pointer">
                                    <option value="">Acción...</option>
                                    <option value="LLAMADA">Llamada</option>
                                    <option value="MENSAJE WPP">Mensaje WPP</option>
                                    <option value="CORREO">Correo</option>
                                    <option value="VISITA">Visita</option>
                                    <option value="SMS">SMS</option>
                                </select>
                            </div>

                            {{-- Textarea + botón enviar --}}
                            <div class="flex items-end gap-2">
                                <textarea x-model="nuevoComentario.comentario"
                                          @keydown.ctrl.enter="guardarComentario()"
                                          placeholder="Escribe el comentario de gestión..."
                                          rows="2"
                                          class="flex-1 px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 resize-none focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all"
                                          :disabled="guardandoComentario"></textarea>
                                <button @click="guardarComentario()"
                                        :disabled="guardandoComentario || !nuevoComentario.comentario.trim() || !nuevoComentario.canal || !nuevoComentario.tipo_contacto || !nuevoComentario.efecto_gestion || !nuevoComentario.accion_cobro"
                                        class="flex items-center justify-center w-9 h-9 shrink-0 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100"
                                        title="Guardar comentario (Ctrl+Enter)">
                                    <template x-if="!guardandoComentario">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                                        </svg>
                                    </template>
                                    <template x-if="guardandoComentario">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1.5">Ctrl+Enter para enviar. Fecha y hora se registran automáticamente.</p>
                        </div>

                    </div>
                </template>
            </div>

            {{-- Tab: Teléfonos --}}
            <div x-show="activeTab === 'telefonos'">

                <template x-if="loadingTelefonos">
                    <div class="p-8 flex flex-col items-center justify-center">
                        <div class="relative w-10 h-10 mb-3">
                            <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-asesco-orange border-t-transparent animate-spin"></div>
                        </div>
                        <p class="text-xs text-gray-400">Cargando datos de contacto...</p>
                    </div>
                </template>

                <template x-if="!loadingTelefonos">
                    <div class="flex flex-col" style="height: calc(100vh - 310px); min-height: 400px;">
                        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-5" x-ref="telefonosList">

                            {{-- Sección Teléfonos --}}
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-asesco-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                    </svg>
                                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Teléfonos</span>
                                    <span class="text-[10px] text-gray-400 font-medium" x-text="'(' + dataTelefonos.length + ')'"></span>
                                </div>
                                <template x-if="dataTelefonos.length === 0">
                                    <p class="text-xs text-gray-400 italic pl-6">Sin teléfonos registrados</p>
                                </template>
                                <template x-if="dataTelefonos.length > 0">
                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-gray-50 border-b border-gray-200">
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Fuente</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Relación</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                                                    <th class="text-center px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">SMS</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Modificado por</th>
                                                    <th class="w-16"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="t in dataTelefonos" :key="t.id">
                                                    <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors" :class="editingId === t.id ? 'bg-orange-50/40' : ''">
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === t.id">
                                                                <select x-model="editForm.fuente" class="w-full px-1.5 py-1 rounded border border-gray-300 text-[11px] bg-white">
                                                                    <option value="">—</option>
                                                                    <option value="EMPRESA">Empresa</option>
                                                                    <option value="ACTUALIZACIÓN">Actualización</option>
                                                                    <option value="AGENTE">Agente</option>
                                                                    <option value="UBICA">Ubica</option>
                                                                    <option value="RECONOCER">Reconocer</option>
                                                                    <option value="GS">GS</option>
                                                                </select>
                                                            </template>
                                                            <template x-if="editingId !== t.id">
                                                                <span class="text-gray-600" x-text="t.fuente || '—'"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === t.id">
                                                                <input type="text" x-model="editForm.dato" class="w-full px-1.5 py-1 rounded border border-gray-300 text-[11px] font-mono bg-white">
                                                            </template>
                                                            <template x-if="editingId !== t.id">
                                                                <span class="font-mono font-medium text-gray-800" x-text="t.dato"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === t.id">
                                                                <input type="text" x-model="editForm.nombre_tercero" class="w-full px-1.5 py-1 rounded border border-gray-300 text-[11px] bg-white">
                                                            </template>
                                                            <template x-if="editingId !== t.id">
                                                                <span class="text-gray-600" x-text="t.nombre_tercero"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === t.id">
                                                                <input type="text" x-model="editForm.calidad" class="w-20 px-1.5 py-1 rounded border border-gray-300 text-[11px] bg-white">
                                                            </template>
                                                            <template x-if="editingId !== t.id">
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase"
                                                                      :class="t.calidad === 'TT' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-purple-50 text-purple-600 border border-purple-100'"
                                                                      x-text="t.calidad"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold"
                                                                  :class="t.tipo_dato === 'celular' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-amber-50 text-amber-600 border border-amber-100'"
                                                                  x-text="t.tipo_dato"></span>
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <button @click="toggleNotificar(t)" class="cursor-pointer" title="Activar/desactivar SMS">
                                                                <svg x-show="t.notificar" class="w-4 h-4 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                                <svg x-show="!t.notificar" class="w-4 h-4 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><circle cx="12" cy="12" r="9"/></svg>
                                                            </button>
                                                        </td>
                                                        <td class="px-3 py-2 text-[10px] text-gray-400 whitespace-nowrap">
                                                            <template x-if="t.modified_by_user">
                                                                <div>
                                                                    <span x-text="t.modified_by_user.name"></span>
                                                                    <br>
                                                                    <span class="text-gray-300" x-text="t.modified_at ? new Date(t.modified_at).toLocaleDateString('es-CO', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'}) : ''"></span>
                                                                </div>
                                                            </template>
                                                        </td>
                                                        <td class="px-2 py-2">
                                                            <template x-if="editingId === t.id">
                                                                <div class="flex items-center gap-1">
                                                                    <button @click="guardarEdicion(t)" class="p-1 rounded hover:bg-green-50 text-green-500 cursor-pointer" title="Guardar">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                    </button>
                                                                    <button @click="editingId = null" class="p-1 rounded hover:bg-red-50 text-red-400 cursor-pointer" title="Cancelar">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                            <template x-if="editingId !== t.id">
                                                                <button @click="iniciarEdicion(t)" class="p-1 rounded hover:bg-orange-50 text-gray-400 hover:text-asesco-orange cursor-pointer" title="Editar">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                                                </button>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>

                            {{-- Sección Correos --}}
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-asesco-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                    </svg>
                                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Correos</span>
                                    <span class="text-[10px] text-gray-400 font-medium" x-text="'(' + dataCorreos.length + ')'"></span>
                                </div>
                                <template x-if="dataCorreos.length === 0">
                                    <p class="text-xs text-gray-400 italic pl-6">Sin correos registrados</p>
                                </template>
                                <template x-if="dataCorreos.length > 0">
                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-gray-50 border-b border-gray-200">
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Fuente</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Correo</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Relación</th>
                                                    <th class="text-center px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Notificar</th>
                                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase tracking-wider">Modificado por</th>
                                                    <th class="w-16"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="c in dataCorreos" :key="c.id">
                                                    <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors" :class="editingId === c.id ? 'bg-orange-50/40' : ''">
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === c.id">
                                                                <select x-model="editForm.fuente" class="w-full px-1.5 py-1 rounded border border-gray-300 text-[11px] bg-white">
                                                                    <option value="">—</option>
                                                                    <option value="EMPRESA">Empresa</option>
                                                                    <option value="ACTUALIZACIÓN">Actualización</option>
                                                                    <option value="AGENTE">Agente</option>
                                                                </select>
                                                            </template>
                                                            <template x-if="editingId !== c.id">
                                                                <span class="text-gray-600" x-text="c.fuente || '—'"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === c.id">
                                                                <input type="text" x-model="editForm.dato" class="w-full px-1.5 py-1 rounded border border-gray-300 text-[11px] font-mono bg-white">
                                                            </template>
                                                            <template x-if="editingId !== c.id">
                                                                <span class="font-mono font-medium text-gray-800" x-text="c.dato"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === c.id">
                                                                <input type="text" x-model="editForm.nombre_tercero" class="w-full px-1.5 py-1 rounded border border-gray-300 text-[11px] bg-white">
                                                            </template>
                                                            <template x-if="editingId !== c.id">
                                                                <span class="text-gray-600" x-text="c.nombre_tercero"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <template x-if="editingId === c.id">
                                                                <input type="text" x-model="editForm.calidad" class="w-20 px-1.5 py-1 rounded border border-gray-300 text-[11px] bg-white">
                                                            </template>
                                                            <template x-if="editingId !== c.id">
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase"
                                                                      :class="c.calidad === 'TT' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-purple-50 text-purple-600 border border-purple-100'"
                                                                      x-text="c.calidad"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <button @click="toggleNotificar(c)" class="cursor-pointer" title="Activar/desactivar notificación">
                                                                <svg x-show="c.notificar" class="w-4 h-4 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                                <svg x-show="!c.notificar" class="w-4 h-4 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><circle cx="12" cy="12" r="9"/></svg>
                                                            </button>
                                                        </td>
                                                        <td class="px-3 py-2 text-[10px] text-gray-400 whitespace-nowrap">
                                                            <template x-if="c.modified_by_user">
                                                                <div>
                                                                    <span x-text="c.modified_by_user.name"></span>
                                                                    <br>
                                                                    <span class="text-gray-300" x-text="c.modified_at ? new Date(c.modified_at).toLocaleDateString('es-CO', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'}) : ''"></span>
                                                                </div>
                                                            </template>
                                                        </td>
                                                        <td class="px-2 py-2">
                                                            <template x-if="editingId === c.id">
                                                                <div class="flex items-center gap-1">
                                                                    <button @click="guardarEdicion(c)" class="p-1 rounded hover:bg-green-50 text-green-500 cursor-pointer" title="Guardar">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                    </button>
                                                                    <button @click="editingId = null" class="p-1 rounded hover:bg-red-50 text-red-400 cursor-pointer" title="Cancelar">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                            <template x-if="editingId !== c.id">
                                                                <button @click="iniciarEdicion(c)" class="p-1 rounded hover:bg-orange-50 text-gray-400 hover:text-asesco-orange cursor-pointer" title="Editar">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                                                </button>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>

                        </div>

                        {{-- Formulario agregar nuevo dato --}}
                        <div class="border-t border-gray-200 bg-gray-50/80 px-4 py-3 shrink-0">
                            <div class="flex items-center gap-1.5 mb-2.5">
                                <svg class="w-3.5 h-3.5 text-asesco-orange shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="text-xs font-semibold text-gray-600">Agregar teléfono o correo</span>
                            </div>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-2">
                                <input type="text" x-model="nuevoDato.dato" placeholder="Número o correo..."
                                       class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all font-mono">
                                <select x-model="nuevoDato.tipo_dato"
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all cursor-pointer">
                                    <option value="">Tipo...</option>
                                    <option value="celular">Celular</option>
                                    <option value="fijo">Fijo</option>
                                    <option value="correo">Correo</option>
                                </select>
                                <select x-model="nuevoDato.fuente"
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all cursor-pointer">
                                    <option value="">Fuente...</option>
                                    <option value="EMPRESA">Empresa</option>
                                    <option value="ACTUALIZACIÓN">Actualización</option>
                                    <option value="AGENTE">Agente</option>
                                    <template x-if="nuevoDato.tipo_dato !== 'correo'">
                                        <option value="UBICA">Ubica</option>
                                    </template>
                                    <template x-if="nuevoDato.tipo_dato !== 'correo'">
                                        <option value="RECONOCER">Reconocer</option>
                                    </template>
                                    <template x-if="nuevoDato.tipo_dato !== 'correo'">
                                        <option value="GS">GS</option>
                                    </template>
                                </select>
                                <input type="text" x-model="nuevoDato.calidad" placeholder="Relación (TT, CD, Esposo...)"
                                       class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all">
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="grid grid-cols-2 gap-2 flex-1">
                                    <input type="text" x-model="nuevoDato.cedula_tercero" placeholder="Cédula del tercero..."
                                           class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all font-mono">
                                    <input type="text" x-model="nuevoDato.nombre_tercero" placeholder="Nombre del tercero..."
                                           class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange transition-all">
                                </div>
                                <label class="flex items-center gap-1.5 cursor-pointer shrink-0">
                                    <input type="checkbox" x-model="nuevoDato.notificar" class="w-3.5 h-3.5 rounded border-gray-300 text-asesco-orange focus:ring-asesco-orange/30 cursor-pointer">
                                    <span class="text-[10px] font-medium text-gray-500" x-text="nuevoDato.tipo_dato === 'correo' ? 'Notificar' : 'SMS'"></span>
                                </label>
                                <button @click="guardarDato()"
                                        :disabled="guardandoDato || !nuevoDato.dato.trim() || !nuevoDato.tipo_dato || !nuevoDato.fuente || !nuevoDato.calidad.trim() || !nuevoDato.cedula_tercero.trim() || !nuevoDato.nombre_tercero.trim()"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-[11px] font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 shrink-0">
                                    <template x-if="!guardandoDato">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    </template>
                                    <template x-if="guardandoDato">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </template>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="!results && !loading">
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-20 h-20 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">Ingresa una cédula para consultar en todos los sistemas EPS</p>
        </div>
    </template>

    {{-- Detail Modal --}}
    <style>
        .modal-overlay-centered {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: fixed !important;
            inset: 0 !important;
            z-index: 9999 !important;
            padding: 16px !important;
            background: rgba(15,23,42,0.55) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
        }
        .modal-overlay-centered[x-cloak],
        .modal-overlay-centered[style*="display: none"] {
            display: none !important;
        }
    </style>
    <template x-teleport="body">
        <div x-show="modal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="modal = false"
             @click.self="modal = false"
             class="modal-overlay-centered">
            {{-- Modal card --}}
            <div x-show="modal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="width:100%;max-width:400px;display:flex;flex-direction:column;border-radius:14px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.35);">
                {{-- Dark header --}}
                <div style="background:#1e2532;padding:14px 18px 12px;flex-shrink:0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:3px;">
                            <svg style="width:11px;height:11px;flex-shrink:0;" fill="none" stroke="#f97316" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                            </svg>
                            <p style="font-size:10px;color:#f97316;text-transform:uppercase;letter-spacing:.08em;font-weight:700;" x-text="modalRecord ? modalRecord._systemName : ''"></p>
                        </div>
                        <p style="font-size:13px;font-weight:600;color:#f1f5f9;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="modalRecord ? (getNombres(modalRecord) + ' ' + getApellidos(modalRecord)).trim() : ''"></p>
                        <p style="font-size:10px;color:#64748b;margin-top:2px;font-family:monospace;" x-text="modalRecord ? getCedulaField(modalRecord) : ''"></p>
                    </div>
                    <button @click="modal = false" style="flex-shrink:0;width:26px;height:26px;border-radius:7px;border:none;background:rgba(255,255,255,0.08);color:#94a3b8;display:flex;align-items:center;justify-content:center;cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div style="flex:1;overflow-y:auto;background:#ffffff;">
                    <template x-if="modalRecord">
                        <div>
                            <template x-for="(value, key) in modalRecord" :key="key">
                                <template x-if="!String(key).startsWith('_') && !isObject(value) && !isArray(value) && hasValue(value)">
                                    <div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:7px 18px;border-bottom:1px solid #f1f5f9;">
                                        <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;flex-shrink:0;width:88px;line-height:1.3;" x-text="formatLabel(key)"></p>
                                        <p style="font-size:11px;color:#1e293b;font-weight:500;text-align:right;word-break:break-all;" x-text="formatValue(value)"></p>
                                    </div>
                                </template>
                            </template>
                            <template x-for="(value, key) in modalRecord" :key="'s-' + key">
                                <template x-if="isObject(value)">
                                    <div>
                                        <div style="padding:5px 18px;background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                                            <p style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.08em;" x-text="formatLabel(key)"></p>
                                        </div>
                                        <template x-for="(subVal, subKey) in value" :key="subKey">
                                            <template x-if="hasValue(subVal)">
                                                <div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:7px 18px;border-bottom:1px solid #f1f5f9;">
                                                    <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;flex-shrink:0;width:88px;line-height:1.3;" x-text="formatLabel(subKey)"></p>
                                                    <p style="font-size:11px;color:#1e293b;font-weight:500;text-align:right;word-break:break-all;" x-text="formatValue(subVal)"></p>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
function consultaPage() {
    return {
        cedula: '',
        loading: false,
        results: null,
        flatRecords: [],
        searchedCedula: '',
        personName: null,
        personEmpresa: null,
        activeTab: 'localizacion',
        modal: false,
        modalRecord: null,
        systemCount: {{ $systemCount }},
        comentarios: [],
        loadingComentarios: false,
        guardandoComentario: false,
        nuevoComentario: { comentario: '', canal: '', tipo_contacto: '', efecto_gestion: '', accion_cobro: '' },
        dataTelefonos: [],
        dataCorreos: [],
        loadingTelefonos: false,
        guardandoDato: false,
        nuevoDato: { dato: '', tipo_dato: '', fuente: '', calidad: '', cedula_tercero: '', nombre_tercero: '', notificar: false },
        editingId: null,
        editForm: {},

        async consultar() {
            const c = this.cedula.trim();
            if (!c || this.loading) return;

            this.loading = true;
            this.results = null;
            this.flatRecords = [];
            this.personName = null;
            this.personEmpresa = null;
            this.activeTab = 'localizacion';
            this.searchedCedula = c;

            try {
                const res = await fetch('{{ route("consultas.consultar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ cedula: c }),
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    alert(err.message || 'Error al consultar');
                    return;
                }

                const data = await res.json();
                // Normalize each result into a records array
                data.results = data.results.map(r => {
                    const rawData = r.data;
                    const records = rawData
                        ? (Array.isArray(rawData) ? rawData : [rawData])
                        : [];
                    return { ...r, records };
                });

                // Flatten all found records for the table
                const now = new Date();
                const consultedAt = now.toLocaleDateString('es-CO', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
                this.flatRecords = data.results
                    .filter(r => r.found && r.records.length > 0)
                    .flatMap(r => r.records.map((rec, idx) => ({
                        ...rec,
                        _systemName: r.name,
                        _systemSlug: r.slug,
                        _recordIdx: idx,
                        _totalInSystem: r.records.length,
                        _consultedAt: consultedAt,
                    })));

                this.results = data;

                // Nombre y empresa desde la tabla de terceros
                if (data.tercero) {
                    this.personName = data.tercero.nombre;
                    this.personEmpresa = data.tercero.empresa;
                } else {
                    // Fallback: extraer de las APIs si no hay tercero
                    this.personName = this.extractPersonName(data.results);
                    this.personEmpresa = null;
                }
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            } finally {
                this.loading = false;
            }
        },

        openModal(row) {
            this.modalRecord = row;
            this.modal = true;
        },

        async cargarComentarios() {
            if (!this.searchedCedula) return;
            this.loadingComentarios = true;
            try {
                const res = await fetch(`/consultas/comentarios/${this.searchedCedula}`, {
                    headers: { 'Accept': 'application/json' },
                });
                this.comentarios = await res.json();
            } catch (e) {
                console.error('Error cargando comentarios:', e);
            } finally {
                this.loadingComentarios = false;
                this.$nextTick(() => {
                    if (this.$refs.comentariosList) {
                        this.$refs.comentariosList.scrollTop = 0;
                    }
                });
            }
        },

        async guardarComentario() {
            const nc = this.nuevoComentario;
            if (!nc.comentario.trim() || !nc.canal || !nc.tipo_contacto || !nc.efecto_gestion || !nc.accion_cobro) return;
            if (this.guardandoComentario) return;

            this.guardandoComentario = true;
            try {
                const res = await fetch('{{ route("consultas.comentarios.crear") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        cedula: this.searchedCedula,
                        comentario: nc.comentario,
                        canal: nc.canal,
                        tipo_contacto: nc.tipo_contacto,
                        efecto_gestion: nc.efecto_gestion,
                        accion_cobro: nc.accion_cobro,
                    }),
                });

                const data = await res.json();
                if (data.success) {
                    // Agregar al inicio de la lista
                    this.comentarios.unshift(data.comentario);
                    // Limpiar formulario
                    this.nuevoComentario = { comentario: '', canal: '', tipo_contacto: '', efecto_gestion: '', accion_cobro: '' };
                    this.$nextTick(() => {
                        if (this.$refs.comentariosList) {
                            this.$refs.comentariosList.scrollTop = 0;
                        }
                    });
                } else {
                    alert(data.message || 'Error al guardar');
                }
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            } finally {
                this.guardandoComentario = false;
            }
        },

        formatFechaComentario(fecha) {
            if (!fecha) return '';
            const d = new Date(fecha);
            if (isNaN(d.getTime())) return fecha;
            return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC' });
        },

        async cargarTelefonos() {
            if (!this.searchedCedula) return;
            this.loadingTelefonos = true;
            try {
                const res = await fetch(`/consultas/telefonos/${this.searchedCedula}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.dataTelefonos = data.telefonos;
                this.dataCorreos = data.correos;
            } catch (e) {
                console.error('Error cargando teléfonos:', e);
            } finally {
                this.loadingTelefonos = false;
            }
        },

        async toggleNotificar(item) {
            try {
                const res = await fetch(`/consultas/telefonos/${item.id}/notificar`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    item.notificar = data.notificar;
                }
            } catch (e) {
                console.error('Error toggling notificar:', e);
            }
        },

        async guardarDato() {
            const nd = this.nuevoDato;
            if (!nd.dato.trim() || !nd.tipo_dato || !nd.fuente || !nd.calidad.trim() || !nd.cedula_tercero.trim() || !nd.nombre_tercero.trim()) return;
            if (this.guardandoDato) return;

            this.guardandoDato = true;
            try {
                const res = await fetch('{{ route("consultas.telefonos.crear") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        referencia: this.searchedCedula,
                        cedula_tercero: nd.cedula_tercero,
                        nombre_tercero: nd.nombre_tercero,
                        calidad: nd.calidad,
                        dato: nd.dato,
                        tipo_dato: nd.tipo_dato,
                        fuente: nd.fuente,
                        notificar: nd.notificar,
                    }),
                });

                const data = await res.json();
                if (data.success) {
                    // Agregar a la lista correcta
                    if (nd.tipo_dato === 'correo') {
                        this.dataCorreos.push(data.tercero);
                    } else {
                        this.dataTelefonos.push(data.tercero);
                    }
                    this.nuevoDato = { dato: '', tipo_dato: '', fuente: '', calidad: '', cedula_tercero: '', nombre_tercero: '', notificar: false };
                } else {
                    alert(data.message || 'Error al guardar');
                }
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            } finally {
                this.guardandoDato = false;
            }
        },

        iniciarEdicion(item) {
            this.editingId = item.id;
            this.editForm = {
                dato: item.dato,
                tipo_dato: item.tipo_dato,
                calidad: item.calidad,
                fuente: item.fuente || '',
                nombre_tercero: item.nombre_tercero,
                cedula_tercero: item.cedula_tercero,
                notificar: item.notificar,
            };
        },

        async guardarEdicion(item) {
            try {
                const res = await fetch(`/consultas/telefonos/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.editForm),
                });

                const data = await res.json();
                if (data.success) {
                    Object.assign(item, data.tercero);
                    this.editingId = null;
                } else {
                    alert(data.message || 'Error al guardar');
                }
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            }
        },

        extractPersonName(results) {
            const found = results.find(r => r.found && r.records && r.records.length > 0);
            if (!found) return null;
            const d = found.records[0];
            const parts = [d.primer_nombre, d.segundo_nombre, d.primer_apellido, d.segundo_apellido]
                .filter(v => v && String(v).trim());
            if (parts.length) return parts.join(' ');
            if (d.nombres || d.apellidos) return [d.nombres, d.apellidos].filter(Boolean).join(' ');
            if (d.nombre_completo) return d.nombre_completo;
            return null;
        },

        getTipoDoc(rec) {
            return rec.tipo_documento || rec.tipo_id || '—';
        },

        getCedulaField(rec) {
            return rec.cedula || rec.numero_documento || rec.identificacion || '—';
        },

        getNombres(rec) {
            const parts = [rec.primer_nombre, rec.segundo_nombre].filter(v => v && String(v).trim());
            if (parts.length) return parts.join(' ');
            return rec.nombres || (rec.nombre_completo ? rec.nombre_completo.split(' ').slice(0, 2).join(' ') : '—');
        },

        getApellidos(rec) {
            const parts = [rec.primer_apellido, rec.segundo_apellido].filter(v => v && String(v).trim());
            if (parts.length) return parts.join(' ');
            return rec.apellidos || (rec.nombre_completo ? rec.nombre_completo.split(' ').slice(2).join(' ') : '—');
        },

        getDepartamento(rec) {
            const val = rec.departamento || rec.depto || rec.dept || '';
            return (val && !/^\d+$/.test(String(val).trim())) ? val : '—';
        },

        getMunicipio(rec) {
            const val = rec.municipio || rec.ciudad || rec.city || '';
            return (val && !/^\d+$/.test(String(val).trim())) ? val : '—';
        },

        getTelefono(rec) {
            if (rec.telefono) return rec.telefono;
            if (rec.telefonos) return rec.telefonos;
            if (rec.celular) return rec.celular;
            if (rec.telefono_1) return rec.telefono_1;
            if (rec.phone) return rec.phone;
            return '—';
        },

        getCorreo(rec) {
            if (rec.correo) return rec.correo;
            if (rec.email) return rec.email;
            if (rec.correo_electronico) return rec.correo_electronico;
            if (rec.mail) return rec.mail;
            if (rec.e_mail) return rec.e_mail;
            return '—';
        },

        formatLabel(key) {
            return String(key).replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        isObject(value) {
            return value !== null && typeof value === 'object' && !Array.isArray(value);
        },

        isArray(value) {
            return Array.isArray(value);
        },

        hasValue(value) {
            return value !== null && value !== undefined && value !== '';
        },

        formatValue(value) {
            if (!this.hasValue(value)) return '';
            if (typeof value === 'boolean') return value ? 'Sí' : 'No';
            if (typeof value === 'number') return String(value);
            if (typeof value === 'string') {
                const dtMatch = value.match(/^(\d{4}-\d{2}-\d{2})[T\s]\d{2}:\d{2}/);
                if (dtMatch) return dtMatch[1];
            }
            return value;
        }
    };
}
</script>
@endpush
@endsection
