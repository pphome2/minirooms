<?php

 #
 # MiniRooms - room manager for website
 #
 # info: main folder copyright file
 #
 #

# configuration - need change it


include("config/config.php");
include("config/$MR_LANGFILE");


function vinput($d) {
    $d=trim($d);
    $d=stripslashes($d);
    $d=strip_tags($d);
    $d=htmlspecialchars($d);
    return $d;
}


echo("<!DOCTYPE HTML>");
echo("<html><head>");
echo("<title>$MR_SITENAME</title>");
echo("<meta charset=\"utf-8\" />");
echo("<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\">");
echo("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />");
echo("<link rel=\"icon\" href=\"favicon.png\">");
echo("<link rel=\"shortcut icon\" type=\"image/png\" href=\"favicon.png\" />");
echo("</head>");
echo("<style>");
include("$MR_CSS2");
echo("</style>");
echo("<body>");

echo("<a href=$MR_ADMINFILE>");



$utime=time();
$loggedin=FALSE;
$passw="";

if (isset($_POST["password"])){
	$passw=md5($_POST["password"]);
	$passw=vinput($passw);
	if ($passw==$MR_ADMIN_PASS){
		$loggedin=TRUE;
		$ADMIN_USER=true;
	}else{
		if ($passw==$MR_PASS){
			$loggedin=TRUE;
			$ADMIN_USER=false;
		}
	}
}
if (isset($_POST["passwordh"])){
	$passw=$_POST["passwordh"];
	$passw=vinput($passw);
	if ($passw==$MR_PASS){
		if (isset($_POST["utime"])){
			$outime=$_POST["utime"];
			$outime=vinput($outime);
			$utime2=$utime-$outime;
			if ($utime2<$LOGIN_TIMEOUT){
				$loggedin=TRUE;
			}
		}else{
			$loggedin=TRUE;
		}
	}
}








if ($loggedin){

	# files
	$roomsfile='';
	if (isset($_POST["submitprint"])){
		$roomsfile=vinput($_POST["printfile"]);
	}

	# load data from file

	if (file_exists($roomsfile)){
		$fileContents=file_get_contents($roomsfile);
		$datat=json_decode($fileContents, true);
 
		$year=substr($roomsfile,strlen($roomsfile)-8,4);
		$month=substr($roomsfile,strlen($roomsfile)-4,2);
		$day=substr($roomsfile,strlen($roomsfile)-2,2);
		
		echo("<center><h3>$year. $month. $day</h3></center>");
		
		echo("<div style='width:90%;margin:auto;'>");
		$db=count($MR_ROOMS);
		for ($i=0;$i<$db;$i++){
			echo("<br />");
			$k=$i+1;
			echo("$L_ROOMNAME: $k");
			echo("<br /><br />");
			echo("<div style='border:1px solid;border-color:lightgrey;padding:20px;'>");
			$dout=htmlspecialchars_decode($datat[$i]);
			echo("$dout");
			echo("</div>");
			echo('<br /><br />');
		}
		echo("</div>");
	
	
	}else{
		echo("<section id=message>$L_FILENOTFOUND</section>");
	}
	
}else{
	echo("<section id=message>$L_NORIGHTS</section>");
}

echo("</a></body></html>");

?>
