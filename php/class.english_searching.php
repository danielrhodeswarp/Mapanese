<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//SEARCH MACHINE FOR ENGLISH LANGUAGE QUERIES

//THE FOLLOWING ADDRESSES NEED SPECIAL ATTENTION:
//TOKYO
//HOKKAIDO
//OSAKA

//MEMO
//~~~~
//-Support Hokkaido also as "Hokkai-do(u)"??????
//-Remove any kind of "JAPAN" token?
//-Don't forget SON and MURA
//-When we have "gun" in the mix, a full address can contain *TWO* "cho"s or TWO "machi"s or one of eacH!

//
class EnglishSearching
{
	var $japaneseAddress;
	var $completedJapaneseAddress;
	
	//Lookup arrays for prefecture handling
	var $prefectures = array();
	var $kanjiPrefectures = array();
	var $prefectureSuffixExceptions = array();
	var $suffixes = array();	//2D array of "Ken" and "shi" etc (for merging "gifu ken" or whatever when a space is present)
	
	var $originalQuery;
	var $cleanedQuery;
	var $multipleResults;	//Boolean
	var $unlabelledParts = array();	//Address components contained in $originalQuery
	var $labelledParts = array();	//Address components contained in $originalQuery labelled by type and for ordering purposes
	var $labelledPartsJa = array();	//Japanese language equivalent of $labelledParts
	var $enAddress;
	var $jaAddress;	//This is simply $labelledPartJa sorted and glued together
	
	//Basically supported formats for $englishLanguageQuery:
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//Any/all of the following in any order separated by comma and/or space
	//
	//prefecturename
	//prefecturename-ken
	//prefecturename-pref
	//prefecturename-prefecture
	//prefecturename ken
	//prefecturename pref
	//prefecturename prefecture
	//
	//cityname
	//cityname-shi
	//cityname-city
	//cityname shi
	//cityname city
	//
	//ku
	//ward
	//
	////townname-mati?
	//townname
	//townname-cho
	//townname-chou
	//townname-machi
	////townname-town
	//townname cho
	//townname chou
	//townname machi
	////townname town
	//
	//Prolly NEED TO SUPPORT prefecturenameken, townnamechou type tokens TOO! YO!
	function __construct($englishLanguageQuery)
	{
		
		
		$this->initializeLookupArrays();
		
		$this->originalQuery = $englishLanguageQuery;
		
		//Lowercase and trim the original query here?
		//(and then speed up by removing all the strlower()s we have in string checks etc???)
		$this->cleanedQuery = trim($englishLanguageQuery);
		$this->cleanedQuery = strtolower($englishLanguageQuery);
		$this->cleanedQuery = str_replace(array('　', '、'), array(' ', ','), $this->cleanedQuery);
		$this->cleanedQuery = preg_replace('|[ ]{2,}|', ' ', $this->cleanedQuery);
		$this->cleanedQuery = preg_replace('|[,]{2,}|', ',', $this->cleanedQuery);
		
		$this->unlabelledParts = $this->getUnlabelledParts($this->cleanedQuery);
		//VAR_DUMP($this->unlabelledParts);
		
		$this->japaneseAddress->setAutoEnFromArray($this->unlabelledParts);
		//VAR_DUMP($this->japaneseAddress);
		
//ECHO $this->japaneseAddress->getEn();
//ECHO $this->japaneseAddress->getJa();
//ECHO '|~|';
		
		
		$jac = new JapaneseAddressCompleter();

		$this->completedJapaneseAddress = $jac->complete($this->japaneseAddress);
		
//ECHO $this->completedJapaneseAddress->getEn();
//ECHO $this->completedJapaneseAddress->getJa();		
		
		/*
		$unusedParts = $this->labelParts_pass_1($this->unlabelledParts);
		PRINT_R($unusedParts);
		$this->labelParts_pass_2($unusedParts);
		
		$this->enAddress = $this->labelledPartsToEnAddress($this->labelledParts);
		$this->jaAddress = $this->labelledPartsToJaAddress($this->labelledParts);
		*/
	}
	
