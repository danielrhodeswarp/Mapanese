<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//NOTE THAT stringy things generally have to be utf-8 safe because
//that's what we are using in the DB and HTML etc

//Security by obscurity lol

//
function obfuscate_integer($integer)
{
	return $integer * 102;
}

//
function unobfuscate_integer($integer)
{
	return $integer / 102;
}

//
function obfuscate_string($string, $repeat_factor = 3)
{
	return str_rot13(strrev(str_repeat($string, $repeat_factor)));	//mb_strrev() WAS in core.lib
}

//
function unobfuscate_string($string, $repeat_factor = 3)
{
	$multiples = str_rot13(strrev($string));	//mb_strrev() WAS in core.lib
	return mb_substr($multiples, 0, mb_strlen($multiples) / $repeat_factor);
}

//UK bank sort code is always [two digits, hyphen, two digits, hyphen, two digits]
//Obfuscate each part and retain the hyphens
function obfuscate_sort_code($sort_code)
{
	if(empty($sort_code))
	{
		return '';
	}
	
	$parts = explode('-', $sort_code);
	
	return obfuscate_integer($parts[0]) . '-' . obfuscate_integer($parts[1]) . '-' . obfuscate_integer($parts[2]);
}

//
function unobfuscate_sort_code($sort_code)
{
	if(empty($sort_code))
	{
		return '';
	}
	
	$parts = explode('-', $sort_code);
	
	return str_pad(unobfuscate_integer($parts[0]), 2, '0', STR_PAD_LEFT) . '-' . str_pad(unobfuscate_integer($parts[1]), 2, '0', STR_PAD_LEFT) . '-' . str_pad(unobfuscate_integer($parts[2]), 2, '0', STR_PAD_LEFT);
}

//Usual acc no is 8 digits but some seem to be 19 digits...
//Reformulate an acc no as:
//[$digits_having_an_odd_index_position][$digits_having_an_even_index_position][$demarker][$count_of_odd_positioned_digits]
//(where the first digit in the acc no has an index of '1')
//eg: 87313672 --> 8337716204
function obfuscate_account_no($acc_no)
{
	if(empty($acc_no))
	{
		return '';
	}
	
	$odds = '';
	$odds_count = 0;
	$evens = '';
	$demarker = '0';	//Allows for 2 digits of gettable $odds_count
	
	$counter = 1;
	for($loop = 0; $loop < strlen($acc_no); $loop++)
	{
		$digit = $acc_no[$loop];
		
		if($counter % 2 == 0)
		{
			$evens .= $digit;
		}
		
		else
		{
			$odds .= $digit;
			$odds_count++;
		}
		
		$counter++;
	}
	
	return $odds . $evens . $demarker . $odds_count;
}

//
function unobfuscate_account_no($obbed_acc_no)
{
	if(empty($obbed_acc_no))
	{
		return '';
	}
	
	$demarker = '0';	//Must be same as in obfuscate_account_no()
	
	//1] Get $odds_count
	//2] Separate odds and evens
	//3] Rebuild
	
	//1]
	//Get non-last zero (our demarker)
	$no_last_zero = rtrim($obbed_acc_no, '0');
	$demarker_pos = strrpos($no_last_zero, '0');
	
	$odds_count = substr($obbed_acc_no, $demarker_pos + 1);
	
	$odds_and_evens = substr($obbed_acc_no, 0, $demarker_pos);
	
	//2]
	$odds = substr($odds_and_evens, 0, $odds_count);
	
	$evens = substr($odds_and_evens, $odds_count);
	
	//3]
	$unobbed_acc_no = '';
	for($loop = 0; $loop < $odds_count; $loop++)
	{
		$unobbed_acc_no .= $odds[$loop];
		
		//if(array_key_exists($loop, $evens))
		if(strlen($evens) > $loop)
		{
			$unobbed_acc_no .= $evens[$loop];
		}
	}
	
	return $unobbed_acc_no;
}