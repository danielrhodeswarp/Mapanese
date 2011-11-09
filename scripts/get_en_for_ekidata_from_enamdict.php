<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');

include $_SERVER['DOCUMENT_ROOT'] . '/conf/include_files.php';


set_time_limit(0);


$rows = MySQL::queryAll("SELECT ekidata.id AS ekidata_id, enamdict.definition AS name_en FROM enamdict, ekidata WHERE enamdict.word = CONCAT(ekidata.name_ja, '駅')");




foreach($rows as $row)
{
	//$safe_name_en = MySQL::esc(preg_replace('/[ ][(]st[)]$/i', '', $row['name_en']));	//Slow!
	//$safe_name_en = MySQL::esc($row['name_en']);
	$safe_name_en = MySQL::esc(str_replace(' (st)', '', $row['name_en']));
	
	$sql = "UPDATE ekidata SET name_en = '{$safe_name_en}' WHERE id = {$row['ekidata_id']}";
	echo $sql . '<br/>';
	
	MySQL::exec($sql);
}