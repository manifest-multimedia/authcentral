<x-backend.dashboard>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h1 class="card-title">{{ __('Two Factor Authentication') }}</h1></div>

                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-900">
                            @if (auth()->user()->two_factor_confirmed_at)
                                {{ __('You have enabled two factor authentication.') }}
                            @else
                                {{ __('You have not enabled two factor authentication.') }}
                            @endif
                        </h3>

                        <div class="mt-3 max-w-xl text-sm text-gray-600">
                            <p>
                                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                            </p>
                        </div>

                        @if (! auth()->user()->two_factor_secret)
                            {{-- Enable 2FA --}}
                            <form method="POST" action="{{ route('two-factor.enable') }}">
                                @csrf
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Enable Two-Factor Authentication') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- Show QR Code --}}
                            <div class="mt-4">
                                <p class="font-semibold">
                                    @if (! auth()->user()->two_factor_confirmed_at)
                                        {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application and enter the setup key below to complete the process.') }}
                                    @else
                                        {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}
                                    @endif
                                </p>
                            </div>

                            <div class="mt-4">
                                <div class="p-2 bg-white">
                                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                </div>
                            </div>

                            {{-- Show Setup Key --}}
                            <div class="mt-4 max-w-xl text-sm">
                                <p class="font-semibold">
                                    {{ __('Setup Key') }}: {{ decrypt(auth()->user()->two_factor_secret) }}
                                </p>
                            </div>

                            @if (! auth()->user()->two_factor_confirmed_at)
                                {{-- Confirm 2FA Setup --}}
                                <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-4">
                                    @csrf

                                    <div class="form-group">
                                        <label for="code" class="form-label">{{ __('Code') }}</label>
                                        <input id="code" 
                                               type="text" 
                                               name="code" 
                                               class="form-control @error('code') is-invalid @enderror" 
                                               required 
                                               autocomplete="one-time-code" 
                                               autofocus />
                                        
                                        @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Confirm') }}
                                        </button>
                                    </div>
                                </form>
                            @endif

                            {{-- Recovery Codes --}}
                            @if (auth()->user()->two_factor_confirmed_at)
                                <div class="mt-4">
                                    <p class="font-semibold">
                                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                                    </p>
                                </div>

                                <div class="grid gap-1 max-w-xl mt-2 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                        <div>{{ $code }}</div>
                                    @endforeach
                                </div>

                                {{-- Regenerate Recovery Codes --}}
                                <form method="POST" action="{{ route('two-factor.recovery-codes') }}" class="mt-4">
                                    @csrf

                                    <div>
                                        <button type="submit" class="btn btn-secondary">
                                            {{ __('Regenerate Recovery Codes') }}
                                        </button>
                                    </div>
                                </form>
                            @endif

                            {{-- Disable 2FA --}}
                            <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4">
                                @csrf
                                @method('DELETE')

                                <div>
                                    <button type="submit" class="btn btn-danger">
                                        {{ __('Disable Two-Factor Authentication') }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.dashboard>