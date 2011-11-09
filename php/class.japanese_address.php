<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Data model for a Japanese address (meaning an address of a location in Japan)
//With address components in both kanji and romaji

class JapaneseAddress
{
	var $components = array();	//Address components
	var $levels = array();
	var $suffixesEn = array();
	var $suffixesJa = array();
	var $suffixesNormalizer = array();
	
	function __construct()
	{
		$this->initializeArrays();
	}
	
	function getAbsentLevels()
	{
		$returnArray = array();
		
		foreach($this->levels as $levelWord => $levelCode)
		{
			if(!array_key_exists($levelCode, $this->components))
			{
				$returnArray[] = $levelWord;
			}
		}
		
		return $returnArray;
	}
	
	function initializeArrays()
	{
		//Levels and their sorting number
		$this->levels['postcode'] = -1;
		$this->levels['ken'] = 0;
		$this->levels['shi'] = 1;
		$this->levels['ku'] = 2;
		$this->levels['gun'] = 3;
		$this->levels['gun_cho'] = 4;
		$this->levels['cho'] = 5;
		$this->levels['son'] = 6;
		$this->levels['basho'] = 7;
		$this->levels['ban'] = 8;
		//$this->levels['nokori'] = 9;
		
		$this->levelWordForLevelNumber = array_flip($this->levels);
		
		//Suffixes and their levels
		$this->suffixesEn['ken'] = 'ken';
		$this->suffixesEn['pref'] = 'ken';
		$this->suffixesEn['prefecture'] = 'ken';
		$this->suffixesEn['fu'] = 'ken';
		$this->suffixesEn['to'] = 'ken';
		$this->suffixesEn['do'] = 'ken';
		$this->suffixesEn['doo'] = 'ken';
		$this->suffixesEn['dou'] = 'ken';
		$this->suffixesEn['shi'] = 'shi';
		$this->suffixesEn['city'] = 'shi';
		$this->suffixesEn['ku'] = 'ku';
		$this->suffixesEn['ward'] = 'ku';
		$this->suffixesEn['gun'] = 'gun';	//"district"?
		$this->suffixesEn['cho'] = 'cho';
		$this->suffixesEn['chou'] = 'cho';
		$this->suffixesEn['machi'] = 'cho';
		$this->suffixesEn['town'] = 'cho';
		$this->suffixesEn['son'] = 'son';
		$this->suffixesEn['mura'] = 'son';
		$this->suffixesEn['village'] = 'son';
		
		$this->suffixesJa['県'] = 'ken';
		$this->suffixesJa['府'] = 'ken';
		$this->suffixesJa['都'] = 'ken';
		$this->suffixesJa['市'] = 'shi';
		$this->suffixesJa['区'] = 'ku';
		$this->suffixesJa['郡'] = 'gun';
		$this->suffixesJa['町'] = 'cho';
		$this->suffixesJa['村'] = 'son';
		
		
		$this->suffixesNormalizer['ken'] = 'ken';
		$this->suffixesNormalizer['pref'] = 'ken';
		$this->suffixesNormalizer['prefecture'] = 'ken';
		$this->suffixesNormalizer['fu'] = 'fu';
		$this->suffixesNormalizer['to'] = 'to';
		$this->suffixesNormalizer['do'] = 'dou';
		$this->suffixesNormalizer['doo'] = 'dou';
		$this->suffixesNormalizer['dou'] = 'dou';
		$this->suffixesNormalizer['shi'] = 'shi';
		$this->suffixesNormalizer['city'] = 'shi';
		$this->suffixesNormalizer['ku'] = 'ku';
		$this->suffixesNormalizer['ward'] = 'ku';
		$this->suffixesNormalizer['gun'] = 'gun';	//"district"?
		$this->suffixesNormalizer['cho'] = 'cho';
		$this->suffixesNormalizer['chou'] = 'cho';
		$this->suffixesNormalizer['machi'] = 'machi';
		$this->suffixesNormalizer['town'] = 'town';
		$this->suffixesNormalizer['son'] = 'son';
		$this->suffixesNormalizer['mura'] = 'mura';
		$this->suffixesNormalizer['village'] = 'village';
	}
	
