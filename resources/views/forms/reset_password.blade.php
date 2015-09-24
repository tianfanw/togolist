<div class="popup-title">
    Reset password
</div>
<div class="popup-body">
    <div class="error-message">
        @if (count($errors) > 0)
            {{ $errors->first() }}
        @endif
    </div>
    <form method="POST" action="/password/reset">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="token" value="{{ $password_reset_token }}">
        {{--<input type="hidden" name="token" value="aaa">--}}
        <div class="form-group clearfix">
            <div class="form-group-right">
                <label class="inline-label">Your email:</label>
                <input type="email" class="form-control" placeholder="Email" name="email"
                       value="{{ $email }}" readonly>
                {{--<input type="email" class="form-control" placeholder="Email" name="email"--}}
                       {{--value="aa@aa.aa" readonly>--}}
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="form-group-right">
                <label class="inline-label">New Password:</label>
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
        <div class="form-group form-submit">
            <button type="submit" class="btn btn-primary" style="margin: 0 auto; display: block;">
                Reset password
            </button>
            <img class="ajax-loader" src="/image/ajax-loader.gif" style="display:none;">
        </div>
    </form>
</div>