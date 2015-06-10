<?php
	// Extract database information from the environment variables
	$db_info = parse_url (getenv ('DATABASE_URL'));
	
	$db_params = array ();
	if (isset ($db_info ['host'])) $db_params ['host']     = $db_info ['host'];
	if (isset ($db_info ['path'])) $db_params ['dbname']   = ltrim ($db_info ['path'], '/');
	if (isset ($db_info ['port'])) $db_params ['port']     = $db_info ['port'];
	if (isset ($db_info ['user'])) $db_params ['user']     = $db_info ['user'];
	if (isset ($db_info ['pass'])) $db_params ['password'] = $db_info ['pass'];
	
	$database_connection_string = 'pgsql:';
	$terms = count ($db_params);
	foreach ($db_params as $field => $value)
	{
	    $terms--;
	    $database_connection_string .= $field . '=' . $value;
	    if ($terms)
	        $database_connection_string .= ';';
	}
?>
