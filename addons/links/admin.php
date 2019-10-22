<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2010 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Links admin module admin.php
| Version 2.4 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $linksmessage,$myserver,$set;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');

if(file_exists("addons/links/lang/lang_".$set['language'].".php"))
	require_once "addons/links/lang/lang_".$set['language'].".php";
else
	require_once "addons/links/lang/lang_en_US.php";

if($_POST['submit']=="Add Link" || $_POST['submit']=="Edit Link") {
			$links=readdata("links");
			if($_POST['link']!="" && $_POST['linkname']!="") {
				if($_POST['submit'] == "Add Link") {
					$count=0;
					$maxid=0;
					while($links[$count][0]!="") {
						if($links[$count][0]>$maxid) $maxid=$links[$count][0];
						$count++;
					}
					$maxid++;
					if(!$fp=fopen("data/links.dat","a")) die($linksmessage[5]);
					fwrite($fp,$maxid."|".encode($_POST['linkname'])."|".htmlentities($_POST['link'])."|".encode($_POST['descr'])."|".$_POST['cat']."||");
				} else { // edit link
					if(!$fp=fopen("data/links.dat","w")) die($linksmessage[5]);
					$count=0;
					while($links[$count][0]!="") {
						if($links[$count][0]==$_POST['id'])
							fwrite($fp,$_POST['id']."|".encode($_POST['linkname'])."|".htmlentities($_POST['link'])."|".encode($_POST['descr'])."|".$_POST['cat']."||");
						else
							fwrite($fp,$links[$count][0]."|".$links[$count][1]."|".$links[$count][2]."|".$links[$count][3]."|".$links[$count][4]."||");
						$count++;
					}
				}
				fclose($fp);
				unset($_GET['action']);
			}
}

if(isset($_POST['linkcat'])) {
		$type="link";
		$filename="data/linkcat.dat";
		$aaa=trim(@file_get_contents($filename));
		unset($linkscat);
		$linkscat=explode("||",$aaa);
		$count=0;
		$maxid=0;
		while($linkscat[$count] != "") {
			$linkcat[$count]=explode("|",$linkscat[$count]);
			if($linkcat[$count][0]>$maxid) $maxid=$linkcat[$count][0];
			$count++;
		}
		$maxid++;
		if($_POST['linkcat']=="Add Category") { // add
			if(!$fp=fopen($filename,"a")) die($linksmessage[106]);
			fwrite($fp,$maxid."|".htmlentities($_POST['name'])."|".encode($_POST['descr'])."||");
		} else {
			if(!$fp=fopen($filename,"w")) die($linksmessage[106]);
			$count=0;
			while($linkcat[$count][0]!="") {
				if($linkcat[$count][0]==$_POST['id'])
					fwrite($fp,$_POST['id']."|".encode($_POST['name'])."|".encode($_POST['descr'])."||");
				else
					fwrite($fp,$linkcat[$count][0]."|".$linkcat[$count][1]."|".$linkcat[$count][2]."||");
				$count++;
			}
		}
		fclose($fp);
		unset($_GET['action']);
}

