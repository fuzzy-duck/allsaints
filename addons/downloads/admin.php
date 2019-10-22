<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Downloads admin module admin.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $downloadsmessage,$myserver,$set;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');

if(file_exists("addons/downloads/lang/lang_".$set['language'].".php"))
	require_once "addons/downloads/lang/lang_".$set['language'].".php";
else
	require_once "addons/downloads/lang/lang_en_US.php";


if(isset($_POST['downloadcat'])) {
		if($_POST['downloadcat']=="Add Category" || $_POST['downloadcat']=="Edit Category")
			$type="download";
		$filename="data/downloadcat.dat";
		$aaa=trim(@file_get_contents($filename));
		unset($downloadcat);
		$downloadcat=explode("||",$aaa);
		$count=0;
		$maxid=0;
		while($downloadcat[$count] != "") {
			$downloadscat[$count]=explode("|",$downloadcat[$count]);
			if($downloadscat[$count][0]>$maxid)
				$maxid=$downloadscat[$count][0];
			$count++;
		}
		$maxid++;
		if($_POST['downloadcat']=="Add Category") { // add
			if(!$fp=fopen($filename,"a"))
				die($langmessage[106]);
			fwrite($fp,$maxid."|".htmlentities($_POST['name'])."|".encode($_POST['descr'])."||");
		} else {
			if(!$fp=fopen($filename,"w")) die($langmessage[106]);
			$count=0;
			while($downloadscat[$count][0]!="") {
				if($downloadscat[$count][0]==$_POST['id'])
					fwrite($fp,$_POST['id']."|".encode($_POST['name'])."|".encode($_POST['descr'])."||");
				else
					fwrite($fp,$downloadscat[$count][0]."|".$downloadscat[$count][1]."|".$downloadscat[$count][2]."||");
				$count++;
			}
		}
		fclose($fp);
		unset($_GET['action']);
}

if($_POST['submit']== "Add Download" || $_POST['submit']== "Edit Download") {
	if($_POST['submit']== "Add Download" && $_POST['filename']=="upload" ) {
		$succeded=false;
		if($_FILES['uploadedfile']['name'] != "") {
			$target_path = "downloads/";
			$target_path .= str_replace(" ", "%20", basename( $_FILES['uploadedfile']['name']));
			if(file_exists($target_path)) unlink($target_path);
			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
				$succeded=true;
				@chmod($target_path, 0644);
			} else {
				$message=$downloadsmessage[123];
			}
		} else $message=$downloadsmessage[97];
	}
	if($succeded || $_POST['submit']=="Edit Download") {
			$aaa=trim(@file_get_contents("data/downloads.dat"));
			unset($download);
			$download=explode("||",$aaa);
			$count=0;
			$maxid=0;
			while($download[$count] != "") {
				$downloads[$count]=explode("|",$download[$count]);
				if($downloads[$count][0]>$maxid) $maxid=$downloads[$count][0];
				$count++;
			}
			$maxid++;
			if($succeded)
				$filenam=str_replace(" ", "%20", $_FILES['uploadedfile']['name']);
			else
				$filenam=htmlentities($_POST['link']);
			if($_POST['descr']=="") $_POST['descr']="-";
			if(($_POST['link']!="" || $succeded) && $_POST['linkname']!="") {
				if($_POST['submit'] == "Add Download") {
					if(!$fp=fopen("data/downloads.dat","a")) die($downloadsmessage[3]);
					fwrite($fp,$maxid."|".encode($_POST['linkname'])."|".encode($filenam)."|".encode($_POST['descr'])."|".$_POST['cat']."||");
				} else {
					if(!$fp=fopen("data/downloads.dat","w")) die($downloadsmessage[3]);
					$count=0;
					while($downloads[$count][0]!="") {
						if($downloads[$count][0]==$_POST['id'])
							fwrite($fp,$maxid."|".encode($_POST['linkname'])."|".$filenam."|".encode($_POST['descr'])."|".$_POST['cat']."||");
						else
							fwrite($fp,$downloads[$count][0]."|".$downloads[$count][1]."|".$downloads[$count][2]."|".$downloads[$count][3]."|".$downloads[$count][4]."||");
						$count++;
					}
				}
				fclose($fp);
				unset($_GET['action']);
			}
	}
}

