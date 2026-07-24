@extends('layouts.app')

@section('title', 'Retenciones')
@section('page-title', 'Retenciones')

@section('content')
<div x-data="retencionesData()" class="space-y-4">

    {{-- Sección 1: Datos Generales --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
        
        <h3 class="text-sm font-bold text-gray-700 mb-3 border-b border-gray-100 pb-2">1. Datos Generales</h3>

        {{-- Grid compacto de 3 o 4 columnas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-x-4 gap-y-3">
            
            {{-- No_Radicación --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">No_Radicación</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Portafolio Empresa --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Portafolio Empresa</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Tipo de Descuento --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Tipo de Descuento</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Cédula TT --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Cédula TT</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Nombre TT --}}
            <div class="xl:col-span-2">
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Nombre TT</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Nombre Sujeto Retención --}}
            <div class="xl:col-span-2">
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Nombre Sujeto Retención</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Calidad Sujeto --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Calidad Sujeto</label>
                <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange transition-all">
            </div>

            {{-- Gestor Encargado --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Gestor Encargado</label>
                <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all">
                    <option value="">Seleccione gestor...</option>
                    @foreach($gestores as $gestor)
                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Etapa Gestor Encargado --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Etapa Gestor Encargado</label>
                <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all">
                    <option value="">Seleccione etapa...</option>
                    <option value="1. Radicación">1. Radicación</option>
                    <option value="2. Seguimiento">2. Seguimiento</option>
                </select>
            </div>

            {{-- Gestor Radicador --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Gestor Radicador</label>
                <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all">
                    <option value="">Seleccione gestor...</option>
                    @foreach($gestores as $gestor)
                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo Negociación --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Tipo Negociación</label>
                <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-asesco-orange focus:border-asesco-orange bg-white transition-all">
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
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden min-h-[350px]">
        {{-- Pestañas Header --}}
        <div class="flex items-center gap-1.5 px-4 py-2 bg-gray-50 border-b border-gray-200">
            <button @click="activeTab = 'datos2'"
                    :class="activeTab === 'datos2' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer">
                DATOS GENERAL DE LA RETENCIÓN
            </button>
            <button @click="activeTab = 'nomina'"
                    :class="activeTab === 'nomina' ? 'bg-asesco-orange text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer">
                RELACIÓN DE DESCUENTOS POR NÓMINA
            </button>
        </div>

        {{-- Contenido Pestañas --}}
        <div class="p-4">
            
            {{-- Pestaña: DATOS GENERAL DE LA RETENCIÓN (2) --}}
            <div x-show="activeTab === 'datos2'" class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-x-4 gap-y-3">
                
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Nit Empresa</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Empresa</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Tipo de Contrato</label>
                    <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white">
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
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Rango Salarial</label>
                    <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white">
                        <option value="">Seleccione...</option>
                        <option value="1. SMLV">1. SMLV</option>
                        <option value="2. DE 1 A 1.9 SMLV">2. DE 1 A 1.9 SMLV</option>
                        <option value="3. DE 2 A MAS">3. DE 2 A MAS</option>
                        <option value="4. Pendiente">4. Pendiente</option>
                        <option value="5. Terminacion de Contrato">5. Terminacion de Contrato</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Fecha Radicación</label>
                    <input type="date" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">IND</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Valor de la Retención</label>
                    <input type="number" x-model.number="valorRetencionTotal" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Correo Empresa</label>
                    <input type="email" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Nombre Contacto Empresa</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Teléfono Contacto Emp.</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Teléfono Sujeto Ret.</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Teléfono 2 Sujeto Ret.</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Correo Sujeto Retención</label>
                    <input type="email" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Estado Retención</label>
                    <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white">
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
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Efecto Gestión Retención</label>
                    <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white">
                        <option value="">Seleccione...</option>
                        <option value="Indagacion">Indagacion</option>
                        <option value="Radicada">Radicada</option>
                        <option value="Aplica">Aplica</option>
                        <option value="Puesta al dia">Puesta al dia</option>
                        <option value="Acuerdo Pago">Acuerdo Pago</option>
                        <option value="Pago Total">Pago Total</option>
                        <option value="No Aplica">No Aplica</option>
                        <option value="No Labora">No Labora</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Fallecido">Fallecido</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Recaudo Retención</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Relación Recaudo Reportado</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Saldo Pendiente</label>
                    <input type="text" :value="formatMoney(saldoPendiente)" readonly class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 bg-gray-100 font-bold focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Eliminada</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Tipo de Cartera</label>
                    <select class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange bg-white">
                        <option value="">Seleccione...</option>
                        <option value="Vigente">Vigente</option>
                        <option value="Castigada">Castigada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Días INI</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Días ED</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Recaudo</label>
                    <input type="text" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-800 focus:outline-none focus:border-asesco-orange">
                </div>
            </div>

            {{-- Pestaña: RELACIÓN DE DESCUENTOS POR NÓMINA (3) --}}
            <div x-show="activeTab === 'nomina'" style="display: none;" class="space-y-4 pt-2">
                
                {{-- Encabezados de Totales --}}
                <div class="flex justify-end gap-6 items-end mb-2 pr-2">
                    <div class="flex flex-col items-center gap-1">
                        <div class="bg-green-200 text-green-800 px-4 py-1 font-bold text-sm rounded shadow-sm min-w-[130px] text-center" x-text="formatMoney(totalAbonos)"></div>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-[10px] font-bold text-gray-600 uppercase">Valor Retención</span>
                        <div class="bg-blue-200 text-blue-800 px-4 py-1 font-bold text-sm rounded shadow-sm min-w-[130px] text-center" x-text="formatMoney(valorRetencionTotal)"></div>
                    </div>
                </div>

                {{-- Tabla de Abonos --}}
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                            <tr>
                                <th class="px-3 py-2 font-semibold">FECHA_DESCUENTO</th>
                                <th class="px-3 py-2 font-semibold">VALOR</th>
                                <th class="px-3 py-2 font-semibold">FECHA_CONSIGNACIÓN</th>
                                <th class="px-3 py-2 font-semibold text-center">REPORTADO_COOMULTRASAN</th>
                                <th class="px-3 py-2 font-semibold text-center">APLICADO_CORE_CM</th>
                                <th class="px-3 py-2 font-semibold">SALDO</th>
                                <th class="px-3 py-2 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(abono, index) in abonos" :key="abono.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-2">
                                        <input type="date" x-model="abono.fecha_descuento" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange">
                                    </td>
                                    <td class="p-2">
                                        <div class="relative">
                                            <span class="absolute left-2 top-1.5 text-gray-500">$</span>
                                            <input type="number" x-model.number="abono.valor" class="w-full pl-6 pr-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange">
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="date" x-model="abono.fecha_consignacion" class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs focus:outline-none focus:border-asesco-orange">
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" x-model="abono.reportado" class="w-4 h-4 text-asesco-orange border-gray-300 rounded focus:ring-asesco-orange cursor-pointer">
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" x-model="abono.aplicado" class="w-4 h-4 text-asesco-orange border-gray-300 rounded focus:ring-asesco-orange cursor-pointer">
                                    </td>
                                    <td class="p-2">
                                        <div class="px-2 py-1.5 bg-gray-100 rounded text-gray-700 font-mono font-medium border border-gray-200" x-text="formatMoney(calcularSaldoFila(index))"></div>
                                    </td>
                                    <td class="p-2 text-center">
                                        <button @click="removeAbono(index)" class="text-red-400 hover:text-red-600 p-1 cursor-pointer" title="Eliminar fila">
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
                <div class="mt-2">
                    <button @click="addAbono()" class="flex items-center gap-1 text-xs font-semibold text-asesco-orange hover:text-asesco-coral transition-colors cursor-pointer px-2 py-1 rounded hover:bg-orange-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar Abono
                    </button>
                </div>

            </div>
            
        </div>
    </div>

    {{-- Sección 3: Gestiones de Retención (Estático) --}}
    <div class="mt-4 border border-green-500 rounded-lg overflow-hidden shadow-sm bg-white">
        <div class="bg-gradient-to-r from-green-600 to-green-500 px-4 py-2">
            <h3 class="text-xs font-bold text-white tracking-wide">GESTIONES DE RETENCIONES</h3>
        </div>
        <div class="p-4 bg-green-50/50">
            <textarea rows="4" class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm resize-y" placeholder="Escriba aquí los detalles de la gestión de retención..."></textarea>
            
            <div class="mt-3 flex justify-end">
                <button class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-5 py-2 rounded-lg transition-colors cursor-pointer shadow-sm">
                    Guardar Gestión
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function retencionesData() {
    return {
        activeTab: 'datos2',
        valorRetencionTotal: null,
        abonos: [
            { id: Date.now(), fecha_descuento: '', valor: null, fecha_consignacion: '', reportado: false, aplicado: false }
        ],
        get totalAbonos() {
            return this.abonos.reduce((sum, abono) => sum + (parseFloat(abono.valor) || 0), 0);
        },
        get saldoPendiente() {
            return (parseFloat(this.valorRetencionTotal) || 0) - this.totalAbonos;
        },
        calcularSaldoFila(index) {
            let saldo = parseFloat(this.valorRetencionTotal) || 0;
            for (let i = 0; i <= index; i++) {
                saldo -= (parseFloat(this.abonos[i].valor) || 0);
            }
            return saldo;
        },
        addAbono() {
            this.abonos.push({ 
                id: Date.now(), 
                fecha_descuento: '', 
                valor: null, 
                fecha_consignacion: '', 
                reportado: false, 
                aplicado: false 
            });
        },
        removeAbono(index) {
            this.abonos.splice(index, 1);
        },
        formatMoney(value) {
            if (value === null || isNaN(value)) return '$0';
            return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
        }
    }
}
</script>
@endpush

@endsection
