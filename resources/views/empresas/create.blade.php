@extends('layouts.app')

@section('title', 'Nueva Empresa')
@section('page-title', 'Nueva Empresa')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header breadcrumb --}}
    <div class="flex items-center gap-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('empresas.index') }}" class="hover:text-[#E8611A] transition-colors">Empresas</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-800 font-medium">Nueva Empresa</span>
    </div>

    {{-- Steps indicator --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 z-0 mx-8">
                <div id="progress-bar" class="h-full bg-gradient-to-r from-[#E8611A] to-[#C94477] transition-all duration-500" style="width:0%"></div>
            </div>
            @foreach([
                ['icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21', 'label' => 'Empresa'],
                ['icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'label' => 'Tarifas'],
                ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'Canales'],
                ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Lineamientos'],
            ] as $idx => $step)
            <button type="button" onclick="irAPaso({{ $idx + 1 }})"
                    class="flex flex-col items-center z-10 step-item" data-step="{{ $idx + 1 }}">
                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 step-circle
                    {{ $idx === 0 ? 'bg-gradient-to-br from-[#E8611A] to-[#C94477] border-[#E8611A] text-white' : 'bg-white border-gray-200 text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-medium mt-1.5 {{ $idx === 0 ? 'text-[#E8611A]' : 'text-gray-400' }} step-label">{{ $step['label'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- STEP 1 --}}
    <div id="step-1" class="step-panel">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800">Paso 1: Información de la Empresa</h3>
                <p class="text-sm text-gray-500 mt-1">Ingresa el nombre de la empresa para comenzar la configuración.</p>
            </div>
            <div class="max-w-md">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de la empresa <span class="text-red-500">*</span></label>
                <input type="text" id="empresa-nombre" placeholder="Ej: SEGUROS S.A."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#E8611A] focus:ring-2 focus:ring-[#E8611A]/20 transition-all">
                <p id="nombre-error" class="text-red-500 text-xs mt-1 hidden">El nombre es requerido.</p>
            </div>
            <div class="flex justify-end mt-8">
                <button onclick="guardarEmpresa(false)" id="btn-step1"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
                    Guardar y Continuar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 2: Tarifas --}}
    <div id="step-2" class="step-panel hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Paso 2: Tarifas y Honorarios</h3>
                    <p class="text-sm text-gray-500 mt-1">Define los tramos con sus porcentajes para cartera Vigente y Castigada.</p>
                </div>
                <button onclick="agregarTramoTarifa()"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-[#E8611A] to-[#C94477] px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Agregar Tramo
                </button>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 w-8">#</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">TRAMO</th>
                            <th class="text-center px-4 py-3 font-semibold text-[#E8611A] bg-orange-50">VIGENTE (%)</th>
                            <th class="text-center px-4 py-3 font-semibold text-[#C94477] bg-pink-50">CASTIGADA (%)</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-400 w-12">Acc.</th>
                        </tr>
                    </thead>
                    <tbody id="tarifas-tbody"></tbody>
                </table>
            </div>
            <p id="tarifas-error" class="text-red-500 text-xs mt-2 hidden">Agrega al menos un tramo de tarifa.</p>
            <div class="flex items-center justify-between mt-8">
                <button onclick="goStep(1)" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 bg-gray-100 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </button>
                <div class="flex gap-3">
                    <button onclick="guardarTarifas(true)"
                            class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 border border-gray-300 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Guardar y salir
                    </button>
                    <button onclick="guardarTarifas(false)"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
                        Guardar y Continuar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 3: Canales --}}
    <div id="step-3" class="step-panel hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Paso 3: Canales de Recaudo</h3>
                    <p class="text-sm text-gray-500 mt-1">Registra los canales de pago disponibles para esta empresa.</p>
                </div>
                <button onclick="agregarCanal()"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-[#E8611A] to-[#C94477] px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Agregar Canal
                </button>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 w-8">#</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">NOMBRE DEL CANAL</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">N° CANAL</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">MEDIO DE PAGO</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-400 w-12">Acc.</th>
                        </tr>
                    </thead>
                    <tbody id="canales-tbody"></tbody>
                </table>
            </div>
            <p id="canales-error" class="text-red-500 text-xs mt-2 hidden">Agrega al menos un canal de recaudo.</p>
            <div class="flex items-center justify-between mt-8">
                <button onclick="goStep(2)" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 bg-gray-100 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </button>
                <div class="flex gap-3">
                    <button onclick="guardarCanales(true)"
                            class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 border border-gray-300 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Guardar y salir
                    </button>
                    <button onclick="guardarCanales(false)"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
                        Guardar y Continuar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 4: Lineamientos --}}
    <div id="step-4" class="step-panel hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Paso 4: Lineamientos de Negociación</h3>
                    <p class="text-sm text-gray-500 mt-1">Define los lineamientos por porcentaje fijo o por tramos de mora.</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="agregarLineamiento('porcentaje')"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-[#E8611A] px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Por Porcentaje
                    </button>
                    <button onclick="agregarLineamiento('tramo')"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-[#C94477] px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Por Tramo
                    </button>
                </div>
            </div>
            <div id="lineamientos-container" class="space-y-4"></div>
            <p id="lineamientos-error" class="text-red-500 text-xs mt-2 hidden">Agrega al menos un lineamiento.</p>
            <div class="flex items-center justify-between mt-8">
                <button onclick="goStep(3)" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 bg-gray-100 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </button>
                <div class="flex gap-3">
                    <button onclick="guardarLineamientos(true)"
                            class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 border border-gray-300 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Guardar y salir
                    </button>
                    <button onclick="guardarLineamientos(false)"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Finalizar y Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let empresaId = null;
let currentStep = 1;
let maxStep = 1; // pasos completados (para habilitar navegación)
let tarifaCount = 0;
let canalCount = 0;
let lineamientoCount = 0;
let tramoLinCount = 0;

// ── Navegación ───────────────────────────────────────────────────────────────

function irAPaso(step) {
    // Solo permite navegar hacia atrás o pasos ya alcanzados
    if (!empresaId && step > 1) return;
    if (step > maxStep) return;
    goStep(step);
}

function goStep(step) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById(`step-${step}`).classList.remove('hidden');
    currentStep = step;
    if (step > maxStep) maxStep = step;
    updateStepUI();
}

function updateStepUI() {
    document.querySelectorAll('.step-item').forEach((item, idx) => {
        const circle = item.querySelector('.step-circle');
        const label = item.querySelector('.step-label');
        const s = idx + 1;
        const canClick = (s <= maxStep);
        item.style.cursor = canClick ? 'pointer' : 'default';
        item.title = canClick ? `Ir al paso ${s}` : '';

        if (s < currentStep) {
            circle.className = 'w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 step-circle bg-gradient-to-br from-[#E8611A] to-[#C94477] border-[#E8611A] text-white';
            circle.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
            label.className = 'text-xs font-medium mt-1.5 text-[#E8611A] step-label';
        } else if (s === currentStep) {
            circle.className = 'w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 step-circle bg-gradient-to-br from-[#E8611A] to-[#C94477] border-[#E8611A] text-white';
            circle.innerHTML = `<span class="text-sm font-bold">${s}</span>`;
            label.className = 'text-xs font-medium mt-1.5 text-[#E8611A] step-label';
        } else {
            circle.className = 'w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 step-circle bg-white border-gray-200 text-gray-400';
            circle.innerHTML = `<span class="text-sm font-semibold">${s}</span>`;
            label.className = 'text-xs font-medium mt-1.5 text-gray-400 step-label';
        }
    });
    const progress = ((currentStep - 1) / 3) * 100;
    document.getElementById('progress-bar').style.width = progress + '%';
}

// ── Helpers ──────────────────────────────────────────────────────────────────

async function apiPost(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        // Extraer mensaje de error de validación si existe
        if (data.errors) {
            const msgs = Object.values(data.errors).flat().join('\n');
            throw new Error(msgs);
        }
        throw new Error(data.message || `Error HTTP ${res.status}`);
    }
    return data;
}

async function apiPatch(url, body) {
    const res = await fetch(url, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || `Error HTTP ${res.status}`);
    return data;
}

function salirAlListado() {
    window.location.href = '/empresas';
}

// ── STEP 1: Empresa ──────────────────────────────────────────────────────────

async function guardarEmpresa(salir = false) {
    const nombre = document.getElementById('empresa-nombre').value.trim();
    if (!nombre) { document.getElementById('nombre-error').classList.remove('hidden'); return; }
    document.getElementById('nombre-error').classList.add('hidden');

    const btn = document.getElementById('btn-step1');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const data = await apiPost('/empresas', { nombre });
        empresaId = data.id;
        maxStep = 2;
        if (salir) { salirAlListado(); return; }
        goStep(2);
    } catch (e) {
        Swal.fire('Error', e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Guardar y Continuar <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';
    }
}

// ── STEP 2: Tarifas ──────────────────────────────────────────────────────────

function agregarTramoTarifa(data = {}) {
    tarifaCount++;
    const tbody = document.getElementById('tarifas-tbody');
    const n = tarifaCount;
    const idx = tbody.children.length + 1;
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100 hover:bg-gray-50 transition-colors';
    tr.id = `tarifa-row-${n}`;
    tr.innerHTML = `
        <td class="px-4 py-2 text-gray-400 text-xs">${idx}</td>
        <td class="px-4 py-2">
            <input type="text" placeholder="Ej: 1 a 30" value="${escHtml(data.nombre_tramo||'')}"
                   class="tarifa-nombre w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#E8611A] focus:ring-1 focus:ring-[#E8611A]/20">
        </td>
        <td class="px-4 py-2 bg-orange-50">
            <div class="flex items-center gap-1">
                <input type="number" min="0" max="100" step="0.01" placeholder="0" value="${data.porcentaje_vigente??''}"
                       class="tarifa-vigente w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:border-[#E8611A]">
                <span class="text-gray-400 text-xs">%</span>
            </div>
        </td>
        <td class="px-4 py-2 bg-pink-50">
            <div class="flex items-center gap-1">
                <input type="number" min="0" max="100" step="0.01" placeholder="0" value="${data.porcentaje_castigada??''}"
                       class="tarifa-castigada w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:border-[#C94477]">
                <span class="text-gray-400 text-xs">%</span>
            </div>
        </td>
        <td class="px-4 py-2 text-center">
            <button onclick="document.getElementById('tarifa-row-${n}').remove(); renumerarFilas('tarifas-tbody')"
                    class="w-7 h-7 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg mx-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>`;
    tbody.appendChild(tr);
}

async function guardarTarifas(salir = false) {
    const rows = document.querySelectorAll('#tarifas-tbody tr');
    const tarifas = [];
    let valid = true;
    rows.forEach(r => {
        const nombre = r.querySelector('.tarifa-nombre')?.value.trim();
        if (!nombre) valid = false;
        tarifas.push({
            nombre_tramo: nombre || '',
            porcentaje_vigente: r.querySelector('.tarifa-vigente')?.value || 0,
            porcentaje_castigada: r.querySelector('.tarifa-castigada')?.value || 0,
        });
    });
    if (!tarifas.length || !valid) { document.getElementById('tarifas-error').classList.remove('hidden'); return; }
    document.getElementById('tarifas-error').classList.add('hidden');

    try {
        await apiPost(`/empresas/${empresaId}/tarifas`, { tarifas });
        maxStep = Math.max(maxStep, 3);
        if (salir) { salirAlListado(); return; }
        goStep(3);
    } catch(e) { Swal.fire('Error al guardar tarifas', e.message, 'error'); }
}

// ── STEP 3: Canales ──────────────────────────────────────────────────────────

const mediosPago = ['CUENTA DE AHORROS', 'CUENTA CORRIENTE', 'EFECTIVO', 'PSE', 'TARJETA CRÉDITO', 'TARJETA DÉBITO', 'OTRO'];

function agregarCanal(data = {}) {
    canalCount++;
    const tbody = document.getElementById('canales-tbody');
    const n = canalCount;
    const idx = tbody.children.length + 1;
    const opts = mediosPago.map(m =>
        `<option value="${m}" ${data.medio_pago === m ? 'selected' : ''}>${m}</option>`
    ).join('');
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100 hover:bg-gray-50 transition-colors';
    tr.id = `canal-row-${n}`;
    tr.innerHTML = `
        <td class="px-4 py-2 text-gray-400 text-xs">${idx}</td>
        <td class="px-4 py-2">
            <input type="text" placeholder="Ej: BANCOLOMBIA" value="${escHtml(data.nombre_canal||'')}"
                   class="canal-nombre w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#E8611A]">
        </td>
        <td class="px-4 py-2">
            <input type="text" placeholder="Ej: 1234567890" value="${escHtml(data.numero_canal||'')}"
                   class="canal-numero w-36 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#E8611A]">
        </td>
        <td class="px-4 py-2">
            <select class="canal-medio w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#E8611A]">
                <option value="">-- Seleccionar --</option>
                ${opts}
            </select>
        </td>
        <td class="px-4 py-2 text-center">
            <button onclick="document.getElementById('canal-row-${n}').remove(); renumerarFilas('canales-tbody')"
                    class="w-7 h-7 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg mx-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>`;
    tbody.appendChild(tr);
}

async function guardarCanales(salir = false) {
    const rows = document.querySelectorAll('#canales-tbody tr');
    const canales = [];
    let valid = true;
    rows.forEach(r => {
        const nombre = r.querySelector('.canal-nombre')?.value.trim();
        const numero = r.querySelector('.canal-numero')?.value.trim();
        const medio = r.querySelector('.canal-medio')?.value;
        if (!nombre || !medio) valid = false;
        canales.push({ nombre_canal: nombre || '', numero_canal: numero || null, medio_pago: medio || '' });
    });
    if (!canales.length || !valid) { document.getElementById('canales-error').classList.remove('hidden'); return; }
    document.getElementById('canales-error').classList.add('hidden');

    try {
        await apiPost(`/empresas/${empresaId}/canales`, { canales });
        maxStep = Math.max(maxStep, 4);
        if (salir) { salirAlListado(); return; }
        goStep(4);
    } catch(e) { Swal.fire('Error al guardar canales', e.message, 'error'); }
}

// ── STEP 4: Lineamientos ──────────────────────────────────────────────────────

function agregarLineamiento(tipo, data = {}) {
    lineamientoCount++;
    const n = lineamientoCount;
    const container = document.getElementById('lineamientos-container');
    const div = document.createElement('div');
    div.className = 'border-2 border-gray-200 rounded-xl p-5 transition-all duration-200';
    div.id = `lin-${n}`;
    const colorBadge = tipo === 'porcentaje' ? 'bg-orange-100 text-[#E8611A]' : 'bg-pink-100 text-[#C94477]';
    const labelTipo = tipo === 'porcentaje' ? 'Por Porcentaje' : 'Por Tramo';
    // El primero que se agrega queda activo por defecto
    const isFirst = container.children.length === 0;

    if (tipo === 'porcentaje') {
        div.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 flex-1">
                <label class="flex items-center gap-2 cursor-pointer" title="Marcar como lineamiento activo">
                    <input type="radio" name="lin-activo" value="${n}" class="lin-radio accent-[#E8611A]" ${isFirst ? 'checked' : ''} onchange="marcarActivo(${n})">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full ${colorBadge}">${labelTipo}</span>
                </label>
                <input type="text" placeholder="Concepto (Ej: CAPITAL)" value="${escHtml(data.concepto||'')}"
                       class="lin-concepto border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#E8611A] w-64">
            </div>
            <button onclick="document.getElementById('lin-${n}').remove()" class="text-red-400 hover:text-red-600 ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        <input type="hidden" class="lin-tipo" value="porcentaje">
        <div class="grid grid-cols-2 gap-4 max-w-sm">
            <div>
                <label class="block text-xs font-semibold text-[#E8611A] mb-1">VIGENTE (%)</label>
                <div class="flex items-center gap-1">
                    <input type="number" min="0" max="100" step="0.01" placeholder="0" value="${data.porcentaje?.porcentaje_vigente??''}"
                           class="lin-pct-vigente w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-center focus:outline-none focus:border-[#E8611A]">
                    <span class="text-gray-400 text-xs">%</span>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#C94477] mb-1">CASTIGADO (%)</label>
                <div class="flex items-center gap-1">
                    <input type="number" min="0" max="100" step="0.01" placeholder="0" value="${data.porcentaje?.porcentaje_castigado??''}"
                           class="lin-pct-castigado w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-center focus:outline-none focus:border-[#C94477]">
                    <span class="text-gray-400 text-xs">%</span>
                </div>
            </div>
        </div>`;
    } else {
        div.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 flex-1">
                <label class="flex items-center gap-2 cursor-pointer" title="Marcar como lineamiento activo">
                    <input type="radio" name="lin-activo" value="${n}" class="lin-radio accent-[#C94477]" ${isFirst ? 'checked' : ''} onchange="marcarActivo(${n})">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full ${colorBadge}">${labelTipo}</span>
                </label>
                <input type="text" placeholder="Concepto (Ej: CAPITAL)" value="${escHtml(data.concepto||'')}"
                       class="lin-concepto border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#E8611A] w-64">
            </div>
            <button onclick="document.getElementById('lin-${n}').remove()" class="text-red-400 hover:text-red-600 ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        <input type="hidden" class="lin-tipo" value="tramo">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-bold text-[#E8611A] uppercase tracking-wide">Tramos Vigente</h4>
                    <button onclick="agregarTramoLineamiento('tramos-vigente-${n}','vigente')" class="text-xs text-[#E8611A] hover:underline flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Agregar
                    </button>
                </div>
                <div class="border border-orange-100 rounded-lg overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-orange-50"><tr><th class="text-left px-3 py-2 font-semibold text-[#E8611A]">Tramo</th><th class="text-center px-3 py-2 font-semibold text-[#E8611A]">% Vigente</th><th class="w-8"></th></tr></thead>
                        <tbody id="tramos-vigente-${n}"></tbody>
                    </table>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-bold text-[#C94477] uppercase tracking-wide">Tramos Castigado</h4>
                    <button onclick="agregarTramoLineamiento('tramos-castigado-${n}','castigado')" class="text-xs text-[#C94477] hover:underline flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Agregar
                    </button>
                </div>
                <div class="border border-pink-100 rounded-lg overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-pink-50"><tr><th class="text-left px-3 py-2 font-semibold text-[#C94477]">Tramo</th><th class="text-center px-3 py-2 font-semibold text-[#C94477]">% Castigado</th><th class="w-8"></th></tr></thead>
                        <tbody id="tramos-castigado-${n}"></tbody>
                    </table>
                </div>
            </div>
        </div>`;
    }

    container.appendChild(div);
    marcarActivo(isFirst ? n : null);
    if (tipo === 'tramo' && data.tramos) {
        data.tramos.filter(t => t.tipo_cartera === 'vigente').forEach(t => agregarTramoLineamiento(`tramos-vigente-${n}`, 'vigente', t));
        data.tramos.filter(t => t.tipo_cartera === 'castigado').forEach(t => agregarTramoLineamiento(`tramos-castigado-${n}`, 'castigado', t));
    }
}

function marcarActivo(activeN) {
    document.querySelectorAll('#lineamientos-container > div').forEach(div => {
        const radio = div.querySelector('.lin-radio');
        if (radio && parseInt(radio.value) === activeN) {
            div.classList.add('border-[#E8611A]');
            div.classList.remove('border-gray-200');
        } else {
            div.classList.remove('border-[#E8611A]');
            div.classList.add('border-gray-200');
        }
    });
}

function agregarTramoLineamiento(tbodyId, tipo, data = {}) {
    tramoLinCount++;
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rowId = `tl-${tramoLinCount}`;
    const tr = document.createElement('tr');
    tr.id = rowId;
    tr.className = 'border-t border-gray-100';
    tr.innerHTML = `
        <td class="px-3 py-1.5">
            <input type="text" placeholder="Ej: 1 a 30" value="${escHtml(data.nombre_tramo||'')}"
                   class="tl-nombre w-full border border-gray-200 rounded px-2 py-1 text-xs focus:outline-none focus:border-current">
        </td>
        <td class="px-3 py-1.5">
            <div class="flex items-center gap-1 justify-center">
                <input type="number" min="0" max="100" step="0.01" placeholder="0" value="${data.porcentaje||''}"
                       class="tl-pct w-16 border border-gray-200 rounded px-2 py-1 text-xs text-center focus:outline-none">
                <span class="text-gray-400">%</span>
            </div>
        </td>
        <td class="px-2 py-1.5 text-center">
            <button onclick="document.getElementById('${rowId}').remove()" class="text-red-400 hover:text-red-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>`;
    tbody.appendChild(tr);
}

async function guardarLineamientos(salir = false) {
    const linBlocks = document.querySelectorAll('#lineamientos-container > div');
    if (!linBlocks.length) { document.getElementById('lineamientos-error').classList.remove('hidden'); return; }
    document.getElementById('lineamientos-error').classList.add('hidden');

    const lineamientos = [];
    linBlocks.forEach(block => {
        const tipo = block.querySelector('.lin-tipo')?.value;
        const concepto = block.querySelector('.lin-concepto')?.value.trim();
        const radio = block.querySelector('.lin-radio');
        const is_active = radio ? radio.checked : false;
        if (!tipo || !concepto) return;
        const obj = { tipo, concepto, is_active };
        if (tipo === 'porcentaje') {
            obj.porcentaje_vigente = parseFloat(block.querySelector('.lin-pct-vigente')?.value) || 0;
            obj.porcentaje_castigado = parseFloat(block.querySelector('.lin-pct-castigado')?.value) || 0;
        } else {
            const tramosV = [];
            block.querySelectorAll('[id^="tramos-vigente-"] tr').forEach(r => tramosV.push({ nombre_tramo: r.querySelector('.tl-nombre')?.value.trim() || '', porcentaje: parseFloat(r.querySelector('.tl-pct')?.value) || 0 }));
            const tramosC = [];
            block.querySelectorAll('[id^="tramos-castigado-"] tr').forEach(r => tramosC.push({ nombre_tramo: r.querySelector('.tl-nombre')?.value.trim() || '', porcentaje: parseFloat(r.querySelector('.tl-pct')?.value) || 0 }));
            obj.tramos_vigente = tramosV;
            obj.tramos_castigado = tramosC;
        }
        lineamientos.push(obj);
    });

    try {
        await apiPost(`/empresas/${empresaId}/lineamientos`, { lineamientos });
        if (salir) { salirAlListado(); return; }
        await Swal.fire({ title: '¡Empresa creada!', text: 'La empresa y toda su configuración fueron guardadas exitosamente.', icon: 'success', confirmButtonColor: '#E8611A', confirmButtonText: 'Ver empresas' });
        window.location.href = '/empresas';
    } catch(e) { Swal.fire('Error al guardar lineamientos', e.message, 'error'); }
}

// ── Utils ─────────────────────────────────────────────────────────────────────

function renumerarFilas(tbodyId) {
    const rows = document.getElementById(tbodyId)?.querySelectorAll('tr');
    if (rows) rows.forEach((r, i) => { const td = r.querySelector('td'); if (td) td.textContent = i + 1; });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init
goStep(1);
</script>
@endpush
