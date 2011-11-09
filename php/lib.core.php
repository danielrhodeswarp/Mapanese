<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Common functions

//Make $text safe (and accurate) for use in HTML source
//Very important!
function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

//
function prel($x)
{
	//error_log(__FILE__ . '| ' . print_r($x, true)); // got tired of typing this
	error_log(print_r($x, true)); // got tired of typing this
}

//For refreshing of checkboxes (XHTML safe)
function checked_when_array_key_is_x($array, $key, $x)
{
	if($array[$key] === $x)
	{
		return 'checked="checked"';
	}
	
	return '';
}

//Emulate json_encode() for cheesy servers (*cough* 123-Reg *cough*) where it isn't installed
function fake_json_encode(array $array)
{
	$return_string = '{';
	
	foreach($array as $key => $value)
	{
		$key = json_encode_string($key);
		
		$return_string .= "\"{$key}\":\"{$value}\",";
	}
	
	return rtrim($return_string, ',') . '}';
}

//From comments [by Yi-Ren Chen at NCTU CSIE] at php.net for json_encode()
//This great li'l function basically clones the utf8 encoding shenanigans that happen inside json_encode()
function json_encode_string($in_str)
{
	//mb_internal_encoding('UTF-8');
	$convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
	$str = "";
	
	for($i = mb_strlen($in_str) - 1; $i >= 0; $i--)
	{
		$mb_char = mb_substr($in_str, $i, 1);
		
		if(mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, 'UTF-8'), $match))
		{
			$str = sprintf("\\u%04x", $match[1]) . $str;
		}
		
		else
		{
			$str = $mb_char . $str;
		}
	}
	
	return $str;
}

//
function get_db_token($file)
{
	$contents = trim(file_get_contents($file));
	
	$parts = explode(':', $contents);
	
	return array(unobfuscate_string($parts[0]), $parts[1]);
}