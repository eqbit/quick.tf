<?php
ob_start();
session_start();
require ('openid.php');

$root = __DIR__;


function logoutbutton() {
    echo "<form action=\"" .$root. "/steamauth/logout.php\" method=\"post\"><input class=\"button\" value=\"Logout\" type=\"submit\" style=\"width: 185px; height: 25px; line-height: 16px;\" /></form>"; //logout button
}

function steamlogin()
{
try {
	require("settings.php");
    $openid = new LightOpenID($steamauth['domainname']);
    
    
    if(!$openid->mode) {
        if(isset($_GET['login'])) {
            $openid->identity = 'http://steamcommunity.com/openid';
            header('Location: ' . $openid->authUrl());
        }
    echo "<form action=\"http://quick.test/?login\" method=\"post\"> <input type=\"image\" src=\"http://quick.test/content/img/login.png\" style='height:23px; width:154px; margin-top: 8px;'></form>";
}

     elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        if($openid->validate()) { 
                $id = $openid->identity;
                $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                preg_match($ptn, $id, $matches);
              
                $_SESSION['steamid'] = $matches[1];
                $_SESSION['steam_steamid'] = $_SESSION['steamid'];				
                 if (isset($steamauth['loginpage'])) {
					header('Location: '.$steamauth['loginpage']);
                 }
        } else {
                echo "User is not logged in.\n";
        }

    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
}

?>
