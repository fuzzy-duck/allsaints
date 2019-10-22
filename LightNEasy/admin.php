<?php
/*++++++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.LightNEasy.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| admin.php admin functions module
| Version 2.5 Mini
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/
function treat_posts() {
	global $message, $edit, $editextra, $langmessage, $set, $pagenum, $menu, $admintemplate;

	if(isset($_POST['return'])) {
		unset($_GET['do']);
		unset($_POST['submit']);
		$edit=0;
		$editextra=0;
	}

	switch($_POST['submit']) {
		case "Save":
			$fp=fopen("data/".$_POST['pagenum'].".html","w");
			fwrite($fp,encode(stripslashes($_POST['texto'])));
			fclose($fp);
			@chmod("data/".$_POST['pagenum'].".html", 0777);
			$pages=readdata("pages");
			$count=0;
			while($pages[$count][0] != "") {
				if($pages[$count][0] == $pagenum) {
					if($_POST['description'] != "")
						$pages[$count][1] = encode($_POST['description']);
					else
						$pages[$count][1] = "-";
					if($_POST['template'] != "")
						$pages[$count][2] = $_POST['template'];
					else
						$pages[$count][2] = "-";
					break;
				}
				$count++;
			}
			$fp=fopen("data/pages.dat","w");
			$count=0;
			while($pages[$count][0] != "") {
				fwrite($fp,$pages[$count][0]."|".$pages[$count][1]."|".$pages[$count][2]."||\n");
				$count++;
			}
			fclose($fp);
			unset($_GET['do']);
			$message=$langmessage[102];
			readmenu();
			break;
		case "Save Extra":
			$fp=fopen("data/extra.htm","w");
			fwrite($fp,encode(stripslashes($_POST['texto'])));
			fclose($fp);
			unset($_GET['do']);
			$message=$langmessage[102];
			break;
		case "Save Menu":
			$fp=fopen("data/menu.dat","w");
			fwrite($fp,encode($_POST['content']));
			fclose($fp);
			unset($_GET['do']);
			$message=$langmessage[134];
			readmenu();
			break;
		case "Delete Page":
			$count=0;
			$fp=fopen("data/menu.dat","w");
			while($menu[$count][0] != "") {
				if($menu[$count][3] != $pagenum)
					fwrite($fp,$menu[$count][0]."|".$menu[$count][1]."|".$menu[$count][2]."|".$menu[$count][3]."|".encode($menu[$count][4])."||\n");
				$count++;
			}
			fclose($fp);
			unlink("./data/$pagenum.html");
			@unlink($pagenum.".php");
			unlink("./data/pages.dat");
			$fp=(fopen("./data/pages.dat","w"));
			foreach($menu as $men) {
				if($men[3] != $pagenum && $men[3]!="") {
                    if($men[5] == "") $men[5] = "-" ;
                    if($men[6] == "") $men[6] = "-" ;
					fwrite($fp,encode($men[3]."|".$men[5]."|".$men[6]."||\n"));
				}
			}
			fclose($fp);
			unset($_GET['do']);
			$pagenum="index";
			readmenu();
			$message=$langmessage[104];
			break;
		case "Save Setup":
			if(!$fp=fopen("data/config.php","w")) die ($langmessage[55]);
			fwrite($fp,"<?php\n");
			if($_POST['password']!="")
				fwrite($fp,"\$set['password']=\"".sha1($_POST['password'])."\";\n");
			else
				fwrite($fp,"\$set['password']=\"".$_POST['oldpassword']."\";\n");
			fwrite($fp,"\$set[\"homepath\"]=\"".$_POST['homepath']."\";\n");
			fwrite($fp,'$set[\'template\']="'.$_POST['template'].'";'."\n");
			fwrite($fp,'$set[\'title\']="'.encode($_POST['title']).'";'."\n");
			fwrite($fp,'$set[\'subtitle\']="'.encode($_POST['subtitle']).'";'."\n");
			fwrite($fp,'$set[\'keywords\']="'.encode($_POST['keywords']).'";'."\n");
			fwrite($fp,'$set[\'description\']="'.encode($_POST['description']).'";'."\n");
			fwrite($fp,'$set[\'author\']="'.encode($_POST['author']).'";'."\n");
			fwrite($fp,'$set[\'footer\']="'.encode($_POST['footer']).'";'."\n");
			fwrite($fp,'$set[\'openfield\']="'.$_POST['openfield'].'";'."\n");
			fwrite($fp,'$set[\'closefield\']="'.$_POST['closefield'].'";'."\n");
			fwrite($fp,'$set[\'gzip\']="'.$_POST['gzip'].'";'."\n");
			fwrite($fp,'$set[\'timeoffset\']="'.$_POST['timeoffset'].'";'."\n");
			fwrite($fp,'$set[\'indexfile\']="'.encode($_POST['indexfile']).'";'."\n");
			fwrite($fp,'$set[\'fromname\']="'.encode($_POST['fromname']).'";'."\n");
			fwrite($fp,'$set[\'fromemail\']="'.$_POST['fromemail'].'";'."\n");
			fwrite($fp,'$set[\'toemail\']="'.$_POST['toemail'].'";'."\n");
			fwrite($fp,'$set[\'language\']="'.$_POST['language'].'";'."\n");
			fwrite($fp,'$set[\'catchpa\']="'.$_POST['catchpa'].'";'."\n");
			fwrite($fp,'$set[\'dateformat\']="'.$_POST['dateformat'].'";'."\n");
			fwrite($fp,'$set[\'newspage\']="'.$_POST['newspage'].'";'."\n");
			fwrite($fp,"?>\n");
			fclose($fp);
			unset($_GET['do']);
			readsetup();
			break;
		case "Create Page":
			$aaa=decode(file_get_contents("./data/menu.dat"));
			unset($mmenu);
			$mmenu=explode("||",$aaa);
			$count=0;
			foreach($mmenu as $mmmenu) {
				$menu[$count]=explode("|",trim($mmmenu));
				$count++;
			}
			$count=0;
			$fp=fopen("./data/menu.dat","w");
			$inc=0;
			while($menu[$count][0] != "") {
				$aa=$menu[$count][0]."|".$menu[$count][1]."|".$menu[$count][2]."|".$menu[$count][3]."|".encode($menu[$count][4])."||\n";
				if($count == strval($_POST['count'])) {
					fwrite($fp,$aa);
					switch($_POST['level']) {
						case "1":
							$bb=strval($menu[$count][0])+1;
							$aa=$bb."|0|0|".trim($_POST['filename'])."|".encode($_POST['label'])."||\n";
							$inc=1;
							break;
						case "2":
							$bb=strval($menu[$count][1])+1;
							$aa=$menu[$count][0]."|".$bb."|0|".trim($_POST['filename'])."|".encode($_POST['label'])."||\n";
							$inc=2;
							break;
						case "3":
							$bb=strval($menu[$count][2])+1;
							$aa=$menu[$count][0]."|".$menu[$count][1]."|".$bb."|".trim($_POST['filename'])."|".encode($_POST['label'])."||\n";
							$inc=3;
							break;
					}
					fwrite($fp,$aa);
				} else {
					if($inc) {
						switch($inc) {
							case 1: {
								if($menu[$count][0]>=$bb)
									$aa=strval($menu[$count][0]+1)."|".$menu[$count][1]."|".$menu[$count][2]."|".$menu[$count][3]."|".encode($menu[$count][4])."||\n";
								break;
							}
							case 2: {
								if($menu[$count][0]==$a && $menu[$count][1]>=$bb)
									$aa=$menu[$count][0]."|".strval($menu[$count][1]+1)."|".$menu[$count][2]."|".$menu[$count][3]."|".encode($menu[$count][4])."||\n";
								break;
							}
							case 3: {
								if($menu[$count][0]==$a && $menu[$count][1]==$b && $menu[$count][2]>=$bb)
									$aa=$menu[$count][0]."|".$menu[$count][1]."|".strval($menu[$count][2]+1)."|".$menu[$count][3]."|".encode($menu[$count][4])."||\n";
								break;
							}
						}
					}
					fwrite($fp,$aa);
				}
				$count++;
			}
			fclose($fp);
			$fp=fopen("./data/pages.dat","a");
			fwrite($fp,encode(trim($_POST['filename'])."|-|-||\n"));
			fclose($fp);
			$message=$langmessage[87];
			unset($_GET['do']);
			$selected['name']=$_POST['label'];
			$pagenum=$_POST['filename'];
			unset($_POST);
			readmenu();
			break;
	}

	if(isset($_GET['do']) && $_GET['do']!="login" && $_GET['do']!="sitemap")
		$admintemplate=true;
	switch($_GET['do']) {
		case "edit":
			$edit=0;
			if($_SESSION[$set['password']]=="1") $edit=1;
			else unset($_GET['do']);
			break;
		case "editextra":
			$editextra=0;
			if($_SESSION[$set['password']]=="1") $editextra=1;
			else unset($_GET['do']);
			break;
		case "logout":
			unset($_SESSION[$set['password']]);
			session_destroy();
			unset($_GET['do']);
			$saida="Location: ".$set['homepath'];
			header($saida);
	}
}

function generate() {
	global $edit, $langmessage, $set, $pagenum, $menu, $templatepath, $selected, $LNEversion, $addons;
	$edit=0;
	$count=0;
	$defaulttempl=$set['template'];
	readmenu();
	while($menu[$count][0]!="") {
		$out="";
		$pagenum=$menu[$count][3];
		if(strval(strstr($pagenum, "#")))
			$pagenum=str_replace("#", "",$pagenum);
		$templ="<?php\n\t\$pagenum=\"$pagenum\";\n\tinclude(\"LightNEasy/runtime.php\");\n?>\n";
		$cntnt=stripslashes(file_get_contents("data/".$pagenum.".html"));
		if($menu[$count][6] != "-") {
			$templ.=file_get_contents("templates/".$menu[$count][6]."/template.php");
			$selected['template']=$menu[$count][6];
		} else {
			$templ.=file_get_contents("templates/".$set['template']."/template.php");
			$selected['template']=$defaulttempl;
		}
		if(!strval(strstr($menu[$count][3], "*"))) {
			$page=$templ;
			$selected['index']=$menu[$count][0];
			$selected['name']=$menu[$count][4];
			$selected['descr']=$menu[$count][5];
			while($page != "") {
				if($pagearray=explode($set['openfield'],$page,2)) {
					$out.=$pagearray[0];
					$page=$pagearray[1];
					if($pagearray=explode($set['closefield'],$page,2)) {
						$command=trim($pagearray[0]);
						$page=$pagearray[1];
						switch($command) {
							case "content": $out.="<?php content(); ?>"; break;
							case "search": $out.="<?php print searchform(); ?>"; break;
							case "extra": $out.="<?php extra(); ?>"; break;
							case "mainmenu": $out.= mainmenu(1); break;
							case "mainmenu1": $out.= mainmenu(1,1); break;
							case "mainmenu2": $out.= mainmenu(1,2); break;
							case "fullmenu": $out.=fullmenu(1); break;
							case "expmenu": $out.= expmenu(1); break;
							case "submenu": $out.= submenu(1); break;
							case "treemenu": $out.= treemenu(1); break;
							case "selected": $out.=$selected['name']; break;
							case "footer": $out.= $set['footer']." - <a href=\"http://lightneasy.org\">LightNEasy ".$LNEversion."</a>"; break;
							case "header": $out.= printheader(1,$menu[$count][6]); break;
							case "homelink": $out.='<a href="'.$set['homepath'].'">'.$langmessage[111].'</a>'; break;
							case "image": $out.="templates/".$set['template']."/images/"; break;
							case "subtitle": $out.= $set['subtitle']; break;
							case "sitemap": $out.= sitemap(1); break;
							case "title": $out.='<a href="'.$set['homepath'].'">'.$set['title'].'</a>'; break;
							case "login": $out.="<a href=\"?do=login\" rel=\"nofollow\">Login</a>"; break;
							default: 
								if(strpos($command, "plugin")!== false) {
									$aa=explode(" ",$command,2);
									$pluginpath="plugins/".trim($aa[1]);
									if(file_exists($pluginpath."/first.mod"))
										$out=file_get_contents($pluginpath."/first.mod").$out;
									if(file_exists($pluginpath."/header.mod"))
										$out=str_replace("</head>",file_get_contents($pluginpath."/header.mod")."\n</head>",$out);
									if(file_exists($pluginpath."/onload.mod"))
										$out=str_replace("<body","<body onload=\"".file_get_contents($pluginpath."/onload.mod")."\"",$out);
									if(file_exists($pluginpath."/include.mod"))
										$out.="<?php include \"plugins/".trim($aa[1])."/include.mod\"; ?>\n";
									if(file_exists($pluginpath."/place.mod"))
										$out.=file_get_contents("$pluginpath/place.mod");
								} else {
									$found=false;
									foreach($addons as $addon) {
										if(intval($addon[3]))
											if($command==$addon[0]) {
												$out.="<?php require_once \"addons/".$addon[0]."/main.php\"; print ".$addon[1]."(); ?>";
												$found=true;
//												break;
											} elseif(substr($command,0,strlen($addon[0])) == $addon[0]) {
												$found=true;
												$out.="<?php require_once \"addons/".$addon[0]."/main.php\"; ";
												$bb = trim(substr($command,strlen($addon[0])));
												$aa = explode(" ",$bb);
												if($aa[3] != "") $out.="print ".$addon[1]."('$aa[0]','$aa[1]','$aa[2]','$aa[3]')";
												elseif($aa[2]!="") $out.="print ".$addon[1]."('$aa[0]','$aa[1]','$aa[2]')";
												elseif($aa[1]!="") $out.="print ".$addon[1]."('$aa[0]','$aa[1]')";
												else $out.="print ".$addon[1]."('$aa[0]')";
												$out.="; ?>";
//												break;
											}
									}
									if(!$found) {
										$out.=$command;
									}
								}
						}
					}
				}
			}
			if(!$fp=fopen($pagenum.".php","w")) die ($langmessage[135].$menu[$count][3].".php");
			fwrite($fp,$out);
			if($page != "") fwrite($fp,$page);
			fclose($fp);
			@chmod($menu[$count][3].".php", 0755);
		}
		$count++;
	}
	unset($_SESSION[$set['password']]);
	session_destroy();
	unset($_GET['do']);
	header("Location: ".$set['homepath']);
}

function addons() {
	global $out, $langmessage, $message, $addons;
	$out.="<h2>".$langmessage[178]."</h2>\n";
	if($_GET['action']=="edit" && $_GET['name']!="") {
		if(!isset($_POST['submit'])) {
			foreach($addons as $addon) {
				if($addon[0]==$_GET['name']) {
					$out.="<form id=\"form1\" name=\"form1\" method=\"post\" action=\"\">\n<table>\n";
					$out.="<tr><td align=\"right\">Name:</td><td><input type=\"text\" name=\"name\" value=\"".$addon[0]."\" /></td></tr>\n";
					$out.="<tr><td align=\"right\">Function name:</td><td><input type=\"text\" name=\"fname\" value=\"".$addon[1]."\" /></td></tr>\n";
					$out.="<tr><td align=\"right\">Admin name:</td><td><input type=\"text\" name=\"aname\" value=\"".$addon[2]."\" /></td></tr>\n";
					$out.="<tr><td align=\"right\">Active:</td><td><input type=\"text\" name=\"active\" value=\"".$addon[3]."\" /></td></tr>\n";
					$out.="<tr><td align=\"right\">Header:</td><td><input type=\"text\" name=\"header\" value=\"".$addon[4]."\" /></td></tr>\n";
					$out.="<tr><td></td><td><input type=\"submit\" name=\"submit\" value=\"Submit\" /></td></tr>\n";
					$out.="</table>\n</form>\n";
				}
			}
		} else {
			if(!$fp=fopen("data/addons.dat","w"))
				die ($langmessage[135]." addons.dat");
			foreach($addons as $addon) {
				if($addon[0]!="") 
					if($addon[0]!=$_POST['name'])
						fwrite($fp, $addon[0]."|".$addon[1]."|".$addon[2]."|".$addon[3]."|".$addon[4]."||\n");
					else
						fwrite($fp, $_POST['name']."|".$_POST['fname']."|".$_POST['aname']."|".$_POST['active']."|".$_POST['header']."||\n");
			}
			fclose($fp);
			$out.="<h3>Addon ".$_POST['name']." updated</h3>\n";
		}
	} else {
		if(!isset($_POST['submit'])) {
			$out.="<form id=\"form1\" name=\"form1\" method=\"post\" action=\"\">\n<table>\n";
			$out.="<tr><td align=\"right\">Name:</td><td><input type=\"text\" name=\"name\" value=\"\" /></td></tr>\n";
			$out.="<tr><td align=\"right\">Function name:</td><td><input type=\"text\" name=\"fname\" value=\"\" /></td></tr>\n";
			$out.="<tr><td align=\"right\">Admin name:</td><td><input type=\"text\" name=\"aname\" value=\"\" /></td></tr>\n";
			$out.="<tr><td align=\"right\">Active:</td><td><input type=\"text\" name=\"active\" value=\"\" /></td></tr>\n";
			$out.="<tr><td align=\"right\">Header:</td><td><input type=\"text\" name=\"header\" value=\"\" /></td></tr>\n";
			$out.="<tr><td></td><td><input type=\"submit\" name=\"submit\" value=\"Add Addon\" /></td></tr>\n";
			$out.="</table>\n</form>\n";
		} else {
			if(!$fp=fopen("data/addons.dat","a"))
				die ($langmessage[135]." addons.dat");
			$record=$_POST['name']."|".$_POST['fname']."|".$_POST['aname']."|".$_POST['active']."|".$_POST['header']."||\n";
			fwrite($fp,$record);
			$out.="<h3>Addon ".clean($_POST['name'])." added</h3>\n";
		}
	}
	$found=false;
	foreach($addons as $addon) {
		if($addon[0]!="") {
		if(!$found) {
			$found=true;
			$out.="<div id=\"LNE_admininput\">\n<table>\n";
		}
		$out.="<tr><td>".$addon[0]."</td><td align=\"middle\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=addons&amp;action=edit&amp;name=".$addon[0]."\">\n";
		$out.="<img src=\"images/edit.png\" alt=\"edit\" title=\""."Edit"."\"align=\"left\" border=\"0\" /></a></td><td>";
		if(intval($addon[3]))
			$out.="<img src=\"images/accept.png\" alt=\"active\" title=\"Active\" border=\"0\" align=\"center\" />";
		$out.="</td></tr>\n";
		}
	}
	if($found)
		$out.="</table>\n</div>\n";
}

function plugins () {
$out="<h2>Plugin administration</h2><br />\n";
if(isset($_GET['src'])) {
	include $_GET['src'];
} else {
	$folders=filelist ( "/./", "plugins", 1);
	$achou=false;
	foreach($folders as $folder) {
		if($achou==false) {
			$out.="<ul>\n";
			$achou=true;
		}
		$out.="<li>";
		if(file_exists("plugins/$folder/setup.mod"))
			$out.="<a href=\"?do=plugins&src=plugins/$folder/setup.mod\"><img src=\"images/toolss.png\" alt=\"setup\" title=\"Setup plugin\" /></a>&nbsp;".$folder;
		else
			$out.=$folder;
		$out.="</li>\n";
	}
	if($achou) $out.="</ul>\n";
}
return $out;
}

function createform() {
	global $langmessage, $menu;
	$out='<br /><br /><div align="center"><form method="post" action=""><h2>'.$langmessage[86].'</h2>';
	$out.='<table><tr><td align="right"><b>'.$langmessage[83].':</b></td><td><select name="level"><OPTION VALUE="1">1&nbsp;</OPTION>';
	$out.='<OPTION VALUE="2">2&nbsp;</OPTION><OPTION VALUE="3">3&nbsp;</OPTION></td></tr>';
	$out.='<tr><td align="right"><b>'.$langmessage[140].':</b></td><td><select name="count">';
	$count=0;
	while($menu[$count][0] != "") {
		$out.='<OPTION VALUE="'.$count.'"';
		$out.=' SELECTED >';
		if($menu[$count][2]!="0")
			$out.=".. ";
		elseif($menu[$count][1]!="0")
			$out.=". ";
		$out.=$menu[$count][4].'&nbsp;</OPTION>';
		$count++;
	}
	$out.='</select></td></tr>';
	$out.='<tr><td align="right"><b>'.$langmessage[84].':</b></td><td><input name="filename" type="text" /></td></tr>';
	$out.='<tr><td align="right"><b>'.$langmessage[85].':</b></td><td><input name="label" type="text" /></td></tr>';
	$out.='<tr><td></td><td>';
	$out.='<input type="hidden" name="submit" value="Create Page" />';
	$out.='<input type="submit" name=\"aa\" value="'.$langmessage[86].'" />';
	$out.='</td></tr></table></form></div>';
	return $out;
}

function setup() {
	global $set, $langmessage;
	$out="<h2 class=\"LNE_title\">$langmessage[130]</h2>\n";
	$out.="<form method=\"post\" action=\"\" id=\"setupform\">\n<div align=\"center\"><fieldset>\n<table>\n";
	$out.="<tr><td align=\"right\">$langmessage[6]:</td>\n";
	$out.="<td><input type=\"text\" name=\"password\" value=\"\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[10]:</td>\n";
	$out.="<td><input type=\"text\" name=\"homepath\" value=\"".$set['homepath']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[11]:</td>\n<td><select name=\"template\">\n";
	$folder="templates";
	$files=filelist('/./',$folder,1);
	foreach( $files as $file) {
//		$file=substr($file,10);
		if($file != ".." && $file != ".") {
		    $out.='<OPTION VALUE="'.$file.'"';
		    if($file == $set['template']) $out.=' SELECTED';
		    $out.='>'.$file."&nbsp;</OPTION>\n";
		}
	}
	$out.="</select>\n</td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[12]:</td><td><input type=\"text\" name=\"title\" value=\"".$set['title']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[13]:</td><td><input type=\"text\" name=\"subtitle\" value=\"".$set['subtitle']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[14]:</td><td><textarea name=\"keywords\" rows=\"4\">".$set['keywords']."</textarea></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[15]:</td><td><textarea name=\"description\" rows=\"4\">".$set['description']."</textarea></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[16]:</td><td><input type=\"text\" name=\"author\" value=\"".$set['author']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[17]:</td><td><input type=\"text\" name=\"footer\" value=\"".$set['footer']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[145]:</td><td><input type=\"text\" name=\"timeoffset\" value=\"".$set['timeoffset']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[151]:</td><td><input type=\"text\" name=\"dateformat\" value=\"";
		if($set['dateformat']!="")
			$out.=$set['dateformat'];
		else
			$out.="%m/%d/%y - %I:%M %p";
	$out.="\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">Open tag:</td><td><input type=\"text\" name=\"openfield\" value=\"".$set['openfield']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">Close tag:</td><td><input type=\"text\" name=\"closefield\" value=\"".$set['closefield']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[19]:</td><td><input type=\"text\" name=\"indexfile\" value=\"".$set['indexfile']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[57]:</td><td><input type=\"text\" name=\"newspage\" value=\"".$set['newspage']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[40]:</td><td><input type=\"text\" name=\"fromname\" value=\"".$set['fromname']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[48]:</td><td><input type=\"text\" name=\"fromemail\" value=\"".$set['fromemail']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[56]:</td><td><input type=\"text\" name=\"toemail\" value=\"".$set['toemail']."\" /></td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[20]:</td>\n<td><select name=\"language\">\n";
	$folder="languages";
	$files=filelist('/./',$folder);
	foreach( $files as $file) {
		if(!is_dir($file) && strpos($file,".php")) {
			$out.='<OPTION VALUE="'.substr($file,5,5).'"';
			if(substr($file,5,5) == $set['language']) $out.=' SELECTED';
			$out.='>'.$file."&nbsp;</OPTION>\n";
		}
	}
	$out.="</select>\n</td></tr>\n";
	$out.="<tr><td align=\"right\">$langmessage[32]:</td>\n<td><select name=\"catchpa\">\n";
	$out.="<OPTION VALUE=\"1\">image&nbsp;</OPTION>\n";
	$out.="<OPTION VALUE=\"0\"";
	if($set['catchpa']==0) $out.=" SELECTED";
	$out.=">text&nbsp;</OPTION>\n";
	$out.="</select>\n</td></tr>\n";
	$out.="<tr><td><input type=\"hidden\" name=\"submit\" value=\"Save Setup\" />\n";
	$out.="<input type=\"hidden\" name=\"gzip\" value=\"0\" />";
	$out.="<input type=\"hidden\" name=\"oldpassword\" value=\"".$set['password']."\" /></td>";
	$out.="<td><input type=\"submit\" name=\"aa\" value=\"$langmessage[25]\" /></td></tr>\n</table>\n</div>\n</fieldset></form>\n";
	return $out;
}

function deleteform() {
	global $langmessage, $selected, $pagenum, $out;
	$out.="<div align=\"center\"><form method=\"post\" action=\"\"><h2>".$langmessage[131].$selected['name']."?</h2>\n";
	$out.="<fieldset><input type=\"hidden\" name=\"submit\" value=\"Delete Page\" />\n";
	$out.="<input type=\"submit\" name=\"aa\" value=\"$langmessage[136]\" />";
	$out.="</fieldset></form></div>\n";
	$out.=showcontent($pagenum);
}

function editmenu() {
	global $langmessage;
	$out="<br /><br /><div align=\"center\">\n";
	$out.="<form method=\"post\" action=\"\">\n<h2 class=\"LNE_title\">$langmessage[91]</h2>\n";
	$out.="<textarea id=\"content\" name=\"content\" rows=\"15\" cols=\"50\">\n";
	$out.=decode(file_get_contents("data/menu.dat"))."</textarea>\n";
	$out.="<input type=\"hidden\" name=\"submit\" value=\"Save Menu\" />\n";
	$out.="<input type=\"submit\" name=\"aa\" value=\"$langmessage[133]\" />\n";
	$out.="</form></div>\n";
	return $out;
}

function settings() {
	global $set, $langmessage, $pagenum, $addons;
	$out.="<h2>$langmessage[34]</h2><br /><div id=\"LNE_admininput\">\n";
	$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=create\"><img src=\"images/addpage.png\" alt=\"$langmessage[35]\" title=\"$langmessage[35]\" style=\"border: 0;\" /></a><br />".$langmessage[35]."</div>\n";
	$out.="<div class=\"LNE_settings\">\n<a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=editextra\"><img src=\"images/extra.png\" alt=\"$langmessage[36]\" title=\"$langmessage[36]\" style=\"border: 0;\" /></a><br />".$langmessage[36]."</div>\n";
	$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=editmenu\"><img src=\"images/menu.png\" alt=\"$langmessage[41]\" title=\"$langmessage[41]\" style=\"border: 0;\" /></a><br />$langmessage[41]</div>\n";
	$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=plugins\"><img src=\"images/plugins.png\" alt=\"$langmessage[177]\" title=\"$langmessage[177]\" style=\"border: 0;\" /></a><br />$langmessage[177]</div>\n";
	$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=addons\"><img src=\"images/add.png\" alt=\"$langmessage[178]\" title=\"$langmessage[178]\" style=\"border: 0;\" /></a><br />$langmessage[178]</div>\n";
	$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=setup\"><img src=\"images/setup.png\" alt=\"$langmessage[44]\" title=\"$langmessage[44]\" style=\"border: 0;\" /></a><br />$langmessage[44]</div>\n";
	require_once "addons/lang_en_US.php";
	foreach($addons as $addon) {
		if($addon[2]!="-" && $addon[0]!="")
			$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=".$addon[0]."\"><img src=\"addons/".$addon[0]."/icon.png\" alt=\"".$addon[0]."\" title=\"".$$addon[0]."\" style=\"border: 0;\" /></a><br />".$$addon[0]."</div>\n";
	}
	$out.="<div class=\"LNE_settings\"><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=generate\"><img src=\"images/generate.png\" alt=\"$langmessage[42]\" title=\"$langmessage[42]\" style=\"border: 0;\" /></a><br />$langmessage[42]</div>\n";
	$out.="</div>\n";
	return $out;
}

function editextra() {
	global $langmessage;
	print "<h2 class=\"LNE_title\">$langmessage[89]</h2>\n<form method=\"post\" action=\"\">\n";
	if(!file_exists("data/extra.htm")) {
	    $fp=fopen("data/extra.htm","w");
	    fclose($fp);
	}
	editor(stripslashes(file_get_contents("data/extra.htm")));
	print savereturn("Save Extra");
}

function editpage($pagenum) {
	global $langmessage,$selected, $set;
	print "<form name=\"f1\" method=\"post\" action=\"\">\n";
	editor(decode(file_get_contents("data/".$pagenum.".html")));
	print "<input  type=\"hidden\" name=\"pagenum\" value=\"$pagenum\" />\n";
	print "<b>".$langmessage[67]."</b>: <input style=\"width: 100%; height: 40px;\" type=\"text\" name=\"description\" value=\"";
	if($selected['descr'] !="-")
		print $selected['descr'];
	print "\" />\n";
	print "<table><tr><td align=\"right\"><b>$langmessage[11]:</b></td><td><select name=\"template\">\n";
	$folder="templates";
	print "<OPTION VALUE=\"\">Default</OPTION>\n";
	$dir=opendir($folder);
	while($file=readdir($dir)) {
		if($file != ".." && $file != "." && is_dir($folder."/".$file)) {
			print '<OPTION VALUE="'.$file.'"';
//			if($set['template']==$file) print " SELECTED";
			print '>'.$file."&nbsp;</OPTION>\n";
		}
	}
	closedir($dir);
	print "</select></td></tr></table>\n";
	print savereturn("Save");
}

function editor($out="") {
	print "<div id=\"LNE_editor\">\n";
	print "<script language=\"JavaScript\" type=\"text/javascript\" src=\"js/wysiwyg.js\"></script>\n";
	print "<script language=\"JavaScript\" type=\"text/javascript\" src=\"js/wysiwyg-settings.js\"></script>\n";
	print "<script language=\"JavaScript\">\n";
	print "WYSIWYG.attach('textarea1', full);\n";
	print "</script>\n";
	print "<textarea id=\"textarea1\" name=\"texto\" style=\"width: 100%; height: 200px; \">$out</textarea>\n";
	print "</div><br />\n";
}

/* outputs the icons for save/return */
function savereturn($value) {
	$out.="<input type=\"hidden\" name=\"submit\" value=\"$value\" />\n";
	$out.="<table><tr><td valign=\"top\">";
	$out.="<input type=\"image\" name=\"aa\" value=\"\" src=\"images/accept.png\" onClick=\"rtoStore()\" style=\"width: 32px; height: 32px;\" /></form>";
	$out.="<form method=\"post\" action=\"\">\n<fieldset style=\"border: none; background: transparent;\">\n</td>";
	$out.="<td valign=\"top\"><input type=\"hidden\" name=\"return\" value=\"Return\" />\n";
	$out.="<input type=\"image\" name=\"aa\" value=\"Return\" src=\"images/back.png\" value=\"\" style=\"width: 32px; height: 32px;\" />\n</fieldset></form>\n</td>";
	$out.="</tr></table>";
	return $out;
}

function adminmenu() {
	global $set,$pagenum;
	$aa="";
	if($_SESSION[$set['password']]=="1") {
		$aa.="\n<div id=\"LNE_admin\">\n<table>\n<tr>\n";
		$aa.="<td><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=edit\">\n";
		$aa.="<img src=\"images/edit.png\" alt=\"edit\" align=\"left\" border=\"0\" /></a></td>\n";
		$aa.="<td><a href=\"".$set['homepath'].$set['indexfile']."?page=".$pagenum."&amp;do=delete\">";
		$aa.="<img src=\"images/editdelete.png\" alt=\"delete\" align=\"left\" border=\"0\" /></a></td>\n";
		$aa.="<td><a href=\"".$set['homepath'].$set['indexfile']."?do=settings\">";
		$aa.="<img src=\"images/tools.png\" alt=\"Settings\" align=\"left\" border=\"0\" /></a></td>\n";
		$aa.="</tr>\n</table>\n</div>\n";
	}
	return $aa;
}
?>
