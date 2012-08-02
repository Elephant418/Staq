<form class="form" method="post" action="">
	<div>
		<label for="login">Login:</label>
		<input type="text" id="login" name="login" value="<?= ( isset( $_POST['login'] ) ? $_POST['login'] : '' ) ?>"/>
	</div>
	<div>
		<label for="password">Password:</label>
		<input type="password" id="password" name="password" />
	</div>
	<input type="submit" value="Submit" />
</form>
