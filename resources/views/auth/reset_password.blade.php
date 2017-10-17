<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>
	<div align="center">
		<form method="post" action="{{route('resetpassword')}}">
			<input type="hidden" name="token" value="{{$token}}">
			<input type="hidden" name="email" value="{{$email}}">
			<p><label for="password">Пароль</label></p>
			<p>
				<input id="password" placeholder="Пароль" required type="password" name="password">
			</p>
			<p><label for="password_confirmation">Повторіть пароль</label></p>
			<p>
				<input id="password_confirmation" placeholder="Повторіть пароль" required type="password" name="password_confirmation">
			</p>
			<p>
				<button type="submit">Зберегти</button>
			</p>
		</form>
	</div>
</body>
</html>