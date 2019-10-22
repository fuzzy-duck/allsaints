<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Downloads send module send.php
| Version 2.4.3 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $downloadsmessage, $set;

require_once "../../data/config.php";
if(file_exists("lang/lang_".$set['language'].".php"))
	require_once "lang/lang_".$set['language'].".php";
else
	require_once "lang/lang_en_US.php";

if(isset($_GET['dlid'])) {
	// there is a download request
	require_once "../../LightNEasy/common.php";
	if(!is_intval($_GET['dlid'])) die ("Downloads - Aha! Clever!");
	if($_GET['upload']=="1")
		$folder="uploads";
	else
		$folder="downloads";
	$downloads=readdata("../../../data/downloads");
	foreach($downloads as $row) {
		if(intval($row[0])==intval($_GET['dlid'])) {
			if(strpos($row[2],"*"))
				$filename = str_replace("*", "",$row[2]);
			else
				$filename="../../$folder/".decode($row[2]);
			if(!file_exists($filename)) die ($downloadsmessage[109]);
			$size = filesize("$filename");
			$ext = explode (".",$filename);
			if ($ext[1]=="php" || $ext[1]=="html") die ($downloadsmessage[108]);
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			switch($ext[1]) {
				case "zip": header("Content-Type: application/zip"); break;
				case "doc": header("Content-Type: application/msword"); break;
				case "pdf": header("Content-Type: application/pdf"); break;
				case "ppt": header("Content-Type: application/ms-powerpoint"); break;
				case "xls": header("Content-Type: application/ms-excel"); break;
				case "mp3": header("Content-Type: audio/mp3"); break;
				case "avi": header("Content-Type: video/avi"); break;
				case "mpg": header("Content-Type: video/mpeg"); break;
				default:
					header("Content-Type: application/save");
			}
			header("Content-Length: $size");
			header("Content-Disposition: attachment; filename=$filename");
			header("Content-Transfer-Encoding: binary");
			$fp = fopen("$filename", "r");
			fpassthru($fp);
			fclose($fp);
			unset($_GET['dlid']);
			break;
		}
	}
	die();
}
?>