	//
	function initializeLookupArrays()
	{
		$this->japaneseAddress = new JapaneseAddress();
		
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
		
		//Prefectures that aren't "ken" (and their corresponding suffix)			
		$this->prefectureSuffixExceptions = array('tokyo' => '-to', 'tookyoo' => '-to', 'toukyou' => '-to', 'osaka' => '-fu', 'oosaka' => '-fu', 'ousaka' => '-fu', 'kyoto' => '-fu', 'kyouto' => '-fu', 'kyooto' => '-fu', 'hokkaido' => '', 'hokkaidou' => '', 'hokkaidoo' => '', 'hokaido' => '', 'hokaidou' => '', 'hokaidou' => '');
		
		//"Ken" or "shi" etc (for merging "gifu ken" or whatever when a space is present)
		$this->suffixes['ken'] = array('ken', 'pref', 'prefecture', 'fu', 'to');
		$this->suffixes['shi'] = array('shi', 'city');
		$this->suffixes['ku'] = array('ku', 'ward');
		$this->suffixes['gun'] = array('gun');	//"district"?
		$this->suffixes['cho'] = array('cho', 'chou', 'machi', 'town');
		$this->suffixes['son'] = array('son', 'mura', 'village');
		
		$this->suffixes['all'] = array_merge($this->suffixes['ken'], $this->suffixes['shi'], $this->suffixes['ku'], $this->suffixes['gun'], $this->suffixes['cho'], $this->suffixes['son']);
		
		//PRINT_R($this->suffixes);
		
	}
	
	//"gifu ken, gifu shi" --> "gifu-ken, gifu-shi" etc
	//NEED KEN CHECK ("to" etc)?
	//NEED TO SMASH "chou" to "cho"???????
	//This function can smash "one, two three, four five" to "one, two, three, four, five"(when no suffix tokens are found) - is that alright?
	function mergeSuffixTokensIn($query)
	{
		//SPlit on comma
		//loop and merge suffix tokens into root token
		//implode with comma and return
		
		$newParts = array();
		//$parts = explode(' ', $query);
		$parts = explode(',', $query);
		//Kill empty parts (or can do via regex?)
		
		
		
		for($loop = 0; $loop < count($parts); $loop++)
		{
			$spacedParts = explode(' ', trim($parts[$loop]));
			//echo "Spaced aprts:";
			//PRINT_R($spacedParts);
			
			for($inner_loop = 0; $inner_loop < count($spacedParts); $inner_loop++)
			{
				//ONLY APPEND IF NOT ALREADY AT THE END OF LEFTSIDE???
				//Assume first token isn't a suffix token
				if($inner_loop < (count($spacedParts) - 1) and in_array($spacedParts[$inner_loop + 1], $this->suffixes['all']))
				{
					$suffix = '-' . $spacedParts[$inner_loop + 1];
					
					//Correct prefecture suffix if "prefecture suffix"
					if(array_key_exists($spacedParts[$inner_loop], $this->prefectureSuffixExceptions))
					{
						$suffix = $this->prefectureSuffixExceptions[$spacedParts[$inner_loop]];
						
						//
						//$this->japaneseAddress->addLevel('ken');
						//$this->japaneseAddress->setLevelEn('ken', "{$spacedParts[$inner_loop]}{$suffix}");
					}
					
					//Change "city" to "shi"
					if($spacedParts[$inner_loop + 1] == 'city')
					{
						$suffix = "-shi";
						
						//
						//$this->japaneseAddress->addLevel('shi');
						//$this->japaneseAddress->setLevelEn('shi', "{$spacedParts[$inner_loop]}{$suffix}");
					}
					
					$newParts[] = "{$spacedParts[$inner_loop]}{$suffix}";
					$inner_loop++;	//Disregard this suffix token as being the root token of a next suffix!
				}
				
				else
				{
					$newParts[] = $spacedParts[$inner_loop];
				}
			}
			
			
		}
		
		return implode(',', $newParts);
	}
	
	//"osaka ken" --> "osaka-fu"
	//"kaizu city" --> "kaizu-shi";
	function getNormalizedSuffix($root, $currentSuffix)
	{
		//Prefecture
		
		
		//City
		
		
	}
	
	//
	function getUnlabelledParts($query)
	{
		$queryWithMergedSuffixTokens = $this->mergeSuffixTokensIn($query);
		
		$unlabelledParts = explode(',', $queryWithMergedSuffixTokens);
		$unlabelledParts = array_map('trim', $unlabelledParts);
		
		//Not separated with commas
		if(count($unlabelledParts) == 1)
		{
			$unlabelledParts = explode(' ', $unlabelledParts[0]);
			$unlabelledParts = array_map('trim', $unlabelledParts);
		}
		
		
		
		$unlabelledParts = array_map(array($this, 'standardizeRomajinization'), $unlabelledParts);
		
		return $unlabelledParts;
	}
	
	
	
