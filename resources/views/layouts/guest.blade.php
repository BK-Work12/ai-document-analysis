<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DocAnalysis AI') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">DocAnalysis AI</span>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-8 py-10">
                    {{ $slot }}
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-6">
                <a href="/" class="text-sm text-gray-600 hover:text-blue-600 transition">
                    ← Back to Home
                </a>
            </div>
        </div>
    </body>
</html>
