<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title>vonchess</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="header">
<a href="{{ $frontendUrl }}">vonchess</a>
</td>
</tr>

<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell">
<h1>Welcome to vonchess!</h1>
<p>Click the link below to verify your email address and activate your account.</p>

<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-primary" target="_blank">Verify Email Address</a>
</td>
</tr>
</table>
</td>
</tr>
</table>

<p>This link will expire in 60 minutes.</p>
<p>If you didn't create an account, you can safely ignore this email.</p>
<p>Best regards, The vonchess Team</p>

<table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<p>If you're having trouble clicking the "Verify Email Address" link, copy and paste the URL below into your web browser:<br /> <a href="{{ $url }}">{{ $url }}</a></p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
<p>&copy; 2026 vonchess. All rights reserved.</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>

</body>
</html>