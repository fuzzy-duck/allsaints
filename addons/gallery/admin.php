<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Gallery admin module admin.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $gallerymessage, $myserver, $set;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');
if(file_exists("addons/gallery/lang/lang_".$set['language'].".php"))
	require_once "addons/gallery/lang/lang_".$set['language'].".php";
else
	require_once "addons/gallery/lang/lang_en_US.php";
require_once "addons/gallery/common.php";
if($_POST['submit']=="Delete Gallery") {
	global $message;
	$folder="galeries/".$_POST['name'];
	$filez=filelist('/./', $folder);
	foreach($filez as $fil)
		$out.=deleteimage($folder."/".$fil,$fil);
	if(@rmdir($folder))
		$message=$gallerymessage[1];
	else
		$message=$gallerymessage[2];
	unset($_GET['do']);
}

if($_POST['submit']=="Create Gallery") {
	if(!is_dir("galeries/".$_POST['galeryname'])) {
		mkdir("galeries/".$_POST['galeryname'], 0777)
			or die ($gallerymessage[63]);
		$message=$gallerymessage[64];
	} else $message=$gallerymessage[65];
	unset($_GET['do']);
}
if($_POST['submit']=="Upload image") {
	$message="";
	if($_FILES['uploadedfile']['name'] != "" && $_POST['gal']!="") {
		if($_POST['where']=="gallery")
			$target_path = "galeries/".$_POST['gal']."/";
		else
			$target_path = "uploads/";
		$target_path = $target_path.basename($_FILES['uploadedfile']['name']);
	} else $message=$gallerymessage[97];
	if($message=="") {
		if(file_exists($target_path)) unlink($target_path);
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			@chmod($target_path, 0644);
			//add entry to images.dat
			if(!$fp=fopen("data/images.dat","a"))
				$message=$gallerymessage[9];
			$imagename=encode(sanitize($_POST['imagename']));
			if($imagename=="")
				$imagename="-";
			fwrite($fp,basename($_FILES['uploadedfile']['name'])."|".$imagename."|".encode(sanitize($_POST['gal']))."||");
			fclose($fp);
			if($message="")
				$message=$gallerymessage[124].basename( $_FILES['uploadedfile']['name']).$gallerymessage[125];
		} else $message=$gallerymessage[123];
	}
	unset($_GET['do']);
}