	//
	function labelParts_pass_1(array $parts)
	{
		$unusedParts = array();
		
		foreach($parts as $part)	//Don't really need 'i' modifiers on these regexes
		{
			if(preg_match('|-to$|i', $part) or preg_match('|-fu$|i', $part) or preg_match('|-ken$|i', $part) or preg_match('|-pref$|i', $part) or preg_match('|-prefecture$|i', $part))
			{
				$this->labelledParts['a_todoufuken'] = $part;
				
				$this->japaneseAddress->addLevel('ken');
				$this->japaneseAddress->setLevelEn('ken', $part);
			}
			
			elseif(preg_match('|-shi$|i', $part) or preg_match('|-city$|i', $part))
			{
				$this->labelledParts['b_shi'] = $part;
				
				$this->japaneseAddress->addLevel('shi');
				$this->japaneseAddress->setLevelEn('shi', $part);
			}
			
			elseif(preg_match('|-gun$|i', $part))
			{
				$this->labelledParts['c_gun'] = $part;
				
				$this->japaneseAddress->addLevel('gun');
				$this->japaneseAddress->setLevelEn('gun', $part);
			}
			
			//"TO" addresses may have "KU" also
			
			
			//"KU" addresses may have "TO" also
			elseif(preg_match('|-ku$|i', $part) or preg_match('|-ward$|i', $part))
			{
				$this->labelledParts['d_ku'] = $part;
				
				$this->japaneseAddress->addLevel('ku');
				$this->japaneseAddress->setLevelEn('ku', $part);
			}		
			
			/*
			elseif(preg_match('|-machi$|i', $part) or preg_match('|-cho$|i', $part) or preg_match('|-chou$|i', $part) or preg_match('|-town$|i', $part))
			{
				$this->labelledParts['e_choumachi'] = $part;
			}
			
			elseif(preg_match('|-son$|i', $part) or preg_match('|-mura$|i', $part) or preg_match('|-village$|i', $part))
			{
				$this->labelledParts['f_sonmura'] = $part;
			}
			*/
			
			/*
			else	//Assume town level token
			{
				$labelledParts['g_town'] = $part;
			}
			*/
			
			else
			{
				
				
				//Token is a prefecture
				if(array_key_exists($part, $this->prefectures))
				{
					if(array_key_exists($part, $this->prefectureSuffixExceptions))
					{
						$this->labelledParts['a_todoufuken'] = $part . $this->prefectureSuffixExceptions[$part];
						
						$this->japaneseAddress->addLevel('ken');
						$this->japaneseAddress->setLevelEn('ken', $part . $this->prefectureSuffixExceptions[$part]);
					}
					
					else
					{
						$this->labelledParts['a_todoufuken'] = "{$part}-ken";
						
						$this->japaneseAddress->addLevel('ken');
						$this->japaneseAddress->setLevelEn('ken', "{$part}-ken");
					}
					
					//Pre-save
					$this->labelledPartsJa['a_todoufuken'] = $this->kanjiPrefectures[$part];
					
					$this->japaneseAddress->setLevelJa('ken', $this->kanjiPrefectures[$part]);
				}
				
				//Token is not a prefecture, check if it's a city
				else
				{
					
					
					//$checkCitySql = "SELECT DISTINCT city_roma, city_kanji FROM postcode WHERE city_roma LIKE '{$part}-shi'";
					$checkCitySql = "SELECT DISTINCT shi_en, shi_ja FROM postcode WHERE shi_en LIKE '{$part}-shi'";
					ECHO $checkCitySql;
					$row = MySQL::queryRow($checkCitySql);
					
					if($row)
					{
						$this->labelledParts['b_shi'] = "{$part}-shi";
						
						//Pre-save
						$this->labelledPartsJa['b_shi'] = $row['shi_ja'];
						
						$this->japaneseAddress->addLevel('shi');
						$this->japaneseAddress->setLevelEn('shi', "{$part}-shi");
						$this->japaneseAddress->setLevelJa('shi', $row['shi_ja']);
					}
					
					else	//Check token is a gun or not
					{
						
						$checkGunSql = "SELECT DISTINCT gun_ja FROM postcode WHERE gun_en LIKE '{$part}-gun%'";
						
						$row = MySQL::queryRow($checkGunSql);
						
						if($row)
						{
							$this->labelledParts['c_gun'] = "{$part}-gun";
								
							//Pre-save
							$this->labelledPartsJa['c_gun'] = $row['gun_ja'];
							
							$this->japaneseAddress->addLevel('gun');
							$this->japaneseAddress->setLevelEn('gun', "{$part}-gun");
							$this->japaneseAddress->setLevelJa('gun', $row['gun_ja']);
						}
						
						/*
						else	//Check token is a town or not
						{
						
						
							//SHould we do this as a second pass when we are more likely to have pref and city information for whittling down?
							
							$checkTownSql = "SELECT DISTINCT town_en, SUBSTRING(town_en, LOCATE('-', town_en)) AS suffix, town_kanji FROM postcode WHERE town_en LIKE '{$part}-machi' OR town_en LIKE '{$part}-cho' OR town_en LIKE '{$part}'";
							//ECHO $checkTownSql;
							$row = $database->queryRow($checkTownSql);
						
							if($row)
							{
								$this->labelledParts['e_choumachi'] = "{$part}{$row['suffix']}";
								
								//Pre-save
								$this->labelledPartsJa['e_choumachi'] = $row['town_kanji'];
							}
						
							else	//Check token is a village or not
							{
								//SHould we do this as a second pass when we are more likely to have pref and city information for whittling down?
								
								$checkVillageSql = "SELECT DISTINCT town_kanji, town_en FROM postcode WHERE town_en LIKE '%{$part}-son%' OR town_en LIKE '%{$part}-mura%'";
								
								$row = $database->queryRow($checkVillageSql);
								
								if($row)
								{
									//May have machi information in town_en (eg. "Izuhara-machi, Naka-mura") so chop things up
									$townParts = explode(', ', $row['town_en']);
									
									$villageParts = $townParts[count($townParts) - 1];
									
									//$rootAndSuffix = explode('-', $villageParts);
									//$labelledParts['f_sonmura'] = "{$part}{$row['suffix']}";
									//$labelledParts['f_sonmura'] = "{$rootAndSuffix}{$row['suffix']}";
								
									
									$this->labelledParts['f_sonmura'] = $villageParts;
									
									//Pre-save
									$townKanji = preg_replace('|町.*|', '町', $row['town_kanji']);
									$this->labelledPartsJa['f_sonmura'] = $townKanji;
								}
								
								else
								{
									$this->labelledParts['g_sonota'] = $part;
								}
							}
						}
						*/
						
						else
						{
							$unusedParts[] = $part;
						}
					}
				}
			}
			
		}
	
		return $unusedParts;
	}
	
