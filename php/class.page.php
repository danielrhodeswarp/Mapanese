<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Class representing a page in the browser
class Page
{
	//Properties---------------
	var $request;		//Container for cleaned up $_REQUEST[]
	var $xhtmlFile;		//File with XHTML contents
		
	//Constructor--------------
	function __construct($xhtml_file)
	{
		//Set the XHTML file
		$this->xhtmlFile = $xhtml_file;
		
		
		
		//Copy $_REQUEST[] over to $this->request
		//(Killing magic quotes if necessary)
		$this->request = $_REQUEST;
		
		if(get_magic_quotes_gpc() == 1)
		{
			$this->request = array_map('stripslashes', $this->request);
		}
		
		$this->requestForXhtml = array_map('html', $this->request);
		$this->requestForDb = array_map('addslashes', $this->request);
	}
	
	//Methods------------------
	
	//	
	function display()
	{	
		header('Content-Type: text/html; charset=utf-8');	//Fudge for 123-Reg's cheesy server
		require_once($_SERVER['DOCUMENT_ROOT'] . "/../html/{$this->xhtmlFile}");
	}
}