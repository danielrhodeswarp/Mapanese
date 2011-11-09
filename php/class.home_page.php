<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Class representing the home page
class HomePage extends Page
{
	var $resultType = '';	//Type of result ('none', 'single' or 'multi') - NOT YET PROPERLY USED
	
	//Constructor
	function __construct()
	{
		parent::__construct('home.html');
		
		//<iframe> version uses (slightly) different XHTML
		if(array_key_exists('output', $this->request) and $this->request['output'] == 'embed')
		{
			$this->xhtmlFile = 'embed.html';
		}
		
		$this->searchInformation = '';
		$this->noscriptInfo = '';
		
		//Default search type
		if(!array_key_exists('type', $this->request))
		{
			$this->request['type'] = 'auto';
		}
		
		//Default resultant Japanese address (search result(s) which must then be geocoded)
		$this->resultant_ja_address = '';

		//Search if appropriate
		if(array_key_exists('q', $this->request))
		{
			$this->request['q'] = trim($this->request['q']);
		}
		
		else
		{
			$this->request['q'] = '';
		}
		
		if(!empty($this->request['q']))
		{
			//Trim the "massaged" request arrays too
			$this->requestForXhtml['q'] = trim($this->requestForXhtml['q']);
			$this->requestForDb['q'] = trim($this->requestForDb['q']);
			
			$this->startSearch($this->request['q']);
		}
		
		else	//Reset query if pure whitespace (trim() won't catch zenkaku space however)
		{
			$this->requestForXhtml['q'] = '';
			
			//Initial noscript info
			$this->noscriptInfo = 'Your browser can\'t handle Google Maps (perhaps JavaScript is disabled?) but your address search will still be translated. Try it!';
		}
	}
	
	//First stage of searching
	function startSearch($q)
	{
		$this->{"search_{$this->request['type']}"}($q);
	}
	
	//Automatic searching (determine type of user's query)
	function search_auto($q)
	{
		$type = $this->determineType($q);
		
		$this->searchInformation .= "<p>Query of <em>{$this->requestForXhtml['q']}</em> was auto'd to: {$type}</p>";
		
		$this->noscriptInfo = $this->searchInformation;
		
		$this->{"search_{$type}"}($q);
	}
	
	//SUPPORT 4, 5 or 6 digit partial postcodes?????
	//Postcode searching
	function search_postcode($q)
	{
		//1) Flatten zenkaku digits
		//2) Remove any non-digit characters
		//3) Roll with first 3 or 7 digits if present else...
		//4) Do nothing
		
		
		$qWithHankakuNumbers = mb_convert_kana($q, 'n');	//Zenkaku numbers to hankaku
		
		$postcode = preg_replace('|[^0-9]|', '', $qWithHankakuNumbers);	//Kill non-digits
		
		$postcode = substr($postcode, 0, 7);	//Grab first 7 digits
		
		if(strlen($postcode) != 3 and strlen($postcode) != 7)
		{
			$this->resultType = 'none';
			return;
		}
		
		
		$addressesForJavascript = array();
		
		$formattedPostcodeWithMark = formatPostcode($postcode, true);	//In lib.japanese.php
		$formattedPostcode = formatPostcode($postcode);
		
		if(strlen($postcode) == 7)
		{
			$sql = "SELECT CONCAT_WS(', ', postcode.basho_en, postcode.son_en, postcode.cho_en, postcode.gun_cho_en, postcode.gun_en, postcode.ku_en, postcode.shi_en, CONCAT_WS(' ', prefecture.name_en, '{$formattedPostcode}')) AS en_address, CONCAT('{$formattedPostcodeWithMark}　', prefecture.name_ja, postcode.city_kanji, postcode.town_kanji) AS ja_address FROM prefecture INNER JOIN postcode ON postcode.prefecture_iso_code = prefecture.iso_code WHERE postcode.postcode = '{$postcode}'";
			
			$row = MySQL::queryRow($sql);	//so only a single result
			
			//No match
			if(!is_array($row))
			{
				$this->setNoMatch();
				return;
			}
			
			//clean
			$enAddress = $this->cleanMultiCommadAddress($row['en_address']);
			
			$addressesForJavascript[$row['ja_address']] = $enAddress;
			
			
			$this->resultant_ja_address = fake_json_encode($addressesForJavascript);
			
			$this->noscriptInfo .= "<p><strong>en</strong>: {$enAddress}</p>";
			$this->noscriptInfo .= "<p><strong>ja</strong>: {$row['ja_address']}</p>";
			
			
		}
		
		
		//Approximate postcode lookup
		elseif(strlen($postcode) == 3)
		{
			$sql = "SELECT CONCAT(prefecture.name_ja, city_kanji, town_kanji) AS ja_address, CONCAT_WS(', ', postcode.basho_en, postcode.son_en, postcode.cho_en, postcode.gun_cho_en, postcode.gun_en, postcode.ku_en, postcode.shi_en, CONCAT_WS(' ', prefecture.name_en)) AS en_address, COUNT(postcode.postcode) AS frequency FROM postcode INNER JOIN prefecture ON prefecture.iso_code = postcode.prefecture_iso_code WHERE postcode LIKE '{$postcode}%' GROUP BY city_kanji";	//ORDER BY frequency DESC
	
			$list = "<ul>";
			
			
			$results = MySQL::queryAll($sql);	//so possibly multiple results
			
			//No match
			if(empty($results))
			{
				$this->setNoMatch();
				return;
			}
			
			foreach($results as $row)
			{
				//clean
				$enAddress = $this->cleanMultiCommadAddress($row['en_address']);
				$jaAddress = $row['ja_address'];
				
				//Kill "以下に掲載がない場合"/"Ikanikeisaiganaibaai"
				$enAddress = str_replace('Ikanikeisaiganaibaai, ', '', $enAddress);
				$jaAddress = str_replace('以下に掲載がない場合', '', $jaAddress);
				
				//COULD DO WITH ken SUFFIX FOR PREFECTURE PART OF enAddress...
				
				
				
				
				$addressesForJavascript[$jaAddress] = $enAddress;
				
				$list .= "<li>{$enAddress} | {$jaAddress}</li>";
				
			}
			
			
			$this->noscriptInfo .= "{$list}</ul>";
			
			$this->resultant_ja_address = fake_json_encode($addressesForJavascript);
				
			
		}
		
		
		
		
	}
	
