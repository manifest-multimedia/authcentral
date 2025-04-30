<x-backend.auth title="Confirm Password" description="Please confirm your password to continue">
    <form class="px-4 mx-auto mw-sm" method="POST" action="{{ route('password.confirm') }}">
        @csrf <!-- CSRF Token -->

        <div class="mb-4">
            @if ($errors->any())
                <div class="mb-4 alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <span>
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <div class="mb-4">
            <p class="fs-13 text-secondary-dark">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>
        </div>

        <div class="mb-4 row g-6">
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="password">Password</label>
                    <input class="shadow form-control text-secondary-dark" id="password" type="password" name="password"
                        required autocomplete="current-password">
                </div>
            </div>
        </div>

        <div class="mb-6 row">
            <div class="col-12">
                <button class="py-3 shadow btn btn-lg btn-primary fs-11 w-100 text-primary-light" type="submit">
                    {{ __('Confirm') }}
                </button>
            </div>
        </div>
        
        <p class="mb-0 text-center fs-13 fw-medium text-light-dark">
            <a class="text-primary link-primary" href="{{ route('dashboard') }}">{{ __('Back to Dashboard') }}</a>
        </p>
    </form>
</x-backend.auth>