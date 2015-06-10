<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	
	<title>Login | Pokemon.vote</title>
	
	<link rel="icon" href="../images/favicon.png" />
	
	<link rel="stylesheet" href="css/reset.css" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="questions-view/questions.css" />
</head>
<body>
	
	<div class="wrapper">
		<h1>
			<img src="images/logo.png" />
		</h1>
		
		<div class="login-box card">
			<h2>
				Select Security Questions
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
				<label for="question_value">
					<?php echo $question['question'] ?>
				</label>
				<input type="text" name="question_value" />
				<input type="hidden" name="question_id" value="<?php echo $question['question_id'] ?>" />
				
				<button name="submit">Submit</button>
			</form>
		</div>
	</div>
	
</body>
</html>
