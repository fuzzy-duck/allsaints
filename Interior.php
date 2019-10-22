<?php
	$pagenum="Interior";
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
<meta name='keywords' content='All Saints, Ilkley, History, Heritage, Organ, Saxon Crosses, Bells, Stained Glass' />
<meta name='description' content='This site gives a history of teh Church and Building at All Saints, Ilkley in West Yorkshire' />
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
<li><a class="selected" href="BriefGuide.php">Brief Guide</a></li>
<li><a href="SaxonCrosses.php">Saxon Crosses</a></li>
<li><a href="Windows.php">Windows</a></li>
<li><a href="BrassPlaques.php">Brasses</a></li>
<li><a href="Organ.php">Organ</a></li>
<li><a href="ChurchBells.php">Bells</a></li>
</ul>
      </div>
      <div id="column1">
        <div class="sidebaritem">
          <div class="sbihead">
            <h1>Interior of the Church</h1>
          </div>
          <div class="sbicontent">
			
<li><a class="selected" href="Interior.php">Interior of the Church</a></li><li><a href="ChurchFurniture.php">Church Furniture</a></li><li><a href="South Porch and Doorway.php">South Porch and Doorway</a></li><li><a href="Church Door.php">Church Door</a></li><li><a href="Font.php">Medieval Font</a></li><li><a href="Effigy.php">Middleton Effigy</a></li><li><a href="Lectern.php">Brass Lectern</a></li><li><a href="Pewends.php">Pew Ends</a></li><li><a href="piscine.php">piscine</a></li><li><a href="Shields.php">Shields</a></li><li><a href="WarMemorials.php">War Memorials</a></li><li><a href="1914-1918.php">1914-1918</a></li><li><a href="1939-1945.php">1939-1945</a></li><li><a href="WindowHeads.php">Window Heads</a></li><li><a href="Watkinson.php">Watkinson Pew</a></li>
          </div>
        </div>
        <div class="sidebaritem">
          <div class="sbihead">
            <h1>Welcome</h1>
          </div>
          <div class="sbicontent">
			<?php extra(); ?>
          </div>
        </div>
        <div class="sidebaritem">
          <div class="sbihead">
            <h1>Links</h1>
          </div>
          <div class="sbicontent">
			<?php require_once "addons/links/main.php"; print showlinks('1'); ?>
          </div>
        </div>
      </div>
      <div id="column2"><?php content(); ?></div>
    </div>
    <div id="footer">
      © 2010 LightNEasy & © 2012 Ilkley All Saints - <a href="http://lightneasy.org">LightNEasy 2.5</a> | <a href="http://validator.w3.org/check?uri=referer">XHTML 1.1</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> | <a href="http://www.dcarter.co.uk">Original design by dcarter</a>
    </div>
  </div>
</body>
</html>
