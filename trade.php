<? 	
require_once 'steamapi.php';
require_once 'steamauth/steamauth.php';
require_once 'connect.php';

function bb( $input ) {
    return preg_replace('/<br\s?\/?>/ius', "\n", str_replace("\n","",str_replace("\r","", htmlspecialchars_decode($input))));
}

$tradeId = $_GET['id'];

$sql = "SELECT * FROM trades WHERE id='" .$tradeId. "' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
	$row = $result->fetch_assoc();

    $hat = $row['name'];
    $effect = $row['effect'];
    $uniqueid = $row['uniqueid'];
    $originalid = $row['originalid'];
    $created = $row['date'];
    $price = $row['price'];
    $owner = $row['owner'];
	$desk = $row['desk'];
}



$sql = "SELECT * FROM users WHERE steamid='" .$owner. "' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
	$row = $result->fetch_assoc();

    $ownerName = $row['name'];
	$tradeUrl = $row['tradeofferurl'];
	$avatarUrl = $row['avatar'];
}


$sql = "SELECT * FROM hats WHERE name=\"" .$effect. " " .$hat. "\" LIMIT 1";
$result = $conn->query($sql);
	
if ($result->num_rows > 0)
{
	$row = $result->fetch_assoc();

    $bptfprice = $row['bptfprice'];
    $buyprice = $row['buyprice'];
    $themed = $row['themed'];
	
	if($themed)
		$qprice = 0;
	else
        $qprice = $row['qprice'];
}


$title = $effect. ' ' .$hat. ' - ' .$price. ' keys. Quick.tf';
	
include 'menu.php';
echo '<div class="fullscreen" id="overall" style="display:none;" onclick="$(this).fadeOut(\'slow\')"></div>';

if($_SESSION['steam_steamid'] == '76561198042938501')
{
	$adminMode = true;
}
else
{
	$adminMode = false;
}

$lastPriceUpdate = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['last_update'];
if($lastPriceUpdate != 0)
	$lastPriceUpdate = gmdate("Y-m-d", $lastPriceUpdate);
else 
	$lastPriceUpdate = 'Never been suggested';
?>

<div class="detail_header">
	<p><H3><? echo strtoupper($effect. " " .$hat); ?></H3></p>
</div>

<div class="detail" style="height: 200px; margin: auto; width: 800px; margin-top: 0px;">
  <div style="width: 20%; float: left;">
    <div class="uimg_big">
	    <img src="http://backpack.tf/images/440/particles/<? echo $effIDbyNameArr[$effect]; ?>_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;" id="detaileffect" >
	    <img src="<?echo $schema["result"]["items"][$hatIDArr[$hat]]["image_url"] ?>" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:3;">
	</div>
  </div>
  
  <div style="width: 79%; float: left;">
	
	<div class="detail-text">
		<span style='display: none; color:#0a7078; margin-left:20px; padding:5px;border: 2px #0a7078 solid' id='span_themed'>THEMED</span></p>
		<p>
		<a href="http://quick.tf/search/<? echo $hat; ?>">  <span class='det'>SEARCH</span></a>
		<a href="http://backpack.tf/item/<? echo $originalid; ?>">  <span class='det'>HISTORY</span></a>
		<a href="http://backpack.tf/stats/Unusual/<? echo $hat; ?>/Tradable/Craftable/<? echo $effIDbyNameArr[$effect]; ?>"><span class='det'>STATS</span></a>
		</p>
		<h3>
		<? if($bptfprice > 0)
			echo "<p class='details'>Suggested price: " .$bptfprice. " keys</p>";
		if($qprice > 0)
			echo "<p class='details'>Recommended price: " .round($qprice/100*90). "-" .round($qprice/100*110). " keys</p>";
		?>
		<p class='details'>Last price update: <? echo $lastPriceUpdate; ?></p>
		</h3>
		
		
	</div>
  </div>
</div>


<a href="<? echo $tradeUrl; ?>" class="link">
<div class="detail_offer" title="Send <? echo $ownerName; ?> a trade offer">
	<h1 id="buyout">BUYOUT : <? echo $price; ?> keys</h1>
	<h1 id="tradeofferpanel" style="display:none;">SEND <? echo $ownerName; ?> A TRADE OFFER</h1>
</div>
</a>


<div class="maindiv_container">

<div class="maindiv_header">
    <a class="link" href="http://quick.tf/profile/<? echo $owner; ?>">
    <div class="floating_avatar" style="background-image: url('<? echo $avatarUrl; ?>');">
    </div>
	<H3><span style="padding-left: 60px"><? echo $ownerName; ?></span></H3>
	</a>
</div>

<div class="umaindiv" id="tinylist" style="margin-top: 0px;">
<div style="width: 96%; background: white; padding: 20px; opacity: 0.9; text-align: left;">
<?
if($desk != NULL)
{
    $desk = nl2br($desk);
	echo $desk;
}
else
{
	echo 'No description';
}
?>
</div>
</div>

</div>
<script>

$('#infoPanel').click(function(){
	$('#fullInfo').slideToggle();
});

$(function() {
    $('.detail_offer').hover(function(){
	    $("#buyout").hide();
		$("#tradeofferpanel").fadeIn(100);
	},
	
	function(){
		$("#tradeofferpanel").hide();
		$("#buyout").fadeIn(100);
	});
});

</script>
<?
$conn->close;
include 'footer.php';
?>


</body>
</html>