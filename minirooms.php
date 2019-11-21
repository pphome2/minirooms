<?php

 #
 # MiniRooms - rooms manager for website
 #
 # info: main folder copyright file
 #
 #


include("config/config.php");
include("config/$MR_LANGFILE");
include("$MR_HEADER");
include("$MR_JS_BEGIN");

$MR_MONTHS=$L_MONTHS_NAME;


function dirlist($dir) {
	global $MR_CONFIG_DIR;

    $result=array();
    $cdir=scandir($dir);
    foreach ($cdir as $key => $value){
		if (!in_array($value,array(".","..",$MR_CONFIG_DIR))){
			$result[]=$value;
		}
	}
	return $result;
}


function vinput($d) {
    $d=trim($d);
    $d=stripslashes($d);
    $d=strip_tags($d);
    $d=htmlspecialchars($d);
    return $d;
}




function mess_error($m){
	echo('
	<div class="message">
  		<div onclick="this.parentElement.style.display=\'none\'" class="toprightclose"></div>
  		<p style="padding-left:40px;">'.$m.'</p>
	</div>
	');
}


function mess_ok($m){
	echo('
		<div class="card">
  			<div onclick="this.parentElement.style.display=\'none\'" class="toprightclose"></div>
  			<div class=card-header><br /></div>
  			<div class="cardbody" id="cardbody">
  				<p style="padding-left:40px;padding-bottom:20px;">'.$m.'</p>
  			</div>
		</div>
	');
}




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

	$datat=array();
	$saved=FALSE;
	
	# date select
	$year='';
	$month='';
	$day='';
	$roomsfile='';
	if (isset($_POST["submitdate"])){
		$year=vinput($_POST["year"]);
		$month=vinput($_POST["month"]);
		if ($month<10){
			$month='0'.$month;
		}
		$day=vinput($_POST["submitdate"]);
		if ($day<10){
			$day='0'.$day;
		}
		$roomsfile=$MR_ROOMS_ROOT."/".$year.$month.$day;
	}
	
	# delete data
	if ($_POST["submitdel"]<>""){
		$roomsfile=vinput($_POST["roomsfile"]);
		unlink($roomsfile);
		$roomsfile="";
		mess_ok("$L_DELETED");
	}
	
	# new or save data from form
	if (isset($_POST["submitsave"])){
		$roomsfile=vinput($_POST["roomsfile"]);
		$year=substr($roomsfile,strlen($roomsfile)-8,4);
		$month=substr($roomsfile,strlen($roomsfile)-4,2);
		$day=substr($roomsfile,strlen($roomsfile)-2,2);
		$db=count($MR_ROOMS);
		for ($i=0;$i<$db;$i++){
			$n=$MR_ROOMS[$i];
			$datat[$i]=$_POST[$n];
		}
		$encodedString=json_encode($datat);
 		file_put_contents($roomsfile, $encodedString);
 		$saved=TRUE;
 		mess_ok($L_SAVED);
	}

	# interval 
	if (isset($_POST["submitinterval"])){
		$roomnum=vinput($_POST["roomnum"]);
		$startyear=vinput($_POST["startyear"]);
		$startmonth=vinput($_POST["startmonth"]);
		$startday=vinput($_POST["startday"]);
		$endmonth=vinput($_POST["endmonth"]);
		$endday=vinput($_POST["endday"]);
		$mtext=vinput($_POST["mtext"]);
		if ( ($startmonth<$endmonth) or (($startmonth=$endmonth)and($startday<=$endday)) ){
			#echo("Terem: $roomnum - $startyear - $startmonth - $startday - $endmonth - $endday - $mtext<br />");
			
			for ($ik=$startmonth;$ik<=$endmonth;$ik++){
				if ($ik==$startmonth){
					$ikx=$startday;
				}else{
					$ikx=1;
				}
				if ($ik==$endmonth){
					$iky=$endday;
				}else{
					$iky=31;
				}
				
				if ($ik<10){
					$mo='0'.$ik;
				}else{
					$mo=$ik;
				}
				
				for ($il=$ikx;$il<=$iky;$il++){
					if ($il<10){
						$da='0'.$il;
					}else{
						$da=$il;
					}
					$rfile=$MR_ROOMS_ROOT."/".$startyear.$mo.$da;
					#echo("$rfile<br />");
					
					if (file_exists($rfile)){
						$fileContents=file_get_contents($rfile);
						$datat=json_decode($fileContents, true);
					}else{
						$datat=array();
					}
					$dbr=count($MR_ROOMS);
					for ($ii=0;$ii<$dbr;$ii++){
						if ($datat[$ii]==""){
							$kk=$ii+1;
							$datat[$ii]=$L_ROOMNAME.': '.$kk."\r\n";
							$dbem=count($MR_EMPTY_TEXT);
							for ($l=0;$l<$dbem;$l++){
								$datat[$ii]=$datat[$ii]."\r\n".$MR_EMPTY_TEXT[$l];
							}
						}
						if ($ii==($roomnum-1)){
							$datat[$ii]=$mtext."\r\n\r\n".$datat[$ii];
						}
					}
					$encodedString=json_encode($datat);
					file_put_contents($rfile, $encodedString);
				}
				
			}
			
		}
	}
	
	

	# files
		

	# generate date
	echo('<center>');
	
	echo("<form action=$MR_ADMINFILE id=0 method=post enctype=multipart/form-data>");
	echo("	<input type='hidden' name='passwordh' id='passwordh' value='$passw'>");
	echo("	<input type='hidden' name='utime' id='utime' value='$utime'>");
	echo("	<select name='year' id='year'>");
	$db=count($MR_YEARS);
	for ($i=0;$i<$db;$i++){
		$selected='';
		if ($MR_YEARS[$i]==$year){
			$selected='selected=selected';
		}
		echo("		<option value='$MR_YEARS[$i]' $selected>$MR_YEARS[$i]</option>");
	}
	echo("	</select>");
	echo("	<select name='month' id='month'>");
	if ($month==0){
		$month=date('m');
	}
	$db=count($MR_MONTHS);
	for ($i=0;$i<$db;$i++){
		$selected='';
		if (($i+1)==$month){
			$selected='selected=selected';
		}
		$k=$i+1;
		echo("		<option value='$k' $selected>$MR_MONTHS[$i]</option>");
	}
	echo("	</select>");
	echo("	<br />");
	$daydb=$month-1;
	$db=$MR_MONTHS_DAY[$daydb];
	for ($i=0;$i<$db;$i++){
		$k=$i+1;
		echo("	<input class='inputsubmit5' type='submit' id='submitdate' name='submitdate' value='$k'>");
	}
	echo("</form>");
	if ($year>0){
		echo("<br /><h2>$year. $month. $day.</h2><br /><br />");
	}
	
	echo('</center>');
	
	

	# generate table
	if (($roomsfile<>'')and(!file_exists($roomsfile))){
		touch($roomsfile);
	}
	if (file_exists($roomsfile)){
		$fileContents=file_get_contents($roomsfile);
		$datat=json_decode($fileContents, true);
 
		echo('<center>');
		
		echo("	<div class='card-header-tab'>");
		$db=count($MR_ROOMS);
		for ($i=0;$i<$db;$i++){
			$bid="";
			if ($i==0){
				$bid="id=defaultOpen";
			}
			echo("	<button $bid class='card-button tab-tablinks active' onclick=\"opentab(event, '$MR_ROOMS[$i]')\">$MR_ROOMS[$i]</button>");
		}
		echo("	</div>");
		
		echo("<form action=$MR_ADMINFILE id=0 method=post enctype=multipart/form-data>");
		echo("	<input type='hidden' name='passwordh' id='passwordh' value='$passw'>");
		echo("	<input type='hidden' name='utime' id='utime' value='$utime'>");
		echo("	<input type='hidden' name='roomsfile' id='roomsfile' value='$roomsfile'>");
		
		for ($i=0;$i<$db;$i++){
			echo("<div id='$MR_ROOMS[$i]' class='card-body tab-tabcontent'>");
			if ($datat[$i]==""){
				$k=$i+1;
				$datat[$i]=$L_ROOMNAME.': '.$k."\r\n";
				$dbe=count($MR_EMPTY_TEXT);
				for ($l=0;$l<$dbe;$l++){
					$datat[$i]=$datat[$i]."\r\n".$MR_EMPTY_TEXT[$l];
				}
			}
			echo("<textarea name='$MR_ROOMS[$i]' id='$MR_ROOMS[$i]'>$datat[$i]</textarea>");
			echo("</div>");
		}
	
		echo('<br /><br />');
		
		echo("	<input class='inputsubmit40' type='submit' id='submitsave' name='submitsave' value='$L_BUTTON_SAVE'>");
		echo("	<input class='inputsubmit40r' type='submit' id='submitdel' name='submitdel' value='$L_BUTTON_DELETE'>");	
	
		echo('</form>');
	
		echo('<br /><br />');
		echo('<br /><br />');
	
		echo("<form target=_blank action=$MR_PRINTFILE id=0 method=post enctype=multipart/form-data>");
		echo("	<input type='hidden' name='passwordh' id='passwordh' value='$passw'>");
		echo("	<input type='hidden' name='utime' id='utime' value='$utime'>");
		echo("	<input type='hidden' name='printfile' id='printfile' value='$roomsfile'>");
		echo("	<input type='submit' id='submitprint' name='submitprint' value='$L_PRINT'>");
		echo('</form>');
	
	
	}
	echo('<center>');	
	echo('<br /><br />');
	echo('<hr />');
	echo('<br /><h3>'.$L_CONTINUOUS_HEADER.'</h3>');
	echo('<br />');
	
	echo("<form action=$MR_ADMINFILE id=100 method=post enctype=multipart/form-data>");
	echo("	<input type='hidden' name='passwordh' id='passwordh' value='$passw'>");
	echo("	<input type='hidden' name='utime' id='utime' value='$utime'>");
	
	echo("$L_ROOMNAME:	<select name='roomnum' id='roomnum'>");
	$db=count($MR_ROOMS);
	for ($i=0;$i<$db;$i++){
		$k=$i+1;
		echo("		<option value='$k' $selected>$MR_ROOMS[$i]</option>");
	}
	echo("	</select> : ");
	
	echo("	<select name='startyear' id='startyear'>");
	$db=count($MR_YEARS);
	for ($i=0;$i<$db;$i++){
		$selected='';
		if ($MR_YEARS[$i]==$year){
			$selected='selected=selected';
		}
		echo("		<option value='$MR_YEARS[$i]' $selected>$MR_YEARS[$i]</option>");
	}
	echo("	</select>");
	
	echo("	<select name='startmonth' id='startmonth'>");
		if ($month==0){
			$month=date('m');
	}
	$db=count($MR_MONTHS);
	for ($i=0;$i<$db;$i++){
		$selected='';
		if (($i+1)==$month){
			$selected='selected=selected';
		}
		$k=$i+1;
		echo("		<option value='$k' $selected>$MR_MONTHS[$i]</option>");
	}
	echo("	</select>");
	
	echo("	<select name='startday' id='startday'>");
	$db=31;
	for ($i=0;$i<$db;$i++){
		$k=$i+1;
		echo("		<option value='$k'>$k</option>");
	}
	echo("	</select> - ");
	echo("	<select name='endmonth' id='endmonth'>");
	if ($month==0){
		$month=date('m');
	}
	$db=count($MR_MONTHS);
	for ($i=0;$i<$db;$i++){
		$selected='';
		if (($i+1)==$month){
			$selected='selected=selected';
		}
		$k=$i+1;
		echo("		<option value='$k' $selected>$MR_MONTHS[$i]</option>");
	}
	echo("	</select>");
	
	echo("	<select name='endday' id='endday'>");
	$db=31;
	for ($i=0;$i<$db;$i++){
		$k=$i+1;
		echo("		<option value='$k'>$k</option>");
	}
	echo("	</select>");
	echo("$L_TEXT_NAME:");
	echo("  <input type='text' name='mtext' id='mtext'>");
	echo("  <input type='submit' value='$L_BUTTON_INTERVAL' name='submitinterval'>");
	echo("</form>");
	
	echo('</center>');	
	echo('<br /><br />');
			
}else{

	# password
	
	echo("<h1>$L_SITENAME</h1>");	
	echo("<div class=spaceline100></div>");
	echo("<form  method='post' enctype='multipart/form-data'>");
	echo("    $L_PASS:");
	echo("    <input type='password' name='password' id='password' autofocus>");
	echo("<div class=spaceline></div>");
	echo("    <input type='submit' value='$L_BUTTON_ALL' name='submit'>");
	echo("</form>");
	echo("<div class=spaceline></div>");
}



include("$MR_JS_END");
include("$MR_FOOTER");

?>
