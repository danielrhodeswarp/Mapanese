<?php include $_SERVER['DOCUMENT_ROOT'] . '/../conf/application.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;"/>
<meta http-equiv="Content-Script-Type" content="text/javascript; charset=UTF-8;"/>
<meta http-equiv="Content-Style-Type" content="text/css; charset=UTF-8;"/>
<meta http-equiv="Content-Language" content="en"/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta name="author" content="<?php echo META_AUTHOR; ?>"/>
<meta name="copyright" content="<?php echo META_COPYRIGHT; ?>"/>
<meta name="description" content="<?php echo META_DESCRIPTION; ?>"/>
<meta name="keywords" content="<?php echo META_KEYWORDS; ?>"/>
<meta name="robots" content="all"/>
<title>Mapanese supported search formats</title>
<link type="image/x-icon" href="/favicon.ico" rel="shortcut icon"/>
</head>
<body>
<h1>Mapanese supported search formats</h1>
<h2>Address written in English</h2>
<h3>Single-token address</h3>
<ul>
<li>Prefecturename</li>
<li>Prefecturename-ken</li>
<li>Prefecturename ken</li>
<li>Prefecturename-pref</li>
<li>Prefecturename pref</li>
<li>Prefecturename-prefecture</li>
<li>Prefecturename prefecture</li>
<li>Cityname</li>
<li>Cityname-shi</li>
<li>Cityname shi</li>
<li>Cityname-city</li>
<li>Cityname city</li>
<li>Wardname</li>
<li>Wardname-ku</li>
<li>Wardname ku</li>
<li>Gunname</li>
<li>Gunname-gun</li>
<li>Gunname gun</li>
<li>Townname</li>
<li>Townname-cho[u]</li>
<li>Townname cho[u]</li>
<li>Townname-machi</li>
<li>Townname machi</li>
<li>Villagename</li>
<li>Villagename-son</li>
<li>Villagename son</li>
<li>Villagename-mura</li>
<li>Villagename mura</li>
<li>OtherPlacename</li>
</ul>
<h3>Multi-token address</h3>
<ul>
<li>"takaido-nishi 3-5-24, suginami-ku, tokyo 168-8505"</li>
<li>"gifu tsukinoe-cho"</li>
</ul>
<h2>Address written in Japanese</h2>
<p>Japanese language equivalents of the above formats also supported.
Note that whitespace and commas aren't necessary for multi-token addresses written in Japanese - as this is how it is done natively.</p>
<h2>Postcode</h2>
<ul>
<li>500 *</li>
<li>5001234</li>
<li>500-1234</li>
<li>500 1234</li>
<li>〒500 *</li>
<li>〒5001234</li>
<li>〒500-1234</li>
<li>〒500 1234</li>
<li>５００ *</li>
<li>５００１２３４</li>
<li>５００ー１２３４</li>
<li>５００　１２３４</li>
<li>〒５００ *</li>
<li>〒５００１２３４</li>
<li>〒５００ー１２３４</li>
<li>〒５００　１２３４</li>
</ul>
<p>* Will match the <em>main</em> part of a postcode with its approximate area</p>
<p>
<a href="/mapanese_info.html">Back to previous info page</a>
<a href="/" style="margin-left:2em;">Back to Mapanese</a>
</p>
<p style="font-size:smaller;"><?php echo META_COPYRIGHT; ?></p>
</body>
</html>