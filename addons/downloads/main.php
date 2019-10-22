<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Downloads main module main.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $set, $myserver;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');

if(isset($_GET['dlid'])) 
	$message=senddownload();

// shows links or downloads
function showdownloads($cat="") {
	//read link categories
	$aaa=trim(@file_get_contents("data/downloadcat.dat"));
	unset($downloadcat);
	unset($downloadscat);
	$downloadcat=explode("||",$aaa);
	$count1=0;
	$count=0;
	while($downloadcat[$count] != "") {
		$aaa=explode("|",$downloadcat[$count]);
		if($cat==$aaa[0] || $cat==0) {
			$downloadscat[$count1]=$aaa;
			$count1++;
		}
		$count++;
	}
	//read links id,nome,link, descricao,cat
	$aaa=trim(@file_get_contents("data/downloads.dat"));
	unset($download);
	unset($downloads);
	$download=explode("||",$aaa);
	$count=0;
	$sel=0;
	while($download[$count] != "") {
		if($cat) {
			$aaa=explode("|",$download[$count]);
			if($aaa[4]==$cat) {
				$downloads[$sel]=$aaa;
				$sel++;
			}
		} else
			$downloads[$count]=explode("|",$download[$count]);
		$count++;
	}
	$count=0;
	$GETarray=$_GET;
	$out="<div id=\"LNE_show\">\n";
	while($downloadscat[$count][0]!="" && $downloadscat[$count][1]!="Uploads") {
		$out.="<h3>".decode($downloadscat[$count][2])."</h3>\n";
		$count1=0;
		$first=1;
		$GETarray=$_GET;
		while($downloads[$count1][0]!="") {
			if($downloads[$count1][4]==$downloadscat[$count][0]) {
				if($first) {
					$out.="<ul>\n";
					$first=0;
				}
				$GETarray['dlid'] = $downloads[$count1][0];
				$out.="<li><a href=\"addons/downloads/send.php?".http_build_query($GETarray,'','&amp;')."\" rel=\"nofollow\">".decode($downloads[$count1][1])."</a><div>".decode($downloads[$count1][3])."</div></li>\n";
			}
			$count1++;
		}
		if(!$first) $out.="</ul>\n";
		$count++;
	}
	$out.="</div>\n";
	return $out;
}

?>
