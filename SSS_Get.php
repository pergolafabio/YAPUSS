<?php 
/** 
    V6 By sebcbien, 18/10/2017
    V6.1 by Jojo (19/10/2017) : server IP adress generated automatically
	V6.2 by sebcbien added ptz placeholder
	V6.3 by Jojo (19/10/2017) : remove hardcoding of file name & location
	V6.4 by Jojo (20/10/2017) : links are opened in a new tab
	V7 by Sebcbien (21/10/2017) : Added enable/disable action
	                              Changed file Name (SSS_Get.php)
	
	ToDo:
	 - accept array of cameras form url arguments
	 - start/stop manual recording
	 - PTZ action
	 
    Thread here:
    https://www.domotique-fibaro.fr/topic/11097-yapuss-passerelle-universelle-surveillance-station/
    Thanks to all open sources examples grabbed all along the web and specially filliboy who made this script possible.
    exemples:
 http://xxxxxx/SSS_Get.php - sans argument, réponds avec la liste de toutes les caméras
 http://xxxxxx/SSS_Get.php?list=json - réponds avec le json de toutes les caméras
 http://xxxxxx/SSS_Get.php?list=camera - affiche la liste de toutes les caméras, infos screenshots etc
 http://xxxxxx/SSS_Get.php?stream_type=jpeg&camera=19&stream=1 - retourne le snapshot de la caméra N° 19, stream N°1
 0: Live stream | 1: Recording stream | 2: Mobile stream  - valeur par défaut: 0 
 http://xxxxxx/SSS_Get.php?action=enable&camera=14 - enable camera 14
 http://xxxxxx/SSS_Get.php?action=disable&camera=12 - disable camera 12
 http://xxxxxx/SSS_Get.php?stream_type=mjpeg&camera=19 - retourne le flux mjpeg pour la caméra 19
 */
// Configuration 
$user = "xxxxxx";  // Synology username with rights to Surveillance station 
$pass = "xxxxxx";  // Password of the user entered above 
$ip_ss = "192.168.xxx.xxx";  // IP-Adress of Synology Surveillance Station
$ip = $_SERVER['SERVER_ADDR']; // IP-Adress of your Web server hosting this script
$file = $_SERVER['PHP_SELF'];  // path& file name of this running php script
$port = "5000";  // default port of Surveillance Station 
$http = "http"; // Change to https if you use a secure connection
$stream_type = $_GET['stream_type'];
$cameraID = $_GET['camera'];
$cameraStream = $_GET["stream"];
$cameraPtz = $_GET["ptz"];
$action = $_GET["action"];
$list = $_GET["list"];
$vCamera = 7; //Version API SYNO.SurveillanceStation.Camera
$vAuth = ""; // 2; with 2, no images displayed, too fast logout problem ?  //Version de l' SYNO.API.Auth a utiliser

if ($cameraStream == NULL && $stream_type == NULL && $cameraID == NULL && $cameraPtz == NULL && $action == NULL) { 
    $list = "camera"; 
} 

if ($cameraStream == NULL) { 
    $cameraStream = "0"; 
} 

if ($stream_type == NULL) { 
    $stream_type = "jpeg"; 
} 
//Get SYNO.API.Auth Path (recommended by Synology for further update)
	$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1&query=SYNO.API.Auth');
	$obj = json_decode($json);
	$AuthPath = $obj->data->{'SYNO.API.Auth'}->path;
//echo $AuthPath;

// Authenticate with Synology Surveillance Station WebAPI and get our SID 
	$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/'.$AuthPath.'?api=SYNO.API.Auth&method=Login&version=6&account='.$user.'&passwd='.$pass.'&session=SurveillanceStation&format=sid'); 
	$obj = json_decode($json); 

