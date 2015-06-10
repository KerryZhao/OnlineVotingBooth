<?php
	// This will set any environment variables that are normally set by Heroku on the server.
	// This file shouldn't be checked into version control so it will only exist locally.
	include '../../config/heroku_env.php';
	
	// Create a new PostgreSQL database connection
	require '../database.php'; // Creates the database connection string
	try 
	{
	$database = new \PDO ($database_connection_string);
	} 
	catch (PDOException $e) 
	{
		die("An error occurred while connecting to the database.");
	}
	
	// Create an authentication manager
	require 'admin-auth-manager.php';
	$auth_manager = new AdminAuthManager ($database);
	
	// If the admin has been authenticated,
	// then show the admin tools view,
	// otherwise show the login page.
	if ($auth_manager->is_authenticated ())
	{
		require 'admin-view/admin-view.php';
		(new AdminView ($database))->show ();
	}
	else
	{
		require 'login-view/login-view.php';
		(new AdminLoginView ($auth_manager))->show ();
	}
?>
