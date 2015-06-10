<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	
	<title>Vote | Pokemon.vote</title>
	
	<link rel="icon" href="images/favicon.png" />
	
	<link rel="stylesheet" href="css/reset.css" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="vote-view/vote.css" />
</head>
<body>
	
	<div class="wrapper">
		<h1>
			<img src="images/logo.png" />
		</h1>
		
		<form method="post">
			<button name="logout" class="logout">Logout</button>
		</form>
		
		<h3>
			Hello, <?php echo $auth_manager->auth_user ()->first_name (); ?>!
			Please choose your favorite starter pokemon, then click the submit button to vote.
		</h3>
		
		<!-- Error Messages -->
		<?php if (! empty ($form_errors)): ?>
			<ul class="form-errors">
			<?php foreach ($form_errors as $error): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		
		<form method="post">
			<ul class="candidate-list">
			<?php foreach ($candidates as $candidate): ?>
				
				<li class="card">
					<label class="candidate">
						<div class="candidate-image" style="background-image: url(images/candidates/<?php echo $candidate ['id']; ?>.png);">
							<div class="candidate-check"></div>
						</div>
						<div class="candidate-content">
							<input type="radio" name="candidate" class="radio-button" value="<?php echo $candidate ['id']; ?>" />
							<input type="hidden" name="candidate_name" value="<?php echo $candidate ['name']; ?>" />
							<span class="candidate-name">
								<?php echo $candidate ['name']; ?>
							</span>
						</div>
					</label>
				</li>
				
			<?php endforeach; ?>
			</ul>
			
			<button name="submit">Submit</button>
		</form>
	</div>
	
	<script>
		var radios = document.querySelectorAll ('input[type="radio"]');
		for (var i = 0; i < radios.length; ++i)
		{
			var radio = radios [i];
			radio.onchange = function ()
			{
				if (this.checked)
				{
					for (var j = 0; j < radios.length; ++j)
					{
						var r = radios [j];
						
						r.offsetParent.querySelector ('.candidate-image').className = 'candidate-image';
					}
					
					this.offsetParent.querySelector ('.candidate-image').className = 'candidate-image checked';
				}
			}
		}
	</script>
	
</body>
</html>
