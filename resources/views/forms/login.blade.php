<div class="popup-title">
    Welcome back, traveller!
</div>
<div class="popup-body">
    <div id="email-login">
        <div class="error-message">
            @if (count($errors) > 0)
                {{ $errors->first() }}
            @endif
        </div>
        <form method="POST" action="/login">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" name="password" value="{{ old('password') }}" required>
            </div>
            <div class="form-group form-hint">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember"> Remember Me
                    </label>
                    <a class="static-popup-link" data-popup-id="forgot_password" href="/password/forgot" style="margin-left: 10px;">forgot password?</a>
                </div>
            </div>
            <div class="form-group form-submit">
                @include('partials.submit-button', ['name' => 'Login'])
                <a class="static-popup-link" data-popup-id="signup" href="/signup" style="float: right;">Sign up</a>
            </div>

        </form>
    </div>
    <div id="facebook-login">
        <h3>Facebook Login</h3>
    </div>
</div>