<?php
include("settings.php");
header("Location: ../");
session_start();
unset($_SESSION['steamid']);
unset($_SESSION['steam_steamid']);
unset($_SESSION['steam_uptodate']);
unset($_COOKIE['steamid']);
unset($COOKIE['hash']);
setcookie("steamid", $_SESSION['steam_steamid'], time()-3600*24*14);
setcookie("hash", $steam_hash, time()-3600*24*14);
?>