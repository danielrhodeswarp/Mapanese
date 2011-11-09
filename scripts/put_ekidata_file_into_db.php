<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');

include $_SERVER['DOCUMENT_ROOT'] . '/conf/include_files.php';


set_time_limit(0);


//setlocale(LC_ALL, 'ja_JP.UTF8');

$handle = fopen('./csv/m_station.csv.utf8.csv', 'r');


$start_sql = "INSERT INTO ekidata(prefecture_iso_code, name_ja, latitude, longitude) VALUES";

$sql = $start_sql;


$count = 0;
//foreach($lines as $key => $value)
while(($line = fgets($handle)) !== false)	//fgetcsv() seems to truncate the $line[9] index (which we want because it's name_ja!!)
{
	$count++;
	
	$line = explode(',', $line);
	
	ECHO "[{$line[9]}]<br/>";
	//$line[9] = mb_convert_encoding($line[9], 'UTF-8', 'EUC-JP');
	
	if($count == 1) continue;	//skip first line
	
	
	
	
	$prefecture_iso_code = MySQL::esc(sprintf("%02d", $line[10]));
	$name_ja = MySQL::esc($line[9]);
	$latitude = MySQL::esc($line[12]);
	$longitude = MySQL::esc($line[11]);
	
	$sql .= "('{$prefecture_iso_code}', '{$name_ja}', '{$latitude}', '{$longitude}'),";
	
	
	if($count % 100 == 0)
	{
		//$count = 0; 
		
		$sql = rtrim($sql, ',');
		$result = MySQL::exec($sql);
		
		
		/*
		if(MDB2::isError($result))	//PEAR or MDB2??
		{
		    //print_r($result);
			//echo $result->userinfo->_doQuery;
			echo $result->getMessage() . '<br/><xmp>'.str_replace('),(', "),\r\n(", $sql).'</xmp><hr/>';
		}
		*/
		
		$sql = $start_sql;
	}
}


$sql = rtrim($sql, ',');
//Exec unexec'd SQL (as we arbitrarily cut of at 100)
MySQL::exec($sql);




echo 'Finished!<br/>';
echo ($count - 1) . ' lines processed.';

fclose($handle);