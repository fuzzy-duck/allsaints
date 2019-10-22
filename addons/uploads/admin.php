<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Uploads admin module admin.php
| Version 2.4.3 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $myserver;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');

function auploads() {
	global $langmessage, $set;
	if(file_exists("addons/uploads/lang/lang_".$set['language'].".php"))
		require_once "addons/uploads/lang/lang_".$set['language'].".php";
	else
		require_once "addons/uploads/lang/lang_en_US.php";
	require_once "addons/uploads/settings.php";
	$message="";
	if($_POST['submitupload']=="Transfer upload") {
		if(!is_intval($_POST['cat']) || !is_intval($_POST['fileid']))
			die ($uploadsmessage[16]);
		$downloads=readdata("downloads");
		if(!$fp=fopen("data/downloads.dat","w"))
			die($langmessage[55]);
		foreach($downloads as $download) {
			fwrite($fp,$download[0]."|".$download[1]."|".$download[2]."|".$download[3]."|");
			if($download[0]!=$_POST['fileid']) {
				fwrite($fp,$download[4]."||");
			} else {
				fwrite($fp,$_POST['cat']."||");
			}
		}
		fclose($fp);
		$filename=sanitize($_POST['filename']);
		rename("./uploads/".$filename, "./downloads/".$filename);
	}
	if($_POST['submitupload']=="savesettings") {
		if(!$_SESSION[$set['password']]!="1" || !is_intval($_POST['maxsize']))
			die($langmessage[98]);
		$max_upload_file_size=$_POST['maxsize'];
		if(!$fp=fopen("addons/uploads/settings.php","w"))
			die($langmessage[55]);
		fwrite($fp,"<?php\n\$adminlevel=".$_POST['adminlevel'].";\n\$max_upload_file_size=$max_upload_file_size;\n?>\n");
		fclose($fp);
		$message=$langmessage[150];
	}
	if($message!="") $out.="<h3 style=\"color: red;\">".$message."</h3>\n";
	$out.="<h2>$uploadsmessage[1]</h2>\n<hr />\n";
	$out.="<h3>$uploadsmessage[18]</h3>\n";
	$out.="<form name=\"formn\" method=\"POST\" action=\"\">\n";
	$out.="<table>\n";
	$out.="<tr><td>$uploadsmessage[20]:</td><td><input type=\"text\" name=\"maxsize\" value=\"$max_upload_file_size\" /></td></tr>\n";
	$out.="<tr><td><input type=\"hidden\" name=\"submitupload\" value=\"savesettings\" /></td>";
	$out.="<td><input type=\"submit\" name=\"aaa\" value=\"$uploadsmessage[19]\" /></td></tr>\n";
	$out.="</table>\n</form>\n";
	$cat=readdata("downloadcat",1,"Uploads");
	$result=readdata("downloads",4,$cat[0][0]);
	$out.="<hr /><h3>$uploadsmessage[14]</h3>\n";
	$out.="<form name=\"formm\" method=\"POST\" action=\"\">\n";
	if($result[0][0]!="") {
		$out.="<table cellspacing=\"5\">\n";
		$cat1=readdata("downloadcat");
		foreach ($result as $row) {
			$out.="<form name=\"form".$row[0]."\" method=\"post\" action=\"\">\n";
			$out.="<tr><td><input type=\"hidden\" name=\"submitupload\" value=\"Transfer upload\" />";
			$out.="<input type=\"hidden\" name=\"fileid\" value=".$row[0]." />";
			$out.="<input type=\"hidden\" name=\"filename\" value=".$row[2]." />";
			$out.="<input type=\"submit\" name=\"aaa\" value=\"$uploadsmessage[15]\" /></td>\n";
			$out.="<td><select name=\"cat\">\n";
			foreach($cat1 as $row1)
				$out.="<option value=\"".$row1[0]."\">".$row1[1]."</option>\n";
			$out.="</select></td>\n";
			
			$out.="<td><a href=\"addons/downloads/send.php?dlid=".$row[0]."&amp;upload=1\">".decode($row[1])."</a></td><td>".$row[2]."</td>\n";
			$out.="</tr>\n</form>\n";
		}
		$out.="</table>\n";
	} else {
		$out.="<p>$uploadsmessage[4]</p>\n";
	}
	return $out;
}
?>
