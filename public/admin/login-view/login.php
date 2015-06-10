<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	
	<title>Admin Login | Pokemon.vote</title>
	
	<link rel="icon" href="../images/favicon.png" />
	
	<link rel="stylesheet" href="../css/reset.css" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="login-view/login.css" />
</head>
<body>
	
	<div class="wrapper">
		<h1>
			<img src="../images/logo.png" />
		</h1>
		
		<div class="login-box card">
			<h2>
				Admin Login
			</h2>
			
			<!-- Error Messages -->
			<?php if (! empty ($form_errors)): ?>
				<ul class="form-errors">
				<?php foreach ($form_errors as $error): ?>
					<li><?php echo $error; ?></li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			
			<form method="post">
				<input
					type="text"
					name="username"
					placeholder="Username"
					value="<?php echo $form_values ['username']; ?>"
					class="<?php echo isset ($form_errors ['username_empty']) ? 'error' : ''; ?>"
					<?php if (! isset ($form_errors ['password_empty'])) { echo 'autofocus'; } ?> />
				
				<input
					type="password"
					name="password"
					placeholder="Password"
					class="<?php echo isset ($form_errors ['password_empty']) ? 'error' : ''; ?>"
					<?php if (isset ($form_errors ['password_empty'])) { echo 'autofocus'; } ?> />
				
				<button name="login">Login</button>
			</form>
		</div>
	</div>
	
</body>
</html>
