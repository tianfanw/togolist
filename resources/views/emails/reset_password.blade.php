<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <p>Dear {{ $username }}:</p>
    <p>We received a request to change your password on ToGoList.</p>
    <p>Click the link below to set a new password:</p>
    <p><a href="{{ url('password/reset?token='.$password_reset_token.'&amp;uid='.$uid) }}">
            {{ url('password/reset?token='.$password_reset_token.'&amp;uid='.$uid) }}
    </a></p>
    <p>If you don't want to change your password, you can ignore this email.</p>
</body>
</html>