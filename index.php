<? 
require_once 'menu.php';
require_once 'steamapi.php';

$sql = "SELECT * FROM hats";
$result = $conn->query($sql);	
	
while($row = $result->fetch_assoc())
{
	$data[] = $row['name'];
	$data[$row['name']]['bp'] = $row['bptfprice'];
	$data[$row['name']]['qp'] = $row['qprice'];
	$data[$row['name']]['buyp'] = $row['buyprice'];
	$data[$row['name']]['themed'] = $row['themed'];
	$data[$row['name']]['class'] = $row['class'];
}


$sql = "SELECT * FROM trades";
$result = $conn->query($sql);

while($row = $result->fetch_assoc())
{
	$trades[$row['id']]['uniqueid'] = $row['uniqueid'];
	$trades[$row['id']]['id'] = $row['id'];
	$trades[$row['id']]['name'] = $row['name'];
	$trades[$row['id']]['effect'] = $row['effect'];
	$trades[$row['id']]['org_id'] = $row['originalid'];
	$trades[$row['id']]['price'] = $row['price'];
	$trades[$row['id']]['owner'] = $row['owner'];
	$trades[$row['id']]['date'] = $row['bumped'];
	
	
	
	$bpPrice = $bpPricesArr[$row['name']][$row['effect']];
	$qPrice = $qPriceArr[$row['name']][$row['effect']];
	
	if($price >= $qPrice or $price >= $bpPrice)
	    $trades[$row['id']]['profit'] = 0;
	else
	{
		if($bpPrice > $qPrice)
	        $trades[$row['id']]['profit'] = $qPrice - $row['price'];
		else
			$trades[$row['id']]['profit'] = $bpPrice - $row['price'];
	}
}


?>

<div class="maindiv_container">

    <div class="maindiv_header">
        <H2>
            Best deals
        </H2>
    </div>

    <div class="umaindiv">
	
	<?
		
	    usort($trades, "sortByProfit");
		
	    for($i = 0; $i < 14; $i ++)
		{
			$hat = $trades[$i]['name'];
			$effect = $trades[$i]['effect'];
			$price = $trades[$i]['price'];
			$profit = $trades[$i]['profit'];
			$link = $trades[$i]['id'];
			$link = 'http://quick.test/trade/' .$link;
			
			echo '
			<a href="' .$link. '">
			<div class="uimg" title="estimated gain - ' .$profit. ' keys">
			    <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
				<img src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;">
			    <div class="utag">' .$price. '</div>
				<div class="uProfitTag">' .$profit. '</div>
			</div>
			</a>
				';
		}
	?>
	
	</div>

</div>

<div class="div_container ">

    <a href="http://quick.test/trades">
    <div class="mainbox" id="trades">
	    Trades
    </div>
	</a>
 
    <a href="http://quick.test/stats">
    <div class="mainbox" id="stats">
	    Unusuals
    </div>
	</a>
 
    <a href="http://quick.test/qb">
    <div class="mainbox" id="qb">
	    QB
    </div>
	</a>
    
	<?
	if(isset($_SESSION['steam_steamid']))
		echo
	'<a href="http://quick.test/profile/' .$_SESSION['steam_steamid'].'">';
	else
		echo
	'<a href="http://quick.test/?login">';
	?>
    <div class="mainbox" id="profile">
	    Profile
    </div>
	</a>
 
</div>

<div class="maindiv_container">

    <div class="maindiv_header">
        <H2>
            Latest Listings
        </H2>
    </div>

    <div class="umaindiv">
	
	<?
        usort($trades, "sortByDate");
		
	    for($i = 0; $i < 28; $i ++)
		{
			$hat = $trades[$i]['name'];
			$effect = $trades[$i]['effect'];
			$price = $trades[$i]['price'];
			$link = $trades[$i]['id'];
			$link = 'http://quick.test/trade/' .$link;
			
			echo '
			<a href="' .$link. '">
			<div class="uimg">
			    <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
				<img src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;">
			    <div class="utag">' .$price. '</div>
			</div>
			</a>
				';
		}
	?>
	
	</div>

</div>





<?php
include 'footer.php';
?>

</body>
</html>