	//town and/or village (based on city and pref if present)
	function labelParts_pass_2(array $parts)
	{
		foreach($parts as $part)	//Don't really need 'i' modifiers on these regexes
		{
			if(preg_match('|-machi$|i', $part) or preg_match('|-cho$|i', $part) or preg_match('|-chou$|i', $part) or preg_match('|-town$|i', $part))
			{
				$this->labelledParts['e_choumachi'] = $part;
				
				$this->japaneseAddress->addLevel('cho');
				$this->japaneseAddress->setLevelEn('cho', $part);
							
			}
			
			elseif(preg_match('|-son$|i', $part) or preg_match('|-mura$|i', $part) or preg_match('|-village$|i', $part))
			{
				$this->labelledParts['f_sonmura'] = $part;
				
				$this->japaneseAddress->addLevel('son');
				$this->japaneseAddress->setLevelEn('son', $part);
							
			}
			
			else
			{
				
					
				//Check token is a town or not
						
				$andClause = '';
				if(array_key_exists('a_todoufuken', $this->labelledParts))
				{
					$andClause = "AND prefecture_iso_code = '{$this->prefectures[$this->labelledParts['a_todoufuken']]}'";
				}
				if(array_key_exists('b_shi', $this->labelledParts))
				{
					//$andClause .= " AND city_roma LIKE '{$this->labelledParts['b_shi']}'";
					$andClause .= " AND shi_en LIKE '{$this->labelledParts['b_shi']}'";
				}
				
				//Addresses with "gun" are, in our database, having the "cho" at CITY level (ie. the city_en field of the database)
				//$fieldWithCho = 'town_roma';
				$fieldWithCho = 'cho_en';
				//$fieldWithChoKanji = 'town_kanji';
				$fieldWithChoKanji = 'cho_ja';
				if(array_key_exists('c_gun', $this->labelledParts))
				{
					//$fieldWithCho = 'big_town_roma';
					$fieldWithCho = 'gun_cho_en';
					//$fieldWithChoKanji = 'big_town_kanji';
					$fieldWithChoKanji = 'gun_cho_ja';
				}
				
				$checkTownSql = "SELECT {$fieldWithCho}, SUBSTRING({$fieldWithCho}, LOCATE('-', {$fieldWithCho})) AS suffix, {$fieldWithChoKanji} FROM postcode WHERE ({$fieldWithCho} LIKE '{$part}-machi' OR {$fieldWithCho} LIKE '{$part}-cho' OR {$fieldWithCho} LIKE '{$part}') {$andClause}";
				ECHO $checkTownSql;
				$row = MySQL::queryRow($checkTownSql);
				
				if($row)
				{
					$this->labelledParts['e_choumachi'] = "{$part}{$row['suffix']}";
					
					//Pre-save
					$this->labelledPartsJa['e_choumachi'] = $row[$fieldWithChoKanji];
					
					$this->japaneseAddress->addLevel('cho');
					$this->japaneseAddress->setLevelEn('cho', "{$part}{$row['suffix']}");
					$this->japaneseAddress->setLevelJa('cho', $row[$fieldWithChoKanji]);
				}
				
				else	//Check token is a village or not
				{
						
						
					//$checkVillageSql = "SELECT village_kanji, village_roma FROM postcode WHERE (village_roma LIKE '%{$part}-son%' OR village_roma LIKE '%{$part}-mura%') {$andClause}";
					$checkVillageSql = "SELECT son_ja, son_en FROM postcode WHERE (son_en LIKE '%{$part}-son%' OR son_en LIKE '%{$part}-mura%') {$andClause}";
					
					$row = MySQL::queryRow($checkVillageSql);
					
					if($row)
					{
						//May have machi information in town_en (eg. "Izuhara-machi, Naka-mura") so chop things up
						//$townParts = explode(', ', $row['town_en']);
					
						//$villageParts = $townParts[count($townParts) - 1];
					
						//$rootAndSuffix = explode('-', $villageParts);
						//$labelledParts['f_sonmura'] = "{$part}{$row['suffix']}";
						//$labelledParts['f_sonmura'] = "{$rootAndSuffix}{$row['suffix']}";
					
						
						$this->labelledParts['f_sonmura'] = $row['son_en'];
									
							//Pre-save
						
						$this->labelledPartsJa['f_sonmura'] = $row['son_ja'];
						
						
						$this->japaneseAddress->addLevel('son');
					$this->japaneseAddress->setLevelEn('son', $row['son_en']);
					$this->japaneseAddress->setLevelJa('son', $row['son_ja']);
					}
							
					else
					{
						$this->labelledParts['g_sonota'] = $part;
						//$this->labelledParts[] = $part;
					}
				}
			}
			
		}
	
		//return $labelledParts;
	}
	
