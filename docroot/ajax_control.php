<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');

session_cache_limiter('private, must-revalidate');	//probably no effect...
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include $_SERVER['DOCUMENT_ROOT'] . '/../conf/include_files.php';

//session_start();	//Need to have *after* class defs to then save those classes in sessions	

//Class representing the Ajax controller (controller as in "MVC")
class AjaxControl
{
	//properties
	var $ajaxModel;
	
	//constructor
	function __construct()
	{
		$this->ajaxModel = new AjaxModel();
	}
	
	//send the results of a model function to the client browser
	function sendResponse($response)
	{
		if($_REQUEST['responseType'] == 'text')
		{
			header('Content-type: text/html; charset=utf-8');
			echo $response;
		}
		
		elseif($_REQUEST['responseType'] == 'xml')
		{
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8"?><rootNode>' . $response . '</rootNode>';	//default root node?
		}
	}
}

//main
$ajaxControl = new AjaxControl();
$ajaxControl->sendResponse($ajaxControl->ajaxModel->{$_REQUEST['job']}());	//send response XML to client browser