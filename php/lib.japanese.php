<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Common functions for Japan and Japanese language handling
//(and, to a lesser extent, generic MB characters)

//Clean up a postcode (to remove hyphens and make hankaku)
function cleanJapanesePostcode($dirtyJapanesePostcode)
{
	//Make hankaku (only *really* applicable for zenkaku Japanese)
	$cleanJapanesePostcode = mb_convert_kana($dirtyJapanesePostcode, 'n');
		//'n' = Convert "zen-kaku" numbers to "han-kaku"
		
	//Remove all non-digits
	$cleanJapanesePostcode = preg_replace('|[^0-9]|', '', $cleanJapanesePostcode);
	
	return $cleanJapanesePostcode;
}

//Convert hiragana (if present) into katakana
function hiraToKata($japanese)
{
	//'C' = Convert "zen-kaku hira-gana" to "zen-kaku kata-kana"
	return mb_convert_kana($japanese, 'C');
}

//Convert katakana (if present) into hiragana
function kataToHira($japanese)
{
	//'C' = Convert "zen-kaku kata-kana" to "zen-kaku hira-gana"
	return mb_convert_kana($japanese, 'c');
}

//The elusive mb_trim() !!!
//Same as trim() but includes zenkaku space character
//Hmmm, actually this doesn't seem to work when there are OTHER zenkaku characters in the string
//(ie. only english string with zenkaku spaces work. Hmm....)
function mb_trim($string)
{
	return trim($string, " 　\t\n\r\0\x0B");	//not working? (seems to garble「テ」in「テキーラ」)
		//Some kind of problem with the mbstring settings?
		
	//use regexes?
	//or how about mb_convert_kana() with 's'
	//(Convert "zen-kaku" space to "han-kaku" (U+3000 -> U+0020))
}

//Returns true if passed string contains multibyte characters
//(is_mb() is perhaps a better name for this function!
//in fact, PHP already *seems* to have an "is_unicode" function!)
function is_mb($string)
{
	return(strlen($string) != mb_strlen($string));
}

//Convert zenkaku punctuation to hankaku
function flattenZenkakuPunctuation($zenkakuString)
{
	$basicSearch = array
	(
		'、',
		'。',
		'！',
		'？',
		'”',
		
		'’',
		
		'＃',
		'＄',
		'％',
		'＆',
		'（',
		
		'）',
		'＝',
		'－',
		'＾',
		'～',
		
		'｜',
		'￥',
		'＠',
		'‘',
		'［',
		
		'｛',
		'］',
		'｝',
		'：',
		'＊',
		
		'；',
		'＋',
		'，',
		'＜',
		'．',
		
		'＞',
		'／',
		'＿',
		'・'
	);
	
	$basicReplace = array
	(
		', ',
		'. ',
		'! ',
		'? ',
		'" ',
		
		"' ",
		
		'#',
		'$',
		'%',
		'&',
		' (',
		
		') ',
		'=',
		'-',
		'^',
		'-',
		
		'|',
		"\\",
		'@',
		'`',
		' [',
		
		' {',
		'] ',
		'} ',
		':',
		'*',
		
		';',
		'+',
		', ',
		'<',
		'. ',
		
		'>',
		'/',
		'_',
		'/'
	);
	
	$flattenedString = str_replace($basicSearch, $basicReplace, $zenkakuString);
	
	return $flattenedString;
}

