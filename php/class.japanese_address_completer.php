<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Take an incomplete (uncategorised English components, no Japanese components etc)
//JapaneseAddress object and complete it (via DB lookups) as much as we can

class JapaneseAddressCompleter
{
	var $japaneseAddress;
	var $prefectures = array();
	var $kanjiPrefectures = array();
	var $prefectureSuffixExceptions = array();
	
	function __construct()
	{
		$this->initializeArrays();
	}
	
	function initializeArrays()
	{
		//Prefectures (lowercase Hepburn spelling) and their ISO code
		$this->prefectures['hokkaido'] = '01';
		$this->prefectures['hokkaidou'] = '01';
		$this->prefectures['hokkaidoo'] = '01';
		$this->prefectures['hokaido'] = '01';
		$this->prefectures['hokaidou'] = '01';
		$this->prefectures['hokaidoo'] = '01';
		$this->prefectures['aomori'] = '02';
		$this->prefectures['iwate'] = '03';
		$this->prefectures['miyagi'] = '04';
		$this->prefectures['akita'] = '05';
		$this->prefectures['yamagata'] = '06';
		$this->prefectures['fukushima'] = '07';
		$this->prefectures['ibaraki'] = '08';
		$this->prefectures['tochigi'] = '09';
		$this->prefectures['gunma'] = '10';
		$this->prefectures['saitama'] = '11';
		$this->prefectures['chiba'] = '12';
		$this->prefectures['tokyo'] = '13';
		$this->prefectures['tookyoo'] = '13';
		$this->prefectures['toukyou'] = '13';
		$this->prefectures['kanagawa'] = '14';
		$this->prefectures['niigata'] = '15';
		$this->prefectures['toyama'] = '16';
		$this->prefectures['ishikawa'] = '17';
		$this->prefectures['fukui'] = '18';
		$this->prefectures['yamanashi'] = '19';
		$this->prefectures['nagano'] = '20';
		$this->prefectures['gifu'] = '21';
		$this->prefectures['shizuoka'] = '22';
		$this->prefectures['aichi'] = '23';
		$this->prefectures['mie'] = '24';
		$this->prefectures['shiga'] = '25';
		$this->prefectures['kyoto'] = '26';
		$this->prefectures['kyouto'] = '26';
		$this->prefectures['kyooto'] = '26';
		$this->prefectures['oosaka'] = '27';
		$this->prefectures['osaka'] = '27';
		$this->prefectures['ousaka'] = '27';
		$this->prefectures['hyogo'] = '28';
		$this->prefectures['hyougo'] = '28';
		$this->prefectures['hyoogo'] = '28';
		$this->prefectures['nara'] = '29';
		$this->prefectures['wakayama'] = '30';
		$this->prefectures['tottori'] = '31';
		$this->prefectures['shimane'] = '32';
		$this->prefectures['okayama'] = '33';
		$this->prefectures['hiroshima'] = '34';
		$this->prefectures['yamaguchi'] = '35';
		$this->prefectures['tokushima'] = '36';
		$this->prefectures['kagawa'] = '37';
		$this->prefectures['ehime'] = '38';
		$this->prefectures['kochi'] = '39';
		$this->prefectures['kouchi'] = '39';
		$this->prefectures['koochi'] = '39';
		$this->prefectures['fukuoka'] = '40';
		$this->prefectures['saga'] = '41';
		$this->prefectures['nagasaki'] = '42';
		$this->prefectures['kumamoto'] = '43';
		$this->prefectures['oita'] = '44';
		$this->prefectures['ooita'] = '44';
		$this->prefectures['ouita'] = '44';
		$this->prefectures['miyazaki'] = '45';
		$this->prefectures['kagoshima'] = '46';
		$this->prefectures['okinawa'] = '47';
		
		//Prefectures (kanji) and their lowercase Hepburn spelling
		$this->kanjiPrefectures['hokkaido'] = '北海道';
		$this->kanjiPrefectures['hokkaidou'] = '北海道';
		$this->kanjiPrefectures['hokkaidoo'] = '北海道';
		$this->kanjiPrefectures['hokaido'] = '北海道';
		$this->kanjiPrefectures['hokaidou'] = '北海道';
		$this->kanjiPrefectures['hokaidoo'] = '北海道';
		$this->kanjiPrefectures['aomori'] = '青森県';
		$this->kanjiPrefectures['iwate'] = '岩手県';
		$this->kanjiPrefectures['miyagi'] = '宮城県';
		$this->kanjiPrefectures['akita'] = '秋田県';
		$this->kanjiPrefectures['yamagata'] = '山形県';
		$this->kanjiPrefectures['fukushima'] = '福島県';
		$this->kanjiPrefectures['ibaraki'] = '茨城県';
		$this->kanjiPrefectures['tochigi'] = '栃木県';
		$this->kanjiPrefectures['gunma'] = '群馬県';
		$this->kanjiPrefectures['saitama'] = '埼玉県';
		$this->kanjiPrefectures['chiba'] = '千葉県';
		$this->kanjiPrefectures['tokyo'] = '東京都';
		$this->kanjiPrefectures['tookyoo'] = '東京都';
		$this->kanjiPrefectures['toukyou'] = '東京都';
		$this->kanjiPrefectures['kanagawa'] = '神奈川県';
		$this->kanjiPrefectures['niigata'] = '新潟県';
		$this->kanjiPrefectures['toyama'] = '富山県';
		$this->kanjiPrefectures['ishikawa'] = '石川県';
		$this->kanjiPrefectures['fukui'] = '福井県';
		$this->kanjiPrefectures['yamanashi'] = '山梨県';
		$this->kanjiPrefectures['nagano'] = '長野県';
		$this->kanjiPrefectures['gifu'] = '岐阜県';
		$this->kanjiPrefectures['shizuoka'] = '静岡県';
		$this->kanjiPrefectures['aichi'] = '愛知県';
		$this->kanjiPrefectures['mie'] = '三重県';
		$this->kanjiPrefectures['shiga'] = '滋賀県';
		$this->kanjiPrefectures['kyoto'] = '京都府';
		$this->kanjiPrefectures['kyouto'] = '京都府';
		$this->kanjiPrefectures['kyooto'] = '京都府';
		$this->kanjiPrefectures['oosaka'] = '大阪府';
		$this->kanjiPrefectures['osaka'] = '大阪府';
		$this->kanjiPrefectures['ousaka'] = '大阪府';
		$this->kanjiPrefectures['hyogo'] = '兵庫県';
		$this->kanjiPrefectures['hyougo'] = '兵庫県';
		$this->kanjiPrefectures['hyoogo'] = '兵庫県';
		$this->kanjiPrefectures['nara'] = '奈良県';
		$this->kanjiPrefectures['wakayama'] = '和歌山県';
		$this->kanjiPrefectures['tottori'] = '鳥取県';
		$this->kanjiPrefectures['shimane'] = '島根県';
		$this->kanjiPrefectures['okayama'] = '岡山県';
		$this->kanjiPrefectures['hiroshima'] = '広島県';
		$this->kanjiPrefectures['yamaguchi'] = '山口県';
		$this->kanjiPrefectures['tokushima'] = '徳島県';
		$this->kanjiPrefectures['kagawa'] = '香川県';
		$this->kanjiPrefectures['ehime'] = '愛媛県';
		$this->kanjiPrefectures['kochi'] = '高知県';
		$this->kanjiPrefectures['kouchi'] = '高知県';
		$this->kanjiPrefectures['koochi'] = '高知県';
		$this->kanjiPrefectures['fukuoka'] = '福岡県';
		$this->kanjiPrefectures['saga'] = '佐賀県';
		$this->kanjiPrefectures['nagasaki'] = '長崎県';
		$this->kanjiPrefectures['kumamoto'] = '熊本県';
		$this->kanjiPrefectures['oita'] = '大分県';
		$this->kanjiPrefectures['ooita'] = '大分県';
		$this->kanjiPrefectures['ouita'] = '大分県';
		$this->kanjiPrefectures['miyazaki'] = '宮崎県';
		$this->kanjiPrefectures['kagoshima'] = '鹿児島県';
		$this->kanjiPrefectures['okinawa'] = '沖縄県';
		
		$this->jaPrefectures['北海道'] = '01';
		$this->jaPrefectures['青森県'] = '02';
		$this->jaPrefectures['岩手県'] = '03';
		$this->jaPrefectures['宮城県'] = '04';
		$this->jaPrefectures['秋田県'] = '05';
		$this->jaPrefectures['山形県'] = '06';
		$this->jaPrefectures['福島県'] = '07';
		$this->jaPrefectures['茨城県'] = '08';
		$this->jaPrefectures['栃木県'] = '09';
		$this->jaPrefectures['群馬県'] = '10';
		$this->jaPrefectures['埼玉県'] = '11';
		$this->jaPrefectures['千葉県'] = '12';
		$this->jaPrefectures['東京都'] = '13';
		$this->jaPrefectures['神奈川県'] = '14';
		$this->jaPrefectures['新潟県'] = '15';
		$this->jaPrefectures['富山県'] = '16';
		$this->jaPrefectures['石川県'] = '17';
		$this->jaPrefectures['福井県'] = '18';
		$this->jaPrefectures['山梨県'] = '19';
		$this->jaPrefectures['長野県'] = '20';
		$this->jaPrefectures['岐阜県'] = '21';
		$this->jaPrefectures['静岡県'] = '22';
		$this->jaPrefectures['愛知県'] = '23';
		$this->jaPrefectures['三重県'] = '24';
		$this->jaPrefectures['滋賀県'] = '25';
		$this->jaPrefectures['京都府'] = '26';
		$this->jaPrefectures['大阪府'] = '27';
		$this->jaPrefectures['兵庫県'] = '28';
		$this->jaPrefectures['奈良県'] = '29';
		$this->jaPrefectures['和歌山県'] = '30';
		$this->jaPrefectures['鳥取県'] = '31';
		$this->jaPrefectures['島根県'] = '32';
		$this->jaPrefectures['岡山県'] = '33';
		$this->jaPrefectures['広島県'] = '34';
		$this->jaPrefectures['山口県'] = '35';
		$this->jaPrefectures['徳島県'] = '36';
		$this->jaPrefectures['香川県'] = '37';
		$this->jaPrefectures['愛媛県'] = '38';
		$this->jaPrefectures['高知県'] = '39';
		$this->jaPrefectures['福岡県'] = '40';
		$this->jaPrefectures['佐賀県'] = '41';
		$this->jaPrefectures['長崎県'] = '42';
		$this->jaPrefectures['熊本県'] = '43';
		$this->jaPrefectures['大分県'] = '44';
		$this->jaPrefectures['宮崎県'] = '45';
		$this->jaPrefectures['鹿児島県'] = '46';
		$this->jaPrefectures['沖縄県'] = '47';
		
		
		
		$this->romForKanjiPref['北海道'] = 'Hokkaido';
		$this->romForKanjiPref['青森県'] = 'Aomori-ken';
		$this->romForKanjiPref['岩手県'] = 'Iwate-ken';
		$this->romForKanjiPref['宮城県'] = 'Miyagi-ken';
		$this->romForKanjiPref['秋田県'] = 'Akita-ken';
		$this->romForKanjiPref['山形県'] = 'Yamagata-ken';
		$this->romForKanjiPref['福島県'] = 'Fukushima-ken';
		$this->romForKanjiPref['茨城県'] = 'Ibaraki-ken';
		$this->romForKanjiPref['栃木県'] = 'Tochigi-ken';
		$this->romForKanjiPref['群馬県'] = 'Gunma-ken';
		$this->romForKanjiPref['埼玉県'] = 'Saitama-ken';
		$this->romForKanjiPref['千葉県'] = 'Chiba-ken';
		$this->romForKanjiPref['東京都'] = 'Tokyo-to';
		$this->romForKanjiPref['神奈川県'] = 'Kanagawa-ken';
		$this->romForKanjiPref['新潟県'] = 'Niigata-ken';
		$this->romForKanjiPref['富山県'] = 'Toyama-ken';
		$this->romForKanjiPref['石川県'] = 'Ishikawa-ken';
		$this->romForKanjiPref['福井県'] = 'Fukui-ken';
		$this->romForKanjiPref['山梨県'] = 'Yamanashi-ken';
		$this->romForKanjiPref['長野県'] = 'Nagano-ken';
		$this->romForKanjiPref['岐阜県'] = 'Gifu-ken';
		$this->romForKanjiPref['静岡県'] = 'Shizuoka-ken';
		$this->romForKanjiPref['愛知県'] = 'Aichi-ken';
		$this->romForKanjiPref['三重県'] = 'Mie-ken';
		$this->romForKanjiPref['滋賀県'] = 'Shiga-ken';
		$this->romForKanjiPref['京都府'] = 'Kyoto-fu';
		$this->romForKanjiPref['大阪府'] = 'Osaka-fu';
		$this->romForKanjiPref['兵庫県'] = 'Hyogo-ken';
		$this->romForKanjiPref['奈良県'] = 'Nara-ken';
		$this->romForKanjiPref['和歌山県'] = 'Wakayama-ken';
		$this->romForKanjiPref['鳥取県'] = 'Tottori-ken';
		$this->romForKanjiPref['島根県'] = 'Shimane-ken';
		$this->romForKanjiPref['岡山県'] = 'Okayama-ken';
		$this->romForKanjiPref['広島県'] = 'Hiroshima-ken';
		$this->romForKanjiPref['山口県'] = 'Yamaguchi-ken';
		$this->romForKanjiPref['徳島県'] = 'Tokushima-ken';
		$this->romForKanjiPref['香川県'] = 'Kagawa-ken';
		$this->romForKanjiPref['愛媛県'] = 'Ehime-ken';
		$this->romForKanjiPref['高知県'] = 'Kochi-ken';
		$this->romForKanjiPref['福岡県'] = 'Fukuoka-ken';
		$this->romForKanjiPref['佐賀県'] = 'Saga-ken';
		$this->romForKanjiPref['長崎県'] = 'Nagasaki-ken';
		$this->romForKanjiPref['熊本県'] = 'Kumamoto-ken';
		$this->romForKanjiPref['大分県'] = 'Oita-ken';
		$this->romForKanjiPref['宮崎県'] = 'Miyazaki-ken';
		$this->romForKanjiPref['鹿児島県'] = 'Kagoshima-ken';
		$this->romForKanjiPref['沖縄県'] = 'Okinawa-ken';
		
		//Prefectures that aren't "ken" (and their corresponding suffix)			
		$this->prefectureSuffixExceptions = array('tokyo' => 'to', 'tookyoo' => 'to', 'toukyou' => 'to', 'osaka' => 'fu', 'oosaka' => 'fu', 'ousaka' => 'fu', 'kyoto' => 'fu', 'kyouto' => 'fu', 'kyooto' => 'fu', 'hokkaido' => '', 'hokkaidou' => '', 'hokkaidoo' => '', 'hokaido' => '', 'hokaidou' => '', 'hokaidou' => '');
		
	}
	
