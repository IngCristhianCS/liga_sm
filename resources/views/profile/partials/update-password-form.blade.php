<section>
    <header>
        <h5 class="font-weight-bold text-dark">
            {{ __('Update Password') }}
        </h5>

        <p class="mt-1 small text-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="form-group">
            <div class="row clearfix">
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            @if ($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
            </div>
        </div>

        <div class="form-group">
            <div class="row clearfix">
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</div>
            @endif
            </div>
        </div>

        <div class="form-group">
            <div class="row clearfix">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
            </div>
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <p class="small text-muted ml-3" id="passwordSavedMessage">
                    {{ __('Saved.') }}
                </p>
                <script>
                    setTimeout(function(){
                      document.getElementById('passwordSavedMessage').style.display = 'none';
                    }, 2000);
                </script>
            @endif
        </div>
    </form>
</section>