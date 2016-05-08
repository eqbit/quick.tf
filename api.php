<? 

ini_set('max_execution_time', 10000);
require_once 'steamauth/steamauth.php';
require_once 'steamapi.php';
require_once 'connect.php';
?>

<?php
if($_SESSION['steam_steamid'] != '76561198042938501')
	echo 'ACCESS DENIED';
else
{
	if(isset($_POST['function']))
		call_user_func($_POST['function']);
	
	$conn->close();
}
?>
