<x-guest-layout>
    <div class="portal-login-card">
        <div class="portal-card-header">
            <p class="eyebrow">Client Portal</p>
            <h2>Account created</h2>
            <p>Welcome, {{ $clientName }}.</p>
        </div>
        <div class="portal-card-body">
            <p class="mb-5 text-center text-sm text-slate-600">Your client account is ready. You can now sign in using your email and password.</p>
            <a href="{{ route('login') }}" class="portal-button flex items-center justify-center text-decoration-none">Go to Sign In</a>
        </div>
    </div>
</x-guest-layout>