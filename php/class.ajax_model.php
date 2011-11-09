<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Class representing the Ajax model (model as in "MVC")
class AjaxModel
{
	
	var $label_styles = array();
		
	//Constructor
	function __construct()
	{
		$this->label_styles['all'] = array('padding' => '1px', 'border' => '1px solid #BBBBBB', 'letterSpacing' => '0px', 'color' => 'black', 'textAlign' => 'center', 'display' => 'block', 'fontFamily' => 'monospace', 'fontSize' => '12px', 'whiteSpace' => 'nowrap');
		$this->label_styles['ken'] = array('backgroundColor' => '#ADD8E6');
		$this->label_styles['shi'] = array('backgroundColor' => '#90EE90');
		$this->label_styles['ku'] = array('backgroundColor' => '#FF8C00');
		$this->label_styles['gun'] = array('backgroundColor' => '#8A2BE2');
		$this->label_styles['gun_cho'] = array('backgroundColor' => '#8A2BE2');
		$this->label_styles['cho'] = array('backgroundColor' => '#FFA');
		$this->label_styles['basho'] = array('backgroundColor' => '#FFA');
	}
	
	//
	function search()	//json?
	{
		$homePage = new HomePage();	//$_REQUEST['q'] and $_REQUEST['type'] that we have will get passed over (^-^)
		
		return $homePage->resultant_ja_address;
	}
	
	//Get prefecture, city and town information from a Japanese postcode
	function getAddressFromPostcode()	//xml
	{
		$cleanPostcode = cleanPostcode($_REQUEST['postcode']);
		
		$sql = "SELECT * FROM postcode WHERE postcode = '{$cleanPostcode}'";
		
		$result = MySQL::queryRow($sql);
		
		if(!$result)
		{
			if($_REQUEST['lang'] ==  'en')
			{
				return '<ajaxerror>Postcode not found!</ajaxerror>';
			}
			
			else
			{
				return '<ajaxerror>郵便番号が見つかりません！</ajaxerror>';
			}
		}
		
		return "<prefecture_iso_code>{$result['prefecture_iso_code']}</prefecture_iso_code><city_en>{$result['city_en']}</city_en><city_kanji>{$result['city_kanji']}</city_kanji><town_en>{$result['town_en']}</town_en><town_kanji>{$result['town_kanji']}</town_kanji><clean_postcode>{$cleanPostcode}</clean_postcode>";
	}
	
	//Return prefecture labels (and coordinates) visible in the specified bounds
	function getVisiblePrefectureLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('ken_', id) AS id, ken, latitude, longitude FROM geo_label_ken WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_ken\"><text>{$row['ken']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['ken'])) . '</style></label>';
		}
		
		return $returnXml;
	}
		
	//Return city labels (and coordinates) visible in the specified bounds
	function getVisibleShiLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('shi_', id) AS id, shi, latitude, longitude FROM geo_label_shi WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_shi\"><text>{$row['shi']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['shi'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//Return ward labels (and coordinates) visible in the specified bounds
	function getVisibleKuLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('ku_', id) AS id, ku, latitude, longitude FROM geo_label_ku WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_ku\"><text>{$row['ku']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['ku'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//Return gun labels (and coordinates) visible in the specified bounds
	function getVisibleGunLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('gun_', id) AS id, gun, latitude, longitude FROM geo_label_gun WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_gun\"><text>{$row['gun']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['gun'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//Return gun_cho labels (and coordinates) visible in the specified bounds
	function getVisibleGunChoLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('guncho_', id) AS id, gun_cho, latitude, longitude FROM geo_label_gun_cho WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_gun_cho\"><text>{$row['gun_cho']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['gun_cho'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//Return town labels (and coordinates) visible in the specified bounds
	function getVisibleChoLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('cho_', id) AS id, cho, latitude, longitude FROM geo_label_cho WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_cho\"><text>{$row['cho']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['cho'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//Return village labels (and coordinates) visible in the specified bounds
	function getVisibleSonLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('son_', id) AS id, son, latitude, longitude FROM geo_label_son WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_son\"><text>{$row['son']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['son'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//Return basho labels (and coordinates) visible in the specified bounds
	function getVisibleBashoLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('basho_', id) AS id, basho, latitude, longitude FROM geo_label_basho WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_basho\"><text>{$row['basho']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng><style>" . fake_json_encode(array_merge($this->label_styles['all'], $this->label_styles['basho'])) . '</style></label>'; 
		}
				
		return $returnXml;
	}
	
	//NOT YET USED
	function getVisibleEkiLabels()	//xml
	{
		$returnXml = '';
		
		$whereClause = "(latitude BETWEEN {$_REQUEST['swLat']} AND {$_REQUEST['neLat']}) AND (longitude BETWEEN {$_REQUEST['swLng']} AND {$_REQUEST['neLng']})";
		
		$sql = "SELECT CONCAT('eki_', id) AS id, name_en, latitude, longitude FROM ekidata WHERE {$whereClause}";
		
		$results = MySQL::queryAll($sql);
		
		foreach($results as $row)
		{
			//<label id="labelId" class="cssClass"><text>labelText</text><lat>latitude</lat><lng>longitude</lng></label> 
			$returnXml .= "<label id=\"{$row['id']}\" class=\"label_eki\"><text>{$row['name_en']}</text><lat>{$row['latitude']}</lat><lng>{$row['longitude']}</lng></label>"; 
		}
				
		return $returnXml;
	}
	
	//Translate English into Japanese via Excite.co.jp (in library.japanese.php)
	function getJaFromEn()	//xml
	{
		return '<translation>' . excite_getJaFromEn($_REQUEST['en']) . '</translation>';
	}
	
	//Translate Japanese into English via Excite.co.jp (in library.japanese.php)
	function getEnFromJa()	//xml
	{
		return '<translation>' . excite_getEnFromJa($_REQUEST['ja']) . '</translation>';
	}
}