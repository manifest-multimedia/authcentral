<x-backend.auth title="Reset Password" description="Create a New Password">
    <form class="px-4 mx-auto mw-sm" method="POST" action="{{ route('password.update') }}">
        @csrf <!-- CSRF Token -->

        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        
        <div class="mb-4">
            @if (session('status'))
                <div class="mb-4 alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
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

        <div class="mb-4 row g-6">
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="email">Email</label>
                    <input class="shadow form-control text-secondary-dark" id="email" type="email" name="email"
                        value="{{ $request->email ?? old('email') }}" required autofocus readonly aria-describedby="emailHelp">
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="password">Password</label>
                    <input class="shadow form-control text-secondary-dark" id="password" type="password" name="password"
                        required aria-describedby="passwordHelp" placeholder="New password">
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="password_confirmation">Confirm Password</label>
                    <input class="shadow form-control text-secondary-dark" id="password_confirmation" type="password"
                        name="password_confirmation" required aria-describedby="passwordConfirmHelp" placeholder="Confirm new password">
                </div>
            </div>
        </div>

        <div class="mb-6 row">
            <div class="col-12">
                <button class="py-3 shadow btn btn-lg btn-primary fs-11 w-100 text-primary-light" type="submit">
                    Reset Password
                </button>
            </div>
        </div>
        
        <p class="mb-0 text-center fs-13 fw-medium text-light-dark">
            <span>Remember your password?</span>
            <a class="text-primary link-primary" href="{{ route('login') }}">Back to Login</a>
        </p>
    </form>
</x-backend.auth>