function editlinks() {
	global $out,$linksmessage;
	$linkscat=readdata("linkcat");
	//read links id,nome,link, descricao,cat
	$links=readdata("links");
	if(substr($_GET['action'],0,4)=="edit") { // edit or editc
		$count=0;
		while($links[$count][0] !="") {
			if($links[$count][0]==$_GET['id']) {
				$record=$count;
				break;
			}
			$count++;
		}
	}
	switch($_GET['action']) {
		case "delete":
			$links=deletedata($links,"links",0,$_GET['id'],5);
			unset($_GET['action']);
			break;
		case "deletec":
			$linkscat=deletedata($linkscat,"linkcat",0,$_GET['id'],3);
			unset($_GET['action']);
	}
	$out.="<div align=\"center\">";
	$out.="<h2>".$linksmessage[40]."</h2>\n<form method=\"post\" action=\"\"><fieldset style=\"border: 0;\"><table>\n";
	$out.="<tr><td>$linksmessage[50]</td><td><input type=\"text\" name=\"name\"";
	if($_GET['action']=="editc") $out.=" value=\"".decode($linkscat[$record][1])."\"";
	$out.=" /></td></tr>\n";
	$out.="<tr><td>$linksmessage[15]</td><td><input type=\"text\" name=\"descr\"";
	if($_GET['action']=="editc") $out.=" value=\"".decode($linkscat[$record][2])."\"";
	$out.=" /></td></tr>\n<tr><td>";
	if($_GET['action']=="editc") $out.="<input type=\"hidden\" name=\"id\" value=".$linkscat[$record][0]." />";
	$out.="</td><td><input type=\"hidden\" ";
		$out.="name=\"linkcat\" ";
	if($_GET['action']=="editc") {
		$out.="value=\"Edit Category\" />\n";
		$out.="<input type=\"submit\" name=\"aa\" value=\"$linksmessage[54]\" />\n";
	} else {
		$out.="value=\"Add Category\" />\n";
		$out.="<input type=\"submit\" name=\"aa\" value=\"$linksmessage[53]\" />\n";
	}
	$out.="</td></tr>\n</table>\n";
	$out.="<table cellspacing=\"5\">";
	$count=0;
	while ($linkscat[$count][0]!="") {
		$out.="<tr><td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
		$out.="id=".$linkscat[$count][0]."&amp;action=editc\"><img src=\"images/edit.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
		$out.="id=".$linkscat[$count][0]."&amp;action=deletec\"><img src=\"images/editdelete.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td>".$linkscat[$count][0]."</td><td>".decode($linkscat[$count][1])."</td><td>".decode($linkscat[$count][2])."</td></tr>\n";
		$count++;
	}
	$out.="</table>\n</form>\n";
	$out.="<h3>".$linksmessage[40]."</h3>\n";
	$out.="<form enctype=\"multipart/form-data\" method=\"post\" action=\"\"><fieldset style=\"border: 0;\"><table>\n<tr>";
	$out.="<td>$linksmessage[50]</td><td><input type=\"text\" name=\"linkname\"";
	if($_GET['action']=="edit") $out.=" value=\"".decode($links[$record][1])."\"";
	$out.=" /></td></tr>\n<tr><td>";
	$out.="\n<tr>";
	$out.="<td>";
	$out.=$linksmessage[69];
	$out.="</td><td><input type=\"text\" name=\"link\"";
	if($_GET['action']=="edit") $out.=" value=\"".$links[$record][2]."\"";
	$out.=" /></td></tr>\n<tr>";
	$out.="<td valign=\"top\" >$linksmessage[67]</td><td><textarea style=\"width: 180px;\" name=\"descr\">";
	if($_GET['action']=="edit") $out.=decode($links[$record][3]);
	$out.="</textarea></td></tr>\n<tr>";
	$out.="<td>$linksmessage[52]</td><td align=\"right\"><select name=\"cat\" >\n";
	$count=0;
	while($linkscat[$count][0]!="") {
		$out.='<option value="'.$linkscat[$count][0].'"';
		if($_GET['action']=="edit" && $linkscat[$count][0]==$_GET['id']) $out.=' SELECTED';
		$out.='>'.decode($linkscat[$count][1])."&nbsp;</option>\n";
		$count++;
	}
	$out.="</select></tr>\n<tr>";
	$out.="<td>";
	if($_GET['action']=="edit") $out.="<input type=\"hidden\" name=\"id\" value=\"".$links[$record][0]."\" />";
	$out.="</td><td>";
		if($_GET['action']=="edit") {
			$out.="<input type=\"hidden\" name=\"submit\" value=\"Edit Link\" />";
			$out.="<input type=\"submit\" name=\"aa\" value=\"$linksmessage[70]\" />";
		} else {
			$out.="<input type=\"hidden\" name=\"submit\" value=\"Add Link\" />";
			$out.="<input type=\"submit\" name=\"aa\" value=\"$linksmessage[71]\" />";
		}
	$out.="</td></tr></table></fieldset></form>\n";
	$out.="<table cellspacing=\"5\">";
	$count=0;
	while ($links[$count][0]!="") {
		$out.="<tr><td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
		$out.="id=".$links[$count][0]."&amp;action=edit\"><img src=\"images/edit.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
			$out.="id=".$links[$count][0]."&amp;action=delete\"><img src=\"images/editdelete.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td>".decode($links[$count][1])."</td><td>".decode($links[$count][2])."</td></tr>\n";
		$count++;
	}
	$out.="</table>\n</div>\n";
}

?>