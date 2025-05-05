<x-backend.dashboard>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Profile Card -->
                <div class="card mb-5">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title">{{ __('Profile Information') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                <!-- Profile Picture -->
                                <div class="mb-3">
                                    @if(auth()->user()->profile_photo_path)
                                        <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 150px; height: 150px; margin: 0 auto;">
                                            <span class="fs-1">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updatePhotoModal">
                                    {{ __('Update Photo') }}
                                </button>
                                
                                <!-- User Roles Section -->
                                <div class="mt-3">
                                    <h6 class="fw-bold">{{ __('Your Roles') }}</h6>
                                    @if(auth()->user()->roles->count() > 0)
                                        <div class="mt-2">
                                            @foreach(auth()->user()->roles as $role)
                                                <span class="badge bg-info text-dark mb-1">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">{{ __('No roles assigned') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-9">
                                <!-- Update Profile Form -->
                                <form method="POST" action="{{ route('user-profile-information.update') }}">
                                    @csrf
                                    @method('PUT')

                                    <!-- Success Message -->
                                    @if (session('status') === 'profile-information-updated')
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ __('Profile updated successfully!') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <!-- Name Input -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ __('Name') }}</label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? auth()->user()->name }}" required autocomplete="name">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Email Input -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email') }}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? auth()->user()->email }}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- User Role Display (Read-only) -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Role') }}</label>
                                        <div class="form-control bg-light">
                                            @if(auth()->user()->roles->count() > 0)
                                                @foreach(auth()->user()->roles as $role)
                                                    <span class="badge bg-info text-dark me-1">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">{{ __('No roles assigned') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-text text-muted">{{ __('Roles define your access permissions in the system') }}</div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Update Password') }}</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user-password.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Success Message -->
                            @if (session('status') === 'password-updated')
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ __('Password updated successfully!') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Current Password Input -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                                <input id="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" name="current_password" required autocomplete="current-password">
                                @error('current_password', 'updatePassword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- New Password Input -->
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password', 'updatePassword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Photo Update Modal -->
    <div class="modal fade" id="updatePhotoModal" tabindex="-1" aria-labelledby="updatePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePhotoModalLabel">{{ __('Update Profile Photo') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user-profile-photo.update') }}" method="POST" enctype="multipart/form-data" id="profilePhotoForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="photo" class="form-label">{{ __('Select New Photo') }}</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <div class="form-text">{{ __('Please select an image file (JPG, PNG, GIF).') }}</div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-backend.dashboard>