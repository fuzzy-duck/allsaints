<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2010 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon LastNews run module main.php
| Version 2.4 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $newsmessage, $set;
if(file_exists("addons/news/lang/lang_".$set['language'].".php"))
	require_once "addons/news/lang/lang_".$set['language'].".php";
else
	require_once "addons/news/lang/lang_en_US.php";

require_once "addons/news/main1.php";

function lastnews($cat=-1) {
	$aa=explode("||",trim(@file_get_contents("./data/news.dat")));
	$gotit=false;
	$count=0;
	while($aa[$count]!="") {
		$zzz=explode("|",trim($aa[$count]));
		$count++;
		if($cat==-1 || $cat==$zzz[7]) {
			$row_db=$zzz;
			$gotit=true;
		}
	}
	if($gotit) return show_one_news($row_db[3],$row_db[5],$row_db[4],$row_db[1],$row_db[2]);
	else return false;
}

?>
