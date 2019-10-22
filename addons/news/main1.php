<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon News include module main1.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $set;
// this function shows the news
function shownews($post_integra=1,$post_cabecalho=9,$comenta=0,$categ=-1) {
	global $newsmessage, $set, $fuso_s, $pagenum;
	$fuso_s = strval($set['timeoffset']) * 3600;
	if($_GET['id']!="") {
		if(!is_intval($_GET["id"])) die ($newsmessage[98]);
		$noticia_numero = $_GET["id"];
	}
	$aa=explode("||",trim(@file_get_contents("data/news.dat")));
	$count=0;
	$lugar=-1;
	$cc=0;
	//read all news or only news=$categ, if set
	while($aa[$cc] != "") {
		$aaa=explode("|",trim($aa[$cc]));
		if($categ==-1 || (strval($aaa[7]) == strval($categ))) {
			$row_db[$count]=$aaa;
			if($noticia_numero!="" && $row_db[$count][0]==$noticia_numero)
				$lugar=$count;
			$count++;
		}
		$cc++;
	}
	$count--;
	$total=$count;
	if($lugar==-1)
		$noticia_numero=$row_db[$count][0];
	else {
	// place the selected news on top
		$n=$row_db[$lugar];
		for($i=$lugar; $i<$total; $i++) {
			$row_db[$i]=$row_db[$i+1];
		}
		$row_db[$total]=$n;
		$post_integra=1;
	}
	$row_cmt=readdata("comments");
	$first=true;
	$firstcomment=true;
	// display expanded news
	$out.="<div id=\"LNEnews\">\n";
	while($row_db[$count][0]!="" && $total-$count<$post_integra) {
		if(!$first) $out.="<hr /><br />";
		$out.=show_one_news($row_db[$count][3],$row_db[$count][5],$row_db[$count][4],$row_db[$count][1],$row_db[$count][2]);
		if($comenta) { // are comments set?
			// read comments
			$j=0;
			foreach($row_cmt as $aaa) {
				if($aaa[0]==$row_db[$count][0]) {
					$comments[$j]=$aaa;
					$j++;
				}
			}
			if($j) { // there are comments
				if($_GET['showcomments'] || $comenta==2) {
					$ff=true;
					$i=0;
					while($comments[$i][0]!="") {
						if($ff) {
							$out.="<div class=\"LNEnews_comments\">".$newsmessage[143].":</div>";
							$ff=false;
						}
						$out.="<div class=\"LNEnews_comment\">\n";
						$out.="<span class=\"poster\">$newsmessage[144]: </span>\n";
						$out.="<span class=\"author\">";
						if($comments[$i][2]=="-")
							$out.=decode($comments[$i][1])."</span>";
						else
							$out.="<a href=\"mailto:".decode($comments[$i][2])."\">".decode($comments[$i][1])."</a></span>";
						$out.="<span class=\"text\">".decode($comments[$i][4])."</span>";
						$out.="<span class=\"time\">".$newsmessage[112]." ".data_formatada($comments[$i][3] + $fuso_s)."</span>";
						if($_SESSION[$set['password']]=="1") {
							$out.="\n<form method=\"post\" action=\"\">\n";
							$out.="<input type=\"hidden\" name=\"newsid\" value=\"".$comments[$i][0]."\" />\n";
							$out.="<input type=\"hidden\" name=\"id\" value=\"".$comments[$i][3]."\" />\n";
							$out.="<input type=\"hidden\" name=\"submit\" value=\"deletecomment\" />\n";
							$out.="<input type=\"image\" name=\"aaa\" src=\"images/editdelete.png\" style=\"width: 16px; height: 16px; border: none; background: transparent;\" value=\"\" title=\"$newsmessage[174]\" />\n";
							$out.="</form>\n";
						}
						$out.="</div>\n";
						$i++;
						unset($_GET['showcomments']);
					}
				} else
					$out.="<a href=\"".$_SERVER['SCRIPT_NAME']."?page=".$pagenum."&amp;id=".$noticia_numero."&amp;showcomments=1\">". $newsmessage[143].": ".$j."</a><br />\n";
			}
			// display comments form
			if($firstcomment) {
				$out.="<br />".commentform($noticia_numero);
				$firstcomment=false;
			}
		}
		$first=false;
		$count--;
	}
	$first=true;
	$GETarray = $_GET;
	$total=$count;
	while($row_db[$count][0]!="" && $total-$count<$post_cabecalho) {
		if($first) {
			$first=false;
			$out.="<div style=\"text-align: center; font-size: 85%; font-weight: bold; \">$newsmessage[113]</div>";
			$out.="<table border='0' align='center'><tr><td>$newsmessage[12]</td><td>$newsmessage[114]</td><td>$newsmessage[16]</td></tr>";
		}
		$GETarray['id'] = $row_db[$count][0];
		$call = $_SERVER['SCRIPT_NAME'] . "?" . http_build_query($GETarray,'','&amp;');
		$out.="<tr><td><a href=\"".$call."\">".decode($row_db[$count][3])."</a></td><td>".strftime("%d/%m/%y - %I:%M %p", $row_db[$count][5] + $fuso_s)."</td><td>".$row_db[$count][1]."</td></tr>";
		$count--;
	}
	if(!$first) $out.="</table>";
	$out.=showrss();
	$out.="</div>\n";
	return $out;
}