	//
	function labelledPartsToEnAddress(array $labelledParts)
	{
		krsort($labelledParts);	//[k]ey [r]everse sort
		
		return implode(', ', $labelledParts);	//Join on comma-space
	}
	
	//$labelledParts are in English
	function labelledPartsToJaAddress(array $labelledParts)
	{
		
		
		
		
		if(array_key_exists('a_todoufuken', $labelledParts))
		{
			//Check pre-saved stuff
			if(!array_key_exists('a_todoufuken', $this->labelledPartsJa))
			{
				$comps = explode('-', $labelledParts['a_todoufuken']);
				$pref_name_en = $comps[0];
				$getPrefKanjiSql = "SELECT name_ja FROM prefecture WHERE name_en LIKE '{$pref_name_en}'";
				$this->labelledPartsJa['a_todoufuken'] = MySQL::queryOne($getPrefKanjiSql);
			}
		}
		
		if(array_key_exists('b_shi', $labelledParts))
		{
			//Check pre-saved stuff
			if(!array_key_exists('b_shi', $this->labelledPartsJa))
			{
				$prefixlessShi = explode('-', $labelledParts['b_shi']);
				$prefixlessShi = $prefixlessShi[0];
				
				//$getCityKanjiSql = "SELECT DISTINCT city_kanji FROM postcode WHERE city_roma LIKE '{$prefixlessShi}-shi'";
				$getCityKanjiSql = "SELECT DISTINCT shi_ja FROM postcode WHERE shi_en LIKE '{$prefixlessShi}-shi'";
				$this->labelledPartsJa['b_shi'] = MySQL::queryOne($getCityKanjiSql);
			}
		}
		
		if(array_key_exists('c_gun', $labelledParts))
		{
			//Check pre-saved stuff
			if(!array_key_exists('c_gun', $this->labelledPartsJa))
			{
				$getGunKanjiSql = "SELECT DISTINCT gun_ja FROM postcode WHERE gun_en LIKE '{$labelledParts['c_gun']}%'";
				$this->labelledPartsJa['c_gun'] = MySQL::queryOne($getGunKanjiSql);
			}
		}
		
		if(array_key_exists('d_ku', $labelledParts))
		{
			//Check pre-saved stuff
			if(!array_key_exists('d_ku', $this->labelledPartsJa))
			{
				$getKuKanjiSql = "SELECT DISTINCT ku_ja FROM postcode WHERE ku_en LIKE '%{$labelledParts['d_ku']}'";
				$this->labelledPartsJa['d_ku'] = MySQL::queryOne($getKuKanjiSql);
			}
		}
		
		if(array_key_exists('e_choumachi', $labelledParts))
		{
			//Check pre-saved stuff
			if(!array_key_exists('e_choumachi', $this->labelledPartsJa))
			{
				$andClause = '';
				if(array_key_exists('a_todoufuken', $labelledParts))
				{
					$andClause = "AND prefecture_iso_code = '{$this->prefectures[$labelledParts['a_todoufuken']]}'";
				}
				if(array_key_exists('b_shi', $labelledParts))
				{
					$andClause .= " AND shi_en LIKE '{$labelledParts['b_shi']}'";
				}
				
				
				$getTownKanjiSql = "SELECT cho_ja FROM postcode WHERE cho_en LIKE '{$labelledParts['e_choumachi']}' {$andClause}";
				ECHO $getTownKanjiSql;
				$this->labelledPartsJa['e_choumachi'] = MySQL::queryOne($getTownKanjiSql);
			}
		}
		
		if(array_key_exists('f_sonmura', $labelledParts))
		{
			//Check pre-saved stuff
			if(!array_key_exists('f_sonmura', $this->labelledPartsJa))
			{
				//prolly need city locking...
				$getVillageKanjiSql = "SELECT DISTINCT son_ja FROM postcode WHERE son_en LIKE '{$labelledParts['f_sonmura']}'";
				$this->labelledPartsJa['f_sonmura'] = MySQL::queryOne($getVillageKanjiSql);
			}
		}
		
		if(array_key_exists('g_sonota', $labelledParts))
		{
			$this->labelledPartsJa['g_sonota'] = $labelledParts['g_sonota'];
		}
		
		ksort($this->labelledPartsJa);	//[k]ey sort
		
		return implode('', $this->labelledPartsJa);	//Join on empty character
	}











	//Kill anything that resembles the god-awful Kunrei-shiki (change to Hepburn)
	//[our databases are Hepburn spelling]
	function standardizeRomajinization($romajiString)
	{
		$standardizedString = str_replace('tu', 'tsu', $romajiString);
		
		//The above will smash "prefecture" into "prefectsure". Fix this!
		$standardizedString = str_replace('prefectsure', 'prefecture', $standardizedString);
		
		$standardizedString = str_replace('ti', 'chi', $standardizedString);
		
		$standardizedString = str_replace('si', 'shi', $standardizedString);
		
		//"du" --> "zu"?
		
		//"di" --> "ji"?
		
		//"hu" --> "fu" EXCEPT the "hu" in "chu" or "shu" (this regex won't catch an initial "hu")
		$standardizedString = preg_replace('|([^cs])hu|i', '$1fu', $standardizedString);
		
		//Initial "hu"
		$standardizedString = preg_replace('|^hu|i', 'fu', $standardizedString);
		
		return $standardizedString;
	}
}