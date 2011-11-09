<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');

include $_SERVER['DOCUMENT_ROOT'] . '/conf/include_files.php';


set_time_limit(0);

//use edict/mini for testing
//$lines = file('edict.mini');

//$lines = file('./csv/enamdict');
$handle = fopen('./csv/enamdict', 'r');


$start_sql = "INSERT INTO enamdict(word, kana, definition) VALUES";

$sql = $start_sql;




$matches = array();
$parts = array();

$word = '';
$kana = '';
$definiton = '';


$count = 0;
//foreach($lines as $key => $value)
while (!feof($handle))
{
	$count++;
	
	$value = fgets($handle);
	
	if($count == 1) continue;	//skip first line
	
	
	$parts = split('/', $value);
	
	$word = $parts[0];
	$kana = '';
	
	
	if(mb_strstr($word, '['))
	{
		
		preg_match('|(.*)\[(.*)\]|U', $word, $matches);
		
		//print_r($matches);
		
		$word = trim($matches[1]);
		
		
		$kana = trim($matches[2]);
	}
	
	
	$definitiony = array_slice($parts, 1, count($parts) - 2);	//skip the actual word token
	
	
	
	$definition = join('/', $definitiony);
	
	
	//echo "<tr><td>{$word}</td><td>{$kana}</td><td>{$definiton}</td></tr>";
	
	
	
	/*
	echo '<pre>';
	print_r($parts);
	echo '</pre>';
	*/
	
	$word = mb_convert_encoding($word, 'UTF-8', 'EUC-JP');
	$kana = mb_convert_encoding($kana, 'UTF-8', 'EUC-JP');
	$definition = mb_convert_encoding($definition, 'UTF-8', 'EUC-JP');
	
	//We are only interested in train stations ="blhablha blha (st)"
	if(preg_match('|^.*[(]st[)]$|U', $definition) === 0)
	{
		continue;
	}
	
	$word = MySQL::esc($word);
	$kana = MySQL::esc($kana);
	$definition = MySQL::esc($definition);
	
	$sql .= "('{$word}', '{$kana}', '{$definition}'),";
	
	
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