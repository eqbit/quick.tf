<?php
require 'cache3.php';
require_once 'steamauth/steamauth.php'; 
function getSteamBPData()
{
	//$steamid = $_GET['steamid'];
    //$backpack = json_decode(file_get_contents("http://api.steampowered.com/IEconItems_440/GetPlayerItems/v0001/?key=5F5BB30E37DF4D3E9FA00ED5BE86D2CB&SteamID=" .$steamid), true);
	
	if($backpack[result][status] == 1)
	{
		file_put_contents('data/profiles/' .$steamid. '.json', json_encode($backpack));
		echo "Just updated";
	}
	else
	{
		echo "Steam API down. Using latest cache";
	}
}

getSteamBPData();

require 'cache_footer.php';
?>