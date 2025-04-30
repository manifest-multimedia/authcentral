<x-backend.auth title="Register" description="Create a new account">
    <form class="px-4 mx-auto mw-sm pb-4" method="POST" action="{{ route('register') }}">
        @csrf <!-- CSRF Token -->

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

        <div class="mb-3 row g-3">
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="name">Name</label>
                    <input class="shadow form-control text-secondary-dark" id="name" type="text" name="name"
                        value="{{ old('name') }}" required autofocus aria-describedby="nameHelp" placeholder="Jay Joeson">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="email">Email</label>
                    <input class="shadow form-control text-secondary-dark" id="email" type="email" name="email"
                        value="{{ old('email') }}" required aria-describedby="emailHelp" placeholder="youremail@pnmtc.edu.gh">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="password">Password</label>
                    <input class="shadow form-control text-secondary-dark" id="password" type="password"
                        name="password" required aria-describedby="passwordHelp" placeholder="password">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label class="mb-1 fw-medium text-light-dark" for="password_confirmation">Password
                        Confirmation</label>
                    <input class="shadow form-control text-secondary-dark" id="password_confirmation" type="password"
                        name="password_confirmation" required aria-describedby="passwordHelp"
                        placeholder="confirm password">
                </div>
            </div>
        </div>

        <div class="mb-4 row align-items-center justify-content-between g-2">
            <div class="col-auto">
                <div class="form-check">
                    <input class="shadow form-check-input" id="remember_me" type="checkbox" name="remember">
                    <label class="form-check-label fs-13 fw-medium text-light-dark" for="remember_me">Remember
                        me</label>
                </div>
            </div>
            <div class="col-auto">
                <a class="fs-13 fw-medium text-primary link-primary" href="{{ route('password.request') }}">Forgot your
                    password?</a>
            </div>
        </div>

        <div class="mb-4 row">
            <div class="col-12">
                <button class="py-3 shadow btn btn-lg btn-primary fs-11 w-100 text-primary-light"
                    type="submit">Register</button>
            </div>
        </div>

        <p class="mb-0 text-center fs-13 fw-medium text-light-dark">
            <span>Existing user?</span>
            <a class="text-primary link-primary" href="{{ route('login') }}">Login</a>
        </p>
    </form>
</x-backend.auth>