function images() {
	global $gallerymessage, $max_upload_image_size, $message;
	if($message!="")
		$out.="<h3>$message</h3>\n";
	$out.="<div align=\"center\">\n";
	if($_GET['do']=="gallery" && $_GET['action']=="delete" && $_GET['name']!="") {
		$out.=deleteimage(sanitize($_GET['name']));
	}
	if($_GET['do']=="gallery" && $_GET['action']=="deletegal" && $_GET['name']!="") {
		$galleryname=$_GET['name'];
		$out.="<h4 style=\"color: red;\">".$gallerymessage[3]."$galleryname?</h4>\n";
		$out.="<p>$gallerymessage[4]</p>\n";
		$out.="<form method=\"post\" action=\"\">\n";
		$out.="<input type=\"hidden\" name=\"name\" value=\"$galleryname\" />\n";
		$out.="<input type=\"hidden\" name=\"submit\" value=\"Delete Gallery\" />\n";
		$out.="<input type=\"submit\" name=\"aaa\" value=\"".$gallerymessage[5]."\" />\n";
		$out.="</form>\n";
	}
	$out.="<form enctype=\"multipart/form-data\" method=\"post\" action=\"\">\n<fieldset style=\"border: 0;\">\n";
	$out.="<h2 class=\"LNE_title\">".$gallerymessage[57]."</h2>\n";
	$out.="<h3>$gallerymessage[58]</h3>\n";
	$out.="<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$max_upload_image_size\" />\n";
	$out.="<table>\n";
	$out.="<tr><td></td><td>$gallerymessage[8]:</td><td><input type=\"text\" name=\"imagename\" style=\"width: 100%;\" /></td></tr>\n";
	$out.="<tr><td></td><td>".$gallerymessage[59].":</td><td><input name=\"uploadedfile\" type=\"file\" />\n</td></tr>\n";
	$out.="<tr><td><input type=\"radio\" name=\"where\" value=\"gallery\" checked /></td><td>".$gallerymessage[61].":</td><td>";
	$out.="<select name=\"gal\">\n";
	$files=filelist('/./',"galeries",1);
	foreach($files as $file)
		$out.='<option value="'.$file."\">".$file."&nbsp;</option>\n";
	$out.="</select>\n</td></tr>\n";
	$out.="<tr><td><input type=\"radio\" name=\"where\" value=\"uploads\" /></td><td>".$gallerymessage[7]."</td><td></td></tr>\n";
	$out.="<tr><td></td>\n<td></td><td>";
	$out.="<input type=\"hidden\" name=\"submit\" value=\"Upload image\" />";
	$out.="<input type=\"submit\" value=\"".$gallerymessage[58]."\" /></td>";
	$out.="</tr>\n</table>\n</fieldset>\n</form>\n<br />\n";
	$out.="<h3>".$gallerymessage[62]."</h3>\n";
	$out.="<form method=\"post\" action=\"\">\n<fieldset style=\"border: 0;\">\n";
	$out.="<table>\n<tr><td>$gallerymessage[117]:</td>\n<td>";
	$out.="<input name=\"galeryname\" type=\"text\" value=\"\" />\n</td></tr>\n";
	$out.="<tr><td><input type=\"hidden\" name=\"submit\" value=\"".$gallerymessage[62]."\" /></td>\n<td>";
	$out.="<input type=\"submit\" name=\"aa\" value=\"$gallerymessage[62]\" /></td>";
	$out.="</tr></table>\n</fieldset>\n</form>\n";
	$out.="<h3>".$gallerymessage[148]." ".$gallerymessage[176]."</h3>\n";

	$none=true;
	foreach($files as $file) {
		if($none) {
			$none=false;
			$out.="<table>";
		}
		if($file != ".." && $file != ".") {
			$out.="<tr>";
			$out.="<td><a href=\"".$_SERVER["SCRIPT_NAME"]."?do=gallery&amp;action=deletegal&amp;name=$file\">";
			$out.="<img src=\"./images/editdelete.png\" alt=\"delete\" title=\"Delete gallery $file\" align=\"left\" border=\"0\" /></a></td>";
			$out.="<td>".$file."</td></tr>\n";
		}
	}
	if(!$none) $out.="</table>\n";

	$out.="<br /><h3>".$gallerymessage[148]." ".$gallerymessage[38]."</h3>\n";
	$out.="<table>\n";
	$folder="./galeries";
	$files=filelist('/./',$folder,1);
	$gal=0;
	foreach($files as $file) {
		if($gal==0) {
			$out.="\n<form method=\"post\" name=\"galery\" action=\"\">\n";
			$out.="<select onchange=\"document.galery.submit();\" name=\"selectgal\">\n";
			$first=$file;
		}
		$gal++;
		$out.="<option value=\"".$file."\"";
		if($_POST['selectgal']==$file)
			$out.=" selected";
		$out.=">".$file."&nbsp;</option>\n";
	}
	if($gal>0) {
		$out.="</select></form>\n<br /><br />\n";
		if($_POST['selectgal']!="")
			$file=$_POST['selectgal'];
		else {
			$file=$first;
		}
		$folder1="galeries/".$file;
		$file1=filelist("/./",$folder1);
		foreach($file1 as $fil) {
			$out.="<tr><td><a href=\"".$_SERVER["SCRIPT_NAME"]."?do=gallery&amp;action=delete&amp;name=$folder1/$fil\">";
			$out.="<img src=\"./images/editdelete.png\" alt=\"delete\" title=\"Delete $fil\" align=\"left\" border=\"0\" /></a></td>";
			$out.="<td>$fil</td>";
			$out.="<td align=\"center\" >";
			$thumb=createThumb($folder1."/".$fil, "thumbs/", 100);
			$out.="<img src=\"thumbs/$thumb\"  alt=\"$thumb\" /></td></tr>\n";
		}
	}
	$out.="</table>\n</div>\n";
	return $out;
}

function deleteimage($pathtofile, $basename="") {
	global $gallerymessage;
	if(@unlink($pathtofile)) {
		//delete thumbnail too
		if($basename=="") {
			$info = pathinfo($pathtofile);
			$basename = $info['basename'];
		}
		@unlink("thumbs/".$basename);
		//delete the entry in images.dat
		$images=readdata("images");
		if(!$fp=fopen("data/images.dat","w"))
			$message=$gallerymessage[9];
		foreach($images as $image) {
			if($image[0]!=$basename && $image[0]!="")
				fwrite($fp, $image[0]."|".$image[1]."|".$image[2]."||");
		}
		fclose($fp);
		$out="<h2 class=\"LNE_message\">".$gallerymessage[149]."</h2>\n";
	} else
		$out="<h2 class=\"LNE_message\">".$gallerymessage[6]."</h2>\n";
	return $out;
}
?>
