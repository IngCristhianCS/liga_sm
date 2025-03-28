<section>
    <header>
        <h5 class="font-weight-bold text-dark">
            {{ __('Profile Information') }}
        </h5>

        <p class="mt-1 small text-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if ($errors->has('name'))
                <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if ($errors->has('email'))
                <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="small mt-2 text-muted">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="text-decoration-underline small text-muted">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-weight-medium small text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p class="small text-muted ml-3" id="savedMessage">
                    {{ __('Saved.') }}
                </p>
                <script>
                    setTimeout(function(){
                      document.getElementById('savedMessage').style.display = 'none';
                    }, 2000);
                </script>
            @endif
        </div>
    </form>
</section>