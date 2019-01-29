<?php

session_start();
require_once 'lib/ts3admin.class.php';
$website_name = "IconAPI"; //Website Name
$ts3ip = "127.0.0.1"; //TS3IP
$ts3port = "9987"; //TS3Port
$ts3qport = "10011"; //TS3Query Port
$ts3user = "serveradmin"; //TS3User Login
$ts3pass = ""; //TS3Query Login

if(isset($_POST['username'])){
	$username = $_POST['username'];
	
	$tsAdmin = new ts3admin($ts3ip, $ts3qport);
	if($tsAdmin->getElement('success', $tsAdmin->connect())) {
		$tsAdmin->login($ts3user, $ts3pass);
		$tsAdmin->setName($website_name);
		$tsAdmin->selectServer($ts3port);
		$clients = $tsAdmin->clientList("-uid -ip -groups -info");
		
		//Find the Client by fetching the Connected IP Adress:
		if($_SERVER['HTTP_CF_CONNECTING_IP']){
			$remoteAddr = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} else {
			$remoteAddr = $_SERVER['REMOTE_ADDR'];
		}
		
		foreach($clients['data'] as $client) {
			$cname = $client['client_nickname'];
			$ip = $client['connection_client_ip'];
			$uid = $client['client_unique_identifier'];
			$groups = $client['client_servergroups'];
			$version = $client['client_version'];
			$platform = $client['client_platform'];
			$clid = $client['clid'];
			$cid = $client['cid'];
			$dbid = $client['client_database_id'];
			$time = time();
			$rid = rand("1", "999999");
			$idname = $time.$rid;
			
			if($remoteAddr == $ip){
				
				//Magic Stuff begins here:
				$endfile = $idname."_".$username;
				
				file_put_contents("dicons/$endfile.png", fopen("https://minotar.net/avatar/$username.png", 'r'));
				$debugger = $tsAdmin->uploadIcon("dicons/$endfile.png");
				$iconID = $debugger['data'][0]['name'];
				$iconID = str_replace("/icon_", "", $iconID);
				
				$permissions = array();
				$permissions['i_icon_id'] = array("$iconID", "0");
				
				$tsAdmin->clientAddPerm($dbid, $permissions);
				
				header("Location: ?icon_set");
				die();
				
			}
		}
		
		echo "HANDLED!";
		
	} else {
		die("ERROR WHILE CONNECTING TO TS3 SERVER!");
	}
		
}
?>
<form action="" method="POST">
	<h1>Simple TS3 Icon System with IP-TS3 Check.</h1>
	<input type="text" name="username" placeholder="Username">
	<button>Set TS3 Icon</button>
</form>