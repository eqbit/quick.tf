<?php
ini_set('max_execution_time', 180);
require 'cache30.php';
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";

function getUserSum()
{
	global $conn;
	$steamid = (string)$_GET['steamid'];
    $sum = json_decode(file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=5F5BB30E37DF4D3E9FA00ED5BE86D2CB&steamids=" .$steamid), true);
	
	$bans =  json_decode(file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key=5F5BB30E37DF4D3E9FA00ED5BE86D2CB&steamids=" .$steamid), true);
	
	$trust = json_decode(file_get_contents("http://steamrep.com/api/beta4/reputation/" .$steamid. "?json=1"), true);
	
	$steamrep = $trust['steamrep']['reputation']['summary'];
	
	$banned = "0";
	
	if($bans['players'][0]['EconomyBan'] == 1 || $steamrep == "SCAMMER")
		$banned = "1";
	
	$sql = "SELECT * FROM users WHERE steamid=\"" .$steamid. "\" LIMIT 1";
    $result = mysqli_query($conn, $sql);
		
    if (mysqli_num_rows($result) == 0) 
    {
		$sql = "INSERT INTO users (steamid) VALUES ('" .$steamid. "')";
		$conn->query($sql);
    }
	$sql = "UPDATE users SET 
	avatar = \"" .$sum['response']['players'][0]['avatarfull']. "\",
	profileurl = \"" .$sum['response']['players'][0]['profileurl']. "\",
	name = \"" .$sum['response']['players'][0]['personaname']. "\",
	tradeban =\"" .$bans['players'][0]['EconomyBan']. "\",
	vacban=\"" .$bans['players'][0]['VACBanned']. "\",
	status=\"" .$sum['response']['players'][0]['personastate']. "\", 
	banned=\"" .$banned. "\", 
	timecreated=\"" .$sum['response']['players'][0]['timecreated']. "\", 
	steamrep=\"" .$steamrep. "\" 
	WHERE steamid=" .$steamid;
		
    if ($conn->query($sql) === TRUE) 
	{
        echo "Record updated successfully";
    } 
	else 
	{
        echo "Error updating record: " . $conn->error;
    }
}

getUserSum();

require 'cache_footer.php';
?>