	function complete(JapaneseAddress $japaneseAddress)
	{
		$this->japaneseAddress = $japaneseAddress;
		
		
		
		$this->categorizeUncategorizedEnglishComponents();	//categorizes *and* gets the Japanese equivalent
		
		//Some kind of back-completing? (eg. fill in city and prefecture if we only have a town etc)
		
		//$this->insertMatchingJapaneseComponents();
		
		$this->japanizePreviouslyCategorisedEnglishComponents();
		
		return $this->japaneseAddress;
	}
	
	function completeKanjiAddress(JapaneseAddress $japaneseAddress)
	{
		$this->japaneseAddress = $japaneseAddress;
		
		
		foreach($this->japaneseAddress->components as $level => $component)
		{	
			$componentEn = $component->getEn();
			
			if(empty($componentEn))
			{
				//ECHO "Component with level of {$level} and Japanese of '{$component->getJa()}' has no English!<br/>";
				
				$levelWord = $this->japaneseAddress->levelWordForLevelNumber[$level];
				
				$this->japaneseAddress->setLevelEn($levelWord, $this->anglicizeComponent($component, $levelWord)); 
			}
		}
		
		return $this->japaneseAddress;
	}
	
	function japanizePreviouslyCategorisedEnglishComponents()
	{
		foreach($this->japaneseAddress->components as $level => $component)
		{	
			$componentJa = $component->getJa();
			
			if(empty($componentJa))
			{
				//ECHO "Component with level of {$level} and English of '{$component->getEn()}' has no Japanese!<br/>";
				
				$levelWord = $this->japaneseAddress->levelWordForLevelNumber[$level];
				
				$this->japaneseAddress->setLevelJa($levelWord, $this->japanizeComponent($component, $levelWord)); 
			}
		}
	}
	
