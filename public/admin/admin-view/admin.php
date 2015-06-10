<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	
	<title>Admin Tools | Pokemon.vote</title>
	
	<link rel="icon" href="../images/favicon.png" />
	
	<link rel="stylesheet" href="../css/reset.css" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="admin-view/admin.css" />
	<link rel="stylesheet" href="login-view/login.css" />
</head>
<body>
		
	<div class="wrapper">
		<h1>
			<img src="../images/logo.png" />
		</h1>
		
		<h3>
			Hello, <?php echo $auth_manager->auth_admin ()->username (); ?>!
			Use the administrator tools below to manage the voting system.
		</h3>
		
		<div class="login-box card">
			<h2>
				Create User
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
				<label for="firstname">First Name</label>
				<input id="firstname" type="text" name="firstname"/>
				
				<label for="lastname">Last Name</label>
				<input id="lastname" type="text" name="lastname"/>
				
				<label for="username">Username</label>
				<input id="username" type="text" name="username"/>
					
				<button name="create-user">Create User</button>
			</form>
		</div>
		
		<div class="login-box card">
			<h2>
				Create Candidate
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
				<label for="name">Candidate Name</label>
				<input id="name" type="text" name="name"/>
					
				<button name="create-candidate">Create Candidate</button>
			</form>
		</div>
		
		<div class="login-box card">
			<h2>
				Reset Vote
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
				<strong>Reset the vote?</strong>
				<strong>(This will clear all votes and candidates!)</strong>
					
				<button name="reset-vote">Reset Vote</button>
			</form>
		</div>
		
		<div class="login-box card">
			<h2>
				Vote Cutoff Date
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
				<strong>Set Vote Cutoff Date</strong><br/>
				<strong>After this date users won't be able to vote and will see results</strong>
				
				<!-- DATE PICKER GOES HERE -->
				<input id="cutoff-date" type="text" name="cutoff-date"/>
					
				<button name="set-cutoff-date">Set Vote Cutoff</button>
			</form>
		</div>
	</div>
</body>
</html>
