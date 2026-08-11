<x-guest-layout>
    <div class="min-h-screen bg-slate-950 py-10 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-md">
            <div class="text-center mb-8">
                <x-application-logo class="mx-auto h-16 w-auto text-white" />
                <h2 class="mt-6 text-3xl font-semibold tracking-tight text-white">Sign in to your portal</h2>
                <p class="mt-3 text-sm text-slate-300">Access your dashboard, reports, and account settings securely.</p>
            </div>

            <div class="overflow-hidden rounded-[2rem] bg-white/95 shadow-2xl ring-1 ring-slate-900/10 backdrop-blur">
                <div class="p-8 sm:p-10">
                    <div class="mb-6 rounded-3xl bg-slate-900/95 px-6 py-5 text-center text-white shadow-sm sm:px-8">
                        <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Welcome back</p>
                        <h3 class="mt-3 text-2xl font-semibold">Login to continue</h3>
                    </div>

                    <x-auth-session-status class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                            <div class="mt-2">
                                <x-text-input id="email" class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                            <div class="mt-2">
                                <x-text-input id="password" class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" type="password" name="password" required autocomplete="current-password" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                Remember me
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <x-primary-button class="w-full rounded-2xl py-3 text-sm font-semibold tracking-wide">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </form>

                    <div class="mt-7 border-t border-slate-200 pt-6 text-center text-sm text-slate-500">
                        Need help? Contact your administrator for access.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
