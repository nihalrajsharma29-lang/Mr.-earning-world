<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Fallback styles when Vite assets are not loaded -->
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: #0f172a;
                color: #e2e8f0;
            }
            .login-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
                background: linear-gradient(180deg, #020617 0%, #0f172a 100%);
            }
            .login-card {
                width: 100%;
                max-width: 28rem;
                background: rgba(15, 23, 42, 0.92);
                border: 1px solid rgba(148, 163, 184, 0.16);
                border-radius: 1.75rem;
                box-shadow: 0 25px 80px rgba(15, 23, 42, 0.35);
                overflow: hidden;
            }
            .login-card-inner {
                padding: 2rem;
            }
            .login-header {
                text-align: center;
                padding: 2rem 1.5rem 1.25rem;
                background: rgba(30, 41, 59, 0.98);
            }
            .login-header h2 {
                margin: 0.75rem 0 0;
                font-size: 2rem;
                line-height: 1.1;
                color: #f8fafc;
            }
            .login-header p {
                margin: 0.75rem auto 0;
                color: #cbd5e1;
                font-size: 0.95rem;
                max-width: 26rem;
            }
            .form-group {
                margin-bottom: 1.25rem;
            }
            label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: #e2e8f0;
                font-size: 0.95rem;
            }
            input[type='email'], input[type='password'] {
                width: 100%;
                border-radius: 1rem;
                border: 1px solid rgba(148, 163, 184, 0.35);
                background: #0f172a;
                color: #e2e8f0;
                padding: 0.95rem 1rem;
                font-size: 0.95rem;
                outline: none;
            }
            input[type='email']::placeholder, input[type='password']::placeholder {
                color: #94a3b8;
            }
            .text-muted {
                color: #94a3b8;
            }
            .remember-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                margin-bottom: 1.25rem;
                font-size: 0.95rem;
                color: #cbd5e1;
            }
            .remember-row label {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                color: #cbd5e1;
            }
            .form-button {
                width: 100%;
                display: inline-flex;
                justify-content: center;
                padding: 0.95rem 1rem;
                border-radius: 1rem;
                border: none;
                background: #6366f1;
                color: #ffffff;
                font-weight: 700;
                font-size: 0.95rem;
                cursor: pointer;
            }
            .form-button:hover {
                background: #4f46e5;
            }
            .link-secondary {
                color: #818cf8;
                text-decoration: none;
            }
            .link-secondary:hover {
                text-decoration: underline;
            }
            .status-message {
                margin-bottom: 1rem;
                padding: 1rem;
                border-radius: 1rem;
                background: #0f172a;
                border: 1px solid rgba(16, 185, 129, 0.4);
                color: #a7f3d0;
            }
            .error-text {
                margin-top: 0.5rem;
                color: #f87171;
                font-size: 0.9rem;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased login-shell">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
