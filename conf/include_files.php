<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//app config
include $_SERVER['DOCUMENT_ROOT'] . '/../conf/application.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.mysql.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/lib.core.php';
//include $_SERVER['DOCUMENT_ROOT'] . '/../php/lib.database.php';
include $_SERVER['DOCUMENT_ROOT'] . '/../php/lib.japanese.php';
//include $_SERVER['DOCUMENT_ROOT'] . '/../php/lib.debug.php';
include $_SERVER['DOCUMENT_ROOT'] . '/../php/lib.data_security.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.page.php';
include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.home_page.php';
include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.error_page.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.ajax_model.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.english_searching.php';
include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.japanese_searching.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.japanese_address.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.japanese_address_component.php';

include $_SERVER['DOCUMENT_ROOT'] . '/../php/class.japanese_address_completer.php';



//db config
include $_SERVER['DOCUMENT_ROOT'] . "/../conf/{$_SERVER['SERVER_NAME']}_conf.php";
