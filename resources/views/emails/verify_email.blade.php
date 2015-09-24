<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <p>Dear {{ $username }}:</p>
    <p>Please <a href="{{ url('email/verify?token='.$email_confirm_token.'&amp;uid='.$uid) }}">click here</a>
        to activate your account.</p>
</body>
</html>