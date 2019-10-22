<?php
/*++++++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.Lightneasy.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| common.php common functions module
| no database Version 2.5
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/
$myserver=$_SERVER['SERVER_NAME'];

// LightNEasy version
$LNEversion="2.5";
//Time offset from server
$fuso_s = strval($set['timeoffset']) * 3600;
//read the addons table
$addons=readdata("addons");

function clean($string) {
	return trim(str_replace('&nbsp;',' ',$string));
}

function compare($x,$y) {
	if($x[0] == $y[0]) return 0;
	elseif($x[0] < $y[0]) return -1;
	else return 1;
}

function credits() {
global $LNEversion;
return "<!-- +++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Content Management System
| Copyright 2007-2012 Fernando Baptista
| http://www.lightneasy.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Mini version $LNEversion
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->\n";
}

function data_formatada($unix_time) {
	global $set;
	return strftime($set['dateformat'], $unix_time);
}

function decode($string) {
	return utf8_decode(stripslashes($string));
}

function deletedata($array,$file,$pos,$value,$numfields) {
	$array1=$array;
	unset($array);
	$fp=fopen("data/".$file.".dat","w");
	$count=0;
	$count1=0;
	while($array1[$count][0]!="") {
		if($array1[$count][$pos]!=$value) {
			for($i=0;$i<$numfields;$i++){
				if($i==$numfields-1) fwrite($fp,$array1[$count][$i]."||");
				else fwrite($fp,$array1[$count][$i]."|");
			}
			$array[$count1]=$array1[$count];
			$count1++;
		}
		$count++;
	}
	fclose($fp);
	return $array;
}

function encode($string) {
	return addslashes(utf8_encode($string));
}

function filelist($pattern, $start_dir='.', $dir=0) {
$filenames=array();
if ($handle = opendir($start_dir)) {
	while (false !== ($file = readdir($handle))) {
		if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
		if($dir) {
			if(is_dir($start_dir."/".$file))
				array_push($filenames, $file);
		} else
			array_push($filenames, $file);
	}
	closedir($handle);
}
$order=-1;
$filesort = create_function('$a,$b', "\$a1=\$a$sortby;\$b1=\$b$sortby; if (\$a1==\$b1) return 0; else return (\$a1<\$b1) ? -1 : 1;");
uasort($filenames, $filesort);
return $filenames;
}

function fullmenu($generat=0) {
       global $pagenum, $menu, $selected, $set;
       $count=0;
       $out="\n";
       while($menu[$count][0] != "") {
// Bugfix by Jochen Wendel:
               if(strpos($menu[$count][3],"#") === false && $menu[$count][0] != "0") {
               $out.='<li';
               if($menu[$count][2]!="0")
                       $out.=" class=\"LNE_menu_doubleintend\"";
               elseif($menu[$count][1]!="0")
                       $out.=" class=\"LNE_menu_intend\"";
               else
                       $out.=" class=\"LNE_menu\"";
               $out.="><a ";
               if($menu[$count][4]==$selected['name'])
                       $out.= 'class="selected" ';
               if(strpos($menu[$count][3],"*"))
                       $out.='href="'.str_replace("*", "",$menu[$count][3]).'">';
               elseif($generat)
                       $out.='href="'.$menu[$count][3].".php\">";
               else
                       $out.='href="'.$set['indexfile'].'?page='.$menu[$count][3].'">';
               $out.=$menu[$count][4]."</a></li>\n";
               }
               $count++;
       }
       return $out;
}

function is_intval($value) {
     return 1 === preg_match('/^[+-]?[0-9]+$/', $value);
}

function loginform() {
	global $langmessage, $LNEversion;
	$out='<div align="center"><form method="post" action=""><h2>LightNEasy '.$LNEversion.' '.$langmessage[120].'</h2>';
	$out.='<p>'.$langmessage[6].':&nbsp;<input  type="password" name="password" value="" />';
	$out.='<input type="hidden" name="submit" value="Login" />';
	$out.='<input type="submit" name="aa" value="'.$langmessage[120].'" /></p></form></div>';
	return $out;
}

function printheader($generate,$tmpl="-") {
global $set, $edit, $editextra, $pagenum, $selected, $langmessage, $cntt, $LNEversion;
if($generate)
	$out.= "\n<?php\n\tprint checktitle();\n?>\n";
else
	$out.= checktitle();
$out.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
$out.="<meta http-equiv='Content-Language' content='".$set['language']."' />\n";
$out.="<meta http-equiv='Content-Script-Type' content='text/javascript' />\n";
$out.="<meta http-equiv='Content-Style-Type' content='text/css' />\n";
$out.="<meta name='keywords' content='".$set['keywords']."' />\n";
$out.="<meta name='description' content='";
if($selected['descr'] != "-")
	$out.=decode($selected['descr']);
else
	$out.=$set['description'];
$out.="' />\n";
$out.="<meta name='author' content='".$set['author']."' />\n";
$out.="<meta name='generator' content='LightNEasy Mini ".$LNEversion."' />\n";
$out.="<meta name='Robots' content='index,follow' />\n";
$out.="<meta http-equiv='imagetoolbar' content='no' /><!-- disable IE's image toolbar -->\n";
if(file_exists("data/news.dat"))
	$out.="<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".$set['title']." RSS Feed\" href=\"LightNEasy/rss.php\" />\n";
$out.="<link rel='stylesheet' type='text/css' href='templates/";
if($selected['template'] != "-")
	$out.=$selected['template'];
else
	if($tmpl != "-")
		$out.=$tmpl;
	else
		$out.=$set['template'];
$out.="/style.css' />\n";
$out.="<link rel='stylesheet' type='text/css' href='css/lightneasy.css' />\n";
if($generate)
	$out.= "<?php\n\tprint checkaddons();\n?>\n";
else
	$out.= checkaddons();
if($generate)
	$out.=credits();
return $out;
}

function checktitle() {
global $cntt, $set, $selected, $langmessage, $pagenum;
$cntt=stripslashes(decode(file_get_contents("data/".$pagenum.".html")));
if(strpos($cntt, '%!$news')!==false) {
	if($_GET['id']!="") {
		if(!is_intval($_GET["id"])) die ($langmessage[98]);
		$noticia_numero = $_GET["id"];
	}
	$aa=explode("||",trim(@file_get_contents("data/news.dat")));
	$count=0;
	$lugar=-1;
	while($aa[$count] != "") {
		$aaa=explode("|",trim($aa[$count]));
		$row_db[$count]=$aaa;
		if($noticia_numero!="" && $row_db[$count][0]==$noticia_numero) {
			$lugar=$count;
			break;
		}
		$count++;
	}
	$count--;
	if($lugar==-1)
		$lugar=$count;
	$out.="<title>".decode($row_db[$lugar][3])."</title>\n";
} else
	$out.="<title>".$selected['name']." - ".$set['title']."</title>\n";
return $out;
}

function checkaddons() {
	global $pagenum, $cntt;
	$addons=readdata("addons",4,"1");
	$xtra=stripslashes(decode(file_get_contents("data/extra.htm")));
	if(strpos($cntt, '%!$plugin')!==false) {
		$one=explode('%!$plugin',$cntt,2);
		$two=explode('$!%',$one[1],2);
		$pluginame="./plugins/".trim($two[0]);
		if(file_exists($pluginame."/header.mod")) {
			$three=file_get_contents($pluginame."/header.mod");
			$out.= $three."\n";
		}
		if(file_exists($pluginame."/first.mod") && !$generate) {
			include "$pluginame/first.mod";
		}
	}
	foreach($addons as $addon) {
		if((strpos( $cntt,"%!$".$addon[0])!==false || strpos( $xtra,"%!$".$addon[0]))!==false && strval($addon[3])) {
			require_once "addons/".$addon[0]."/header.php";
		}
	}
	return $out;
}

function readdata($file, $field=-1, $value=""){
	// read a data file, returns an array with the content
	$content=@file_get_contents("data/".$file.".dat");
	str_replace("||\n", "||", $content);
	$aa=array();
	$aa = explode("||",trim($content));
	$count = 0;
	$count1 = 0;
	foreach($aa as $aaa) {
		$bb[$count] = explode("|",trim($aaa));
		if($field==-1 || ($field!=-1 && $bb[$count][$field]==$value)) {
			$cc[$count1] = $bb[$count];
			$count1++;
		}
		$count++;
	}
	return $cc;
}

function readmenu() {
	//Read menu
	global $menu, $pagenum,$selected;
	$aaa=decode(trim(file_get_contents("data/menu.dat")));
	array($menu);
	unset($mmenu);
	$mmenu=explode("||",$aaa);
	$count=0;
	foreach($mmenu as $mmmenu) {
		$menu[$count]=explode("|",trim($mmmenu));
		if($menu[$count][3]==$pagenum) {
			$selected['index']=$menu[$count][0];
			$selected['name']=$menu[$count][4];
		}
		$count++;
	}
	//Read page data
	$aaa=decode(trim(file_get_contents("data/pages.dat")));
	$pag=array();
	$pag=explode("||",$aaa);
	$selected['descr']="-";
	$selected['template']="-";
	foreach($pag as $ppag) {
		$page=array();
		$page=explode("|",trim($ppag));
		$count=0;
		while($menu[$count][0] !="") {
			if($menu[$count][3] == trim($page[0])) {
				$menu[$count][5] = trim($page[1]);
				$menu[$count][6] = trim($page[2]);
				if($menu[$count][4]==$selected['name']) {
					$selected['descr'] = $page[1];
					$selected['template'] = $page[2];
					break;
				}
			}
			$count++;
		}
	}
//print_r($menu);
}

function readsetup() {
	global $set, $langmessage;
	require "data/config.php";
	$set['title']=decode($set['title']);
	$set['subtitle']=decode($set['subtitle']);
	$set['keywords']=decode($set['keywords']);
	$set['description']=decode($set['description']);
	$set['author']=decode($set['author']);
	$set['footer']=decode($set['footer']);
	$set['indexfile']=decode($set['indexfile']);
	$set['fromname']=decode($set['fromname']);
	if($set['language']=="") $set['language']="en_US";
	require "./languages/lang_".$set['language'].".php";
}

function sanitize($text) {
	if(strpos($text,null) !== false)
		die($langmessage[98]);
	// Convert problematic ascii characters to their true values
	$search = array("40","41","58","65","66","67","68","69","70",
		"71","72","73","74","75","76","77","78","79","80","81",
		"82","83","84","85","86","87","88","89","90","97","98",
		"99","100","101","102","103","104","105","106","107",
		"108","109","110","111","112","113","114","115","116",
		"117","118","119","120","121","122"
		);
	$replace = array("(",")",":","a","b","c","d","e","f","g","h",
		"i","j","k","l","m","n","o","p","q","r","s","t","u",
		"v","w","x","y","z","a","b","c","d","e","f","g","h",
		"i","j","k","l","m","n","o","p","q","r","s","t","u",
		"v","w","x","y","z"
		);
	$entities = count($search);
	for ($i=0;$i < $entities;$i++) $text = preg_replace("#(&\#)(0*".$search[$i]."+);*#si", $replace[$i], $text);
	// the following is based on code from bitflux (http://blog.bitflux.ch/wiki/)
	// Kill hexadecimal characters completely
	$text = preg_replace('#(&\#x)([0-9A-F]+);*#si', "", $text);
	// remove any attribute starting with "on" or xmlns
	$text = preg_replace('#(<[^>]+[\\"\'\s])(onmouseover|onmousedown|onmouseup|onmouseout|onmousemove|onclick|ondblclick|onload|xmlns)[^>]*>#iU', ">", $text);
	do {
		$oldtext = $text;
		preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $text);
	// remove javascript: and vbscript: protocol
	} while ($oldtext != $text);
	$text = preg_replace('#([a-z]*)=([\`\'\"]*)script:#iU', '$1=$2nojscript...', $text);
	$text = preg_replace('#([a-z]*)=([\`\'\"]*)javascript:#iU', '$1=$2nojavascript...', $text);
	$text = preg_replace('#([a-z]*)=([\'\"]*)vbscript:#iU', '$1=$2novbscript...', $text);
	$text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU', "$1>", $text);
	$text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU', "$1>", $text);
	return $text;
}

function search($run=false) {
	global $out, $set, $langmessage, $message;
	if($_POST['submit']=="search" && $_POST['search']!="" && $_POST['search']!=$langmessage[49]) {
		$needle=sanitize($_POST['search']);
		$out.="<h3>$langmessage[68]\"$needle\":</h3>\n<ul>\n";
		$posts=array();
		$posts=readdata("news");
		//check within titles
		$cont=0;
		while($posts[$cont][0]!="") {
			$text=strip_tags(decode($posts[$cont][3]));
			if(($pos=stripos($text, $needle))!==false) {
				$text=strip_tags($text);
				$first=substr($text,0,strval($pos));
				$last=substr($text , strval($pos)+strlen($needle));
				$out.="<li><a href=\"".$set['newspage'].".php?id=".$posts[$cont][0]."\">$first<b>$needle</b>$last</a></li>\n";
			}
			$text=strip_tags(decode($posts[$cont][4]));
			if(($pos=stripos($text, $needle))!==false) {
				$first=substr($text,0,strval($pos));
				if(strlen($first)>=50)
					$first="...".substr($first,strlen($first)-50);
				$last=substr($text , strval($pos)+strlen($needle));
				if(strlen($last)>=50)
					$last=substr($last, 0,50)."...";
				$out.="<li><a href=\"".$set['newspage'].".php?id=".$posts[$cont][0]."\">".$posts[$cont][3]."</a><p>$first<b>$needle</b>$last</p></li>\n";
			}
			$cont++;
		}
		$out.="</ul>";
	}
	if($run)
		print $out;
}

function searchform() {
	global $set, $langmessage, $message;
	$out.="<div class=\"f_search\">\n<form method=\"post\" action=\"index.php?do=search\">\n";
	$out.="<p><input type=\"text\" name=\"search\" value=\"$langmessage[49]\" class=\"search\" onblur=\"if(this.value=='') this.value='$langmessage[49]';\" onfocus=\"if(this.value=='search...') this.value='';\" />\n";
	$out.="<input type=\"hidden\" name=\"submit\" value=\"search\" />\n";
	$out.="<input type=\"submit\" value=\"$langmessage[65]\" class=\"submit\" /></p>\n";
	$out.="</form>\n</div>\n";
	return $out;
}

function sv($s) {
	if (!isset($_SERVER)) {
		global $_SERVER;
		$_SERVER = $GLOBALS['HTTP_SERVER_VARS'];
	}
	if (isset($_SERVER[$s]))return $_SERVER[$s];
	else return'';
}

function convertRGB($color) {
    $color = eregi_replace('[^0-9a-f]', '', $color);
    return array(hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
}

function createImage($text, $width, $height, $font = 5) {
    global $fontColor, $bgColor, $lineColor, $set;

    if($img = @ImageCreate($width, $height)) {
      list($R, $G, $B) = convertRGB($fontColor);
      $fontColor = ImageColorAllocate($img, $R, $G, $B);
      list($R, $G, $B) = convertRGB($bgColor);
      $bgColor = ImageColorAllocate($img, $R, $G, $B);
      list($R, $G, $B) = convertRGB($lineColor);
      $lineColor = ImageColorAllocate($img, $R, $G, $B);
		imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $bgColor);
      for($i = 0; $i <= $width; $i += 5) {
        @ImageLine($img, $i, 0, $i, $height, $lineColor);
      }
      for($i = 0; $i <= $height; $i += 5) {
        @ImageLine($img, 0, $i, $width, $i, $lineColor);
      }

      $hcenter = $width / 2;
      $vcenter = $height / 2;
      $x = round($hcenter - ImageFontWidth($font) * strlen($text) / 2);
      $y = round($vcenter - ImageFontHeight($font) / 2);
      ImageString($img, $font, $x, $y, $text, $fontColor);

      if(function_exists('ImagePNG')) {
        @ImagePNG($img, "data/catchpa.png");
		return("png");
      } else if(function_exists('ImageGIF')) {
		@ImageGIF($img, "data/catchpa.gif");
		return("gif");
      }
      else if(function_exists('ImageJPEG')) {
        @ImageJPEG($img, "data/catchpa.jpg");
        return("jpg");
      }
      ImageDestroy($img);
    }
}

function catchpa(){
    global $fontColor, $bgColor, $lineColor, $set, $out;
	$fontSize = 5;              // font size (1 - 5)
	$fontColor = "000000";      // font color (RGB hexcode)
	$bgColor = "FFFFFF";        // background color (RGB hexcode)
	$lineColor = "B0B0B0";      // line color (RGB hexcode)
	srand((double) microtime() * 1000000);
	$secCode = '';
	for($i = 0; $i < 6; $i++)
		$secCode .= rand(0, 9);
	$_SESSION[session_id()] = $secCode;
	$ext=createImage($secCode, 71, 21, $fontSize);
	return("<input type=\"text\" name=\"secCode\" maxlength=\"6\" style=\"width:50px\" />\n&nbsp;<b>&laquo;</b>&nbsp;<img src=\"data/catchpa.$ext\" width=\"71\" height=\"21\" align=\"absmiddle\" />");
}

//replacement for PHP5 function http_build_query() if that function doesn't exist
//taken from the PHP online manual
if(!function_exists('http_build_query')) {
    function http_build_query($data,$prefix=null,$sep='',$key='') {
        $ret    = array();
            foreach((array)$data as $k => $v) {
                $k    = urlencode($k);
                if(is_int($k) && $prefix != null) {
                    $k    = $prefix.$k;
                };
                if(!empty($key)) {
                    $k    = $key."[".$k."]";
                };

                if(is_array($v) || is_object($v)) {
                    array_push($ret,http_build_query($v,"",$sep,$k));
                }
                else {
                    array_push($ret,$k."=".urlencode($v));
                };
            };

        if(empty($sep)) {
            $sep = ini_get("arg_separator.output");
        };

        return    implode($sep, $ret);
    };
};

?>
