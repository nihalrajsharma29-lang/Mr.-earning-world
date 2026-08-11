<style>
    * {
        box-sizing: border-box;
    }

    .portal-login {
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 20px;
        background:
            radial-gradient(circle at 15% 20%, rgba(37, 99, 235, .18), transparent 30%),
            radial-gradient(circle at 85% 80%, rgba(124, 58, 237, .18), transparent 30%),
            linear-gradient(135deg, #07111f 0%, #0b1730 48%, #111827 100%);
        font-family: Arial, Helvetica, sans-serif;
    }

    .portal-login-card {
        width: 100%;
        max-width: 440px;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
    }

    /* TOP SECTION */
    .portal-top {
        padding: 34px 36px 32px;
        text-align: center;
        background: linear-gradient(135deg, #0f1f3d, #172554);
        color: white;
    }

    .portal-logo {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid rgba(255, 255, 255, .25);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .20);
        overflow: hidden;
    }

    .portal-logo img {
        width: 48px;
        height: 48px;
        object-fit: contain;
        display: block;
    }

    .portal-top h1 {
        margin: 0;
        font-size: 30px;
        line-height: 1.2;
        font-weight: 800;
    }

    /* FORM */
    .portal-form {
        padding: 34px 36px 30px;
    }

    .portal-welcome {
        margin-bottom: 25px;
    }

    .portal-welcome h2 {
        margin: 0;
        color: #111827;
        font-size: 22px;
        font-weight: 800;
    }

    .portal-welcome p {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .portal-field {
        margin-bottom: 20px;
    }

    .portal-field label {
        display: block;
        margin-bottom: 8px;
        color: #1e293b;
        font-size: 13px;
        font-weight: 700;
    }

    .portal-input {
        width: 100%;
        height: 50px;
        padding: 0 15px;
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        outline: none;
        background: #f8fafc;
        color: #0f172a;
        font-size: 14px;
        transition: .2s ease;
    }

    .portal-input:focus {
        background: #ffffff;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, .10);
    }

    .portal-input::placeholder {
        color: #94a3b8;
    }

    .portal-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
    }

    .portal-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin: 5px 0 22px;
    }

    .portal-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 13px;
    }

    .portal-remember input {
        width: 15px;
        height: 15px;
        accent-color: #4f46e5;
    }

    .portal-forgot {
        color: #4f46e5;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }

    .portal-forgot:hover {
        text-decoration: underline;
    }

    .portal-button {
        width: 100%;
        height: 52px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: white;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(79, 70, 229, .25);
        transition: .2s ease;
    }

    .portal-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(79, 70, 229, .32);
    }

    .portal-status {
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 10px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        font-size: 13px;
    }

    .portal-footer {
        padding-top: 22px;
        margin-top: 25px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.5;
    }

    @media (max-width: 520px) {
        .portal-login {
            padding: 15px;
        }

        .portal-top {
            padding: 30px 24px 27px;
        }

        .portal-form {
            padding: 28px 23px 25px;
        }

        .portal-top h1 {
            font-size: 25px;
        }
    }
</style>


<div class="portal-login">

    <div class="portal-login-card">

        {{-- TOP --}}
        <div class="portal-top">

            <div class="portal-logo">
                <img
                    src="https://www.clipartmax.com/png/middle/154-1541050_transparent-dashboard-logo-png.png"
                    alt="Dashboard Logo"
                >
            </div>

            <h1>Dashboard</h1>

        </div>


        {{-- LOGIN FORM --}}
        <div class="portal-form">

            <div class="portal-welcome">
                <h2>Welcome back</h2>
                <p>Sign in to continue to your account.</p>
            </div>


            <x-auth-session-status
                class="portal-status"
                :status="session('status')"
            />


            <form method="POST" action="{{ route('login') }}">
                @csrf


                {{-- EMAIL --}}
                <div class="portal-field">

                    <label for="email">
                        Email address
                    </label>

                    <input
                        id="email"
                        class="portal-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        required
                        autofocus
                        autocomplete="username"
                    >

                    @if ($errors->get('email'))
                        <div class="portal-error">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                </div>


                {{-- PASSWORD --}}
                <div class="portal-field">

                    <label for="password">
                        Password
                    </label>

                    <input
                        id="password"
                        class="portal-input"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >

                    @if ($errors->get('password'))
                        <div class="portal-error">
                            {{ $errors->first('password') }}
                        </div>
                    @endif

                </div>


                {{-- REMEMBER / FORGOT --}}
                <div class="portal-row">

                    <label class="portal-remember">

                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                        >

                        <span>Remember me</span>

                    </label>


                    @if (Route::has('password.request'))

                        <a
                            class="portal-forgot"
                            href="{{ route('password.request') }}"
                        >
                            Forgot password?
                        </a>

                    @endif

                </div>


                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="portal-button"
                >
                    Sign in to Portal
                </button>

            </form>


            <div class="portal-footer">
                Secure portal access · Your credentials are protected.
            </div>

        </div>

    </div>

</div>