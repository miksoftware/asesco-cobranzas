<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asesco Cobranzas — Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-gradient-screen {
            background: linear-gradient(135deg, #E8611A 0%, #d52d80 100%);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #E8611A 0%, #d52d80 100%);
            background-size: 200% 200%;
            animation: gradientShift 4s ease infinite;
        }
        .btn-gradient:hover {
            background-size: 200% 200%;
            animation: gradientShift 1.5s ease infinite;
            filter: brightness(1.1);
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(232,97,26,0.15);
            border-color: #E8611A;
        }
    </style>
</head>
<body class="bg-gradient-screen min-h-screen flex items-center justify-center p-4 overflow-hidden relative">

    <!-- Card -->
    <div class="relative z-10 w-full max-w-md">
        <div class="bg-white rounded-2xl p-8 shadow-2xl">

            <!-- Logo -->
            <div class="text-center mb-6">
                <img src="{{ asset('images/logo_asesco.png') }}" alt="ASESCO BPO" class="h-28 mx-auto object-contain max-w-full">
            </div>

            <!-- Errores -->
            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-3" role="alert">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-600 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5" autocomplete="off">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-600 mb-1.5">Correo electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               class="input-focus w-full pl-10 pr-4 py-3 rounded-xl bg-white border border-gray-200 text-gray-800 placeholder-gray-400 text-sm focus:outline-none transition-all duration-300"
                               placeholder="tu@email.com">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-600 mb-1.5">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="input-focus w-full pl-10 pr-12 py-3 rounded-xl bg-white border border-gray-200 text-gray-800 placeholder-gray-400 text-sm focus:outline-none transition-all duration-300"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center" aria-label="Mostrar contraseña">
                            <svg id="eye-icon" class="w-5 h-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-gray-300 bg-white text-[#E8611A] focus:ring-[#E8611A]/50 focus:ring-offset-0">
                        <span class="text-sm text-gray-500">Recordarme</span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="btn-gradient w-full py-3 rounded-xl text-white font-semibold text-sm tracking-wide shadow-lg shadow-[#E8611A]/20 hover:shadow-[#E8611A]/40 transition-all duration-300 cursor-pointer">
                    Iniciar Sesión
                </button>
            </form>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-400 mt-6">
                &copy; {{ date('Y') }} ASESCO BPO. Todos los derechos reservados.
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>
</body>
</html>
