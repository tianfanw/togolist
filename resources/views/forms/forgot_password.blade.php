<div class="popup-title">
    Forgot password?
</div>
<div class="popup-body" style="width:250px;">
    <div>
        <p>Enter the email address associated with your account, and we'll email you a link to reset your password.</p>
    </div>
    <div class="error-message">
        @if (count($errors) > 0)
            {{ $errors->first() }}
        @endif
    </div>
    <form method="POST" action="/password/forgot">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" name="email"
                   value="{{ old('email') }}" style="margin-left: 15px;" required>
        </div>
        <div class="form-group form-submit" style="text-align: center">
            @include('partials.submit-button', ['name' => 'Send Reset Link'])
        </div>
    </form>
</div>