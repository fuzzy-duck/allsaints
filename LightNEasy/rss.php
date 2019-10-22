<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://lightneasy.org
+----------------------------------------------------+
| RSS feed creator rss.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
// $pathtonews must point to the page that displays your news, relative to server www root
// replace the page name if needed, default is "news" on the root www directory
require_once("../data/config.php");
// for a generated page news.php - faster
$pathtonews="/".$set['newspage'].".php?";
// for the news page inside LightNEasy - if you can't generate pages:
//$pathtonews="/LightNEasy.php?page=".$set['newspage']."&amp;";
require_once "common.php";
$set['title']=decode($set['title']);
$set['description']=decode($set['description']);
header('Content-type: application/rss+xml; charset=utf-8');
$out.="<?xml version=\"1.0\" ?>\n";
$out.="<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/RSS\">\n";
$out.="<channel>\n";
$out.="<atom:link href=\"http://".sv(SERVER_NAME).sv(PHP_SELF)."\" rel=\"self\" type=\"application/rss+xml\" />\n";
$out.="<title>".$set['title']."</title>\n";
$out.="<description>".$set['description']."</description>\n";
$out.="<link>http://".sv(SERVER_NAME).sv(PHP_SELF)."</link>\n";
$aa=explode("||",trim(file_get_contents("../data/news.dat")));
//read all news or only news=$categ, if set
foreach($aa as $caca) {
	$aaa=explode("|",trim($caca));
	if($aaa[3] !="") {
		$out.="<item>\n<title>".sanitize(stripslashes(decode($aaa[3])))."</title>\n";
		$descr=str_replace("&","&amp;",substr(strip_tags(stripslashes(decode($aaa[4]))),0,120));
		$out.="<description>".$descr."...\n</description>\n";
		$out.="<link>"."http://".sv(SERVER_NAME).$pathtonews."id=".$aaa[0]."</link>\n";
		$out.="<guid>"."http://".sv(SERVER_NAME).$pathtonews."id=".$aaa[0]."</guid>\n";
		$out.="</item>\n";
	}
}
$out.="</channel>\n</rss>\n";
print $out;
?>
