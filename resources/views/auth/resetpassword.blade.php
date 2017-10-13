<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div align="center">
		<h1>Creation a new password!</h1>
		<form method="post" action="http://localhost:8000/resetpassword">
			<input type="hidden" name="token" value="{{$token}}">
			<input type="hidden" name="email" value="{{$email}}">
			<div>
				<input required type="password" name="password">
			</div>
			<div>
				<input required type="password" name="password_confirmation">
			</div>
			<div>
				<button type="submit">Зберегти</button>
			</div>
		</form>
	</div>
</body>
</html>