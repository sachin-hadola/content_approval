<x-guest-layout>
    <div class="mb-4 text-muted small">
        {{ __('Welcome back! Please login to your account.') }}
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success mt-3 shadow-sm border-0 bg-success-subtle text-success">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold text-secondary">Email address</label>
            <input id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-bold text-secondary">Password</label>
            <input id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" type="password" name="password" required />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label text-secondary" for="remember_me">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="d-grid gap-2 mb-4">
            <button class="btn btn-primary btn-lg shadow-sm w-100">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
    
    <div class="mt-4 pt-3 border-top text-muted small text-start">
        <p class="fw-bold mb-2">Test Accounts (password: password)</p>
        <ul class="list-unstyled mb-0 ms-3">
            <li><i class="bi bi-person me-2"></i>author@example.com (Author)</li>
            <li><i class="bi bi-person-gear me-2"></i>manager@example.com (Manager)</li>
            <li><i class="bi bi-shield-lock me-2"></i>admin@example.com (Admin)</li>
        </ul>
    </div>
</x-guest-layout>
