<?php
	$pagenum="BriefGuide";
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
            <h1>Brief Guide</h1>
          </div>
          <div class="sbicontent">
			
              <li><a href="stones.php">Roman Stones</a></li>
              <li><a href="carved-heads.php">Two Carved Heads & Dog-Tooth Archway</a></li>
              <li><a href="South Porch and Doorway.php">South Porch and Doorway</a></li>
              <li><a href="anglo.php">Anglo-Saxon Stone Crosses</a></li>
              <li><a href="norman.php">Norman Font & Wood Cover</a></li>
              <li><a href="piscine-bowl.php">Piscine Bowl</a></li>
              <li><a href="middleton-effigy.php">Middleton Effigy</a></li>
              <li><a href="alter-table.php">Altar Table</a></li>
              <li><a href="piscine.php">Piscine</a></li>
              <li><a href="watkinson.php">Watkinson Pew</a></li>
              <li><a href="brass.php">Brass Plaques</a></li>
              <li><a href="bells.php">Bells in tower</a></li>
              <li><a href="Watkinson.php">Watkinson Pew</a></li>
              <li><a href="two-stones.php">Two Stone Pillars</a></li>
              <li><a href="crucifixion.php">The Crucifixion Window</a></li>
              <li><a href="christ.php">Christ Welcomes the Children Window</a></li>
              <li><a href="organ.php">Organ & Oak Casings</a></li>
              <li><a href="bell.php">The Bell Ringers Window</a></li>
              <li><a href="victorian-pews.php">Victorian Pews</a></li>
              <li><a href="pulpit.php">Pulpit</a></li>
              <li><a href="four-marys.php">The Four Marys at the Tomb of Christ</a></li>

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
