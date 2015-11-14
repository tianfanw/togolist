<div class="popup-title">
    Welcome to ToGoList, traveler!
</div>
<div class="popup-body">
    <form method="POST" action="/signup">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="error-message">
            @if (count($errors) > 0)
                {{ $errors->first() }}
            @endif
        </div>
        <div style="clear: both; overflow: auto;">
            <div id="signin-left">
                <div class="form-group clearfix">
                    <div class="form-group-right">
                        <label class="inline-label">Email:</label>
                        <input type="email" class="form-control" placeholder="Email" name="email"
                               value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="form-group-right">
                        <label class="inline-label">Password:</label>
                        <input type="password" pattern=".{6,}" class="form-control"
                               placeholder="At least 6 characters" name="password" value="{{ old('password') }}" required>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="form-group-right">
                        <label class="inline-label">Password again:</label>
                        <input type="password" pattern=".{6,}" class="form-control"
                               placeholder="Enter your password again" name="password_confirmation" required>
                    </div>
                </div>
            </div>
            <div id="signin-right">
                <div class="form-group clearfix">
                    <div class="form-group-right">
                        <label class="inline-label">First name:</label>
                        <input type="text" class="form-control" placeholder="Required"
                               name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="form-group-right"class="form-group-right">
                        <label class="inline-label">Last name:</label>
                        <input type="text" class="form-control" placeholder="Required"
                               name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">Say something about yourself:</label>
            <textarea class="form-control" rows="3" placeholder="Optional" name="bio"></textarea>
        </div>
        <div class="form-group form-submit">
            @include('partials.submit-button', ['name' => 'Sign Up'])
            <a class="static-popup-link" data-popup-id="login" href="/login" style="margin-left: 40px;">Log In</a>
        </div>
    </form>
</div>