function show_one_news($a,$b,$c,$d,$e) {
	global $newsmessage, $fuso_s;
	$fuso_s = strval($set['timeoffset']) * 3600;
	$out ="<span class=\"LNEnews_title\" >".decode($a)."</span>\n";
	$out.="<span class=\"LNEnews_date\">$newsmessage[112] ".data_formatada($b + $fuso_s)."</span>\n";
	$out.="<div class=\"LNEnews_text\">".stripslashes(decode($c))."</div>\n";
	$out.="<span class=\"LNEnews_author\">$newsmessage[16]: ";
	if($e!="-")
		$out.="<a href=\"mailto: $e\">".decode($d)."</a>";
	else
		$out.=decode($d);
	$out.="</span>\n<br />\n";
	return $out;
}

function commentform($newsid) {
	global $newsmessage, $editar, $set;
	$out="<form action=\"\" method=\"post\"><fieldset class=\"noborder\">\n";
	if($_SESSION[$set['password']]=="1") {
		$out.="<input type=\"hidden\" name=\"commentname\" value=\"".$set['fromname']."\" />\n";
		$out.="<input type=\"hidden\" name=\"commentemail\" value=\"".$set['fromemail']."\" />\n";
	} else {
		$out.="<b>$newsmessage[50]:&nbsp;</b><br />\n";
		$out.="<input type=\"text\" name=\"commentname\" style=\"width:250px\" value=\"";
		if($editar) $out.=$_POST['commentname'];
		$out.="\" /><br/>\n";
		$out.="<b>$newsmessage[73]:&nbsp;</b><br/>\n";
		$out.="<input type=\"text\" name=\"commentemail\" style=\"width:250px\" value=\"";
		if($editar) $out.=$_POST['commentemail'];
		$out.="\"><br/>\n";
	}
	$out.="<b>$newsmessage[138]:&nbsp;</b><br/>\n";
	$out.="<textarea name=\"commentmessage\" style=\"width:100%; height:80px\">";
	if($editar) $out.=$_POST['commentmessage'];
	$out.="</textarea><br/>\n";
	if(function_exists('adminmenu')) {
		srand((double) microtime() * 1000000);
		$a = rand(0, 19);
		$_SESSION[session_id()]=$a;
		$out.="<input type=\"hidden\" name=\"secCode\" value=\"$a\" />\n";
	} elseif($set['catchpa']=="0") {
		$out.="<b>$newsmessage[99]:</b><br/>";
		//text catchpa
		srand((double) microtime() * 1000000);
		$a = rand(0, 9);
		$b = rand(0, 9);
		$c=$a+$b;
		$out.="$a + $b = ";
		$_SESSION[session_id()] = $c;
		$out.="<input type=\"text\" name=\"secCode\" maxlength=\"2\" style=\"width:20px\" />\n";
	} else {
		$out.="<b>$newsmessage[99]:</b><br/>";
		// image catchpa
		$out.= catchpa();
	}
	$out.="<input type=\"hidden\" name=\"submit\" value=\"sendcomment\" /><br />";
	$out.="<input type=\"hidden\" name=\"newsid\" value=\"$newsid\" /><br/>";
	$out.="<input type=\"submit\" value=\"$newsmessage[137]\" />\n</td></tr>\n</fieldset></form><br />\n";
	return $out;
}

function showrss() {
	$out="News feed: <a href=\"./LightNEasy/rss.php\"><img src=\"images/rss.png\" alt=\"RSS feed\" title=\"News RSS feed\" style=\"border: none; padding: 0; margin: 0;\"/></a>\n";
	return $out;
}


?>
