<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Database connectivity
define('DB_HOST', 'localhost');	//or whatever
define('DB_USER', 'root');	//or whatever
define('DB_PASS', 'somepassword');	//or whatever
define('DB_NAME', 'mapanese');	//or whatever

MySQL::connect(DB_USER, DB_PASS, DB_HOST, DB_NAME);
