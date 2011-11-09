<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

require_once 'library.database.php';	//For connectToDatabase()

//Attempt to connect to DB
//Bomb out if we can't
$database = connectToDatabase();

$debugEcho = $database->queryOne("SELECT setting FROM config WHERE name = 'debug_echo'");
$debugLog = $database->queryOne("SELECT setting FROM config WHERE name = 'debug_log'");
$debugEmail = $database->queryOne("SELECT setting FROM config WHERE name = 'debug_email'");
$debugLogFile = $database->queryOne("SELECT setting FROM config WHERE name = 'debug_log_file'");

function debug_echo($item)
{
	global $debugEcho;
	
	if($debugEcho != 'yes')
	{
		return;
	}
	
	echo '<pre class="debug">';
	var_dump($item);
	echo '</pre>';
}

function debug_log($message)
{
	//Debug log is ALWAYS safe to write?
	global $debugLog;
	
	if($debugLog != 'yes')
	{
		return;
	}
	
	
	global $debugLogFile;
	
	$dateAndTime = date('Y/m/d H:i:s');
	
	error_log("{$dateAndTime}] {$message}\n", 3, $debugLogFile); 
}

function debug_email($message)
{
	//Debug email is ALWAYS safe to send?
	
	global $debugEmail;
	
	if($debugEmail != 'yes')
	{
		return;
	}
	
	//mb_language('ja');
	//mb_send_mail();
}







?>