//Check if auth ok
if($obj->success != "true"){
	echo "error";
	exit();
}else{

//authentification successful
$sid = $obj->data->sid;
//echo '<p>'.$sid.'</p>';
//echo '<p>'.$obj.'</p>';

//Get SYNO.SurveillanceStation.Camera path (recommended by Synology for further update)
        $json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1&query=SYNO.SurveillanceStation.Camera');
        $obj = json_decode($json);
        $CamPath = $obj->data->{'SYNO.SurveillanceStation.Camera'}->path;
//print $CamPath;

// Get Snapshot
if ($cameraID != NULL && $stream_type == "jpeg" && $cameraPtz == NULL && $action == NULL) { 

// Setting the correct header so the PHP file will be recognised as a JPEG file 
	header('Content-Type: image/jpeg'); 
// Read the contents of the snapshot and output it directly without putting it in memory first 
	readfile($http.'://'.$ip_ss.':'.$port.'/webapi/'.$CamPath.'?camStm='.$cameraStream.'&version='.$vCamera.'&cameraId='.$cameraID.'&api=SYNO.SurveillanceStation.Camera&preview=true&method=GetSnapshot&_sid='.$sid); 
exit();
}

//print $list;
//get Camera List
if ($list == "json") {
	echo "<p>Json camera list viewer</p>";
	echo "<p><a href=https://codebeautify.org/jsonviewer>https://codebeautify.org/jsonviewer</a></p>";
	https://codebeautify.org/jsonviewer

//list of known cams 
	$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/'.$CamPath.'?api=SYNO.SurveillanceStation.Camera&version='.$vCamera.'&method=List&_sid='.$sid);
	$obj = json_decode($json);
	echo $json;
exit();
}

if ($list == "camera") {
	echo "<p>camera list</p>";

//list of known cams 
	$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/'.$CamPath.'?api=SYNO.SurveillanceStation.Camera&version='.$vCamera.'&method=List&_sid='.$sid);
	$obj = json_decode($json);
foreach($obj->data->cameras as $cam){
	$id_cam = $cam->id;
	$nomCam = $cam->detailInfo->camName;
	$vendor = $cam->vendor;
	$model = $cam->model;
	echo "<p>-----------------------------------------------------------------------------------------</p>";
	echo "<p>Cam <b>". $nomCam ." (" . $id_cam . ")</b> detected</p>";
	echo "<p>Vendor <b>". $vendor ." Model:(" . $model . ")</b></p>";
	echo "<p> Snapshot on Stream Live: <a href=http://".$ip.$file."?stream_type=jpeg&camera=".$id_cam."&stream=0 target='_blank'>http://".$ip.$file."?stream_type=jpeg&camera=".$id_cam."&stream=0</a></p>";
	echo "<p> Snapshot on Stream Recording on Syno: <a href=http://".$ip.$file."?stream_type=jpeg&camera=".$id_cam."&stream=1 target='_blank'>http://".$ip.$file."?stream_type=jpeg&camera=".$id_cam."&stream=1</a></p>";
	echo "<p> Snapshot on Stream Mobile: <a href=http://".$ip.$file."?stream_type=jpeg&camera=".$id_cam."&stream=2 target='_blank'>http://".$ip.$file."?stream_type=jpeg&camera=".$id_cam."&stream=2</a></p>";
	echo "<p> Stream MJPEG: <a href=http://".$ip.$file."?stream_type=mjpeg&camera=".$id_cam." target='_blank'>http://".$ip.$file."?stream_type=mjpeg&camera=".$id_cam."</a></p>";
	// http://diskstation412/get_mjpeg/getV1.php?cam=19&format=mjpeg
	//check if cam is connected
	if(!$cam->status) {
		//check if cam is activated and not recording
		if($cam->enabled && !$cam->recStatus) {
	//showing a snapshot of the camera
			echo "<img src='".$http."://".$ip_ss.":".$port."/webapi/".$CamPath."?api=SYNO.SurveillanceStation.Camera&version=7&method=GetSnapshot&preview=true&camStm=1&cameraId=".$id_cam."&_sid=".$sid."' alt='image JPG' width='480' height='360'>";
		}
		else{
			echo "<p>Cam " . $id_cam . " RECORDING .... Should be skipped ?</p>";
			echo "<img src='".$http."://".$ip_ss.":".$port."/webapi/".$CamPath."?api=SYNO.SurveillanceStation.Camera&version=7&method=GetSnapshot&preview=true&camStm=1&cameraId=".$id_cam."&_sid=".$sid."' alt='image JPG' width='480' height='360'>";
		}
	}
	else{
		echo "<p>Cam " . $id_cam . " deconnected</p>";
	}
}
exit();
}

if ($cameraPtz != NULL) {
	echo "Camera PTZ argument: ".$cameraPtz."  --  Camera id: ".$cameraID;
exit();
}

if ($action != NULL) {
//list of known cams 
	$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/'.$CamPath.'?api=SYNO.SurveillanceStation.Camera&version=3&method=List&_sid='.$sid);
	$obj = json_decode($json);

	foreach($obj->data->cameras as $cam){
	$id_cam = $cam->id;
	$nomCam = $cam->detailInfo->camName;
		//echo "cam ".$id_cam." ".$nomCam." Action: ".$action." cam (".$cameraID.")</p>";
			//check if cam is activated or not
		if($action == "disable" && $id_cam == $cameraID) {
				//if cam already Disabled
				if(!$cam->enabled) {
					echo '{"id_cam":'.$id_cam.',"camName":"'.$nomCam.'","Status":"Disabled"}';
					exit();
				}
				echo '{"id_cam":'.$id_cam.',"camName":"'.$nomCam.'","Status":"Disabled"}';
				//Deactivate cam
				$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/'.$CamPath.'?api=SYNO.SurveillanceStation.Camera&method=Disable&version=3&cameraIds='.$cameraID.'&_sid='.$sid);
				exit();
		}else if($action == "enable" && $id_cam == $cameraID) {
				//if cam already Enabled
				if($cam->enabled) {
					echo '{"id_cam":'.$id_cam.',"camName":"'.$nomCam.'","Status":"Disabled"}';
					exit();
				}
				echo '{"id_cam":'.$id_cam.',"camName":"'.$nomCam.'","Status":"Enabled"}';
				$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/'.$CamPath.'?api=SYNO.SurveillanceStation.Camera&method=Enable&version=3&cameraIds='.$cameraID.'&_sid='.$sid);
				exit();
		}
	}
}

// Get MJPEG
if ($cameraID != NULL && $stream_type == "mjpeg") {
	$link_stream = 'http://' . $ip_ss . ':' . $port . '/webapi/SurveillanceStation/videoStreaming.cgi?api=SYNO.SurveillanceStation.VideoStream&version=1&method=Stream&cameraId=' . $cameraID . '&format=mjpeg&_sid=' . $sid;

	set_time_limit ( 60 ); 

	$r = ""; 
	$i = 0; 
	$boundary = "\n--myboundary"; 
	$new_boundary = "newboundary"; 

	$f = fopen ( $link_stream, "r" ); 

	if (! $f) { 
		// **** cannot open 
		print "error"; 
	} else { 
		// **** URL OK 
		header ( "Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0" ); 
		header ( "Cache-Control: private" ); 
		header ( "Pragma: no-cache" ); 
		header ( "Expires: -1" ); 
		header ( "Content-type: multipart/x-mixed-replace;boundary={$new_boundary}" ); 

		while ( true ) { 

			while ( substr_count ( $r, "Content-Length:" ) != 2 ) { 
				$r .= fread ( $f, 32 ); 
			} 

			$pattern = "/Content-Length\:\s([0-9]+)\s\n(.+)/i"; 
			preg_match ( $pattern, $r, $matches, PREG_OFFSET_CAPTURE ); 
			$start = $matches [2] [1]; 
			$len = $matches [1] [0]; 
			$end = strpos ( $r, $boundary, $start ) - 1; 
			$frame = substr ( "$r", $start + 2, $len ); 

			print "--{$new_boundary}\n"; 
			print "Content-type: image/jpeg\n"; 
			print "Content-Length: ${len}\n\n"; 
			print $frame; 
			usleep ( 40 * 1000 ); 
			$r = substr ( "$r", $start + 2 + $len ); 
		} 
	} 

	fclose ( $f ); 
}

//Get SYNO.API.Auth Path (recommended by Synology for further update)
	$json = file_get_contents($http.'://'.$ip_ss.':'.$port.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1&query=SYNO.API.Auth');
	$obj = json_decode($json);
	$AuthPath = $obj->data->{'SYNO.API.Auth'}->path;

//Logout and destroying SID
	$json = file_get_contents($http."://".$ip_ss.":".$port."/webapi/".$AuthPath."?api=SYNO.API.Auth&method=Logout&version=".$vAuth."&session=SurveillanceStation&_sid=".$sid);
	$obj = json_decode($json);
	}
?>