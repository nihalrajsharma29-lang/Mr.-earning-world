<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Client Portal') }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        .portal-login-shell {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background:
                radial-gradient(
                    circle at 15% 20%,
                    rgba(59, 130, 246, 0.20),
                    transparent 32%
                ),
                radial-gradient(
                    circle at 85% 80%,
                    rgba(99, 102, 241, 0.20),
                    transparent 32%
                ),
                linear-gradient(
                    135deg,
                    #020617 0%,
                    #0f172a 50%,
                    #111827 100%
                );
        }

        .portal-login-container {
            width: 100%;
            max-width: 460px;
        }

        /* Dashboard logo + title */
        .portal-brand {
            text-align: center;
            margin-bottom: 28px;
            color: white;
        }

        .portal-brand-logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: white;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.30);
        }

        .portal-brand-logo img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            display: block;
        }

        .portal-brand h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        /* Login card */
        .portal-login-card {
            width: 100%;
            overflow: hidden;
            border-radius: 28px;
            background: #ffffff;
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.35),
                0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .portal-card-header {
            padding: 30px 34px 26px;
            text-align: center;
            background: linear-gradient(
                135deg,
                #111827,
                #1e293b
            );
            color: white;
        }

        .portal-card-header .eyebrow {
            margin: 0;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .portal-card-header h2 {
            margin: 9px 0 0;
            font-size: 25px;
            font-weight: 700;
        }

        .portal-card-header p {
            margin: 8px 0 0;
            color: #cbd5e1;
            font-size: 13px;
        }

        .portal-card-body {
            padding: 32px;
        }

        .portal-form-group {
            margin-bottom: 22px;
        }

        .portal-label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .portal-input {
            width: 100%;
            height: 50px;
            padding: 0 16px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 14px;
            outline: none;
        }

        .portal-input:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .portal-input::placeholder {
            color: #94a3b8;
        }

        .portal-error {
            margin-top: 7px;
            color: #dc2626;
            font-size: 12px;
        }

        .portal-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 24px;
        }

        .portal-remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
        }

        .portal-remember input {
            width: 16px;
            height: 16px;
            accent-color: #4f46e5;
        }

        .portal-forgot {
            color: #4f46e5;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .portal-forgot:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .portal-button {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(
                135deg,
                #4f46e5,
                #6366f1
            );
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);
        }

        .portal-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(79, 70, 229, 0.32);
        }

        .portal-help {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .portal-footer {
            margin-top: 20px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }

        @media (max-width: 640px) {
            .portal-login-shell {
                padding: 24px 14px;
            }

            .portal-brand h1 {
                font-size: 25px;
            }

            .portal-card-body {
                padding: 24px;
            }

            .portal-card-header {
                padding: 25px 22px;
            }

            .portal-card-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <main class="portal-login-shell">

        <div class="portal-login-container">

            <!-- Logo + Dashboard -->
            <div class="portal-brand">

                <div class="portal-brand-logo">
                    <img
                        src="https://www.clipartmax.com/png/middle/154-1541050_transparent-dashboard-logo-png.png"
                        alt="Dashboard Logo"
                    >
                </div>

                <h1>Dashboard</h1>

            </div>

            {{ $slot }}

            <div class="portal-footer">
                Secure portal access
            </div>

        </div>

    </main>

</body>

</html>