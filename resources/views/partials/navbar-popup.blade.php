@if (Auth::guest())
<!-- Login popup window -->
<div class="popup" id="login">
    <div class="popup-content">
        <span class="glyphicon glyphicon-remove-circle popup-close"></span>
        @include('forms.login')
    </div>
</div>

<!-- Forgot password popup window -->
<div class="popup" id="forgot_password">
    <div class="popup-content">
        <span class="glyphicon glyphicon-remove-circle popup-close"></span>
        @include('forms.forgot_password')
    </div>
</div>

<!-- Signup popup window -->
<div class="popup" id="signup">
    <div class="popup-content">
        <span class="glyphicon glyphicon-remove-circle popup-close"></span>
        @include('forms.signup')
    </div>
</div>
@else
<!-- Profile popup window -->
<div class="popup" id="profile">
    <div class="popup-content">
        <span class="glyphicon glyphicon-remove-circle popup-close"></span>
        <div class="popup-title">
            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </div>
        <div class="popup-body">
            Introduction
            @if( Auth::user()->status == 'unactivated')
                <a class="ajax-link" href="/email/resend">Resend email</a>
            @endif
        </div>
    </div>
</div>

<!-- Notification popup window -->
<div class="popup" id="notification">
    <div class="popup-content">
        <span class="glyphicon glyphicon-remove-circle popup-close"></span>
        <div class="popup-title">
            Notification Center
        </div>
        <div class="popup-body">
            notifications
        </div>
    </div>
</div>
@endif