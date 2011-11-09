<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Database connectivity
define('DB_USER', 'oh');
define('DB_PASS', 'what');
define('DB_HOST', 'a');
define('DB_NAME', 'palaver');

MySQL::connect(DB_USER, DB_PASS, DB_HOST, DB_NAME);
