<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');

include $_SERVER['DOCUMENT_ROOT'] . '/../conf/include_files.php';

//session_start();	//Need to have *after* class defs to then save those classes in sessions

$homePage = new HomePage();

if(isset($_REQUEST['job']))
{
	$homePage->{$_REQUEST['job']}();
}

$homePage->display();

//exit;