//Replace stock katakana English (eg. ビル, ガーデン) with the real English
////world, business, garden, centre, tower, building, place, heights, green, port, park, town
////office, orange, techno, emerald, new, valley, frontier, landmark, lucent
////maple, stage, sweden, hills, starlight, opera, city, sunshine, highland
////Huis ten Bosch
function anglicizeKatakanaEnglish($englishString, $kanjiString)
{
	//Reduce false positives by examining the corresponding kanji string (which may also contain katakana)
	if(!mb_strpos($kanjiString, 'ワールド') and
		!mb_strpos($kanjiString, 'ビジネス') and
		!mb_strpos($kanjiString, 'ガーデン') and
		!mb_strpos($kanjiString, 'センター') and
		!mb_strpos($kanjiString, 'タワー') and
		!mb_strpos($kanjiString, 'ビル') and
		!mb_strpos($kanjiString, 'プレイス') and
		!mb_strpos($kanjiString, 'ハイツ') and
		!mb_strpos($kanjiString, 'グリーン') and
		!mb_strpos($kanjiString, 'ポート') and
		!mb_strpos($kanjiString, 'パーク') and
		!mb_strpos($kanjiString, 'タウン') and
		!mb_strpos($kanjiString, 'オフィス') and
		!mb_strpos($kanjiString, 'オレンジ') and
		!mb_strpos($kanjiString, 'テクノ') and
		!mb_strpos($kanjiString, 'エメラルド') and
		!mb_strpos($kanjiString, 'ニュー') and
		!mb_strpos($kanjiString, 'バレー') and
		!mb_strpos($kanjiString, 'フロンティア') and
		!mb_strpos($kanjiString, 'ランドマーク') and
		!mb_strpos($kanjiString, 'ルーセント') and
		!mb_strpos($kanjiString, 'メイプル') and
		!mb_strpos($kanjiString, 'ステージ') and
		!mb_strpos($kanjiString, 'スウェーデン') and
		!mb_strpos($kanjiString, 'ヒルズ') and
		!mb_strpos($kanjiString, 'スターライト') and
		!mb_strpos($kanjiString, 'オペラ') and
		!mb_strpos($kanjiString, 'シティ') and
		!mb_strpos($kanjiString, 'サンシャイン') and
		!mb_strpos($kanjiString, 'ハイランド') and
		!mb_strpos($kanjiString, 'シーサイド') and
		!mb_strpos($kanjiString, 'ハウステンボス')
		)
	{
		return $englishString;
	}
	
	$basicSearch = array
	(
		'waarudo',
		'bijinesu',
		'gaaden',
		'sentaa',
		'tawaa',
		'biru',
		'pureisu',
		'haitsu',
		'guriin',
		'pooto',
		'paaku',
		'taun',
		'ofisu',
		'orenji',
		'tekuno',
		'emerarudo',
		'nyuu',
		'baree',
		'furontia',
		'randomaaku',
		'ruusento',
		'meipuru',
		'suteeji',
		'suweeden',
		'hiruzu',
		'sutaaraito',
		'opera',
		'shiti',
		'sanshain',
		'hairando',
		'shiisaido',
		'hausutenbosu'
	);
	
	$basicReplace = array
	(
		' World ',
		' Business ',
		' Garden ',
		' Centre ',
		' Tower ',
		' Building ',
		' Place ',
		' Heights ',
		' Green ',
		' Port ',
		' Park ',
		' Town ',
		' Office ',
		' Orange ',
		' Techno ',
		' Emerald ',
		' New ',
		' Valley ',
		' Frontier ',
		' Landmark ',
		' Lucent ',
		' Maple ',
		' Stage ',
		' Sweden ',
		' Hills ',
		' Starlight ',
		' Opera ',
		' City ',
		' Sunshine ',
		' Highland ',
		' Seaside ',
		' Huis ten Bosch '
	);
	
	$anglicizedString = str_replace($basicSearch, $basicReplace, $englishString);
	
	return $anglicizedString;
}

