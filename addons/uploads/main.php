<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Uploads run module main.php
| Version 2.4.3 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/

function uploads() {
	global $uploadsmessage, $set;
	if(file_exists("addons/uploads/lang/lang_".$set['language'].".php"))
		require_once "addons/uploads/lang/lang_".$set['language'].".php";
	else
		require_once "addons/uploads/lang/lang_en_US.php";
	require_once "addons/uploads/settings.php";

	//check if uploads cathegory exists and add it
	$found=false;
	$maxid=0;
	$downloadsc=readdata("downloadcat");
	foreach($downloadsc as $cat) {
		if($cat[1]=="Uploads") {
			$found=true;
			$upid=strval($cat[0]);
		}
		if(strval($cat[0])>$maxid)
			$maxid=strval($cat[0]);
	}
	if(!$found) {
		$maxid++;
		$upid=$maxid;
		$fp=fopen("data/downloadcat.dat","a");
		fwrite($fp,$upid."|Uploads|Uploads go here||");
		fclose($fp);
	}

	$message="";
	if($_POST['submitupload']=="Add Upload") {
		if($_POST['secCode'] != $_SESSION[session_id()]) {
			$message=$uploadsmessage[8];
		} else {
			$succeded=false;
			$message=$_FILES["file"]["error"];
			if($_FILES['uploadedfile']['name'] != "") {
				$_FILES['uploadedfile']['name']=str_replace(" ", "%20", $_FILES['uploadedfile']['name']);
				$target_path = "./uploads/".basename($_FILES['uploadedfile']['name']);
				if(file_exists($target_path))
					unlink($target_path);
				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
					$succeded=true;
					$message=$uploadsmessage[12];
					@chmod($target_path, 0644);
				} else {
					$message=$uploadsmessage[11];
				}
			} else
				$message=$uploadsmessage[9];
			if($succeded) {
				//find maxid
				$uploads=readdata("downloads");
				$maxid=0;
				foreach($uploads as $upload) {
					if($upload[0]>$maxid)
						$maxid=$upload[0];
				}
				$maxid++;
				$filenam=basename( $_FILES['uploadedfile']['name']);
				if(!$fp=fopen("data/downloads.dat","a"))
					$message=$uploadsmessage[10];
				else {
					fwrite($fp,$maxid."|".encode(sanitize($_POST['nome']))."|".$filenam."|");
					if($_POST['description']=="")
						fwrite($fp,"-");
					else
						fwrite($fp,encode(sanitize($_POST['description'])));
					fwrite($fp,"|".$upid."||");
					fclose($fp);
				}
			}
		}
	} else {
			$out.="\n<div id=\"LNE_show\">\n";
			$out.="<div align=\"center\">\n<h3>$uploadsmessage[5]</h3>\n";
			$out.="<form enctype=\"multipart/form-data\" method=\"post\" action=\"\"><fieldset style=\"border: 0;\"><table>\n";
			$out.="<tr><td align=\"right\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$max_upload_file_size\" /><b>$uploadsmessage[13]:&nbsp;</b></td>";
			$out.="<td><input type=\"text\" name=\"nome\" style=\"width: 100%;\" /></td></tr>\n";
			$out.="<tr><td align=\"right\"><b>$uploadsmessage[5]:&nbsp;</b></td><td><input style=\" text-align: left;\" name=\"uploadedfile\" type=\"file\" />\n</td></tr>\n";
			$out.="<tr><td align=\"right\"><b>$uploadsmessage[22]:&nbsp;</b></td><td><textarea name=\"description\" style=\"width: 100%;\"></textarea></td></tr>\n";
			$out.="<tr><td align=\"right\"><b>$uploadsmessage[20]:&nbsp;</b></td><td>$max_upload_file_size&nbsp;$uploadsmessage[23]</td></tr>\n";
			$out.="<tr><td align=\"right\"><b>$uploadsmessage[6]:&nbsp;</b></td>\n";
			if($set['extension']=="0") {
				//text catchpa
				srand((double) microtime() * 1000000);
				$a = rand(0, 9);
				$b = rand(0, 9);
				$c=$a+$b;
				$out.="<td>$a + $b = ";
				$_SESSION[session_id()] = $c;
				$out.="<input type=\"text\" name=\"secCode\" maxlength=\"2\" style=\"width:20px\" /></td></tr>\n";
			} else {
				// image catchpa
				$out.="<td>".catchpa()."</td></tr>\n";
			}
			$out.="<tr><td></td><td><input type=\"hidden\" name=\"cat\" value=\"".$crow['id']."\" /><input type=\"hidden\" name=\"submitupload\" value=\"Add Upload\" />\n";
			$out.="<input type=\"submit\" name=\"aaa\" value=\"$uploadsmessage[7]\" />\n";
			$out.="</td><td>&nbsp</td></tr>\n</table>\n</fieldset>\n</form>\n</div>\n";
	}
	if($message!="")
		$out.="<h3 style=\"color: red;\">$message</h3>\n";
	$out.="<h3>$uploadsmessage[14]</h3>\n<ul>\n";
	$downloads=readdata("downloads",4,$upid);
	foreach($downloads as $download) {
		$out.="<li><b>".decode($download[1])."</b> - ".decode($download[3])."</li>\n";
		If(!$found)
			$found=true;
	}
	$out.="</ul>";
	if(!$found)
		$out.="<h3>$uploadsmessage[4]</h3>";
	$out.="</div>\n";
	return $out;
}
?>
