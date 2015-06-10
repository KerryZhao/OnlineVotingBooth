<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	
	<title>Results| Pokemon.vote</title>
	
	<link rel="icon" href="images/favicon.png" />
	
	<link rel="stylesheet" href="css/reset.css" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="result-view/result.css" />
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
			You voted for <?php
							if ($auth_manager->user_has_voted ())
								echo $auth_manager->auth_user()->vote()->candidate_name();
							else
								echo 'nobody';
							?>!
		</h3>

		<ul class="candidate-list">
			<?php foreach ($votes as $vote): ?>
				
				<li class="card">
					<label class="candidate">
						<div class="candidate-image" style="background-image: url(images/candidates/<?php echo $vote ['id']; ?>.png);">
							<div class="candidate-check"></div>
						</div>
						<div class="candidate-content">
							<?php echo $vote ['tally']; ?>
						</div>
					</label>
				</li>
				
			<?php endforeach; ?>
		</ul>

	</div>

</body>
</html>