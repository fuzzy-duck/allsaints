<?php
/*++++++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://Lightneasy.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| lightneasy.php main file
| Version 2.5 Mini
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

session_start();
clearstatcache();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Redirects to install.php if data files don't exist
if(!file_exists("data/config.php")) header ("Location: LightNEasy/install.php");

// Detects the insertion of code in the $_GET array
foreach ($_GET as $check_url) {
	if ((eregi("<[^>]*script*\"?[^>]*>", $check_url)) || (eregi("<[^>]*object*\"?[^>]*>", $check_url)) || (eregi("<[^>]*iframe*\"?[^>]*>", $check_url)) || (eregi("<[^>]*applet*\"?[^>]*>", $check_url)) || (eregi("<[^>]*meta*\"?[^>]*>", $check_url)) || (eregi("<[^>]*style*\"?[^>]*>", $check_url)) || (eregi("<[^>]*form*\"?[^>]*>", $check_url)) || (eregi("\([^>]*\"?[^)]*\)", $check_url)) || (eregi("\"", $check_url))) die ("Hijacking attempt, dying....");
}
unset($check_url);

// Global variable containing messages to the user;
$message="";

// Installs the common functions
require_once "LightNEasy/common.php";

$set = array();
readsetup();
if($set['language']=="") $set['language']="en_US";

if (!eregi($set['indexfile'], sv('PHP_SELF')) && !eregi('index.php', sv('PHP_SELF')))
	die ('Access Denied!');

// Checks if there was a login attempt
if($_POST['submit']=="Login") {
	if(sha1(trim($_POST['password']))==$set['password']) {
		$_SESSION[$set['password']]="1";
		$message=$langmessage[95];
		unset($_GET['do']);
	} else $message=$langmessage[96];
}

// Disables $_GET and $_POST if the user is not logged in, except for the allowed posts

// Disables $_GET except for login and sitemap
if($_GET['do']!="login" && $_GET['do']!="sitemap" && $_SESSION[$set['password']] != "1") unset($_GET['do']);


### LightNEay global variables: ###
// $set - settings
// $langmessage - the language file
// $message - general messages to the user


// edit these 2 following values to your convenience
$max_upload_file_size=2000000;
$max_upload_image_size=250000;

// $menu - contains the menu
$menu=array(array('m1','m2','m3','link','name','descr','template'));

// $selected - contains the information of the current page
$selected=array('index','m2','m3','link','name','descr','template');

// $pagenum - the file name of the current page
$pagenum=sanitize($_GET['page']);
if($pagenum=="") $pagenum="index";

$admintemplate=false;
// $out - String containing the page to be sent to the browser
$out="";

### End of global variables ###

switch($_POST['submit']) {
	case "login":
		if(sha1(trim($_POST['password']))==$set['password']) {
			$_SESSION[$set['password']]=1;
			unset($_GET['do']);
			$message=$langmessage[95];
		} else
			$message=$langmessage[96];
	default:
}

// Read the menu
readmenu();

// reads the admin functions if the user is logged in
if($_SESSION[$set['password']] == "1") {
	require_once "./LightNEasy/admin.php";
//call admin functions for treating inputs if logged in
	treat_posts();
} else
// Disables $_POST['submit'] except for login, send message and send comment
	if($_POST['submit']!="login" && $_POST['submit']!="Send message" && $_POST['submit']!="sendcomment" && $_SESSION[$set['password']] != "1") unset($_POST['submit']);


if($selected['template'] != "-")
	$templatepath="./templates/".$selected['template']."/template.php";
else
	$templatepath="./templates/".$set['template']."/template.php";
if (!file_exists($templatepath)) $templatepath="./templates/lightneasy/template.php";
if (!file_exists($templatepath)) die ($templatepath." ".$langmessage[109]);

if(file_exists("LightNEasy/install.php"))
	if(!@unlink("LightNEasy/install.php"))
		$message=$langmessage[24]."<br />".$message;

if($_GET['do']=="generate") generate();
//if($_GET['do']=="generate") $message="Function disabled";

if($admintemplate) {
	$selected['template']="admintemplate";
	$templatepath="./templates/".$selected['template']."/template.php";
}

### Create page for display ###

//Display the template and call the embebbed functions
$page=file_get_contents($templatepath);

$out="";
while($page != "") {
	if($pagearray=explode($set['openfield'],stripslashes($page),2)) {
		$out.=$pagearray[0];
		$page=$pagearray[1];
		if($pagearray=explode($set['closefield'],$page,2)) {
			$command=trim($pagearray[0]);
			$page=$pagearray[1];
			switch($command) {
				case "header": $out.= printheader(0); break;
				case "title": $out.='<a href="'.$set['homepath'].'">'.$set['title'].'</a>'; break;
				case "subtitle": $out.=$set['subtitle']; break;
				case "content": content(); break;
				case "homelink": $out.='<a href="'.$set['homepath'].'">Home</a>'; break;
				case "image": $out.="./templates/".$set['template']."/images/"; break;
				case "mainmenu": $out.= mainmenu(0); break;
				case "mainmenu1": $out.= mainmenu(0,1); break;
				case "mainmenu2": $out.= mainmenu(0,2); break;
				case "search": $out.=searchform(); break;
				case "treemenu": $out.=treemenu(0); break;
				case "submenu": $out.=submenu(0); break;
				case "fullmenu": $out.= fullmenu(0); break;
				case "expmenu": $out.= expmenu(0); break;
				case "sitemap": $out.= sitemap(0); break;
				case "selected": $out.= $selected['name']; break;
				case "login": $out.= loginout(); break;
				case "extra": extra(); break;
				case "footer": $out.= $set['footer']; break;
				default: 
					if(strpos($command, "content")!== false) {
						$aa=explode(" ",$command,2);
						showcontent($pagenum, decode("data/".$pagenum."_".$aa[1].".html"));
					} elseif(strpos($command, "plugin")!== false) {
						$aa=explode(" ",$command,2);
						$pluginpath="plugins/".trim($aa[1]);
						if(file_exists($pluginpath."/first.mod"))
							$out=file_get_contents($pluginpath."/first.mod").$out;
						if(file_exists($pluginpath."/header.mod"))
							$out=str_replace("</head>",file_get_contents($pluginpath."/header.mod")."\n</head>",$out);
						if(file_exists($pluginpath."/onload.mod"))
							$out=str_replace("<body","<body onload=\"".file_get_contents($pluginpath."/onload.mod")."\"",$out);
						if(file_exists($pluginpath."/include.mod"))
							include "plugins/".trim($aa[1])."/include.mod";
						if(file_exists($pluginpath."/place.mod"))
							$out.=file_get_contents("$pluginpath/place.mod");
					} else {
						$found=false;
						foreach($addons as $addon) {
							if($command==$addon[0] && strval($addon[3])) {
								$found=true;
								require_once "addons/".$addon[0]."/main.php";
								$out.=$addon[1]();
								break;
							} elseif(substr($command,0,strlen($addon[0])) == $addon[0] && strval($addon[3])) {
								$found=true;
								require_once "addons/".$addon[0]."/main.php";
								$bb = trim(substr($command, strlen($addon[0])));
								$aa = explode(" ",$bb);
								if($aa[3] != "") $out .= $addon[1](clean($aa[0]),clean($aa[1]),clean($aa[2]),clean($aa[3]));
								elseif($aa[2]!="") $out .= $addon[1](clean($aa[0]),clean($aa[1]),clean($aa[2]));
								elseif($aa[1]!="") $out .= $addon[1](clean($aa[0]),clean($aa[1]));
								else $out .= $addon[1](clean($aa[0]));
								break;
							}
						}
						if(!$found)
							$out .= $command;
					}
			}
		} else break;
	} else break;
}
if($page != "") $out.=$page;

// finished page creation, send it to the browser
print $out;

### Execution end ###

function content() {
  global $pagenum, $edit, $set, $selected, $message, $menu, $out, $langmessage, $LNEversion, $message, $addons;
  if($message!="") $out.="<h2 class=\"LNE_message\">".$message."</h2>\n";
  if(function_exists('adminmenu')) $out.=adminmenu();
	switch($_GET['do']) {
	case "search":
		$out.="<h2 class=\"LNE_title\">$langmessage[66]</h2>\n";
		$out.=search();
		break;
	case "addons":
		$out.=addons();
		break;
	case "plugins":
		$out.=plugins();
		break;
	case "download":
		$out.="<h2 class=\"LNE_title\">$langmessage[48]</h2>\n";
		$out.="<div align=\"center\">\n<h3>$langmessage[49]</h3>\n";
		edit("download");
		break;
	case "sitemap":
		$out.="<h2 class=\"LNE_title\">$langmessage[88]</h2>\n<p><ul>";
		$out.=fullmenu(0);
		$out.="</ul></p>";
		break;
	case "edit":
		print $out;
		$out="";
		editpage($pagenum);
		break;
	case "editextra":
		print $out;
		$out="";
		editextra();
		break;
	case "login":
		$out.= loginform();
		break;
	case "settings":
		$out.= settings();
		break;
	case "setup":
		$out.= setup();
		break;
	case "editmenu":
		$out.= editmenu();
		break;
	case "delete":
		deleteform();
		break;
	case "create":
		$out.= createform();
		break;
	default:
		$found=false;
		foreach($addons as $addon) {
			if($_GET['do']==$addon[0] && $_SESSION[$set['password']]=="1" && intval($addon[3])) {
				require_once "addons/".$addon[0]."/admin.php";
				$out.=$addon[2]();
				$found=true;
				break;
			}
		}
		if(!$found) {
			if(!file_exists("./data/".$pagenum.".html")) {
				$achou=false;
				foreach($menu as $men) {
					if($men[3]==$pagenum) {
						$achou=true;
						break;
					}
				}
				if($achou) {
					$fp=fopen("./data/".$pagenum.".html","w");
					fwrite($fp,"<h1 class=\"LNE_title\">".$men['4']."</h1>\n");
					fclose($fp);
					$out.=showcontent($pagenum);
				} else
					$out.="<h2>".$langmessage[116]."</h2>\n";
			} else
				$out.=showcontent($pagenum);
		}
	}
}

function extra() {
	global $out;
	if(file_exists("data/extra.htm")) $out.=showcontent($pagenum,"extra.htm");
}

function showcontent($pagenum,$file="") {
	global $out, $addons;
	$open="%!$";
	$close="$!%";
	if($file=="")
		$page=decode(file_get_contents("data/".$pagenum.".html"));
	else
		if(file_exists("data/".$file))
			$page=stripslashes(decode(file_get_contents("data/".$file)));
		else 
			$page="<h2>$langmessage[116]</h2>\n";
	while(strpos($page,$open)) {
		$pagearray=explode($open,$page,2);
		$out.=$pagearray[0];
		unset($pagearray1);
		$pagearray1=explode($close,$pagearray[1],2);
		if(substr($pagearray1[0],0,7)=="include") {
			print $out;
			$out="";
			include(trim(substr($pagearray1[0],7)));
		} elseif(substr($pagearray1[0],0,8)=="function") {
			$bb=trim(substr($pagearray1[0],8));
			$aa=explode(" ",$bb);
			if($aa[3]!="") $out.=$aa[0]($aa[1],$aa[2],$aa[3]);
			elseif($aa[2]!="") $out.=$aa[0]($aa[1],$aa[2]);
			elseif($aa[1]!="") $out.=$aa[0]($aa[1]);
			else $out.=$aa[0]();
		} elseif(substr($pagearray1[0],0,6)=="plugin") {
			$pluginame="./plugins/".clean(substr($pagearray1[0],6));
			if(file_exists($pluginame."/header.mod"))
				$out=str_replace("</head",file_get_contents($pluginame."/header.mod")."\n</head",$out);
			if(file_exists($pluginame."/first.mod"))
				include $pluginame."/first.mod";
			if(file_exists($pluginame."/onload.mod"))
				$out=str_replace("<body","<body onload=\"".file_get_contents($pluginame."/onload.mod")."\"",$out);
			if(file_exists("$pluginame/place.mod"))
				$out.=file_get_contents("$pluginame/place.mod");
			if(file_exists("$pluginame/include.mod")) {
				print $out;
				include "$pluginame/include.mod";
				$out="";
			}
		} else {
			$found=false;
			foreach($addons as $addon) {
				if(strval($addon[3])) {
					if($pagearray1[0]==$addon[0]) {
						$found=true;
						require_once "addons/".$addon[0]."/main.php";
						$out.=$addon[1]();
						break;
					} elseif(substr($pagearray1[0],0,strlen($addon[0])) == $addon[0]) {
						$found=true;
						require_once "addons/".$addon[0]."/main.php";
						$bb = trim(substr($pagearray1[0], strlen($addon[0])));
						$aa = explode(" ",$bb);
						if($aa[3] != "") $out .= $addon[1](clean($aa[0]),clean($aa[1]),clean($aa[2]),clean($aa[3]));
						elseif($aa[2]!="") $out .= $addon[1](clean($aa[0]),clean($aa[1]),clean($aa[2]));
						elseif($aa[1]!="") $out .= $addon[1](clean($aa[0]),clean($aa[1]));
						else $out.= $addon[1](clean($aa[0]));
						break;
					}
				}
			}
			if(!$found)
				$out .= "\n".$pagearray1[0]."\n";
		}
		$page = $pagearray1[1];
	}
	if($page!="") $out .= $page;
}

function mainmenu($generat, $span=0) {
	global $pagenum,$menu,$selected,$set;
	$aa="\n";
	$count=0;
	$first=true;
	while($menu[$count][0] != "") {
		if($menu[$count][1]=="0" && $menu[$count][2]=="0" && strpos($menu[$count][3],"#") === false) {
			$aa.='<li';
			if($first) {
				$first=false;
				$aa.=' class="first"';
			}
			$aa.='>';
			if($span==3) $aa.="<span>";
			$aa.='<a ';
			if($menu[$count][0]==$selected['index'])
				$aa.='class="selected" ';
			if(strpos($menu[$count][3],"*"))
				$aa.='href="'.str_replace("*", "",$menu[$count][3]).'">';
			else
				if($generat)
					$aa.='href="'.$menu[$count][3].".php\">";
				else
					$aa.='href="'.$set['indexfile'].'?page='.$menu[$count][3].'">';
			if($span==2) $aa.="<span>";
			$aa.=$menu[$count][4];
			if($span==1) $aa.="<span>";
			if($span==2 || $span==1) $aa.="</span>";
			$aa.="</a>";
			if($span==3) $aa.="</span>";
			$aa.="</li>\n";
		}
		$count++;
	}
	return $aa;
}

function treemenu($generat=0) {
    global $pagenum, $menu, $selected, $set;
    $replace_chars=array(" ", ",", ".", "/", "?", "!", "-", ";", "'");
//    $out='<ul class="menu2">'."\n";
    $intend=0;
    for($count=0;$menu[$count][0] != "";$count++) {
        #$out.='mira-intend'.$intend.'mira-changed'.$changed.'mira-count'.$count.'menudecountuno'.$menu[$count][1];
        $changed=false;
        if($menu[$count][1]!="0" && $intend<1) {
            $changed=true;
            $intend=1;
            $out.='<ul class="sub">'."\n";
        }
        if($menu[$count][2]!="0" && $intend<2) {
            $changed=true;
            $intend=2;
            $out.="<ul>"."\n";
        }
        if($menu[$count][2]=="0" && $intend==2) {
            $changed=true;
            $intend--;
            $out.="</ul></li>"."\n";
        }
        if($menu[$count][1]=="0" && $intend==1) {
            $changed=true;
            $intend--;
            $out.="</ul></li>"."\n";
        }
        if($menu[$count][1]=="0" && $menu[$count][2]=="0") {
            $out.='<li class="top"><a class="top_link" id="'.str_replace($replace_chars, "_", $menu[$count][4]).'" ';
        }
        else {
            if($menu[$count][2]=="0" && $menu[$count][1]==$menu[$count+1][1]) {
                $out.='<li><a class="fly" ';
            }
            else {
                $out.="<li><a ";
            }
        }
        #if($menu[$count][4]==$selected['name'])
        #   $out.= 'class="selected" ';
        if(strpos($menu[$count][3],"*"))
            $out.='href="'.str_replace("*", "",$menu[$count][3]).'">';
        elseif($generat)
            $out.="href=\"".$menu[$count][3].".php\">";
        else
            $out.="href=\"".$set['indexfile'].'?page='.$menu[$count][3]."\">";
        if($menu[$count][1]=="0" && $menu[$count][2]=="0") {
            if($menu[$count][0]==$menu[$count+1][0]) {
                $out.="<span class=\"down\">".$menu[$count][4]."</span></a>"."\n";
            }
            else {
                $out.="<span>".$menu[$count][4]."</span></a></li>"."\n";
            }
        }
        else {
            if($menu[$count][2]=="0" && $menu[$count][1]==$menu[$count+1][1]) {
                $out.=$menu[$count][4]."</a>"."\n";
            }
            else {
                $out.=$menu[$count][4]."</a></li>"."\n";
            }
        }
    }
    if($intend==1) {    
        $out.="</ul>";
    }
//    $out.="</ul>";
    return $out;
}

function expmenu($generat) {
	global $pagenum,$menu,$selected,$set;
	$count=0;
	$aa="\n";
	while($menu[$count][0] != "") {
		if(($menu[$count][1]=="0" && $menu[$count][2]=="0" || $menu[$count][0]==$selected['index']) && strpos($menu[$count][3],"#") === false) {
			$aa.='<li';
			if($menu[$count][2]!="0") $aa.=" class=\"LNE_menu_doubleintend\"";
			elseif($menu[$count][1]!="0") $aa.=" class=\"LNE_menu_intend\"";
			else $aa.=" class=\"LNE_menu\"";
			$aa.="><a ";
			if($menu[$count][4]==$selected['name']) $aa.='class="selected" ';
			if(strpos($menu[$count][3],"*")) $aa.='href="'.str_replace("*", "",$menu[$count][3]).'">';
			else
				if($generat) $aa.='href="'.$menu[$count][3].".php\">";
				else $aa.='href="'.$set['indexfile'].'?page='.$menu[$count][3].'">';
			$aa.=$menu[$count][4]."</a></li>\n";
		}
		$count++;
	}
	return $aa;
}

function submenu($generat) {
	global $pagenum,$menu,$selected,$set;
	$count=0;
	while($menu[$count][0] != "") {
		if($menu[$count][3]==$pagenum) {
			$m1=$menu[$count][0];
			$m2=$menu[$count][1];
			$m3=$menu[$count][2];
			if($m3) $sub=3;
			elseif($m2) $sub=2;
			else $sub=1;
			break;
		}
		$count++;
	}
	$count=0;
	$aa="\n";
	while($menu[$count][0] != "") {
		if(strpos($menu[$count][3],"#") === false) {
		if($menu[$count][0]==$selected['index'] && ($menu[$count][1]!="0" || $menu[$count][2]!="0")) {
			if(($sub==1 && $menu[$count][2]==0) || ($sub==2 && $menu[$count][0]==$m1) || ($sub==3 && $menu[$count][1]==$m2 && $menu[$count][0]==$m1)) {
			$aa.='<li><a ';
			if($menu[$count][4]==$selected['name']) $aa.='class="selected" ';
			if(strpos($menu[$count][3],"*")) $aa.='><a href="'.str_replace("*", "",$menu[$count][3]).'">'.$menu[$count][4].'</a></li>';
			else
				if($generat) $aa.="href=\"".$menu[$count][3].".php\">".$menu[$count][4].'</a></li>';
				else $aa.='href="'.$set['indexfile'].'?page='.$menu[$count][3].'">'.$menu[$count][4].'</a></li>';
			}
		}
		}
		$count++;
	}
	return $aa;
}

function sitemap($generate) {
	// display the sitemap
	global $set, $pagenum, $langmessage;
	if($generate)
		return '<a href="?do=sitemap">'.$langmessage[88].'</a>';
	else
		return '<a href="'.$set['indexfile'].'?page='.$pagenum.'&amp;do=sitemap">'.$langmessage[88].'</a>';
}

function loginout() {
	// displays the login/logout link
	global $set, $langmessage;
	if($_SESSION[$set['password']]=="1")
		$out.='<a href="'.$set['homepath'].$set['indexfile'].'?do=logout" rel="nofollow">'.$langmessage[121].'</a>';
	else
		$out.='<a href="'.$set['homepath'].$set['indexfile'].'?do=login" rel="nofollow">'.$langmessage[120].'</a>';
	return $out;
}
?>
