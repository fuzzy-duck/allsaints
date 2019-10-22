<?php
	$pagenum="about";
	include("LightNEasy/runtime.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<?php
	print checktitle();
?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta http-equiv='Content-Language' content='en_US' />
<meta http-equiv='Content-Script-Type' content='text/javascript' />
<meta http-equiv='Content-Style-Type' content='text/css' />
<meta name='keywords' content='LightNEasy, CMS, Content Management, PHP, Free CMS, Website builder, Open Source' />
<meta name='description' content='LightNEasy is a light and simple Content Management System and Website Builder' />
<meta name='author' content='Fernando Baptista' />
<meta name='generator' content='LightNEasy Mini 2.5' />
<meta name='Robots' content='index,follow' />
<meta http-equiv='imagetoolbar' content='no' /><!-- disable IE's image toolbar -->
<link rel="alternate" type="application/rss+xml" title="All Saints Ilkley RSS Feed" href="LightNEasy/rss.php" />
<link rel='stylesheet' type='text/css' href='templates/contrast_80/style.css' />
<link rel='stylesheet' type='text/css' href='css/lightneasy.css' />
<?php
	print checkaddons();
?>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Content Management System
| Copyright 2007-2012 Fernando Baptista
| http://www.lightneasy.org
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| LightNEasy Mini version 2.5
++++++++++++++++++++++++++++++++++++++++++++++++++++++
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
</head>
<body>
  <div id="main">
    <div id="links">
      <a href="./">Home</a> | <a href="?do=sitemap">Site Map</a> | <a href="?do=login" rel="nofollow">Login</a>
    </div>
    <div id="logo"><h1><a href="./">All Saints Ilkley</a></h1></div>
    <div id="content">
      <div id="menu">
        <ul>
<li class="first"><a href="index.php">Heritage</a></li>
<li><a href="BriefGuide.php">Brief Guide</a></li>
<li><a href="SaxonCrosses.php">Saxon Crosses</a></li>
<li><a href="Windows.php">Windows</a></li>
<li><a class="selected" href="about.php">About</a></li>
<li><a href="news.php">News</a></li>
<li><a href="BrassPlaques.php">Brass Plaques</a></li>
</ul>
      </div>
      <div id="column1">
        <div class="sidebaritem">
          <div class="sbihead">
            <h1>About</h1>
          </div>
          <div class="sbicontent">
			

          </div>
        </div>
        <div class="sidebaritem">
          <div class="sbihead">
            <h1>Last News</h1>
          </div>
          <div class="sbicontent">
			<?php require_once "addons/lastnews/main.php"; print lastnews(); ?>
          </div>
        </div>
        <div class="sidebaritem">
          <div class="sbihead">
            <h1>Wise words</h1>
          </div>
          <div class="sbicontent">
			<?php extra(); ?>
          </div>
        </div>
      </div>
      <div id="column2"><?php content(); ?></div>
    </div>
    <div id="footer">
      Copyright © 2010 LightNEasy - <a href="http://lightneasy.org">LightNEasy 2.5</a> | <a href="http://validator.w3.org/check?uri=referer">XHTML 1.1</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> | <a href="http://www.dcarter.co.uk">Original design by dcarter</a>
    </div>
  </div>
</body>
</html>
