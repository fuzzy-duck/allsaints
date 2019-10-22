<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Gallery run module main.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
require_once "addons/gallery/common.php";
function galery($gal="", $width=0, $height=0) {
	global $langmessage;
	if($gal!="") {
		$count=1;
		$out.="<h2>".$gal."</h2><br />\n";
		$galeries[0]=$gal;
	} else {
		if(isset($_POST['gal'])) $gal=sanitize($_POST['gal']);
		$folder="galeries";
		$files=filelist('/./',$folder,1);
		$folder="galeries";
		$count=0;
		foreach($files as $file) {
			if($file != ".." && $file != "." && is_dir($folder."/".$file)) {
				$galeries[$count]=$file;
				$count++;
			}
		}
	}
	if($count>1) {
		$out.="\n<form method=\"post\" name=\"galery\" action=\"\"><fieldset style=\"border: 0;\">\n";
		$out.="<select onchange=\"document.galery.submit();\" name=\"gal\" class=\"LNE_select\">\n";
		for($i=0;$i<$count;$i++) {
			$out.='<option value="'.$galeries[$i].'"';
			if($gal==$galeries[$i]) $out.=" selected";
			$out.=">".$galeries[$i]."&nbsp;</option>\n";
			if($gal=="") $gal=$galeries[$i];
		}
		$out.="</select>\n";
		$out.="<input type=\"hidden\" name=\"showgalery\" value=\"$langmessage[94]\" />\n";
		$out.="</fieldset></form>\n";
		$out.="<br />\n";
	} else {
		$gal=$galeries[0];
	}
	//$gal contains the galery folder
	$gal="galeries/".$gal;
	$filez=filelist('/./',$gal);
	$names=readdata("images");
	foreach($filez as $file) {
		if($width==0)
			$width=100;
		$found=false;
		foreach($names as $name) {
			if($name[0]==$file) {
				if($name[1]!="-") {
					$nome=decode($name[1]);
					$found=true;
				}
			}
		}
		if(!$found)
			$name=$file;
		else
			$name=$nome;
		$out.="<a href=\"$gal/$file\" rel=\"lytebox[".$gal."]\" title=\"$name\" >";
		$out.="<img src=\"thumbs/".createThumb( $gal."/".$file, "thumbs/", $width )."\" width=\"$width\" alt=\"$name\" class=\"bordered\" /></a>\n";
	}
	return $out;
}
?>