	function getLevelComponent($level)
	{
		if(!array_key_exists($this->levels[$level], $this->components))
		{
			return false;
		}
		
		return $this->components[$this->levels[$level]];
	}
	
	//
	function addLevel($level, $rootJa = null, $suffixJa = null, $rootEn = null, $suffixEn = null)
	{
		$this->components[$this->levels[$level]] = new JapaneseAddressComponent($rootJa, $suffixJa, $rootEn, $suffixEn);
	}
	
	function setLevelAll($level, $rootJa, $suffixJa, $rootEn, $suffixEn)
	{
		if(!array_key_exists($this->levels[$level], $this->components))
		{
			$this->addLevel($level, $rootJa, $suffixJa, $rootEn, $suffixEn);
		}
		
		else
		{
			$this->components[$this->levels[$level]]->setAll($rootJa, $suffixJa, $rootEn, $suffixEn);
		}
	}
	
	function setLevelJa($level, $rootJa, $suffixJa = null)
	{
		if(!array_key_exists($this->levels[$level], $this->components))
		{
			$this->addLevel($level, $rootJa, $suffixJa, null, null);
		}
		
		else
		{
			$this->components[$this->levels[$level]]->setJa($rootJa, $suffixJa);
		}
	}
	
	function setLevelEn($level, $rootEn, $suffixEn = null)
	{
		if(!array_key_exists($this->levels[$level], $this->components))
		{
			$this->addLevel($level, null, null, $rootEn, $suffixEn);
		}
		
		else
		{
			$this->components[$this->levels[$level]]->setEn($rootEn, $suffixEn);
		}
	}
	
	//Determin lang
	function setAuto($root, $suffix = null)
	{
		if(mb_strlen($root) != strlen($root))	//$root is always present so use it to check
		{
			//ja
			$this->setAutoJa($root, $suffix);
		}
		
		else
		{
			//en
			$this->setAutoEn($root, $suffix);
		}
	}
	
	//Set component level according to suffix
	function setAutoEn($rootEn, $suffixEn = null)
	{
		$throwAway = new JapaneseAddressComponent();
		
		$throwAway->setEn($rootEn, $suffixEn);
		
		if(array_key_exists($throwAway->suffixEn, $this->suffixesEn))
		{
			//"cho" or even "son" will likely belong to the gun if a gun is present...
			if(($throwAway->suffixEn == 'cho' or $throwAway->suffixEn == 'machi' or $throwAway->suffixEn == 'son' or $throwAway->suffixEn == 'mura') and $gunComponent = $this->getLevelComponent('gun'))	//Also need a "not already done the gun_cho" boolean flag?
			{
				$this->setLevelEn('gun_cho', $throwAway->rootEn, $this->suffixesNormalizer[$throwAway->suffixEn]);
			}
			
			else
			{
				$this->setLevelEn($this->suffixesEn[$throwAway->suffixEn], $throwAway->rootEn, $this->suffixesNormalizer[$throwAway->suffixEn]);
			}
		}
		
		elseif(preg_match('/[0-9]{3}[-][0-9]{4}/', $throwAway->rootEn))
		{
			$this->setLevelEn('postcode', $throwAway->rootEn);
			$this->setLevelJa('postcode', $throwAway->rootEn);
		}
		
		//"Manome 357" is, for example, a valid address that *doesn't* have a "1-2-3" type 'ban'
		//(so use is_numeric())
		elseif(preg_match('/[0-9]{1,}[-][0-9]{1,}[-][0-9]{1,}/', $throwAway->rootEn) or is_numeric($throwAway->rootEn))
		{
			$this->setLevelEn('ban', $throwAway->rootEn);
			$this->setLevelJa('ban', $throwAway->rootEn);
		}
		
		else
		{
			//What to do with things with dodgy or missing suffixes?
			//$this->setLevelEn('nokori_' . rand(111, 999), $rootEn, $suffixEn);
			$this->components['nokori_' . rand(111, 999)] = new JapaneseAddressComponent(null, null, $rootEn, $suffixEn);
		}
	}
	
