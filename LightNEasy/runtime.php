<?php
/*++++++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://Lightneasy.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| runtime.php runtime module
| Version Mini 2.5
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/
session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$message="";
$menu=array();

//Includes the common functions
require_once "./LightNEasy/common.php";

readsetup();

$selected=array('index','name');
readmenu();

function extra() {
	if(file_exists("./data/extra.htm")) printcontent($pagenum,"extra.htm");
}

function content() {
  global $pagenum,$edit,$set,$selected,$message,$menu,$langmessage,$LNEversion;
	if($message!="")
		print "<h2 class\"LNE_message\">$message</h2>\n";
	switch($_GET['do']) {
		case "search":
			print "<h2 class=\"LNE_title\">$langmessage[66]</h2>\n";
			print search(true);
			break;
		case "sitemap": {
			print "<h2 class=\"LNE_title\">$langmessage[88]</h2>\n<p><ul>\n";
			print fullmenu(1);
			print "</ul></p>\n";
			break;
		}
		case "login": {
			print "<div align=\"center\"><h2 class=\"LNE_title\">LightNEasy $LNEversion $langmessage[120]</h2><br />\n";
			print "<form method=\"post\" action=\"".$set['indexfile']."\"><fieldset>\n";
			print "<p>Enter your password:&nbsp;<input  type=\"password\" name=\"password\" value=\"\" />\n";
			print "<input type=\"hidden\" name=\"submit\" value=\"login\" />\n";
			print "<input type=\"submit\" name=\"aa\" value=\"$langmessage[120]\" /></p>\n</form></div>\n";
			break;
		}
		default: {
			printcontent($pagenum);
		}
	}
}

function printcontent($pagenum,$file="") {
	global $out, $addons;
	$open="%!$";
	$close="$!%";
	if($file=="")
		$page=decode(file_get_contents("./data/".$pagenum.".html"));
	else
		$page=decode(file_get_contents("./data/".$file));
	while(strpos($page,$open)) {
		$pagearray=explode($open,$page,2);
		unset($pagearray1);
		$pagearray1=explode($close,$pagearray[1],2);
		$out=$pagearray[0];
				if(substr($pagearray1[0],0,7)=="include") {
					include trim(substr($pagearray1[0],7));
				} elseif(substr($pagearray1[0],0,5)=='place')
					$out.= trim(file_get_contents(trim(substr($pagearray1[0],5))));
				elseif(substr($pagearray1[0],0,6)=="plugin") {
					$pluginame="./plugins/".trim(substr($pagearray1[0],6));
					if(file_exists($pluginame."/place.mod"))
						$out.= file_get_contents($pluginame."/place.mod");
					if(file_exists($pluginame."/include.mod")) {
						print $pagearray[0];
						$pagearray[0]="";
						include "$pluginame/include.mod";
					}
				} elseif(substr($pagearray1[0],0,8)=="function") {
					$bb=trim(substr($pagearray1[0],8));
					$aa=explode(" ",$bb);
					if($aa[3]!="") $aa[0]($aa[1],$aa[2],$aa[3]);
					elseif($aa[2]!="") $aa[0]($aa[1],$aa[2]);
					elseif($aa[1]!="") $aa[0]($aa[1]);
					else $aa[0]();
				} else {
					$found=false;
					foreach($addons as $addon) {
						if($pagearray1[0]==$addon[0]) {
							$found=true;
							require_once "addons/".$addon[0]."/main.php";
							$out.=$addon[1]();
							break;
						} elseif(substr($pagearray1[0],0,strlen($addon[0])) == $addon[0]) {
							$found=true;
							require_once "./addons/".$addon[0]."/main.php";
							$bb = clean(substr($pagearray1[0],strlen($addon[0])));
							$aa = explode(" ",clean($bb));
							if($aa[3] != "") $out.=$addon[1](clean($aa[0]),clean($aa[1]),clean($aa[2]),clean($aa[3]));
							elseif($aa[2]!="") $out.=$addon[1](clean($aa[0]),clean($aa[1]),clean($aa[2]));
							elseif($aa[1]!="") $out.=$addon[1](clean($aa[0]),clean($aa[1]));
							else $out.=$addon[1](clean($aa[0]));
							break;
						}
					}
					if(!$found)
						$out.="\n".html_entity_decode($pagearray1[0])."\n";
				}
		$page=$out.$pagearray1[1];
	}
	print $page;
}
?>