	//Add appropriate Japanese to an address component that was specified, in the search box, as
	//"gifu-ken" (or whatever) - ie. the user knows what level it is
	//ALERT! THe SQL and stuff is similar to that in addComponentIfLevel()...
	function japanizeComponent(JapaneseAddressComponent $addressComponent, $levelWord)
	{
		$rootJa = '';
		
		//$postcode = DB_TABLE_POSTCODE;
		$postcode = 'postcode';
		
		switch($levelWord)
		{
			case 'ken':
				$rootJa = $this->kanjiPrefectures[$addressComponent->rootEn];
				break;
			
			case 'shi':
				$getCitySql = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a city
					$getCitySql = "SELECT shi_ja FROM {$postcode} WHERE shi_en LIKE '{$addressComponent->rootEn}-shi' AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
			
				else
				{
					$getCitySql = "SELECT shi_ja FROM {$postcode} WHERE shi_en LIKE '{$addressComponent->rootEn}-shi'";
				}
			
				$rootJa = MySQL::queryOne($getCitySql);
				break;
			
			case 'ku':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a ward
					$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a ward
					$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
				}
				
				$checkWardSql = "SELECT ku_ja FROM {$postcode} WHERE (ku_en LIKE '{$addressComponent->rootEn}-ku') {$andClause}";
				
				$rootJa = MySQL::queryOne($checkWardSql);
				break;
			
			case 'gun':
				$checkGunSql = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a gun
					$checkGunSql = "SELECT gun_ja FROM {$postcode} WHERE gun_en LIKE '{$addressComponent->rootEn}-gun' AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
				
				else
				{
					$checkGunSql = "SELECT gun_ja FROM {$postcode} WHERE gun_en LIKE '{$addressComponent->rootEn}-gun'";
				}
				
				$rootJa = MySQL::queryOne($checkGunSql);
				break;
			
			case 'gun_cho':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a ward
					$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a gun-level cho
					$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
				}
				
