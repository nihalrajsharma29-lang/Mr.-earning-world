<x-guest-layout>
    <div class="portal-login-card">
        <div class="portal-card-header">
            <p class="eyebrow">Client Portal</p>
            <h2>Create your client account</h2>
            <p>Enter your details to set up portal access.</p>
        </div>

        <div class="portal-card-body">
            @if($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ request()->fullUrl() }}">
                @csrf

                <div class="portal-form-group">
                    <label class="portal-label" for="name">Name</label>
                    <input class="portal-input" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                </div>

                <div class="portal-form-group">
                    <label class="portal-label" for="email">Email</label>
                    <input class="portal-input" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div class="portal-form-group">
                    <label class="portal-label" for="phone">Phone</label>
                    <input class="portal-input" id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel">
                </div>

                <div class="portal-form-group">
                    <label class="portal-label" for="address">Address</label>
                    <textarea class="portal-input" id="address" name="address" rows="3" autocomplete="street-address">{{ old('address') }}</textarea>
                </div>

                <div class="portal-form-group">
                    <label class="portal-label" for="password">Password</label>
                    <input class="portal-input" id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
                </div>

                <div class="portal-form-group">
                    <label class="portal-label" for="password_confirmation">Confirm Password</label>
                    <input class="portal-input" id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                </div>

                <button class="portal-button" type="submit">Create Client Account</button>
            </form>
        </div>
    </div>
</x-guest-layout>