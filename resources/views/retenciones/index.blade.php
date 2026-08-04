@extends('layouts.app')

@section('title', 'Retenciones')
@section('page-title', 'Retenciones')

@section('content')
<div x-data="retencionesData()" class="space-y-4 relative">

    {{-- Spinner/Overlay de carga moderno --}}
    <div x-show="loading" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4 border border-white/50 w-64 text-center transform transition-all">
            <svg class="animate-spin h-10 w-10 text-asesco-orange" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-sm font-bold text-gray-800 tracking-wide uppercase">Guardando...</span>
        </div>
    </div>

    {{-- Sección 1: Datos Generales --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm relative">
        <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-2">
            <h3 class="text-sm font-bold text-gray-700">1. Datos Generales</h3>
            <div class="flex gap-2">
                <button x-show="!is_section1_locked" @click="saveSection1()" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors">
                    Guardar Datos
                </button>
                <button x-show="is_section1_locked" @click="unlockSection(1)" class="bg-gray-500 hover:bg-gray-600 text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors">
                    Editar Datos
                </button>
            </div>
        </div>

        {{-- Grid compacto de 6 columnas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-x-4 gap-y-3">
            
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="No Radicación">No Radicación</label>
                <input type="text" x-model="s1.no_radicacion" disabled class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 font-bold bg-gray-100 focus:outline-none transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Portafolio Empresa">Portafolio Empresa</label>
                <select x-model="s1.portafolio_empresa" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all disabled:bg-gray-100 disabled:text-gray-500">
                    <option value="">Seleccione...</option>
                    <option value="COOMULTRASAN MULTIACTIVA">COOMULTRASAN MULTIACTIVA</option>
                    <option value="COMFABOY">COMFABOY</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Tipo de Descuento">Tipo de Descuento</label>
                <input type="text" x-model="s1.tipo_descuento" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Cédula TT">Cédula TT</label>
                <input type="text" x-model="s1.cedula_tt" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Nombre TT">Nombre TT</label>
                <input type="text" x-model="s1.nombre_tt" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Nombre Sujeto Retención">Nombre Sujeto Retención</label>
                <input type="text" x-model="s1.nombre_sujeto_retencion" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Calidad Sujeto">Calidad Sujeto</label>
                <input type="text" x-model="s1.calidad_sujeto" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-gray-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Gestor Encargado">Gestor Encargado</label>
                <select x-model="s1.gestor_encargado_id" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all disabled:bg-gray-100 disabled:text-gray-500">
                    <option value="">Seleccione gestor...</option>
                    @foreach($gestores as $gestor)
                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Etapa Gestor Encargado">Etapa Gestor Encargado</label>
                <select x-model="s1.etapa_gestor_encargado" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all disabled:bg-gray-100 disabled:text-gray-500">
                    <option value="">Seleccione etapa...</option>
                    <option value="1. Radicación">1. Radicación</option>
                    <option value="2. Seguimiento">2. Seguimiento</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Gestor Radicador">Gestor Radicador</label>
                <select x-model="s1.gestor_radicador_id" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all disabled:bg-gray-100 disabled:text-gray-500">
                    <option value="">Seleccione gestor...</option>
                    @foreach($gestores as $gestor)
                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Tipo Negociación">Tipo Negociación</label>
                <select x-model="s1.tipo_negociacion" :disabled="is_section1_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all disabled:bg-gray-100 disabled:text-gray-500">
                    <option value="">Seleccione tipo...</option>
                    <option value="Ninguna">Ninguna</option>
                    <option value="Pago Total (1C)">Pago Total (1C)</option>
                    <option value="Pago Negociado (3C)">Pago Negociado (3C)</option>
                    <option value="Pago Alterno (6C)">Pago Alterno (6C)</option>
                </select>
            </div>

        </div>
    </div>

    {{-- Sección 2: Pestañas (Tabs) --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden min-h-[350px]" x-show="retencion_id">
        {{-- Pestañas Header --}}
        <div class="flex items-center gap-1.5 px-4 py-2 bg-gray-50 border-b border-gray-200 overflow-x-auto">
            <button @click="activeTab = 'datos2'"
                    :class="activeTab === 'datos2' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap">
                DATOS GENERAL DE LA RETENCIÓN
            </button>
            <button @click="activeTab = 'nomina'"
                    :class="activeTab === 'nomina' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap">
                RELACIÓN DE DESCUENTOS POR NÓMINA
            </button>
            <button @click="activeTab = 'historial'"
                    :class="activeTab === 'historial' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer whitespace-nowrap flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                HISTORIAL DE RETENCIÓN
            </button>
        </div>

        {{-- Contenido Pestañas --}}
        <div class="p-4 h-[350px] overflow-y-auto">
            
            {{-- Pestaña: DATOS GENERAL DE LA RETENCIÓN (2) --}}
            <div x-show="activeTab === 'datos2'" style="display: none;">
                <div class="flex justify-end mb-3 pb-2 border-b border-gray-100">
                    <div class="flex gap-2">
                        <button x-show="!is_section2_locked" @click="saveSection2()" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors">
                            Guardar Datos
                        </button>
                        <button x-show="is_section2_locked" @click="unlockSection(2)" class="bg-gray-500 hover:bg-gray-600 text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors">
                            Editar Datos
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-x-4 gap-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Nit Empresa">Nit Empresa</label>
                        <input type="text" x-model="s2.nit_empresa" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Empresa">Empresa</label>
                        <input type="text" x-model="s2.empresa" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Correo Empresa">Correo Empresa</label>
                        <input type="email" x-model="s2.correo_empresa" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Nombre Contacto Empresa">Nombre Contacto Empresa</label>
                        <input type="text" x-model="s2.nombre_contacto_empresa" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Teléfono Contacto Emp.">Teléfono Contacto Emp.</label>
                        <input type="text" x-model="s2.telefono_contacto_empresa" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Tipo de Contrato">Tipo de Contrato</label>
                        <select x-model="s2.tipo_contrato" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="1. Termino Fijo">1. Termino Fijo</option>
                            <option value="2. Termino Indefinido">2. Termino Indefinido</option>
                            <option value="3. Obra o Labor">3. Obra o Labor</option>
                            <option value="4. En Propiedad">4. En Propiedad</option>
                            <option value="5. Prestacion de Servicios">5. Prestacion de Servicios</option>
                            <option value="6. Pendiente">6. Pendiente</option>
                            <option value="7. Terminacion de Contrato">7. Terminacion de Contrato</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Rango Salarial">Rango Salarial</label>
                        <select x-model="s2.rango_salarial" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="1. SMLV">1. SMLV</option>
                            <option value="2. DE 1 A 1.9 SMLV">2. DE 1 A 1.9 SMLV</option>
                            <option value="3. DE 2 A MAS">3. DE 2 A MAS</option>
                            <option value="4. Pendiente">4. Pendiente</option>
                            <option value="5. Terminacion de Contrato">5. Terminacion de Contrato</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Fecha Radicación">Fecha Radicación</label>
                        <input type="date" x-model="s2.fecha_radicacion" disabled class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none bg-gray-100 disabled:text-gray-500 font-bold">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Estado Retención">Estado Retención</label>
                        <select x-model="s2.estado_retencion" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="Solicitada">Solicitada</option>
                            <option value="Enviada">Enviada</option>
                            <option value="Seguimiento">Seguimiento</option>
                            <option value="Aceptada">Aceptada</option>
                            <option value="Rechazada">Rechazada</option>
                            <option value="Suspension">Suspension</option>
                            <option value="Activacion">Activacion</option>
                            <option value="Regularizada">Regularizada</option>
                            <option value="Terminada">Terminada</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Fallecido">Fallecido</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Efecto Gestión Retención">Efecto Gestión Retención</label>
                        <select x-model="s2.efecto_gestion_retencion" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="Indagación">Indagación</option>
                            <option value="Radicada">Radicada</option>
                            <option value="Aplica">Aplica</option>
                            <option value="Puesta al día">Puesta al día</option>
                            <option value="Pago Total (1C)">Pago Total (1C)</option>
                            <option value="Pago Negociado (3C)">Pago Negociado (3C)</option>
                            <option value="Pago Alterno (6C)">Pago Alterno (6C)</option>
                            <option value="No Aplica">No Aplica</option>
                            <option value="No Labora">No Labora</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Fallecido">Fallecido</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Valor de la Retención">Valor de la Retención</label>
                        <input type="number" x-model.number="s2.valor_retencion_total" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs font-bold text-red-600 focus:outline-none focus:border-asesco-orange transition-all disabled:bg-gray-100 disabled:text-red-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Recaudo Retención">Recaudo Retención</label>
                        <input type="text" :value="formatMoney(totalAbonos)" readonly class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-green-600 bg-gray-100 font-bold focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Saldo Pendiente">Saldo Pendiente</label>
                        <input type="text" :value="formatMoney(saldoPendiente)" readonly class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-red-600 bg-gray-100 font-bold focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Recaudo Mes Actual">Recaudo Mes Actual</label>
                        <input type="text" :value="formatMoney(recaudoMesActual)" readonly class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-green-600 bg-gray-100 font-bold focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Teléfono Sujeto Ret.">Teléfono Sujeto Ret.</label>
                        <input type="text" x-model="s2.telefono_sujeto_retencion" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Teléfono 2 Sujeto Ret.">Teléfono 2 Sujeto Ret.</label>
                        <input type="text" x-model="s2.telefono_2_sujeto_retencion" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Correo Sujeto Retención">Correo Sujeto Retención</label>
                        <input type="email" x-model="s2.correo_sujeto_retencion" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1 truncate" title="Tipo de Cartera">Tipo de Cartera</label>
                        <select x-model="s2.tipo_cartera" :disabled="is_section2_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">Seleccione...</option>
                            <option value="Vigente">Vigente</option>
                            <option value="Castigada">Castigada</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Pestaña: RELACIÓN DE DESCUENTOS POR NÓMINA (3) --}}
            <div x-show="activeTab === 'nomina'" style="display: none;" class="space-y-4 pt-2">
                
                <div class="flex justify-between mb-2">
                    <div class="flex gap-2">
                        <button x-show="!is_abonos_locked" @click="saveAbonos()" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors">
                            Guardar Abonos
                        </button>
                        <button x-show="is_abonos_locked" @click="unlockSection(3)" class="bg-gray-500 hover:bg-gray-600 text-white text-[11px] px-3 py-1.5 rounded shadow-sm font-bold transition-colors">
                            Editar Abonos
                        </button>
                    </div>

                    {{-- Encabezados de Totales --}}
                    <div class="flex justify-end gap-6 items-end pr-2">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Total Abonos</span>
                            <div class="bg-green-200 text-green-800 px-4 py-1 font-bold text-sm rounded shadow-sm min-w-[130px] text-center" x-text="formatMoney(totalAbonos)"></div>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Valor Retención</span>
                            <div class="bg-blue-200 text-blue-800 px-4 py-1 font-bold text-sm rounded shadow-sm min-w-[130px] text-center" x-text="formatMoney(s2.valor_retencion_total)"></div>
                        </div>
                    </div>
                </div>

                {{-- Tabla de Abonos --}}
                <div class="overflow-x-auto overflow-y-auto max-h-[250px] custom-scrollbar border border-gray-200 rounded-lg">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
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
                            <template x-for="(abono, index) in abonos" :key="index">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-2">
                                        <input type="date" x-model="abono.fecha_descuento" :disabled="is_abonos_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    <td class="p-2">
                                        <div class="relative">
                                            <span class="absolute left-2 top-1.5 text-gray-500">$</span>
                                            <input type="number" x-model.number="abono.valor" :disabled="is_abonos_locked" class="w-full pl-6 pr-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="date" x-model="abono.fecha_consignacion" :disabled="is_abonos_locked" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" x-model="abono.reportado" :disabled="is_abonos_locked" class="w-4 h-4 text-asesco-orange border-gray-300 rounded focus:ring-asesco-orange cursor-pointer disabled:opacity-50">
                                    </td>
                                    <td class="p-2 text-center align-middle">
                                        <div class="flex items-center justify-center gap-1 relative">
                                            <template x-if="!abono.soporte">
                                                <div class="relative">
                                                    <input type="file" @change="uploadSoporte($event, index)" :disabled="is_abonos_locked" accept=".pdf,image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer disabled:cursor-not-allowed z-10" title="Subir soporte">
                                                    <button type="button" :disabled="is_abonos_locked" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded disabled:opacity-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="abono.soporte">
                                                <div class="flex gap-1">
                                                    <a :href="abono.soporte.startsWith('http') ? abono.soporte : '/storage/' + abono.soporte" target="_blank" class="text-green-600 hover:bg-green-50 p-1.5 rounded transition-colors" title="Ver soporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    </a>
                                                    <button type="button" x-show="!is_abonos_locked" @click="abono.soporte = null" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors cursor-pointer" title="Eliminar soporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" x-model="abono.aplicado" :disabled="is_abonos_locked" class="w-4 h-4 text-asesco-orange border-gray-300 rounded focus:ring-asesco-orange cursor-pointer disabled:opacity-50">
                                    </td>
                                    <td class="p-2">
                                        <div class="px-2 py-1.5 bg-gray-100 rounded text-gray-700 font-mono font-medium border border-gray-200" x-text="formatMoney(calcularSaldoFila(index))"></div>
                                    </td>
                                    <td class="p-2 text-center">
                                        <button x-show="!is_abonos_locked" @click="removeAbono(index)" class="text-red-400 hover:text-red-600 p-1 cursor-pointer" title="Eliminar fila">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="abonos.length === 0">
                                <td colspan="7" class="p-4 text-center text-gray-400 text-xs italic">
                                    No hay abonos registrados. Haz clic en "Agregar Abono" para empezar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                {{-- Botón agregar fila --}}
                <div class="mt-2" x-show="!is_abonos_locked">
                    <button @click="addAbono()" class="flex items-center gap-1 text-xs font-semibold text-asesco-orange hover:text-asesco-coral transition-colors cursor-pointer px-2 py-1 rounded hover:bg-orange-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar Abono
                    </button>
                </div>

            </div>

            {{-- Pestaña: HISTORIAL DE RETENCIÓN --}}
            <div x-show="activeTab === 'historial'" style="display: none;" class="pt-2">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="text-xs font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Registro de Actividades
                    </h3>
                    
                    <div class="space-y-4 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
                        <template x-for="history in histories" :key="history.id">
                            <div class="flex gap-3 text-sm">
                                <div class="flex flex-col items-center">
                                    <div class="w-2 h-2 rounded-full bg-asesco-orange mt-1.5"></div>
                                    <div class="w-px h-full bg-gray-300 my-1"></div>
                                </div>
                                <div class="flex-1 pb-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-gray-800">
                                            <span class="font-bold text-gray-900" x-text="history.user ? history.user.name : 'Sistema'"></span> 
                                            ha <span class="font-semibold text-asesco-orange" x-text="history.accion.toLowerCase()"></span> 
                                            en la sección <span class="font-semibold" x-text="history.seccion"></span>
                                        </p>
                                        <span class="text-[10px] text-gray-500" x-text="new Date(history.created_at).toLocaleString()"></span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5 font-medium" x-text="history.campo"></p>
                                    
                                    <div class="mt-2" x-show="history.accion === 'EDITADO' || history.valor_anterior || history.valor_nuevo">
                                        <button @click="openHistoryModal(history)" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded border border-blue-100 transition-colors">
                                            Ver detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="histories.length === 0" class="text-center text-gray-400 text-xs italic py-4">
                            No hay historial registrado para esta retención.
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    {{-- Sección 3: Gestiones de Retención --}}
    <div class="mt-4 border border-green-500 rounded-lg overflow-hidden shadow-sm bg-white" x-show="retencion_id">
        <div class="bg-gradient-to-r from-green-600 to-green-500 px-4 py-2 flex justify-between items-center">
            <h3 class="text-xs font-bold text-white tracking-wide">GESTIONES DE RETENCIONES</h3>
        </div>
        
        <div class="p-4 bg-green-50/30 border-b border-gray-100 max-h-[170px] overflow-y-auto custom-scrollbar">
            <template x-for="gestion in gestiones" :key="gestion.id">
                <div class="mb-3 bg-white p-3 rounded border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-gray-700" x-text="gestion.user ? gestion.user.name : 'Usuario'"></span>
                        <span class="text-[10px] text-gray-500" x-text="new Date(gestion.created_at).toLocaleString()"></span>
                    </div>
                    <p class="text-xs text-gray-600 whitespace-pre-wrap" x-text="gestion.detalle"></p>
                </div>
            </template>
            <div x-show="gestiones.length === 0" class="text-xs text-gray-400 italic mb-2">No hay gestiones registradas.</div>
        </div>

        <div class="p-4 bg-green-50/50">
            <textarea x-model="nueva_gestion" rows="3" class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-xs resize-y" placeholder="Escriba aquí los detalles de una nueva gestión..."></textarea>
            
            <div class="mt-3 flex justify-end">
                <button @click="saveGestion()" :disabled="!nueva_gestion.trim()" class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-xs font-semibold px-5 py-2 rounded-lg transition-colors cursor-pointer shadow-sm">
                    Guardar Gestión
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Historial Detalle --}}
    <div x-show="historyModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        
        {{-- Modal Content --}}
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
                                    {{-- Separator line --}}
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

</div>

@push('scripts')
<script>
// Parse data from PHP if viewing existing retencion
const existingRetencion = @json($retencion ?? null);
const nextNoRadicacion = @json($nextNoRadicacion ?? '');

function retencionesData() {
    return {
        loading: false,
        activeTab: 'datos2',
        retencion_id: existingRetencion ? existingRetencion.id : null,
        
        // Secciones locks
        is_section1_locked: existingRetencion ? existingRetencion.is_section1_locked : false,
        is_section2_locked: existingRetencion ? existingRetencion.is_section2_locked : false,
        is_abonos_locked: existingRetencion ? existingRetencion.is_abonos_locked : false,

        // Data objects
        s1: {
            no_radicacion: existingRetencion?.no_radicacion || nextNoRadicacion,
            portafolio_empresa: existingRetencion?.portafolio_empresa || '',
            tipo_descuento: existingRetencion?.tipo_descuento || '',
            cedula_tt: existingRetencion?.cedula_tt || new URLSearchParams(window.location.search).get('cedula') || '',
            nombre_tt: existingRetencion?.nombre_tt || '',
            nombre_sujeto_retencion: existingRetencion?.nombre_sujeto_retencion || new URLSearchParams(window.location.search).get('nombre') || '',
            calidad_sujeto: existingRetencion?.calidad_sujeto || '',
            gestor_encargado_id: existingRetencion?.gestor_encargado_id || '',
            etapa_gestor_encargado: existingRetencion?.etapa_gestor_encargado || '',
            gestor_radicador_id: existingRetencion?.gestor_radicador_id || '',
            tipo_negociacion: existingRetencion?.tipo_negociacion || '',
        },
        s2: {
            nit_empresa: existingRetencion?.nit_empresa || '',
            empresa: existingRetencion?.empresa || '',
            tipo_contrato: existingRetencion?.tipo_contrato || '',
            rango_salarial: existingRetencion?.rango_salarial || '',
            fecha_radicacion: existingRetencion?.fecha_radicacion || new Date().toISOString().split('T')[0],
            valor_retencion_total: existingRetencion?.valor_retencion_total || null,
            correo_empresa: existingRetencion?.correo_empresa || '',
            nombre_contacto_empresa: existingRetencion?.nombre_contacto_empresa || '',
            telefono_contacto_empresa: existingRetencion?.telefono_contacto_empresa || '',
            telefono_sujeto_retencion: existingRetencion?.telefono_sujeto_retencion || '',
            telefono_2_sujeto_retencion: existingRetencion?.telefono_2_sujeto_retencion || '',
            correo_sujeto_retencion: existingRetencion?.correo_sujeto_retencion || '',
            estado_retencion: existingRetencion?.estado_retencion || '',
            efecto_gestion_retencion: existingRetencion?.efecto_gestion_retencion || '',
            tipo_cartera: existingRetencion?.tipo_cartera || '',
        },

        // Relaciones
        abonos: existingRetencion?.abonos || [],
        gestiones: existingRetencion?.gestiones || [],
        histories: existingRetencion?.histories || [],

        nueva_gestion: '',
        
        // Modal History
        historyModalOpen: false,
        selectedHistory: null,

        // Computed
        get totalAbonos() {
            return this.abonos.reduce((sum, abono) => sum + (parseFloat(abono.valor) || 0), 0);
        },
        get recaudoMesActual() {
            const now = new Date();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();
            return this.abonos.reduce((sum, abono) => {
                if (!abono.fecha_descuento) return sum;
                const [year, month] = abono.fecha_descuento.split('-');
                if (parseInt(month, 10) - 1 === currentMonth && parseInt(year, 10) === currentYear) {
                    return sum + (parseFloat(abono.valor) || 0);
                }
                return sum;
            }, 0);
        },
        get saldoPendiente() {
            return (parseFloat(this.s2.valor_retencion_total) || 0) - this.totalAbonos;
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

        // Methods
        calcularSaldoFila(index) {
            let saldo = parseFloat(this.s2.valor_retencion_total) || 0;
            for (let i = 0; i <= index; i++) {
                saldo -= (parseFloat(this.abonos[i].valor) || 0);
            }
            return saldo;
        },
        addAbono() {
            this.abonos.push({ 
                fecha_descuento: '', 
                valor: null, 
                fecha_consignacion: '', 
                reportado: false, 
                aplicado: false,
                soporte: null
            });
        },
        removeAbono(index) {
            this.abonos.splice(index, 1);
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
                timer: 4000,
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
            const data = { ...this.s1, retencion_id: this.retencion_id };
            const res = await this.postData('{{ route('retenciones.saveSection1') }}', data);
            if (res.success) {
                const isNew = !this.retencion_id;
                if (isNew) {
                    this.retencion_id = res.retencion_id;
                    this.s1.no_radicacion = res.no_radicacion;
                    window.history.pushState(null, '', `/retenciones/${this.retencion_id}`);
                }
                this.is_section1_locked = true;
                this.showSuccessAndReload('Datos Generales guardados con éxito.');
            }
        },

        async saveSection2() {
            if (!this.retencion_id) return this.showError('Debe guardar la Sección 1 primero.');
            const data = { ...this.s2, retencion_id: this.retencion_id };
            const res = await this.postData('{{ route('retenciones.saveSection2') }}', data);
            if (res.success) {
                this.is_section2_locked = true;
                this.showSuccessAndReload('Datos de la retención guardados con éxito.');
            }
        },

        async saveAbonos() {
            if (!this.retencion_id) return this.showError('Debe guardar la Sección 1 primero.');
            const data = { abonos: this.abonos, retencion_id: this.retencion_id };
            const res = await this.postData('{{ route('retenciones.saveAbonos') }}', data);
            if (res.success) {
                this.is_abonos_locked = true;
                this.showSuccessAndReload('Abonos guardados con éxito.');
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
                const response = await fetch('{{ route('retenciones.uploadAbonoSoporte') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Error al subir el archivo');
                
                this.abonos[index].soporte = result.path;
            } catch (error) {
                this.showError(error.message);
            } finally {
                this.loading = false;
                event.target.value = ''; // Reset file input
            }
        },

        async saveGestion() {
            if (!this.retencion_id) return this.showError('Debe guardar la Sección 1 primero.');
            const data = { detalle: this.nueva_gestion, retencion_id: this.retencion_id };
            const res = await this.postData('{{ route('retenciones.saveGestion') }}', data);
            if (res.success) {
                this.gestiones.unshift(res.gestion);
                this.nueva_gestion = '';
                this.showSuccessAndReload('Gestión guardada con éxito.');
            }
        },

        async unlockSection(section) {
            const result = await Swal.fire({
                title: '¿Está seguro?',
                text: 'Los cambios quedarán registrados en el historial.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8611A',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, editar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;
            
            const data = { section: section };
            const res = await this.postData(`/retenciones/${this.retencion_id}/unlock`, data);
            if (res.success) {
                if (section == 1) this.is_section1_locked = false;
                if (section == 2) this.is_section2_locked = false;
                if (section == 3) this.is_abonos_locked = false;
                this.reloadHistories();
            }
        },

        // Helper to refresh history without reloading page
        async reloadHistories() {
            if (!this.retencion_id) return;
            try {
                const response = await fetch(`/retenciones/${this.retencion_id}/historial`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (response.ok) {
                    const result = await response.json();
                    this.histories = result.histories;
                }
            } catch (e) {
                console.error('Error cargando historial:', e);
            }
        }
    }
}
</script>
@endpush

@endsection
