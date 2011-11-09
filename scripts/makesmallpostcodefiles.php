<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');


$_SERVER['SERVER_NAME'] = 'www.mapanese.ark';
$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__) . '/..';
include $_SERVER['DOCUMENT_ROOT'] . '/conf/include_files.php';



for($loop = 1; $loop < 48; $loop++)
{
	$char_loop = sprintf("%02d", $loop);
	
	$postcode_entries = MySQL::query("SELECT * FROM postcode WHERE prefecture_iso_code = '{$char_loop}'");
	
	$contents = '';
	
	$file = fopen("./pc_{$char_loop}.sql", 'w');
	
	
	while(($pc = $postcode_entries->fetch_assoc()) != null)
	{
		$contents = <<<SQL
INSERT INTO postcode(postcode, city_katakana, city_kanji, town_katakana, town_kanji, city_en, town_en, prefecture_iso_code, shi_ja, shi_en, ku_ja, ku_en, gun_ja, gun_en, gun_cho_ja, gun_cho_en, cho_ja, cho_en, son_ja, son_en, basho_ja, basho_en) VALUES('{$pc['postcode']}', '{$pc['city_katakana']}', '{$pc['city_kanji']}', '{$pc['town_katakana']}', '{$pc['town_kanji']}', '{$pc['city_en']}', '{$pc['town_en']}', '{$pc['prefecture_iso_code']}', '{$pc['shi_ja']}', '{$pc['shi_en']}', '{$pc['ku_ja']}', '{$pc['ku_en']}', '{$pc['gun_ja']}', '{$pc['gun_en']}', '{$pc['gun_cho_ja']}', '{$pc['gun_cho_en']}', '{$pc['cho_ja']}', '{$pc['cho_en']}', '{$pc['son_ja']}', '{$pc['son_en']}', '{$pc['basho_ja']}', '{$pc['basho_en']}');\n
SQL;
		fwrite($file, $contents);
	}
	
	
	
	
	fclose($file);
}