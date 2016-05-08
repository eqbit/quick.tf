<?
    $hatArr = json_decode(file_get_contents('http://quick.test/data/hatNames.json'), true);
	
    $request = htmlspecialchars($_GET["request"]);
	$request = trim($request);
	$request =  stripslashes($request);
	
if(strpos($request, 'steamcommunity.com') !== false)
{
	if(strpos($request, 'steamcommunity.com/id') !== false){
		$request = str_replace("https", "http", $request);
		$request = str_replace("http://steamcommunity.com/id/", "", $request);
		$request = str_replace("steamcommunity.com/id/", "", $request);
		$request = str_replace("/", "", $request);
		$data = file_get_contents("http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key=5F5BB30E37DF4D3E9FA00ED5BE86D2CB&vanityurl=" .$request);
	
	    $link = json_decode($data, true);
	
	    header ("Location: http://backpack.tf/profiles/" .$link[response][steamid]);
	}
	else
	{
		if(strpos($request, 'steamcommunity.com/profiles/7') !== false){
		    $request = str_replace("https", "http", $request);
			$request = str_replace("http://steamcommunity.com/profiles/", "", $request);
		    $request = str_replace("steamcommunity.com/profiles/", "", $request);
		    $request = str_replace("/", "", $request);
			
			header ("Location: http://backpack.tf/profiles/" .$request);
		}
	}
}
else
{
	$existed = false;
	
	foreach($hatArr as $hat)
	{
		if(stripos($hat, $request) !== false)
		{
			$existed = true;
		    break;
		}
	}
	
	if(!$existed)
		header ("Location: http://quick.test");
	else
		header ("Location: http://quick.test/search/" .$request);
}
	
?>