//Convert zenkaku katakana to romaji
function katakanaToRomaji($katakanaString)
{
	$multiSearch = array
	(
		'チャ',
		'チュ',
		'チェ',
		'チョ',
		
		'シャ',
		'シュ',
		'シェ',
		'ショ',
		
		'ジャ',
		'ジュ',
		'ジェ',
		'ジョ',
		
		'キャ',
		'キュ',
		'キョ',
		
		'ギャ',
		'ギュ',
		'ギョ',
		
		'リュ',
		'リョ',
		
		'ミャ',
		'ミュ',
		'ミョ',
		
		'ヒャ',
		'ヒュ',
		'ヒョ',
		
		'ニャ',
		'ニュ',
		'ニョ',
		
		'ビャ',
		'ビュ',
		'ビョ',
		
		'ピャ',
		'ピュ',
		'ピョ',
		
		'ヂャ',
		'ヂュ',
		'ヂョ',
		
		'ファ',
		'フィ',
		'フェ',
		'フォ',
		
		'ウィ',
		'ウェ',
		'ウォ',
		
		'ヴァ',
		'ヴィ',
		'ヴェ',
		'ヴォ',
		
		'ティ',
		'ディ'
	);
	
	$multiReplace = array
	(
		'cha',
		'chu',
		'che',
		'cho',
		
		'sha',
		'shu',
		'she',
		'sho',
		
		'ja',
		'ju',
		'je',
		'jo',
		
		'kya',
		'kyu',
		'kyo',
		
		'gya',
		'gyu',
		'gyo',
		
		'ryu',
		'ryo',
		
		'mya',
		'myu',
		'myo',
		
		'hya',
		'hyu',
		'hyo',
		
		'nya',
		'nyu',
		'nyo',
		
		'bya',
		'byu',
		'byo',
		
		'pya',
		'pyu',
		'pyo',
		
		'dya',
		'dyu',
		'dyo',
		
		'fa',
		'fi',
		'fe',
		'fo',
		
		'wi',
		'we',
		'wo',
		
		'va',
		'vi',
		've',
		'vo',
		
		'ti',
		'di'
	);
	
	$basicSearch = array
	(
		'ア',
		'イ',
		'ウ',
		'エ',
		'オ',
		
		'ヴ',
		
		'カ',
		'キ',
		'ク',
		'ケ',
		'コ',
		
		'ガ',
		'ギ',
		'グ',
		'ゲ',
		'ゴ',
		
		'タ',
		'チ',
		'ツ',
		'テ',
		'ト',
		
		'ダ',
		'ヂ',
		'ヅ',
		'デ',
		'ド',
		
		'ハ',
		'ヒ',
		'フ',
		'ヘ',
		'ホ',
		
		'バ',
		'ビ',
		'ブ',
		'ベ',
		'ボ',
		
		'パ',
		'ピ',
		'プ',
		'ペ',
		'ポ',
		
		'マ',
		'ミ',
		'ム',
		'メ',
		'モ',
		
		'ナ',
		'ニ',
		'ヌ',
		'ネ',
		'ノ',
		
		'サ',
		'シ',
		'ス',
		'セ',
		'ソ',
		
		'ザ',
		'ジ',
		'ズ',
		'ゼ',
		'ゾ',
		
		'ラ',
		'リ',
		'ル',
		'レ',
		'ロ',
		
		'ヤ',
		'ユ',
		'ヨ',
		
		'ン',
		'ワ',
		'ヲ'
	);
	
	$basicReplace = array
	(
		'a',
		'i',
		'u',
		'e',
		'o',
		
		'vu',
		
		'ka',
		'ki',
		'ku',
		'ke',
		'ko',
		
		'ga',
		'gi',
		'gu',
		'ge',
		'go',
		
		'ta',
		'chi',
		'tsu',
		'te',
		'to',
		
		'da',
		'di',
		'du',
		'de',
		'do',
		
		'ha',
		'hi',
		'fu',
		'he',
		'ho',
		
		'ba',
		'bi',
		'bu',
		'be',
		'bo',
		
		'pa',
		'pi',
		'pu',
		'pe',
		'po',
		
		'ma',
		'mi',
		'mu',
		'me',
		'mo',
		
		'na',
		'ni',
		'nu',
		'ne',
		'no',
		
		'sa',
		'shi',
		'su',
		'se',
		'so',
		
		'za',
		'ji',
		'zu',
		'ze',
		'zo',
		
		'ra',
		'ri',
		'ru',
		're',
		'ro',
		
		'ya',
		'yu',
		'yo',
		
		'n',
		'wa',
		'wo'
	);
	
	$romajiString = str_replace($multiSearch, $multiReplace, $katakanaString);
	
	$romajiString = str_replace($basicSearch, $basicReplace, $romajiString);
	
	$romajiString = preg_replace('|ッchi|', 'tchi', $romajiString);
	
	$romajiString = preg_replace('|ッ([a-z]{1})|', '${1}${1}', $romajiString);
	
	//Smash hyphens ('-') used as 'ー'
	$romajiString = preg_replace('|([^0-9])[-]([^0-9])|', '${1}ー${2}', $romajiString);
	
	$romajiString = preg_replace('|([a-z]{1})ー|', '${1}${1}', $romajiString);
	
	return $romajiString;
}

//Insert hyphen at correct place in specified (hankaku) postcode string
function formatPostcode($postcode, $prefixPostcodeMark = false)
{
	if(strlen($postcode) != 7)
	{
		return $postcode;
	}
	
	$temp = substr($postcode, 0, 3) . '-' . substr($postcode, 3, 4);
	
	if($prefixPostcodeMark)
	{
		$temp = '〒' . $temp;
	}
	
	return $temp;
}

//This is TOO SIMPLE! Think about multi-digit kanji numbers!
function kanjiNumberToArabicNumber($kanjiNumber)
{
	$search = array('一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
	
	$replace = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
	
	return str_replace($search, $replace, $kanjiNumber);
}

//Get Japanese translation of passed English string via Excite.co.jp
//RETURNS UNICODE (but Excite.co.jp works in SJIS)
//Naughty naughty!
function excite_getJaFromEn($en)
{
	$source = file_get_contents('http://excite.co.jp/world/english/?wb_lp=ENJA&before=' . urlencode($en));
	preg_match('|<textarea cols=36 rows=15 name="after" wrap="virtual" style="width:320px;height:250px;">(.*)</textarea>|Us', $source, $matches);
	return mb_convert_encoding(trim($matches[1]), 'UTF-8', 'SJIS');
}

//Get English translation of passed Japanese string via Excite.co.jp
//ACCEPTS UNICODE (but Excite.co.jp works in SJIS)
//Naughty naughty!
function excite_getEnFromJa($ja)
{
	$source = file_get_contents('http://excite.co.jp/world/english/?wb_lp=JAEN&before=' . urlencode(mb_convert_encoding($ja, 'SJIS', 'UTF-8')));
	preg_match('|<textarea cols=36 rows=15 name="after" wrap="virtual" style="width:320px;height:250px;">(.*)</textarea>|Us', $source, $matches);
	return trim($matches[1]);
}