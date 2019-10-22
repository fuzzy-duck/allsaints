<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2011 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon News admin module admin.php
| Version 2.4.2 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $newsmessage,$myserver,$set;
if ($_SERVER['SERVER_NAME']!=$myserver)
	die ('Access Denied!');

if(file_exists("addons/news/lang/lang_".$set['language'].".php"))
	require_once "addons/news/lang/lang_".$set['language'].".php";
else
	require_once "addons/news/lang/lang_en_US.php";

if($_POST['newscat']=="Add Category" || $_POST['newscat']=="Edit Category") {
	$filename="data/newscat.dat";
	$aaa=trim(@file_get_contents($filename));
	unset($newsscat);
	$newsscat=explode("||",$aaa);
	$count=0;
	$maxid=0;
	while($newsscat[$count] != "") {
		$newscat[$count]=explode("|",$newsscat[$count]);
		if($newscat[$count][0]>$maxid) $maxid=$newsscat[$count][0];
		$count++;
	}
	$maxid++;
	if($_POST['newscat']=="Add Category") { // add
		if(!$fp=fopen($filename,"a")) die($langmessage[106]);
		fwrite($fp,$maxid."|".encode($_POST['name'])."|".encode($_POST['descr'])."||");
	} else {
		if(!$fp=fopen($filename,"w")) die($langmessage[106]);
		$count=0;
		while($newscat[$count][0]!="") {
			if($newscat[$count][0]==$_POST['id'])
				fwrite($fp,$_POST['id']."|".encode($_POST['name'])."|".encode($_POST['descr'])."||");
			else
				fwrite($fp,$newscat[$count][0]."|".$newscat[$count][1]."|".$newscat[$count][2]."||");
			$count++;
		}
	}
	fclose($fp);
	unset($_GET['action']);
}

if($_POST['submit'] == "Add News" || $_POST['submit'] == "Edit News") {
			$autor = encode($_POST["autor"]);
			$email= encode($_POST["email"]);
			$titulo = encode($_POST["titulo"]);
			$texto = encode(stripslashes(stripslashes($_POST['texto'])));
			if($email=="") $email="-";
			if($titulo=="") $titulo="-";
			if($autor=="") $autor="-";
			if($texto=="") $texto="-";
			$cat=$_POST['cat'];
			$data = time();
			$array=readdata("news");
			if($_POST['submit'] == "Add News") {
				$i=0;
				$maxid=0;
				while($array[$i][0] != "") {
					if(strval($array[$i][0])>$maxid) $maxid=strval($array[$i][0]);
					$i++;
				}
				$maxid++;
				if(!$fp=fopen("data/news.dat","a")) die ($newsmessage[76]);
				fwrite($fp,"$maxid|$autor|$email|$titulo|$texto|$data|1|$cat||");
				$message=$newsmessage[75];
			} else {
				if(!$fp=fopen("data/news.dat","w")) die ($newsmessage[76]);
				$i=0;
				while($array[$i][0] != "") {
					if($_POST['reg']==$array[$i][0])
						fwrite($fp,$array[$i][0]."|$autor|$email|$titulo|$texto|$data|1|$cat||");
					else
						fwrite($fp,$array[$i][0]."|".$array[$i][1]."|".$array[$i][2]."|".$array[$i][3]."|".$array[$i][4]."|".$array[$i][5]."|".$array[$i][6]."|".$array[$i][7]."||");
					$i++;
				}
				$message=$newsmessage[71];
			}
			fclose($fp);
			unset($_GET['action']);
}

if($_POST['submit']=="deletecomment") {
			$comments=file_get_contents("data/comments.dat");
			unlink("data/comments.dat");
			unset($aaa);
			$aaa=explode("||",$comments);
			$fp=fopen("data/comments.dat","w");
			$count=0;
			while(trim($aaa[$count]) != "") {
				$comment=explode("|",trim($aaa[$count]));
				if($comment[3]!=$_POST['id'])
					fwrite($fp,$aaa[$count]."||");
				$count++;
			}
			fclose($fp);
}

