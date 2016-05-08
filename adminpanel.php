<? 
ini_set('max_execution_time', 600);
require_once 'steamapi.php';

$title = 'adminpanel';

require_once 'menu.php';

echo '
<div class="fullscreen" id="overall" style="display:none;">
    <div class="fullscreen-text" id="overall-text"></div>
</div>';


if($_SESSION['steam_steamid'] != '76561198042938501')
	echo 'ACCESS DENIED';
else
{ 
    echo '<div style="width:300px; margin:100px; float: left">
	';
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"fillUnusualsDB\" type=\"submit\" name=\"function\" ></form></p>
	";
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"fillClassDB\" type=\"submit\" name=\"function\" /></form></p>
	";
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genOPFiles\" type=\"submit\" name=\"function\" /></form></p>
	";
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"editHatTags\" type=\"submit\" name=\"function\" /></form></p>
	";
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genHatIDs\" type=\"submit\" name=\"function\" /></form></p>
	";
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genHatNums\" type=\"submit\" name=\"function\" /></form></p>
	";
	
	echo "<p><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"getSchema\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"getBPData\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"getEffects\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"getExtEffects\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genTier\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"getHats\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"getBPPrices\" type=\"submit\" name=\"function\" /></form></p>
	";
	
	echo "<p style='margin:40px'><form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genAvgHatPrices\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genQPrices\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genClassArr\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genBuyPrices\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genBuyPricesImproved\" type=\"submit\" name=\"function\" /></form>
	";
	echo "<form action='api.php' method=\"post\" target=\"_blank\"><input class=\"button\" value=\"genOPfiles\" type=\"submit\" name=\"function\" /></form></p>
	";
}

?>


</div>

<div style="width:300px; margin:100px; float: left">
	
<?

echo '<p><H2>Total unique visitors: ' .$counterVal. '</H2></p>';

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$numUsers = 0;


if($result->num_rows > 0)
{
	while($row = $result->fetch_assoc())
	{
		$numUsers ++;
	}
}

echo '<H2>Users: ' .$numUsers. '</H2>
</div>';

$conn->close();
?>

</body>
</html> 
