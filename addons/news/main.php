<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon News run module main.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $newsmessage, $set, $message;
if(file_exists("addons/news/lang/lang_".$set['language'].".php"))
	require_once "addons/news/lang/lang_".$set['language'].".php";
else
	require_once "addons/news/lang/lang_en_US.php";

require_once "addons/news/main1.php";

if($_POST['submit']=="sendcomment")
	$message=sendcomment();
if($_POST['submit']=="deletecomment")
	$message=deletecomment();

function deletecomment() {
	$newsid=sanitize($_POST['newsid']);
	$id=sanitize($_POST['id']);
	$row_cmt=readdata("comments");
	if(!$fp=fopen("./data/comments.dat","w")) die ($newsmessage[142]);
	foreach($row_cmt as $row) {
		if($row[0]!=$newsid || $row[3]!=$id) {
			if($row[0]!="")
				fwrite($fp,$row[0]."|".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."||\n");
		}
	}
	fclose($fp);
	return $newsmessage[175];
}

function sendcomment() {
	global $editar, $newsmessage;
	if(!is_intval(trim($_POST['newsid'])) || !is_intval(trim($_POST['secCode'])) || !is_intval($_SESSION[session_id()])) die ($newsmessage[98]);
	$editar=true;
	if($_POST['commentname']=="" || $_POST['commentmessage']=="")
		return $newsmessage[101];
	else {
		if($_POST['secCode'] != $_SESSION[session_id()])
			return $newsmessage[139];
		else {
			$text=sanitize(strip_tags($_POST['commentmessage']));
			$order = array("\r\n", "\n", "\r");
			$text = str_replace($order, "<br />", $text);
			if($_POST['commentemail']=="")
				$email="-";
			else
				$email = sanitize(strip_tags($_POST['commentemail']));
			$name=sanitize(strip_tags($_POST['commentname']));
			if(!$fp=fopen("./data/comments.dat","a"))
				die ($newsmessage[142]);
			fwrite($fp,$_POST['newsid']."|".encode($name)."|".encode($email)."|".time()."|".encode($text)."||\n");
			fclose($fp);
			$editar=false;
			return $newsmessage[141];
		}
	}
//	return $message;
}
?>
