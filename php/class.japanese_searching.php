<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//SEARCH MACHINE FOR JAPANESE LANGUAGE QUERIES

//(at least) THE FOLLOWING ADDRESSES NEED SPECIAL ATTENTION:
//TOKYO
//HOKKAIDO
//OSAKA

//Q] What about postcodes? (and the postcode character)???
//A1] Japanese one-line addresses don't usually include the postcode anyway!!!
//A2] And we have "search by raw postcode" functionality anyway...

class JapaneseSearching
{
	var $japaneseAddress;
	public $completedJapaneseAddress;
	
	var $originalQuery;
	var $unlabelledParts = array();	//Address components contained in $originalQuery
	var $labelledParts = array();	//Address components contained in $originalQuery labelled by type and for ordering purposes
	var $enAddress;
	var $jaAddress;
	
	//Basically supported formats for $japaneseLanguageQuery:
	//月ノ会町１−２−３
	//月ノ会町
	//岐阜市
	//岐阜県
	//岐阜県岐阜市
	//岐阜県岐阜市月ノ会町
	//岐阜県岐阜市月ノ会町１−２−３
	//any ordering/amount of the following type of tokens (separated by zenkaku or hankaku space)
	//岐阜県  岐阜市  月ノ会町  １−２−３
	//any ordering/amount of the following type of tokens (separated by comma)
	//岐阜県,岐阜市,月ノ会町,１−２−３
	//any ordering/amount of the following type of tokens (separated by comma and/or space)
	//岐阜県, 岐阜市, 月ノ会町, １−２−３
	function __construct($japaneseLanguageQuery)
	{
		$this->originalQuery = $japaneseLanguageQuery;
		
		//Split on comma and/or hankaku space and/or zenkaku space
		$this->unlabelledParts = mb_split('[ 　,]{1,}', $this->originalQuery);
		
		//If $originalQuery was already an all-on-one-line Japanese address string,
		//then split into parts intelligently
		if(count($this->unlabelledParts) == 1)
		{
			$this->unlabelledParts = $this->jaAddressToParts($this->unlabelledParts[0]);
		}
		
/*		
ECHO '<xmp>';
VAR_DUMP($this->unlabelledParts);		
ECHO '</xmp>';	
*/
		
		
		$this->japaneseAddress = new JapaneseAddress();
		$this->japaneseAddress->setAutoJaFromArray($this->unlabelledParts);


		
		$jac = new JapaneseAddressCompleter();

		$this->completedJapaneseAddress = $jac->completeKanjiAddress($this->japaneseAddress);		
		
	

		
		
		
		
		
		
		//$this->labelledParts = $this->labelParts($this->unlabelledParts);
		
		//$this->enAddress = $this->labelledPartsToEnAddress($this->labelledParts);
		
		//$this->jaAddress = $this->labelledPartsToJaAddress($this->labelledParts);
	}
	
	//What about "gun" suffix?????? Is that city level?
	function jaAddressToParts($jaAddress)
	{
		//BUT MAY COINCIDENTALLY BE IN THE ADDRESS STRING!
		//Remove "日本" (how about "日本国"??)
		//$jaAddress = str_replace('日本', '', $jaAddress);
		//Let's remove only an initial "日本"
		$jaAddress = preg_replace('/^日本/', '', $jaAddress);
		
		//Put a pipe after any "separator" kanji
		//namely, (hokkaidou|ken|shi|to|fu|gun|ku|machi_or_cho|son_or_mura)
		$parts = preg_replace('/(北海道|県|市|都|府|郡|区|町|村)/', '$1|', $jaAddress);
		
		$parts = explode('|', $parts);
		
		
		//Now, $parts[lastOne] may be, for example "西荘1-16-12" (ie. townname does not end in '町').
		//SO should we split these cases again by word/digit boundary?
		
		return $parts;
	}

	//
	function jaAddressToLabelledParts($jaAddress)
	{
		$unlabelledParts = $this->jaAddressToParts($jaAddress);
		
		$labelledParts = $this->labelParts($unlabelledParts);
		
		return $labelledParts;
	}
	
	//Also works when $parts has only one entry which is a full Japanese address string ;-)
	function labelParts(array $parts)
	{
		$labelledParts = array();
		
		foreach($parts as $part)
		{
			if(preg_match('/.*県$/', $part))
			{
				$labelledParts['a_pref'] = $part;
			}
			
			elseif($part == '北海道')
			{
				$labelledParts['a_pref'] = '北海道';
			}
			
			elseif(preg_match('/.*市$/', $part))
			{
				$labelledParts['b_city'] = $part;
			}
			
			//"TO" addresses may have "KU" also
			elseif(preg_match('/.*都$/', $part))
			{
				$labelledParts['c_metropolis'] = $part;
			}
			
			//"KU" addresses may have "TO" also
			elseif(preg_match('/.*区$/', $part))
			{
				$labelledParts['d_ward'] = $part;
			}
			
			elseif(preg_match('/.*町$/', $part))
			{
				$labelledParts['e_town'] = $part;
			}
			
			else	//Assume town level token
			{
				$labelledParts['f_town'] = $part;
			}
		}
		
		return $labelledParts;
	}
	
	//
	function labelledPartsToJaAddress(array $labelledParts)
	{
		ksort($labelledParts);	//[k]ey sort
		
		return implode('', $labelledParts);	//Join on empty character
	}
	
	//
	function labelledPartsToEnAddress(array $labelledParts)
	{
		
		
		krsort($labelledParts);	//[k]ey [r]everse sort
		
		if(array_key_exists('a_pref', $labelledParts))
		{
			$labelledParts['a_pref'] = MySQL::queryOne("SELECT name_en FROM prefecture WHERE name_ja = '{$labelledParts['a_pref']}'") . ' Prefecture';
		}
		
		if(array_key_exists('b_city', $labelledParts))
		{
			$labelledParts['b_city'] = MySQL::queryOne("SELECT DISTINCT shi_en FROM postcode WHERE shi_ja = '{$labelledParts['b_city']}'");
		}
		
		if(array_key_exists('e_town', $labelledParts))	//Limit via above city and prefecture fields (if present)?????
		{
			$labelledParts['e_town'] = MySQL::queryOne("SELECT DISTINCT cho_en FROM postcode WHERE cho_ja = '{$labelledParts['e_town']}'");
		}
		
		return implode(', ', $labelledParts);	//Join on comma-space
	}
}