function adminnews() {
	global $newsmessage, $message, $out;
	$row_cat=readdata("newscat");
	$row_db=readdata("news");
	$noticia_numero = $_GET['id'];
	switch($_GET['action']) {
	case "deletec":
		if(!$fp=fopen("data/newscat.dat","w")) die ($newsmessage[127]);
		$i_delc=0;
		while($row_cat[$i_delc][0]!="") {
			if(strval($row_cat[$i_delc][0])!=$noticia_numero)
				fwrite($fp,$row_cat[$i_delc][0]."|".$row_cat[$i_delc][1]."|".$row_cat[$i_delc][2]."||");
			$i_delc++;
		}
		fclose($fp);
		unset($row_cat);
		$row_cat=readdata("newscat");
		$message=$newsmessage[126];
		break;
	case "delete":
		if(!$fp=fopen("data/news.dat","w")) die ($newsmessage[76]);
		$i=0;
		while($row_db[$i][0] != "") {
			if(strval($row_db[$i][0])!=$noticia_numero)
				fwrite($fp,$row_db[$i][0]."|".$row_db[$i][1]."|".$row_db[$i][2]."|".$row_db[$i][3]."|".$row_db[$i][4]."|".$row_db[$i][5]."|".$row_db[$i][6]."|".$row_db[$i][7]."||");
			$i++;
		}
		fclose($fp);
		unset($row_db);
		$row_db=readdata("news");
		$message=$newsmessage[128];
		break;
	case "edit":
		$i=0;
		while($row_db[$i][0] != "") {
			if(strval($row_db[$i][0])==$noticia_numero) break;
			$i++;
		}
		break;
	case "editc":
		$i=0;
		while($row_cat[$i][0]!="") {
			if(strval($row_cat[$i][0])==$noticia_numero) break;
			$i++;
		}
	}
	$out.="<h2 class=\"LNE_title\">$newsmessage[72]</h2>\n";
	if($message!="") $out.="<h3 class=\"LNE_message\">$message</h3>\n";
	$out.="<script type=\"text/javascript\" src=\"js/richedit.js\"></script>\n";
	$out.="<div>";
	$out.="<form name=\"adicionar\" method=\"post\" action=\"\">\n
	<fieldset style=\"border: 0;\">\n<table style=\"border: 0;\">
	<tr><td>$newsmessage[16]:</td><td><input type='text' name='autor' value=\"";
	if($_GET['action']=="edit") $out.=decode($row_db[$i][1]);
	$out.="\" /></td></tr>\n
	<tr><td>$newsmessage[73]:</td><td><input type='text' name='email' value=\"";
	if($_GET['action']=="edit") $out.=decode($row_db[$i][2]);
	$out.="\" /></td></tr>\n
	<tr><td>$newsmessage[12]:</td><td><input type='text' name='titulo' value=\"".decode($row_db[$i][3])."\" /></td></tr>\n";
	$out.="<tr><td>$newsmessage[52]:</td><td><select name=\"cat\" >\n";
	$count=0;
	while($row_cat[$count][0]!="") {
		$out.='<option value="'.$row_cat[$count][0].'"';
		if($_GET['action']=="edit" && $row_db[$i][7]==$row_cat[$count][0]) $out.=' SELECTED';
		$out.='>'.decode($row_cat[$count][1])."&nbsp;</option>\n";
		$count++;
	}
	$out.="</select></td></tr>\n</table>\n";
	print $out;
	$out="";
	if($_GET['action']=="edit")
		editor(decode($row_db[$i][4]));
	else
		editor();
	if($_GET['action']=="edit") {
		$out.="<input type='hidden' name='reg' value='".$row_db[$i][0]."' />";
		$out.="<input type='hidden' name='submit' value='Edit News' />";
		$out.="<input type=\"submit\" onClick=\"rtoStore()\" name=\"aa\" value='$newsmessage[77]' />\n";
	} else {
		$out.="<input type='hidden' name='submit' value='Add News' />";
		$out.="<input type=\"submit\" onClick=\"rtoStore()\" name=\"aa\" value='$newsmessage[74]' />\n";
	}
	$out.="</fieldset></form>";
	$out.="<h3>$newsmessage[80]</h3>\n<table>\n";
	$i_edit=0;
	while($row_db[$i_edit][0] != "") {
		$out.="<tr><td><a href='".$_SERVER["SCRIPT_NAME"]."?do=news&amp;action=edit&amp;id=".$row_db[$i_edit][0]."'><img src=\"images/edit.png\" alt=\"edit\" style=\"align: left; border: 0;\" /></a></td><td><a href='".$_SERVER["SCRIPT_NAME"]."?do=news&amp;action=delete&amp;id=".$row_db[$i_edit][0]."'><img src=\"images/editdelete.png\" alt=\"delete\" style=\"align: left; border: 0;\" /></a></td><td><b>".decode($row_db[$i_edit][3])."</b></td><td>".strftime("%d/%m/%y - %I:%M %p", $row_db[$i_edit][5] + $fuso_s)."</td><td>$newsmessage[79]: ".$row_db[$i_edit][7]."</td></tr>\n";
		$i_edit++;
	}
	$out.="</table></div>\n";
	$out.="<br /><h2>$newsmessage[78]</h2>\n";
	$out.="<div><form name=\"form1\" method=\"post\" action=\"\"><fieldset style=\"border: 0;\"><table>\n";
	$out.="<tr><td>$newsmessage[50]</td><td><input type=\"text\" name=\"name\"";
	if($_GET['action']=="editc") $out.=" value=\"".decode($row_cat[$i][1])."\"";
	$out.=" /></td></tr>\n<tr><td>$newsmessage[67]</td><td><input type=\"text\" name=\"descr\"";
	if($_GET['action']=="editc") $out.=" value=\"".decode($row_cat[$i][2])."\"";
	$out.=" /></td></tr>\n";
	if($_GET['action']=="editc") $out.="<tr><td>$newsmessage[79]</td><td><input type=\"text\" name=\"newid\" value=\"".$row_cat[$i][0]."\" /></td></tr>";
	$out.="<tr><td></td><td>";
	$out.="<input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\" />\n";
	if($_GET['action']=="editc") {
		$out.="<input type=\"hidden\" name=\"newscat\" value=\"Edit Category\" />\n";
		$out.="<input type=\"submit\" name=\"aa\" value=\"$newsmessage[54]\" />\n";
	} else {
		$out.="<input type=\"hidden\" name=\"newscat\" value=\"Add Category\" />\n";
		$out.="<input type=\"submit\" name=\"aa\" value=\"$newsmessage[53]\" />\n";
	}
	$out.="</td></tr>\n</table></fieldset></form>\n";
	$out.="<h3>$newsmessage[78]</h3>\n";
	$out.="<table>\n";
	$i=0;
	while($row_cat[$i][0]!="") {
		$out.="<tr><td><a href='".$_SERVER["SCRIPT_NAME"]."?do=news&amp;action=editc&amp;id=".$row_cat[$i][0]."'><img src=\"images/edit.png\" alt=\"edit\" style=\"align: left; border: 0;\" /></a></td><td><a href='".$_SERVER["SCRIPT_NAME"]."?do=news&amp;action=deletec&amp;id=".$row_cat[$i][0]."'><img src=\"images/editdelete.png\" alt=\"delete\" style=\"align: left; border: 0;\" /></a></td><td><b>".decode($row_cat[$i][1])."</b></td><td>".decode($row_cat[$i][2])."</td><td>Id: ".$row_cat[$i][0]."</td></tr>\n";
		$i++;
	}
	$out.="</table>\n</div><br />";
}


?>