	//English address searching
	function search_en_address($q)
	{
		$englishSearcher = new EnglishSearching($q);
		
		$addressesForJavascript = array();
		
		$enAdd = $englishSearcher->completedJapaneseAddress->getEn(', ');
		$jaAdd = $englishSearcher->completedJapaneseAddress->getJa();
		
		$addressesForJavascript[$jaAdd] = $enAdd;	//single result
		
		$this->resultant_ja_address = fake_json_encode($addressesForJavascript);
		
		$this->noscriptInfo .= "<p><strong>en</strong>: {$enAdd}</p><p><strong>ja</strong>: {$jaAdd}</p>";
	}
	
	//Japanese address searching
	function search_ja_address($q)
	{
		$japaneseSearcher = new JapaneseSearching($q);
		
		$addressesForJavascript = array();
		
		$enAdd = $japaneseSearcher->completedJapaneseAddress->getEn(', ');
		$jaAdd = $japaneseSearcher->completedJapaneseAddress->getJa();
		
		$addressesForJavascript[$jaAdd] = $enAdd;	//single result
		
		$this->resultant_ja_address = fake_json_encode($addressesForJavascript);
		
		$this->noscriptInfo .= "<p><strong>en</strong>: {$enAdd}</p><p><strong>ja</strong>: {$jaAdd}</p>";
		
	}
	
	
	//Google Maps Japan link searching
	function search_gm_jp_link($q)
	{
		
		$parts = explode('?', $q);
		
		$url = $parts[0];
		$allParms = $parts[1];
		
		//URL check?
		//Starts with "http://maps.google"~
		
		$parms = explode('&', $allParms);
		
		if(in_array('f=q', $parms))
		{
			//echo 'place/address search';
			
			//Get "q=whatever" part
			$linkQ = '';
			foreach($parms as $parm)
			{
				if(strpos($parm, 'q=') === 0)
				{
					$parts = explode('=', $parm);
					
					$linkQ = $parts[1];
					break;
				}
			}		
			
			$type = $this->determineType($linkQ);	//Google Maps Japan supports a *few* English language queries. SO, determine type and let's roll
			$this->{"search_{$type}"}($linkQ);
		}
		
		elseif(in_array('f=d', $parms))
		{
			//echo 'transit search';
		}
		
		//Simply plot both places for a transit search?
	}
	
	
	//NEED TO IGNORE ZENKAKU SPACES IN OTHERWISE ALL ENGLISH QUERIES!!!!!!!!!!!!!!!!!!!!!!!!!!
	//Determine actual type of query when user selects "auto"
	function determineType($q)
	{
		//Notes and determination assumptions:
		//anything that has zenkaku (except 「〒」、「−」and「　」) is 'ja_address'
		//does Google Maps' JavaScript geocoding seem to handle "〒500ー8131" ???...
		
		if(is_mb($q))
		{
			//Remove some zenkaku punctuation that may be used in a postcode string
			//and see if we are *still* zenkaku
			$noPostcodePuncuation = str_replace(array('〒', '−', 'ー'), array('', '', ''), $q);
			$noPostcodePunctuationHankakuNumbers = mb_convert_kana($noPostcodePuncuation, 'n');
			
			if(is_mb($noPostcodePunctuationHankakuNumbers))
			{
				return 'ja_address';
			}
			
			else
			{
				return 'postcode';
			}
		}
		
		elseif(ctype_digit(substr($q, 0, 7)))
		{
			return 'postcode';
		}
		
		elseif(preg_match('|^http[:][/][/]|i', $q))
		{
			return 'gm_jp_link';
		}
		
		elseif(preg_match('|[a-zA-Z]|', $q) == 1)
		{
			return 'en_address';
		}
		
		else
		{
			return 'postcode';
		}
		
		/*
		else
		{
			return 'en_address';
		}
		*/
	}
	
	function cleanMultiCommadAddress($multiCommadAddress)
	{
		$cleanAddress = preg_replace('/^([,][ ]){1,}/', '', $multiCommadAddress);
		$cleanAddress = preg_replace('/([,][ ]){1,}$/', '', $cleanAddress);
		return preg_replace('/([,][ ]){2,}/', ', ', $cleanAddress);
	}
	
	//-------------------------------------------
	
	function setNoMatch()
	{
		$this->resultType = 'none';
		$this->resultant_ja_address = '{}';	//Empty JavaScript object
	}
	
	function setSingleMatch($jaAdd, $enAdd)
	{
		$this->resultType = 'single';
	}
	
	function setMultiMatch(array $jaAdd_enAdd)
	{
		$this->resultType = 'multi';
	}
}