function editdownloads($type="") {
	global $downloadsmessage,$max_upload_file_size;
	$downloadscat=readdata("downloadcat");
	//read links id,nome,link, descricao,cat
	$downloads=readdata("downloads");
	if($_GET['action']=="edit") { // edit or editc
		$count=0;
		while($downloads[$count][0] !="") {
			if($downloads[$count][0]==$_GET['id']) {
				$record=$count;
				break;
			}
			$count++;
		}
	}
	if($_GET['action']=="editc") { // edit or editc
		$count=0;
		while($downloadscat[$count][0] !="") {
			if($downloadscat[$count][0]==$_GET['id']) {
				$record=$count;
				break;
			}
			$count++;
		}
	}
	switch($_GET['action']) {
		case "delete":
			$downloads=deletedata($downloads,$type."s",0,$_GET['id'],5);
			unset($_GET['action']);
			break;
		case "deletec":
			$downloadscat=deletedata($downloadscat,$type."cat",0,$_GET['id'],3);
			unset($_GET['action']);
	}
	$out.="<div align=\"center\">\n<h3>".$downloadsmessage[49]."</h3>\n<form method=\"post\" action=\"\"><fieldset style=\"border: 0;\">\n<table cellspacing=\"5\">\n";
	$out.="<tr><td>$downloadsmessage[50]</td><td><input type=\"text\" name=\"name\"";
	if($_GET['action']=="editc") $out.=" value=\"".decode($downloadscat[$record][1])."\"";
	$out.=" /></td></tr>\n";
	$out.="<tr><td>$downloadsmessage[15]</td><td><input type=\"text\" name=\"descr\"";
	if($_GET['action']=="editc") $out.=" value=\"".decode($downloadscat[$record][2])."\"";
	$out.=" /></td></tr>\n<tr><td>";
	if($_GET['action']=="editc") $out.="<input type=\"hidden\" name=\"id\" value=".$downloadscat[$record][0]." />";
	$out.="</td><td><input type=\"hidden\" name=\"downloadcat\" ";
	if($_GET['action']=="editc") {
		$out.="value=\"Edit Category\" />\n";
		$out.="<input type=\"submit\" name=\"aa\" value=\"$downloadsmessage[54]\" />\n";
	} else {
		$out.="value=\"Add Category\" />\n";
		$out.="<input type=\"submit\" name=\"aa\" value=\"$downloadsmessage[53]\" />\n";
	}
	$out.="</td></tr>\n</table></div>\n";
	$out.="<table cellspacing=\"5\">";
	$GETarray=$_GET;
	$count=0;
	while ($downloadscat[$count][0]!="") {
		$out.="<tr><td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
		$out.="id=".$downloadscat[$count][0]."&amp;action=editc\"><img src=\"images/edit.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
		$out.="id=".$downloadscat[$count][0]."&amp;action=deletec\"><img src=\"images/editdelete.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td>".$downloadscat[$count][0]."</td><td>".decode($downloadscat[$count][1])."</td><td>".decode($downloadscat[$count][2])."</td></tr>\n";
		$count++;
	}
	$out.="</table>\n</form>\n";
	$out.="<div align=\"center\"><h3>".$downloadsmessage[48]."</h3>\n";
	$out.="<form enctype=\"multipart/form-data\" method=\"post\" action=\"\"><fieldset style=\"border: 0;\"><table>\n<tr>";
	$out.="<td></td>";
	$out.="<td>$downloadsmessage[50]</td><td><input type=\"text\" name=\"linkname\"";
	if($_GET['action']=="edit") $out.=" value=\"".decode($downloads[$record][1])."\"";
	$out.=" /></td></tr>\n<tr><td>";
	if($_GET['action']=="edit") {
		$out.="<input style=\"width: 14px;\" type=\"radio\" name=\"filename\" value=\"upload\" /></td><td>$downloadsmessage[122]</td><td><input style=\" text-align: left;\" name=\"uploadedfile\" type=\"file\" name=\"uploadfile\" />\n</td></tr>\n";
		$out.="<td><input style=\"width: 14px;\" type=\"radio\" checked=\"checked\" name=\"filename\" value=\"filename\" /></td>";
	} else {
		$out.="<input style=\"width: 14px;\" type=\"radio\" checked=\"checked\" name=\"filename\" value=\"upload\" /></td><td>$downloadsmessage[122]</td><td><input style=\" text-align: left;\" name=\"uploadedfile\" type=\"file\" name=\"uploadfile\" />\n</td></tr>\n";
		$out.="<td><input style=\"width: 14px;\" type=\"radio\" name=\"filename\" value=\"filename\" /></td>";
	}
	$out.="<td>".$downloadsmessage[84]; //filename
	$out.="</td><td><input type=\"text\" name=\"link\"";
	if($_GET['action']=="edit") $out.=" value=\"".$downloads[$record][2]."\"";
	$out.=" /></td></tr>\n<tr>";
	$out.="<td><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$max_upload_file_size\" /></td>";
	$out.="<td valign=\"top\" >$downloadsmessage[67]</td><td><textarea style=\"width: 180px;\" name=\"descr\">";
	if($_GET['action']=="edit") $out.=decode($downloads[$record][3]);
	$out.="</textarea></td></tr>\n<tr>";
	$out.="<td></td>";
	$out.="<td>$downloadsmessage[52]</td><td align=\"right\"><select name=\"cat\" >\n";
	$count=0;
	while($downloadscat[$count][0]!="") {
		$out.='<option value="'.$downloadscat[$count][0].'"';
		if($_GET['action']=="edit" && $downloadscat[$count][0]==$_GET['id']) $out.=' SELECTED';
		$out.='>'.decode($downloadscat[$count][1])."&nbsp;</option>\n";
		$count++;
	}
	$out.="</select></tr>\n<tr>";
	$out.="<td></td>";
	$out.="<td>";
	if($_GET['action']=="edit") $out.="<input type=\"hidden\" name=\"id\" value=\"".$downloads[$record][0]."\" />";
	$out.="</td><td>";
		if($_GET['action']=="edit") {
			$out.="<input type=\"hidden\" name=\"submit\" value=\"Edit Download\" />";
			$out.="<input type=\"submit\" name=\"aa\" value=\"$downloadsmessage[56]\" />";
		} else {
			$out.="<input type=\"hidden\" name=\"submit\" value=\"Add Download\" />";
			$out.="<input type=\"submit\" name=\"aa\" value=\"$downloadsmessage[55]\" />";
		}
	$out.="</td></tr></table></fieldset></form></div>\n";
	$out.="<table cellspacing=\"5\">";
	$count=0;
	while ($downloads[$count][0]!="") {
		$out.="<tr><td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
		$out.="id=".$downloads[$count][0]."&amp;action=edit\"><img src=\"images/edit.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td><a href=\"".$_SERVER['SCRIPT_NAME']."?";
		if($_GET['do']!="") $out.="do=".$_GET['do']."&amp;";
			$out.="id=".$downloads[$count][0]."&amp;action=delete\"><img src=\"images/editdelete.png\" style=\"align: left; border: 0;\" ></a></td>\n";
		$out.="<td>".decode($downloads[$count][1])."</td><td>".decode($downloads[$count][2])."</td></tr>\n";
		$count++;
	}
	$out.="</table>\n";
	return $out;
}


?>
