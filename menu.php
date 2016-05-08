<? 
$root = __DIR__;
require_once $root.'/steamauth/steamauth.php'; 
require_once 'connect.php'; 

if($_SESSION['steam_steamid'] == '76561198042938501')
	$adminMode = true;
else
	$adminMode = false;
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<link rel="stylesheet" type="text/css" href="http://quick.test/style.css" />
<link href="http://quick.test/content/img/ico.png" rel="shortcut icon" type="image/x-icon" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<? if(isset($title))
{
	echo '<title>' .$title. '</title>';
}
else
{
	echo '<title>quick.test - Unusual quickmarket</title>';
}?>
</head>
<body>


<?php

echo '
<div id="wrapper">
<div id="header">
<a href="http://quick.test"><div class="menu"><img src="http://quick.test/content/img/logo.png"></div></a>
<a href="http://quick.test/qb"><div class="menu-link">QB</div></a>
<a href="http://quick.test/stats"><div class="menu-link">Prices</div></a>
<a href="http://quick.test/trades"><div class="menu-link">Listings</div></a>';

	echo '
<div class="menu" style="float: right;"><form method="get" id="bpform" ><input type="text" name="search" id="searchbox" placeholder="Search..." style="background-color: white;" /></form></div>';
	    if(!isset($_SESSION['steamid'])) 
		{
			if(isset($_COOKIE['hash']))
			{
				$sql = "SELECT * FROM users WHERE steamidhash = '" .$_COOKIE['hash']. "' LIMIT 1";
				$result = $conn->query($sql);
				
				if ($result->num_rows > 0)
				{
					$row = $result->fetch_assoc();
					$steamid = $row['steamid'];
					$_SESSION['steamid'] = $steamid;
					$_SESSION['steam_steamid'] = $steamid;
					
					include ('steamauth/userInfo.php');
			        if($steamprofile['personastate'] == 0)
				        $color = "#d0d0d0";
			        else
				        $color = "#38c300";
			        echo '
<div class="menu" style="padding: 0; float: right;"><a href="http://quick.test/profile/' .$_SESSION['steam_steamid']. '"><img src="'.$steamprofile['avatar'].'" title="' .$steamprofile['personaname']. '" style="margin-top:2px; border:2px solid ' .$color. '; border-radius:2px;"></a></div>';
				}
				else
				{
					unset($_COOKIE['hash']);
					echo '<div class="menu" style="padding: 0px; float: right;">';
                    steamlogin();
			        echo '</div>';
				}
			}
			else
			{
			    echo '<div class="menu" style="padding: 0px; float: right;">';
                steamlogin();
			    echo '</div>';
			}
        }  
		else
		{
            include ('steamauth/userInfo.php');
			if($steamprofile['personastate'] == 0)
				$color = "#d0d0d0";
			else
				$color = "#38c300";
			echo '<div class="menu" style="padding: 0; float: right;"><a href="http://quick.test/profile/' .$_SESSION['steam_steamid']. '"><img src="'.$steamprofile['avatar'].'" title="' .$steamprofile['personaname']. '" style="margin-top:2px; border:2px solid ' .$color. '; border-radius:2px;"></a></div>';
			
			if($_SESSION['steam_steamid'] != $_COOKIE['steamid'] || !isset($_COOKIE['hash']))
			{
			    $_SESSION['steam_steamid'] = str_replace('/', '', $_SESSION['steam_steamid']);
				$sql = "SELECT * FROM users WHERE steamid = '" .$_SESSION['steam_steamid']. "' LIMIT 1";
				$result = $conn->query($sql);
				
				if ($result->num_rows > 0)
				{
					setcookie("steamid", $_SESSION['steam_steamid'], time()+3600*24*14);
					
					$steam_hash = md5($_SESSION['steam_steamid'].rand(0, 10000));
					
					$sql = "UPDATE users SET steamidhash='" .$steam_hash. "' WHERE steamid='" .$_SESSION['steam_steamid']. "'";
					$conn->query($sql);
					
					setcookie("hash", $steam_hash, time()+3600*24*14);
				}
				else
				{
					$mysqlTimestamp = time();
					$steam_hash = md5($_SESSION['steam_steamid'].rand(0, 10000));
					$sql = "INSERT INTO users (name, steamid, registered, steamidhash) VALUES ('".$_SESSION['steam_personaname']."', '" .$_SESSION['steam_steamid']. "', '" .$mysqlTimestamp. "', '" .$steam_hash. "')";
					$conn->query($sql);
					setcookie("steamid", $_SESSION['steam_steamid'], time()+3600*24*14);
					setcookie("hash", $steam_hash, time()+3600*24*14);
				}
				$conn->close();
			}
		}
		
if(isset($_SESSION['steamid']))
{
	echo '
		 <a href="http://quick.test/profile/' .$_SESSION['steam_steamid']. '"><div class="menu-link">Profile</div></a>
	';
}
if($adminMode)
{
	echo '<a href="http://quick.test/adminpanel.php"><div class="menu-link">Control Panel</div></a>';
}
		
		 echo '
</div>
<div id="content">
';
?>



<script>
$("#bpform").on("submit", function(event){     

    // prevent form from being submitted
    event.preventDefault();

    // get value of text box using .val()
    var name = $("#searchbox").val();
	var str = 'steamcommunity.com';
	//var temp = name.split("/").filter(function(el){ return !!el; }).pop();

	if(name != '' && name.length > '2')
	{
		if(name.indexOf(str) != -1)
		{
			window.open("http://quick.test/qsearch.php/?request=" + name, "_blank");
		}
		else
		{
            window.open("http://quick.test/qsearch.php/?request=" + name, "_self");
		}
	    $("#searchbox").val("");
	}
});
</script>


