<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

date_default_timezone_set('Europe/London');

//Default/base settings for html <meta> tags and on-page smallprint and etc
define('META_AUTHOR', 'Warp Asylum Ltd (UK)');
define('META_COPYRIGHT', 'Copyright &copy; 2007 ~ ' . date('Y') . ' Warp Asylum Ltd (UK)');
define('META_ROBOTS', 'all');
define('META_DESCRIPTION', 'Search English language maps of Japan. Maps of Japan with English labels and search.');
define('META_KEYWORDS', 'english,language,mapping,geocoding,japan,map,maps,japanese,mapanese,gis');	//Don't space after comma!


//Company stuff
define('COMPANY_NAME', 'Warp Asylum');
define('COMPANY_NUMBER', '7144850');
define('COMPANY_VAT_NUMBER', '');
define('COMPANY_ADDRESS_1', '159 Curzon Road');
define('COMPANY_ADDRESS_2', 'Ashton-under-Lyne');
define('COMPANY_ADDRESS_3', '');
define('COMPANY_ADDRESS_4', '');
define('COMPANY_ADDRESS_POSTCODE', 'OL6 9NB');
//at least one page on public website should legally have something like:
//Warp Asylum is a limited company registered in England and Wales. Company number: 7144850. Registered office address: 159 Curzon Road, Ashton-under-Lyne, OL6 9NB.


//----THE FOLLOWING JUNK ISN'T ACTUALLY USED WITH MAPANESE'S FRAMEWORK!!

//<title> root
define('TITLE_ROOT_EN', "Japanese nightlife? It's GaijiNavi!");
define('TITLE_ROOT_JA', '外人バーのGaijiNavi');	//外じナビ？
define('TITLE_SEPARATOR', ' | ');

//Breadcrumb root
define('BREADCRUMB_ROOT_EN', 'Home');
define('BREADCRUMB_ROOT_JA', 'ホーム');
define('BREADCRUMB_SEPARATOR', ' &gt; ');