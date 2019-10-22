<?php
/*---------------------------------------------------+
| LightNEasy Content Management System
| Copyright 2007 - 2012 Fernando Baptista
| http://www.lightneasy.org
+----------------------------------------------------+
| Addon Contact Form function main.php
| Version 2.5 Mini
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
global $set, $contactmessage, $message;

if(file_exists("addons/contact/lang/lang_".$set['language'].".php"))
	require_once "addons/contact/lang/lang_".$set['language'].".php";
else
	require_once "addons/contact/lang/lang_en_US.php";

if($_POST['submit']=="Send message")
	$message=sendmessage();
	
$out.="<h2 class=\"LNE_message\">".$message."</h2>\n";

function contact() {
	global $pagenum, $contactmessage, $set;
	$out="<div id=\"LNE_contact\">\n<form method=\"post\" action=\"\"><fieldset class=\"noborder\" >\n";
	$out.="<b>$contactmessage[30]:</b><br />\n";
	$out.="<input  type=\"text\" name=\"name\" value=\"\" /><br />\n";
	$out.="<b>$contactmessage[31]:</b><br />\n";
	$out.="<input  type=\"text\" name=\"email\" value=\"\" /><br />\n";
	$out.="<b>$contactmessage[32]:</b><br />\n";
	$out.="<textarea name=\"text\" rows=\"8\" style=\"width: 100%;\"></textarea><br />\n";
	$out.="<b>$contactmessage[99]:&nbsp;</b><br />\n";
	if($set['catchpa']==0) {
		//text catchpa
		srand((double) microtime() * 1000000);
		$a = rand(0, 9);
		$b = rand(0, 9);
		$c=$a+$b;
		$out.="<b>$a + $b = </b>";
		$_SESSION[session_id()] = $c;
		$out.="<input type=\"text\" name=\"secCode\" maxlength=\"2\" style=\"width:20px\" /><br />";
	} else {
		// image catchpa
		$out.= catchpa();
	}
	$out.="<p><input type=\"hidden\" name=\"page\" value=\"$pagenum\" />\n";
	$out.="<input type=\"hidden\" name=\"submit\" value=\"Send message\" />\n";
	$out.="<input type=\"submit\" name=\"aa\" value=\"$contactmessage[33]\" /></p>";
	$out.="</fieldset></form></div>\n";
	return $out;
}

function sendmessage() {
	global $set, $contactmessage, $message;
	if(!is_intval(trim($_POST['secCode'])) || !is_intval($_SESSION[session_id()])) die ("Contact - aha! Clever!");
	if($_POST['secCode'] != $_SESSION[session_id()]) {
		$message=$contactmessage[139];
	} else {
		if(isset($_POST['text'])) {
			$message=$contactmessage[26];
			if($_POST['text']!="" && $_POST['name']!="") {
				//Contribution from user Utaka:
                if(extension_loaded("mbstring") && function_exists("mb_encode_mimeheader")) {
                    mb_language("uni");
                    mb_internal_encoding("UTF-8");
                    $fromname =  '"'. mb_encode_mimeheader($set['fromname']).'" <'.$set['fromemail'].'> ';
                } else {
                    $fromname = $set['fromemail'];
                }
                $email = html_entity_decode(sanitize($_POST['email']));
                $text = html_entity_decode(sanitize($_POST['text']));
                $name = html_entity_decode(sanitize($_POST['name']));

                $additional_header = array();
                $additional_header[] = 'MIME-Version: 1.0';
                $additional_header[] = 'Content-Type: text/plain; charset=utf-8';
//                $additional_header[] = 'Content-Transfer-Encoding: 8bit ';
                $additional_header[] = 'From: ' .$fromname;
                $to=$set['toemail']."\r\n";
                if(function_exists(mb_send_mail))
					if(!mb_send_mail($to, $contactmessage[27].$set['fromname'], $contactmessage[27].$name." at ".$email."\r\n".$text, implode("\r\n", $additional_header) ))
						$message=$contactmessage[28];
				else
					if(!mail($to, $contactmessage[27].$set['fromname'], $contactmessage[27].$name." at ".$email."\r\n".$text, implode("\r\n", $additional_header) ))
						$message=$contactmessage[28];
			} else
				$message=$contactmessage[29];
		} else
			$message=$contactmessage[29];
	}
	return $message;
}
?>
