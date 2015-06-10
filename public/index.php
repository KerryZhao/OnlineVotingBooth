<?php
	// This will set any environment variables that are normally set by Heroku on the server.
	// This file shouldn't be checked into version control so it will only exist locally.
	include '../config/heroku_env.php';
	
	// Create a new PostgreSQL database connection
	require 'database.php'; // Creates the database connection string
	try 
	{
	$database = new \PDO ($database_connection_string);
	} 
	catch (PDOException $e) 
	{
		die("An error occurred while connecting to the database.");
	}

	// Create a user authentication manager
	require 'user-auth-manager.php';
	$auth_manager = new UserAuthManager ($database);
	
	// If the user has been authenticated,
	// then show either the vote page or the status page,
	// otherwise show the login page.
	if ($auth_manager->is_authenticated ())
	{
		if ($auth_manager->has_security_questions ())
		{
			require 'questions-view/questions-view.php';
			(new QuestionsView ($auth_manager, $database))->show ();
		}
		else if ($_SESSION [AuthManager::_SESSION_AUTH_KEY] [UserAuthManager::USER_NEED_VERIFY])
		{
			require 'two-step-auth-view/two-step-auth-view.php';
			(new TwoStepAuthView ($auth_manager, $database))->show ();
		}
		else if( $auth_manager->cutoff_has_passed ())
		{
			require 'result-view/result-view.php';
			(new ResultView ($database))->show();
		}
		// If the user has already voted, then show the status page,
		// otherwise show the vote page.
		else if ($auth_manager->user_has_voted ())
		{
			require 'status-view/status-view.php';
			(new StatusView ($auth_manager, $database))->show ();
		}
		else
		{
			require 'vote-view/vote-view.php';
			(new VoteView ($database))->show ();
		}
	}
	else
	{
		require 'login-view/login-view.php';
		(new LoginView ($auth_manager))->show ();
	}
?>
