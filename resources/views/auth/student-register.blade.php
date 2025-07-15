<x-backend.auth title="Student Registration" description="Register as a student to access the college portal">
    <form class="px-4 mx-auto mw-sm pb-4" method="POST" action="{{ route('student.register.store') }}">
        @csrf

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
                <label for="name" class="form-label text-dark fw-medium fs-13">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>
        </div>

        <div class="mb-3 row g-3">
            <div class="col-12">
                <label for="email" class="form-label text-dark fw-medium fs-13">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="mb-3 row g-3">
            <div class="col-md-6">
                <label for="phone" class="form-label text-dark fw-medium fs-13">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
            </div>
            <div class="col-md-6">
                <label for="date_of_birth" class="form-label text-dark fw-medium fs-13">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
            </div>
        </div>

        <div class="mb-3 row g-3">
            <div class="col-12">
                <label for="gender" class="form-label text-dark fw-medium fs-13">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>

        <div class="mb-3 row g-3">
            <div class="col-md-6">
                <label for="password" class="form-label text-dark fw-medium fs-13">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label text-dark fw-medium fs-13">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <div class="mb-4 row">
            <div class="col-12">
                <button class="py-3 shadow btn btn-lg btn-primary fs-11 w-100 text-primary-light" type="submit">
                    Register as Student
                </button>
            </div>
        </div>

        <p class="mb-0 text-center fs-13 fw-medium text-light-dark">
            <span>Already have an account?</span>
            <a class="text-primary link-primary" href="{{ route('login') }}">Login</a>
        </p>

        <div class="mt-4 text-center">
            <hr class="my-3">
            <p class="mb-2 text-center fs-13 fw-medium text-light-dark">
                <span>Need a regular account?</span>
            </p>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('sign-up') }}">
                Regular Registration
            </a>
        </div>
    </form>
</x-backend.auth>
