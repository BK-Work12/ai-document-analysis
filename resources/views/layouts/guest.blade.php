<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Analyst Saferwealth') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif
        <style>
            body { font-family: 'Inter', sans-serif; }
            .auth-bg {
                background: linear-gradient(135deg, #eef8f6 0%, #ffffff 50%, #f3fbfa 100%);
                color: #244f4b;
            }
            .brand-icon {
                background: linear-gradient(135deg, #2d7670 0%, #39ac99 100%);
            }
            .brand-text {
                color: #2d7670;
                font-weight: 700;
            }
            .auth-card {
                border-color: #d9ebe8;
            }
            .back-home {
                color: #4b7f7a;
            }
            .back-home:hover {
                color: #2d7670;
            }
            .auth-input {
                border: 1px solid #bcded8;
                border-radius: 0.5rem;
            }
            .auth-input:focus {
                border-color: #2d7670;
                box-shadow: 0 0 0 3px rgba(45, 118, 112, 0.18);
                outline: none;
            }
            .auth-btn-primary {
                background: linear-gradient(135deg, #2d7670 0%, #39ac99 100%);
                color: #ffffff;
                font-weight: 600;
                border-radius: 0.5rem;
                transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            }
            .auth-btn-primary:hover {
                filter: brightness(1.03);
                box-shadow: 0 10px 24px -10px rgba(45, 118, 112, 0.45);
                transform: translateY(-1px);
            }
            .auth-link {
                color: #2d7670;
                font-weight: 600;
            }
            .auth-link:hover {
                color: #245f5a;
            }
        </style>
    </head>
    <body class="font-sans auth-bg">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 brand-icon rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl brand-text">Analyst Saferwealth</span>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md bg-white shadow-xl rounded-2xl overflow-hidden border auth-card">
                <div class="px-8 py-10">
                    {{ $slot }}
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-6">
                <a href="/" class="text-sm back-home transition">
                    ← Back to Home
                </a>
            </div>
        </div>
    </body>
</html>
