<div class="col-md-4 col-md-offset-5 main">

	<h1 class="page-header text-center">Login</h1>

	<form method="post" action="/login" id="login-form">

		<div class="form-group">
			<label for="name">Username</label>
			<input type="text" class="form-control" required id="username" name="username" />
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="name">Password</label>
			<input type="password" class="form-control" required id="password" name="password" />
			<div id="login-error" class="help-block with-errors"></div>
		</div>

		<button id="login-button" type="submit" class="btn btn-default">Login</button>

	</form>

</div>
