<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2010 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Links run function main.php
| Version 2.4 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $set, $myserver;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');

// shows links or downloads
function showlinks($cat="") {
	//read link categories
	$aaa=trim(@file_get_contents("data/linkcat.dat"));
	unset($linkcat);
	unset($linkscat);
	$linkcat=explode("||",$aaa);
	$count1=0;
	$count=0;
	while($linkcat[$count] != "") {
		$aaa=explode("|",$linkcat[$count]);
		if($cat==$aaa[0] || $cat==0) {
			$linkscat[$count1]=$aaa;
			$count1++;
		}
		$count++;
	}
	//read links id,nome,link, descricao,cat
	$aaa=trim(@file_get_contents("data/links.dat"));
	unset($link);
	unset($links);
	$link=explode("||",$aaa);
	$count=0;
	$sel=0;
	while($link[$count] != "") {
		if($cat) {
			$aaa=explode("|",$link[$count]);
			if($aaa[4]==$cat) {
				$links[$sel]=$aaa;
				$sel++;
			}
		} else
			$links[$count]=explode("|",$link[$count]);
		$count++;
	}
	$count=0;
	$GETarray=$_GET;
	$out.="<div id=\"LNE_show\">\n";
	while($linkscat[$count][0]!="") {
		$out.="<h3>".decode($linkscat[$count][2])."</h3>\n";
		$count1=0;
		$first=1;
		while($links[$count1][0]!="") {
			if($links[$count1][4]==$linkscat[$count][0]) {
				if($first) {
					$out.="<ul>\n";
					$first=0;
				}
				$out.="<li><a href=\"".$links[$count1][2]."\" onclick=\"window.open(this.href,'_blank');return false;\">".decode($links[$count1][1])."</a><div>".decode($links[$count1][3])."</div></li>\n";
			}
			$count1++;
		}
		if(!$first) $out.="</ul>\n";
		$count++;
	}
	$out.="</div>\n";
	return $out;
}

