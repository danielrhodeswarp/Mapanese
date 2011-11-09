<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Class representing the error page
class ErrorPage extends Page
{
	function __construct()
	{
		parent::__construct('error.html');
		
		$this->errorXhtml = $this->{"do_{$_REQUEST['error_code']}"};
	}
	
	function do_404()
	{
		return "<h2>Document Unfound</h2><p>The entered URL doesn't exist within the GaijiNavi site!</p>";
	}
	
	
}