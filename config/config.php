<?php

 #
 # MiniRooms - rooms manager for website
 #
 # info: main folder copyright file
 #
 #

# configuration - need change it


$COPYRIGHT="© 2019. <a href=https://github.com/pphome2/minirooms>MiniRooms</a>";

# need md5 passcode -- password
$MR_ADMIN_PASS="9F9858AFB7AD47216D2658E767E9E855";
$MR_PASS="5f4dcc3b5aa765d61d8327deb882cf99";

## auto logout - second
$LOGIN_TIMEOUT=600;

$ADMIN_USER=false;
$AUTO_DATE_TO_FIRST=true;

$MR_SITENAME="MiniRooms - Test";
$MR_SITE_HOME="http://www.google.com";
$MR_ROOMS_ROOT="rooms";
$MR_CONFIG_DIR="config";

$MR_CSS="site.css";
$MR_CSS2="site2.css";
$MR_JS_BEGIN="";
$MR_JS_END="js_end.js";
$MR_HEADER="header.php";
$MR_FOOTER="footer.php";

$MR_ADMINFILE="minirooms.php";
$MR_PRINTFILE="print.php";

$MR_MONTHS=array();
$y=date('Y');
$MR_YEARS=array($y,$y+1);

$MR_ROOMS=array("I","II","III","IV","V","VI","VII","VIII","IX","X","XI");

$MR_EMPTY_TEXT=array(" 8.00- 9.00:",
					" 9.00-10.00:",
					"10.00-11.00:",
					"11.00-12.00:",
					"12.00-13.00:",
					"13.00-14.00:",
					"14.00-15.00:",
					"15.00-16.00:",
					"16.00-17.00:",
					"");


$MR_MONTHS=array();

$MR_MONTHS_DAY=array("31","28","31","30","31","30","31","31","30","31","30","31");

# language
$MR_LANGFILE="hu.php";


?>
