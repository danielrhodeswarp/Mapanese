<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Data model for a Japanese address component (meaning a component of an address of a location in Japan)
//With address components in both kanji and romaji

class JapaneseAddressComponent
{
	var $rootJa;
	var $rootEn;
	var $suffixJa;
	var $suffixEn;
	//
	var $recognizedSuffixesJa = array();
	var $recognizedSuffixesEn = array();
	
	//Constructor
	function __construct($rootJa = null, $suffixJa = null, $rootEn = null, $suffixEn = null)
	{
		$this->initializeArrays();
		
		//$this->rootJa = $rootJa;
		//$this->suffixJa = $suffixJa;
		//$this->rootEn = $rootEn;
		//$this->suffixEn = $suffixEn;
		
		$this->setEn($rootEn, $suffixEn);
		$this->setJa($rootJa, $suffixJa);
	}
	
	function initializeArrays()
	{
		$this->recognizedSuffixesJa = array('県', '市', '都', '府', '郡', '区', '町', '村');
		$this->recognizedSuffixesEn = array('ken', 'pref', 'prefecture', 'shi', 'city', 'to', 'fu', 'gun', 'ku', 'ward', 'machi', 'cho', 'chou', 'town', 'mura', 'son', 'village');
	}
	
	function setAll($rootJa, $suffixJa, $rootEn, $suffixEn)
	{
		$this->rootJa = $rootJa;
		$this->suffixJa = $suffixJa;
		$this->rootEn = $rootEn;
		$this->suffixEn = $suffixEn;
	}
	
	//Can pass, for example, ("岐阜市") or ("岐阜", "市")
	//[passing ("岐阜市", "市") will cheese things up!]
	function setJa($root, $suffix = null)
	{
		if(!is_null($suffix))
		{
			$this->suffixJa = $suffix;
			$this->rootJa = $root;
		}
		
		else
		{
			$positionOfLastCharacter = mb_strlen($root) - 1;
			
			$lastCharacter = mb_substr($root, $positionOfLastCharacter);
			
			if(in_array($lastCharacter, $this->recognizedSuffixesJa))
			{
				$this->suffixJa = $lastCharacter;
				$this->rootJa = mb_substr($root, 0, $positionOfLastCharacter - 0);
			}
			
			else	//No recognizable suffix so throw everything into $this->rootJa and have no suffix
			{
				$this->rootJa = $root;
			}
		}
	}
	
	//Can pass, for example, ("Gifu-shi") or ("Gifu", "shi")
	//[passing ("Gifu-shi", "shi") will cheese things up!]
	function setEn($root, $suffix = null)
	{
		if(!is_null($suffix))
		{
			$this->suffixEn = $suffix;
			$this->rootEn = $root;
			
		}
		
		else
		{
			$parts = explode('-', $root);
			
			if(count($parts) == 1)	//No hyphen in $root
			{
				$this->rootEn = $root;
			}
			
			else/*if(count($parts) == 2)*/	//assume only 1 hyphen in $root?
			{
				if(in_array($parts[1], $this->recognizedSuffixesEn))
				{
					$this->suffixEn = $parts[1];
					$this->rootEn = $parts[0];
					
				}
				
				else	//Convert, eg., "takaido-nishi" to "takaidonishi" (for matching to our DB)
				{
					//$this->rootEn = $root;
					if(is_numeric(str_replace('-', '', $root)))
					{	
						$this->rootEn = $root;
					}
					
					else
					{
						$this->rootEn = str_replace('-', '', $root);
					}
				}
			}
			
			
		}
	}
	
	function getJa()
	{
		return "{$this->rootJa}{$this->suffixJa}";	//Absent suffix won't show anyway
	}
	
	function getEn()
	{
		if(empty($this->suffixEn))
		{
			return ucwords("{$this->rootEn}");
		}
		
		else
		{
			return ucwords("{$this->rootEn}-{$this->suffixEn}");
		}
	}
}