				$checkGunChoSql = "SELECT gun_cho_ja FROM {$postcode} WHERE (gun_cho_en LIKE '{$addressComponent->rootEn}-cho' OR gun_cho_en LIKE '{$addressComponent->rootEn}-machi') {$andClause}";
				
				$rootJa = MySQL::queryOne($checkGunChoSql);
				break;
			
			case 'cho':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a cho
					$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a cho
					$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
				}
				
				if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
				{
					//Already have ward so use that to narrow the search for a cho
					$andClause .= " AND ku_en LIKE '{$wardLevelComponent->rootEn}-ku'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a cho
					$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
				}
				
				if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
				{
					//Already have gun_cho so use that to narrow the search for a cho
					$andClause .= " AND gun_cho_en LIKE '{$gunChoLevelComponent->rootEn}-{$gunChoLevelComponent->suffixEn}'";
				}
				
				$checkChoSql = "SELECT cho_ja FROM {$postcode} WHERE (cho_en LIKE '{$addressComponent->rootEn}-cho' OR cho_en LIKE '{$addressComponent->rootEn}-machi') {$andClause}";
					
				$rootJa = MySQL::queryOne($checkChoSql);
				break;
			
			case 'son':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a son
					$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a son
					$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
				}
				
				if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
				{
					//Already have ward so use that to narrow the search for a son
					$andClause .= " AND ku_en LIKE '{$wardLevelComponent->rootEn}-ku'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a son
					$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
				}
				
				if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
				{
					//Already have gun_cho so use that to narrow the search for a son
					$andClause .= " AND gun_cho_en LIKE '{$gunChoLevelComponent->rootEn}-{$gunChoLevelComponent->suffixEn}'";
				}
				
				if($choLevelComponent = $this->japaneseAddress->getLevelComponent('cho'))
				{
					//Already have cho so use that to narrow the search for a son
					$andClause .= " AND cho_en LIKE '{$choLevelComponent->rootEn}-{$choLevelComponent->suffixEn}'";
				}
				
				$checkSonSql = "SELECT son_ja FROM {$postcode} WHERE (son_en LIKE '{$addressComponent->rootEn}-son' OR son_en LIKE '{$addressComponent->rootEn}-mura') {$andClause}";
				
				$rootJa = MySQL::queryOne($checkSonSql);
				break;
			
			case 'basho':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a basho
					$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a basho
					$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
				}
				
				if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
				{
					//Already have ward so use that to narrow the search for a basho
					$andClause .= " AND ku_en LIKE '{$wardLevelComponent->rootEn}-ku'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a basho
					$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
				}
				
				if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
				{
					//Already have gun_cho so use that to narrow the search for a basho
					$andClause .= " AND gun_cho_en LIKE '{$gunChoLevelComponent->rootEn}-{$gunChoLevelComponent->suffixEn}'";
				}
				
				if($choLevelComponent = $this->japaneseAddress->getLevelComponent('cho'))
				{
					//Already have cho so use that to narrow the search for a basho
					$andClause .= " AND cho_en LIKE '{$choLevelComponent->rootEn}-{$choLevelComponent->suffixEn}'";
				}
				
				if($sonLevelComponent = $this->japaneseAddress->getLevelComponent('son'))
				{
					//Already have son so use that to narrow the search for a basho
					$andClause .= " AND son_en LIKE '{$sonLevelComponent->rootEn}-{$sonLevelComponent->suffixEn}'";
				}
			
				$checkBashoSql = "SELECT basho_ja FROM {$postcode} WHERE (basho_en LIKE '{$addressComponent->rootEn}') {$andClause}";
			//VAR_DUMP($checkBashoSql);
				$rootJa = MySQL::queryOne($checkBashoSql);
				break;
			
		}
		//ECHO "rootJa is: {$rootJa}<hr/>";
		return $rootJa;
	}
	
	//Add appropriate English to an address component that was specified, in the search box, as
	//"岐阜県" (or whatever) - ie. the user knows what level it is
	//ALERT! THe SQL and stuff is similar to that in addComponentIfLevel()...
	function anglicizeComponent(JapaneseAddressComponent $addressComponent, $levelWord)
	{
		//ECHO "{$levelWord}";	
	
		$rootEn = '';
		
		//$postcode = DB_TABLE_POSTCODE;
		$postcode = 'postcode';
		
		switch($levelWord)
		{
			case 'ken':
				$rootEn = $this->romForKanjiPref[$addressComponent->getJa()];
				//$rootEn = $this->database->queryOne('SELECT name_en FROM ' . DB_TABLE_PREFECTURE . " WHERE name_ja = '{$addressComponent->getJa()}' OR short_name_ja = '{$addressComponent->getJa()}'");
				//Use array instead of DB lookup?
				break;
			
			case 'shi':
				$getCitySql = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a city
					$getCitySql = "SELECT shi_en FROM {$postcode} WHERE shi_ja LIKE '{$addressComponent->getJa()}' AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
			
				else
				{
					$getCitySql = "SELECT shi_en FROM {$postcode} WHERE shi_ja LIKE '{$addressComponent->getJa()}'";
				}
			
				$rootEn = MySQL::queryOne($getCitySql);
				break;
			
			case 'ku':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a ward
					$andClause .= "AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a ward
					$andClause .= " AND shi_ja LIKE '{$cityLevelComponent->getJa()}'";
				}
				
				$checkWardSql = "SELECT ku_en FROM {$postcode} WHERE (ku_ja LIKE '{$addressComponent->getJa()}') {$andClause}";
				
				$rootEn = MySQL::queryOne($checkWardSql);
				break;
			
			case 'gun':
				$checkGunSql = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a gun
					$checkGunSql = "SELECT gun_en FROM {$postcode} WHERE gun_ja LIKE '{$addressComponent->getJa()}' AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
				
				else
				{
					$checkGunSql = "SELECT gun_en FROM {$postcode} WHERE gun_ja LIKE '{$addressComponent->getJa()}'";
				}
				
				$rootEn = MySQL::queryOne($checkGunSql);
				break;
			
			case 'gun_cho':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a ward
					$andClause .= "AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a gun-level cho
					$andClause .= " AND gun_ja LIKE '{$gunLevelComponent->getJa()}'";
				}
				
				$checkGunChoSql = "SELECT gun_cho_en FROM {$postcode} WHERE (gun_cho_ja LIKE '{$addressComponent->getJa()}') {$andClause}";
				
				$rootEn = MySQL::queryOne($checkGunChoSql);
				break;
			
			case 'cho':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a cho
					$andClause .= "AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a cho
					$andClause .= " AND shi_ja LIKE '{$cityLevelComponent->getJa()}'";
				}
				
				if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
				{
					//Already have ward so use that to narrow the search for a cho
					$andClause .= " AND ku_ja LIKE '{$wardLevelComponent->getJa()}'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a cho
					$andClause .= " AND gun_ja LIKE '{$gunLevelComponent->getJa()}'";
				}
				
				if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
				{
					//Already have gun_cho so use that to narrow the search for a cho
					$andClause .= " AND gun_cho_ja LIKE '{$gunChoLevelComponent->getJa()}'";
				}
				
				$checkChoSql = "SELECT cho_en FROM {$postcode} WHERE (cho_ja LIKE '{$addressComponent->getJa()}') {$andClause}";
					
				$rootEn = MySQL::queryOne($checkChoSql);
				break;
			
			case 'son':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a son
					$andClause .= "AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a son
					$andClause .= " AND shi_ja LIKE '{$cityLevelComponent->getJa()}'";
				}
				
				if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
				{
					//Already have ward so use that to narrow the search for a son
					$andClause .= " AND ku_ja LIKE '{$wardLevelComponent->getJa()}'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a son
					$andClause .= " AND gun_ja LIKE '{$gunLevelComponent->getJa()}'";
				}
				
				if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
				{
					//Already have gun_cho so use that to narrow the search for a son
					$andClause .= " AND gun_cho_ja LIKE '{$gunChoLevelComponent->getJa()}'";
				}
				
				if($choLevelComponent = $this->japaneseAddress->getLevelComponent('cho'))
				{
					//Already have cho so use that to narrow the search for a son
					$andClause .= " AND cho_ja LIKE '{$choLevelComponent->getJa()}'";
				}
				
				$checkSonSql = "SELECT son_en FROM {$postcode} WHERE (son_ja LIKE '{$addressComponent->getJa()}') {$andClause}";
				
				$rootEn = MySQL::queryOne($checkSonSql);
				break;
			
			case 'basho':
				$andClause = '';
			
				if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
				{
					//Already have prefecture so use that to narrow the search for a basho
					$andClause .= "AND prefecture_iso_code = '{$this->jaPrefectures[$kenLevelComponent->getJa()]}'";
				}
				
				if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
				{
					//Already have city so use that to narrow the search for a basho
					$andClause .= " AND shi_ja LIKE '{$cityLevelComponent->getJa()}'";
				}
				
				if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
				{
					//Already have ward so use that to narrow the search for a basho
					$andClause .= " AND ku_ja LIKE '{$wardLevelComponent->getJa()}'";
				}
				
				if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
				{
					//Already have gun so use that to narrow the search for a basho
					$andClause .= " AND gun_ja LIKE '{$gunLevelComponent->getJa()}'";
				}
				
				if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
				{
					//Already have gun_cho so use that to narrow the search for a basho
					$andClause .= " AND gun_cho_ja LIKE '{$gunChoLevelComponent->getJa()}'";
				}
				
				if($choLevelComponent = $this->japaneseAddress->getLevelComponent('cho'))
				{
					//Already have cho so use that to narrow the search for a basho
					$andClause .= " AND cho_ja LIKE '{$choLevelComponent->getJa()}'";
				}
				
				if($sonLevelComponent = $this->japaneseAddress->getLevelComponent('son'))
				{
					//Already have son so use that to narrow the search for a basho
					$andClause .= " AND son_ja LIKE '{$sonLevelComponent->getJa()}'";
				}
			
				$checkBashoSql = "SELECT basho_en FROM {$postcode} WHERE (basho_ja LIKE '{$addressComponent->getJa()}') {$andClause}";
			//VAR_DUMP($checkBashoSql);
				$rootEn = MySQL::queryOne($checkBashoSql);
				break;
			
		}
		//ECHO "rootEn is: {$rootEn}<hr/>";
		return $rootEn;
	}
	
	function categorizeUncategorizedEnglishComponents()
	{
		//Sort components?
		
		foreach($this->japaneseAddress->components as $componentIndex => $component)
		{
			if(strpos($componentIndex, 'nokori_') === 0)
			{
				//$this->japaneseAddress->components[] =
				$this->categorize($component, $componentIndex); 
			}
		}
	}
	
	function categorize(JapaneseAddressComponent $addressComponent, $nokoriIndex)
	{
		$absentLevels = $this->japaneseAddress->getAbsentLevels();
		
		foreach($absentLevels as $absentLevel)
		{
			if($this->addComponentIfLevel($addressComponent, $absentLevel, $nokoriIndex) === true)
			{
				break;
			}
		}
	}
	
	function addComponentIfLevel(JapaneseAddressComponent $addressComponent, $level, $nokoriIndex)
	{
		//$postcode = DB_TABLE_POSTCODE;
		$postcode = 'postcode';
		
		if($level == 'ken')	//Use lookup tables as opposed to DB lookups ;-)
		{
			if(array_key_exists($addressComponent->rootEn, $this->prefectures))
			{
				if(array_key_exists($addressComponent->rootEn, $this->prefectureSuffixExceptions))
				{
					$this->japaneseAddress->setLevelEn('ken', $addressComponent->rootEn, $this->prefectureSuffixExceptions[$addressComponent->rootEn]);
				}
				
				else
				{	
					$this->japaneseAddress->setLevelEn('ken', $addressComponent->rootEn, 'ken');
				}
				
				//Ja
				$this->japaneseAddress->setLevelJa('ken', $this->kanjiPrefectures[$addressComponent->rootEn]);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
		}
		
		elseif($level == 'shi')	//DB lookup narrowed by prefecture (if present) [prolly don't need narrowing because duplicate city names prolly don't happen]
		{
			$checkCitySql = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a city
				$checkCitySql = "SELECT shi_ja, shi_en FROM {$postcode} WHERE shi_en LIKE '{$addressComponent->rootEn}-shi' AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			else
			{
				$checkCitySql = "SELECT shi_ja, shi_en FROM {$postcode} WHERE shi_en LIKE '{$addressComponent->rootEn}-shi'";
			}
			
			$row = MySQL::queryRow($checkCitySql);
			
			if($row)
			{
				$this->japaneseAddress->setLevelEn('shi', $addressComponent->rootEn, 'shi');
				
				//Ja
				$this->japaneseAddress->setLevelJa('shi', $row['shi_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
		}
		
		elseif($level == 'ku')
		{
			$andClause = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a ward
				$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
			{
				//Already have city so use that to narrow the search for a ward
				$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
			}
			
			$checkWardSql = "SELECT ku_ja, ku_en FROM {$postcode} WHERE (ku_en LIKE '{$addressComponent->rootEn}-ku') {$andClause}";
			
			$row = MySQL::queryRow($checkWardSql);
			
			if($row)
			{
				$this->japaneseAddress->setLevelEn('ku', $addressComponent->rootEn, 'ku');
				
				//Ja
				$this->japaneseAddress->setLevelJa('ku', $row['ku_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
		}
		
		elseif($level == 'gun')
		{
			$checkGunSql = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a gun
				$checkGunSql = "SELECT gun_ja, gun_en FROM {$postcode} WHERE gun_en LIKE '{$addressComponent->rootEn}-gun' AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			else
			{
				$checkGunSql = "SELECT gun_ja, gun_en FROM {$postcode} WHERE gun_en LIKE '{$addressComponent->rootEn}-gun'";
			}
			
			$row = MySQL::queryRow($checkGunSql);
			
			if($row)
			{
				$this->japaneseAddress->setLevelEn('gun', $addressComponent->rootEn, 'gun');
				
				//Ja
				$this->japaneseAddress->setLevelJa('gun', $row['gun_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
			
		}
		
		elseif($level == 'gun_cho')
		{
			$andClause = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a ward
				$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
			{
				//Already have gun so use that to narrow the search for a gun-level cho
				$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
			}
			
			$checkGunChoSql = "SELECT gun_cho_ja, gun_cho_en FROM {$postcode} WHERE (gun_cho_en LIKE '{$addressComponent->rootEn}-cho' OR gun_cho_en LIKE '{$addressComponent->rootEn}-machi') {$andClause}";
			
			$row = MySQL::queryRow($checkGunChoSql);
			
			if($row)
			{
				$this->japaneseAddress->setLevelEn('gun_cho', $row['gun_cho_en']);
				
				//Ja
				$this->japaneseAddress->setLevelJa('gun_cho', $row['gun_cho_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
			
		}
		
		elseif($level == 'cho')
		{
			$andClause = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a cho
				$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
			{
				//Already have city so use that to narrow the search for a cho
				$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
			}
			
			if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
			{
				//Already have ward so use that to narrow the search for a cho
				$andClause .= " AND ku_en LIKE '{$wardLevelComponent->rootEn}-ku'";
			}
			
			if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
			{
				//Already have gun so use that to narrow the search for a cho
				$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
			}
			
			if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
			{
				//Already have gun_cho so use that to narrow the search for a cho
				$andClause .= " AND gun_cho_en LIKE '{$gunChoLevelComponent->rootEn}-{$gunChoLevelComponent->suffixEn}'";
			}
			
			
			
			$checkChoSql = "SELECT cho_ja, cho_en FROM {$postcode} WHERE (cho_en LIKE '{$addressComponent->rootEn}-cho' OR cho_en LIKE '{$addressComponent->rootEn}-machi') {$andClause}";
			
			
			
			$row = MySQL::queryRow($checkChoSql);
			
			if($row)
			{
				$this->japaneseAddress->setLevelEn('cho', $row['cho_en']);
				
				//Ja
				$this->japaneseAddress->setLevelJa('cho', $row['cho_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
		}
		
		elseif($level == 'son')
		{
			$andClause = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a son
				$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
			{
				//Already have city so use that to narrow the search for a son
				$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
			}
			
			if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
			{
				//Already have ward so use that to narrow the search for a son
				$andClause .= " AND ku_en LIKE '{$wardLevelComponent->rootEn}-ku'";
			}
			
			if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
			{
				//Already have gun so use that to narrow the search for a son
				$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
			}
			
			if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
			{
				//Already have gun_cho so use that to narrow the search for a son
				$andClause .= " AND gun_cho_en LIKE '{$gunChoLevelComponent->rootEn}-{$gunChoLevelComponent->suffixEn}'";
			}
			
			if($choLevelComponent = $this->japaneseAddress->getLevelComponent('cho'))
			{
				//Already have cho so use that to narrow the search for a son
				$andClause .= " AND cho_en LIKE '{$choLevelComponent->rootEn}-{$choLevelComponent->suffixEn}'";
			}
			
			$checkSonSql = "SELECT son_ja, son_en FROM {$postcode} WHERE (son_en LIKE '{$addressComponent->rootEn}-son' OR son_en LIKE '{$addressComponent->rootEn}-mura') {$andClause}";
			//VAR_DUMP($checkSonSql);
			$row = MySQL::queryRow($checkSonSql);
			//VAR_DUMP($row);
			if($row)
			{
				$this->japaneseAddress->setLevelEn('son', $row['son_en']);
				
				//Ja
				$this->japaneseAddress->setLevelJa('son', $row['son_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
			
		}
		
		elseif($level == 'basho')
		{
			$andClause = '';
			
			if($kenLevelComponent = $this->japaneseAddress->getLevelComponent('ken'))
			{
				//Already have prefecture so use that to narrow the search for a basho
				$andClause .= "AND prefecture_iso_code = '{$this->prefectures[$kenLevelComponent->rootEn]}'";
			}
			
			if($cityLevelComponent = $this->japaneseAddress->getLevelComponent('shi'))
			{
				//Already have city so use that to narrow the search for a basho
				$andClause .= " AND shi_en LIKE '{$cityLevelComponent->rootEn}-shi'";
			}
			
			if($wardLevelComponent = $this->japaneseAddress->getLevelComponent('ku'))
			{
				//Already have ward so use that to narrow the search for a basho
				$andClause .= " AND ku_en LIKE '{$wardLevelComponent->rootEn}-ku'";
			}
			
			if($gunLevelComponent = $this->japaneseAddress->getLevelComponent('gun'))
			{
				//Already have gun so use that to narrow the search for a basho
				$andClause .= " AND gun_en LIKE '{$gunLevelComponent->rootEn}-gun'";
			}
			
			if($gunChoLevelComponent = $this->japaneseAddress->getLevelComponent('gun_cho'))
			{
				//Already have gun_cho so use that to narrow the search for a basho
				$andClause .= " AND gun_cho_en LIKE '{$gunChoLevelComponent->rootEn}-{$gunChoLevelComponent->suffixEn}'";
			}
			
			if($choLevelComponent = $this->japaneseAddress->getLevelComponent('cho'))
			{
				//Already have cho so use that to narrow the search for a basho
				$andClause .= " AND cho_en LIKE '{$choLevelComponent->rootEn}-{$choLevelComponent->suffixEn}'";
			}
			
			if($sonLevelComponent = $this->japaneseAddress->getLevelComponent('son'))
			{
				//Already have son so use that to narrow the search for a basho
				$andClause .= " AND son_en LIKE '{$sonLevelComponent->rootEn}-{$sonLevelComponent->suffixEn}'";
			}
			
			$checkBashoSql = "SELECT basho_en FROM {$postcode} WHERE (basho_en LIKE '{$addressComponent->rootEn}') {$andClause}";
			
			$row = MySQL::queryRow($checkBashoSql);
			
			if($row)
			{
				$this->japaneseAddress->setLevelEn('basho', $row['basho_en']);
				
				//Ja
				$this->japaneseAddress->setLevelJa('basho', $row['basho_ja']);
				
				//Added with a category so trash from "nokori_xyz"
				unset($this->japaneseAddress->components[$nokoriIndex]);
				
				return true;
			}
			
			return false;
			
		}
		
		
	}
}