	function setAutoEnFromArray(array $array)
	{
		foreach($array as $value)
		{
			$this->setAutoEn($value);
		}
	}
	
	function setAutoJaFromArray(array $array)
	{
		foreach($array as $value)
		{
			$this->setAutoJa($value);
		}
	}
	
	//Set component level according to suffix
	function setAutoJa($rootJa, $suffixJa = null)
	{
		$throwAway = new JapaneseAddressComponent();
		
		$throwAway->setJa($rootJa, $suffixJa);
		
		if(array_key_exists($throwAway->suffixJa, $this->suffixesJa))
		{
			//Need gun_cho stuff? (as seen in setAutoEn())
			
			$this->setLevelJa($this->suffixesJa[$throwAway->suffixJa], $rootJa, $suffixJa);
		}
		
		elseif(preg_match('/[0-9]{3}[-][0-9]{4}/', $throwAway->rootJa))
		{
			$this->setLevelEn('postcode', $throwAway->rootJa);
			$this->setLevelJa('postcode', $throwAway->rootJa);
		}
		
		//"Manome 357" is, for example, a valid address that *doesn't* have a "1-2-3" type 'ban'
		//(so use is_numeric())
		elseif(preg_match('/[0-9]{1,}[-][0-9]{1,}[-][0-9]{1,}/', $throwAway->rootJa) or is_numeric($throwAway->rootJa))
		{
			$this->setLevelEn('ban', $throwAway->rootJa);
			$this->setLevelJa('ban', $throwAway->rootJa);
		}
		
		//What to do with things with dodgy or missing suffixes?
		else
		{
			//What to do with things with dodgy or missing suffixes?
			//$this->setLevelEn('nokori_' . rand(111, 999), $rootEn, $suffixEn);
			$this->components['nokori_' . rand(111, 999)] = new JapaneseAddressComponent($rootJa, $suffixJa, null, null);
		}
	}
	
	function getJa($withPostcode = true)
	{
		$returnString = '';
		
		ksort($this->components);
		
		foreach($this->components as $level => $component)
		{
			if($level == $this->levels['postcode'])
			{
				if($withPostcode)
				{
					$returnString .= "〒{$component->getJa()}　";
				}
			}
			
			else
			{
				$returnString .= $component->getJa();
			}
		}
		
		return $returnString;
	}
	
	function getEn($separator = ', ')
	{
		$returnComponents = array();
		
		krsort($this->components);
		$printedPostcode = false;
		$printedBan = false;
		
		foreach($this->components as $level => $component)
		{//echo "{$level}/{$this->levelWordForLevelNumber[$level]}>";
			//We need special treatment to put the 1-2-3 'ban' number *after* the town/village/whatever
			//AND to put the postcode after the prefecture with no comma
			
			//Ignore missing components (an empty() check doesn't seem to work?)
			if($component->getEn() == '')
			{
				continue;
			}
			
			
			if($level == $this->levels['ban'])
			{
				continue;
			}
			
			elseif(($level == $this->levels['cho'] or $level == $this->levels['son'] or $level == $this->levels['basho']) and (array_key_exists($this->levels['ban'], $this->components)))	//Use $this->getLevelComponent() insterad of array_key_exists()???
			{
				if(!$printedBan)
				{
				$returnComponents[] = "{$component->getEn()} {$this->components[$this->levels['ban']]->getEn()}";
				$printedBan = true;
				}
				else
				{
					$returnComponents[] = $component->getEn();
				}
			}
			
			/*
			elseif(($level == $this->levels['cho'] or $level == $this->levels['son'] or $level == $this->levels['basho']) and (array_key_exists($this->levels['ban'], $this->components)))
			{
			}
			*/
			
			elseif($level == $this->levels['ken'] and ($postcode = $this->getLevelComponent('postcode')))
			{
				$returnComponents[] = ucfirst($component->rootEn) . " {$postcode->rootEn}";
				
				$printedPostcode = true;
			}
			
			elseif($level == $this->levels['postcode'] and $printedPostcode)
			{
				continue;
			}
			
			else
			{
				$returnComponents[] = $component->getEn();
			}
		}
		
		return implode($separator, $returnComponents);
	}
}

?>