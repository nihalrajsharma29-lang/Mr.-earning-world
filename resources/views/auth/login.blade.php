<x-guest-layout>
    <div class="min-h-screen bg-slate-950 flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">

        <div class="w-full max-w-md">

            {{-- Logo / Heading --}}
            <div class="mb-8 text-center">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-xl">
                    <x-application-logo class="h-10 w-auto" />
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-white">
                    Sign in to your portal
                </h1>

                <p class="mt-3 text-sm leading-6 text-slate-400">
                    Access your dashboard, reports, and account settings securely.
                </p>
            </div>

            {{-- Login Card --}}
            <div class="overflow-hidden rounded-3xl bg-white shadow-2xl">

                {{-- Card Header --}}
                <div class="bg-slate-900 px-7 py-7 text-center sm:px-9">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
                        Welcome back
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-white">
                        Login to continue
                    </h2>

                    <p class="mt-2 text-sm text-slate-400">
                        Enter your credentials to access your account.
                    </p>
                </div>

                <div class="px-7 py-8 sm:px-9">

                    {{-- Session Status --}}
                    <x-auth-session-status
                        class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                        :status="session('status')"
                    />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Email address
                            </label>

                            <div class="mt-2">
                                <x-text-input
                                    id="email"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="you@example.com"
                                    class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
                                />
                            </div>

                            <x-input-error
                                :messages="$errors->get('email')"
                                class="mt-2 text-sm text-red-600"
                            />
                        </div>

                        {{-- Password --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <label
                                    for="password"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Password
                                </label>

                                @if (Route::has('password.request'))
                                    <a
                                        href="{{ route('password.request') }}"
                                        class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-500"
                                    >
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <div class="mt-2">
                                <x-text-input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
                                />
                            </div>

                            <x-input-error
                                :messages="$errors->get('password')"
                                class="mt-2 text-sm text-red-600"
                            />
                        </div>

                        {{-- Remember Me --}}
                        <div>
                            <label
                                for="remember_me"
                                class="inline-flex cursor-pointer items-center gap-3 text-sm text-slate-600"
                            >
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >

                                <span>Remember me</span>
                            </label>
                        </div>

                        {{-- Login Button --}}
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3.5 text-sm font-bold tracking-wide text-white shadow-lg shadow-indigo-600/20 transition duration-200 hover:bg-indigo-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/30 active:scale-[0.99]"
                        >
                            Log in
                        </button>
                    </form>

                    {{-- Footer --}}
                    <div class="mt-7 border-t border-slate-200 pt-6 text-center">
                        <p class="text-sm text-slate-500">
                            Need help?
                            <span class="font-medium text-slate-700">
                                Contact your administrator for access.
                            </span>
                        </p>
                    </div>

                </div>
            </div>

            {{-- Bottom text --}}
            <p class="mt-6 text-center text-xs text-slate-500">
                Secure portal access
            </p>

        </div>
    </div>
</x-guest-layout>