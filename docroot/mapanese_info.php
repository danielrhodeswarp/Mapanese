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
<style type="text/css">
table.history_table
{
	border-collapse:collapse;
	border:1px solid black;
}
table.history_table thead, tbody, tfoot, tr, th, td
{
	border:1px solid black;
}
table.history_table thead tr th
{
	color:white;
	background-color:#404080;
}
</style>
<link type="image/x-icon" href="/favicon.ico" rel="shortcut icon"/>
<title>All about Mapanese</title>
</head>
<body>
<h1>All about Mapanese</h1>
<p>Mapanese aims to be an <em>English language enabling</em> of the Google Maps map of Japan.
This means that places on the map are labelled in English and that searches can be performed in English.</p>
<h2>Licence</h2>
<p style="font-variant:small-caps;">This Mapanese software source code is released under the terms of the BSD "New BSD License".
	Please see the <em>LICENCE</em> text file in the root folder of the source code.</p>
<h2>Disclaimer</h2>
<p>Mapanese's labelling and/or searching may not always be 100% accurate.
This is to be expected.</p>
<p>Also, Mapanese is not connected to or related to Google and/or Google Maps and/or Zenrin.</p>
<h2>Romaji spelling</h2>
<p>Internally, Mapanese uses a Hepburn-like spelling system.
Within reason, other spelling systems (ie. Kunrei-shiki) are also accepted at the search box.</p>
<h2>TODO</h2>
<ul>
<li>Rewrite the whole damn thing to use a reverse index for less fussy address format support</li>
<li>Optimize for speed</li>
<li>Train stations (http://www.ekidata.jp/tools/latlon.php) and airports and what-have-you</li>
<li>Some kind of "exactly", "maybe" or "one of" categorisation for result(s)</li>
<li>(better?)handling of [***-gun, ***-cho] and [***-shi, ***-ku]</li>
<li>Return multiple results where relevant?</li>
<li>Finalise return balloons and prefecture spelling/suffix.</li>
<li>Leverage (if possible) the public transport route finding capability of <a href="http://maps.google.co.jp/maps?f=d&amp;hl=ja&amp;geocode=&amp;time=10:07&amp;date=08%2F01%2F08&amp;ttype=dep&amp;saddr=%E6%B8%8B%E8%B0%B7&amp;daddr=%E5%85%AD%E6%9C%AC%E6%9C%A8%E3%83%92%E3%83%AB%E3%82%BA&amp;noal=0&amp;noexp=0&amp;sort=time&amp;sll=35.658555,139.715574&amp;sspn=0.035078,0.058365&amp;ie=UTF8&amp;z=14&amp;om=1&amp;start=0">Google Maps Japan</a></li>
<li>"Link to this", "embed this", "print this" links (and maybe "mail this" link)</li>
<li>Somehow accept Google Maps Japan links</li>
</ul>
<h2>History</h2>
<table class="history_table" summary="Mapanese version history">
<thead>
<tr><th>Date</th><th>Version notes</th></tr>
</thead>
<tbody>
<tr><td>09/11/2011</td><td>First open sourced version in GitHub (v1.0gh) - same as live site at http://mapanese.info</td></tr>
<tr><td>04/08/2010</td><td>Migrated to Google Maps API v3 - which has mostly been a force for good (faster to load tiles and Mapanese codebase now smaller). I note, however, that some little "things" that used to happen out of the box no longer happen with API v3. jQuery removed as only being used for one trivial little thing.</td></tr>
<tr><td>03/11/2009</td><td>Made city and town level labels appear sooner. Put Japan more in centre on first load. Linked to a help/info channel on Twitter.</td></tr>
<tr><td>06/08/2009</td><td>Titivated and validated all HTML. Compacted all JavaScript. Replaced Prototype with jQuery (somewhat arbitrarily). Added empty string and whitespace only checks for search query.</td></tr>
<tr><td>14/06/2009</td><td>Noticed that a search for "osaka" was whizzing off to mainland China! Fixed (?) easily by using geocoderObject.setBaseCountryCode('jp') in the Google Maps code.</td></tr>
<tr><td>18/03/2009</td><td>Back up (as it was at 23/03/2008 minus the "Pacific Ocean" type labels (for performance) minus any ads) as version 1.0 at <strong>www.mapanese.info</strong></td></tr>
<tr><td>01/12/2008</td><td>Chose not to renew the mapane.se domain (too expensive/poor service). Mapanese still down...</td></tr>
<tr><td>07/08/2008</td><td>Chose not to renew the server that Mapanese was hosted on (too expensive/poor service). Mapanese down...</td></tr>
<tr><td>2nd half of 2008 (ish)?</td><td>It's worth noting that Google and/or Zenrin have added their <em>own</em> English labels to the Google Maps map of Japan which, whilst not going down quite as deep as the Mapanese labels, are an additional, interesting and probably more accurate reference. You may not get them depending on your browser's "preferred language" type settings.</td></tr>
<tr><td>23/03/2008</td><td>Hopefully fixed some tough little JavaScript errors that were popping up in Microsoft Internet Explorer</td></tr>
<tr><td>18/02/2008</td><td>Fixed some zooming/centering problems</td></tr>
<tr><td>16/02/2008</td><td>Beta up. Added rudimentary support for multi-token queries in Japanese or English. [Also added some "Pacific Ocean" type labels.]</td></tr>
<tr><td>27/12/2007</td><td>First alpha up</td></tr>
</tbody>
</table>
<h2>Supported search formats</h2>
<p>Mapanese attempts to parse any address search that you throw at it.
Please see the <a href="mapanese_formats.html">formats page</a> for the boring technical details,
but basically the following <em>types</em> of search are supported:</p>
<h3>Address written in English</h3>
<h4>Partial address</h4>
<ul>
<li>"Tokyo"</li>
<li>"Okinawa-ken"</li>
<li>"Akita ken"</li>
<li>"Gifu-pref"</li>
<li>"Nagano pref"</li>
<li>"Hiroshima-prefecture"</li>
<li>"Nagasaki prefecture"</li>
<li>"Nagoya"</li>
<li>"Nagoya-shi"</li>
<li>"Nagoya shi"</li>
<li>"Nagoya-city"</li>
<li>"Nagoya city"</li>
<li>"Townname"</li>
<li>"Townname-cho[u]"</li>
<li>"Townname cho[u]"</li>
<li>"Townname-machi"</li>
<li>"Townname machi"</li>
</ul>
<h4>Full address</h4>
<ul>
<li>"takaido-nishi 3-5-24, suginami-ku, tokyo 168-8505"</li>
<li>"gifu tsukinoe-cho"</li>
<li xml:lang="ja">"東京都杉並区高井戸西3-5-24" &dagger;</li>
<li xml:lang="ja">"杉並区, 高井戸西3-5-24" &dagger;</li>
<li></li>
</ul>
<h3>Postcode</h3>
<ul>
<li>500 *</li>
<li>5001234</li>
<li>500-1234</li>
<li>500 1234</li>
<li>〒500 *</li>
<li>〒5001234</li>
<li>〒500-1234</li>
<li>〒500 1234</li>
</ul>
<p>* Will match the <em>main</em> part of a postcode with its approximate area</p>
<p>&dagger; This kind of search (ie. Japanese) will give you the English version too!</p>
<p>Just try it! And if it doesn't work, try shuffling the search terms. If <em>that</em> doesn't work, drop us a line!</p>
<h2>This site's favicon</h2>
<p>This site's favicon - or "favourite icon" - which is the little picture of a map you can see at the left of your address bar or tab bar is the "map" icon from Famfamfam's rather excellent SILK set. Check it out at <a href="http://famfamfam.com">Famfamfam</a>.</p>
<h2>Contact Mapanese</h2>
<p>Drop the Mapanese team a line with this handy contact form:</p>
<form action="/mapanese_contact_email_forwarder.php" method="post">
<fieldset>
Your email address: <input type="text" name="email" /> (not stored or published)
<br/>
Contact reason: <select name="reason">
<option value="broken_format">Couldn't get a certain query format to work</option>
<option value="no_result">No result for an extant address</option>
<option value="incorrect_label">Label and map don't match</option>
<option value="bug_report">I've found another bug</option>
<option value="feature_request">I want to request a feature/functionality</option>
<option value="kudos">Kudos ;-)</option>
<option value="misc">Other reason</option>
</select>
<br/>
Message: <textarea name="message" rows="10" cols="40"></textarea>
<br/>
<input type="submit" value="Send message" style="float:right;" />
</fieldset>
</form>
<div>
<a href="/">Back to Mapanese</a>
<br/>
<span style="font-size:smaller;"><?php echo META_COPYRIGHT; ?></span>
</div>
</body>
</html>