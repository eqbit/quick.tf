<? 
ini_set('max_execution_time', 120);
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

if(!isset($_SESSION['steam_steamid']))
	header ("Location: http://quick.test/?login");

$profileid = $_SESSION['steam_steamid'];



function themedTag($thm)
{
	if($thm == 1)
		echo '<li class="small" style="border: solid #0a7078 2px; color: #0a7078" title="generated price">THEMED</li>';
}

$myBP = file_get_contents("http://quick.test/getBP.php/?steamid=76561198042938501");
$myItems=json_decode(file_get_contents("data/profiles/76561198042938501.json"), true);
$myKeysAmount = 0;

foreach($myItems['result']['items'] as $item)
{
	if($item['defindex'] == "5021")
	{
		$myKeysAmount++;
		continue;
	}
}


$responseBP = file_get_contents("http://quick.test/getBP.php/?steamid=" .$profileid);
$responseSum = file_get_contents("http://quick.test/getSum.php/?steamid=" .$profileid);

$sql = "SELECT * FROM users WHERE steamid=\"" .$profileid. "\" LIMIT 1";
$result = mysqli_query($conn, $sql);


		
if (mysqli_num_rows($result) > 0) 
{
	$row = $result->fetch_assoc();
	
	$banned = $row['banned'];
	$tradeurl = $row['tradeofferurl'];
	$timecreated = $row['timecreated'];
	
	if($row['steamrep'] == 'SCAMMER')
	{
	    $steamrep = 'SCAMMER';
		$srcoolor = "red";
	}
	elseif($row['steamrep'] == 'CAUTION')
	{
		$steamrep = 'CAUTION';
		$srcolor = "orange";
	}
	else
	{
		$steamrep = 'OK';
		$srcolor = "green";
	}
	
	if($row['tradeban'] == 1)
	{
		$tradeban = 'BANNED';
		$tradecolor = "red";
	}
	else
	{
		$tradeban = 'OK';
		$tradecolor = "green";
	}
	
	
}
$title = 'QB - Quick.tf';

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


include 'menu.php';


$items=json_decode(file_get_contents("data/profiles/" .$profileid. ".json"), true);

foreach($items['result']['items'] as $item)
{
	if($item['defindex'] == "5021")
	{
		$keysAmount++;
		continue;
	}
		
	if($item['quality'] == 5)
	{
		if($item['defindex']==266)
			continue;
		
		$unusualsAmount++;
		$existedhats[] = $item['id'];
	}
	
}

if($myKeysAmount < 12)
{
	echo '
<div class="fullscreen" id="overall-unav">
    <div class="fullscreen-text" id="overall-text-unav">The service is temporarily unavailable</div>
</div>';
}

echo '

<div class="fullscreen" id="overall" style="display:none;">
    <div class="fullscreen-text" id="overall-text">Max value exceeded</div>
</div>

<div class="qb_header">
	<span id="name">QB</span>
</div>

<div class="detail_unusuals">
  
  <div style="width: 90%; margin: auto;">
	
	<div class="detail-qb" >
		<h2>
		<p> <span>In stock: ' .$myKeysAmount. ' keys</span></p>
		<p><span id="qboffer">.</span></p> 
		</h2>
	</div>
	
	<div class="unusual_block">';
	   
	$i = 0;
	   
	foreach($items['result']['items'] as $item)
	{
		if($item['quality'] == 5)
	    {
		    if($item['defindex']==266)
			continue;
		    
			$hat = $IDHatArr[$item['defindex']];
		    $effect=$effNamebyIDArr[$item['attributes'][0]['float_value']];
			
			if($effect == NULL || $buyPriceArr[$hat][$effect] == 0 || $buyPriceArr[$hat][$effect] == 'TOO HIGH')
				continue;
			
		    echo '
			<div class="uimg" id="' .$i. '_unusual_taken" style="display: none"  offer="' .$buyPriceArr[$hat][$effect]. '" taken="true">
			
			    <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
						
			    <img src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;">
			</div>
			';
			
			$i ++;
		}
	}   
	   
    echo '	   
	</div>
	
	<div class="qb_offer_bottom" id="offer-text" style="display: none">
	    Click "Send a trade offer" button. <br>
		Take <span id="our_offer">0</span> keys from our side and put unusuals you want to sell.
		
		
		<p><span class="note">NOTICE</span>   This is not an automated banking site.<br>
		All the offers will be reviewed manually. It might take a while.</p>
		
		<p><span class="note">NOTICE</span>   Our prices only applies to clean hats.<br>
		If your hat is duped, you will receive a counter offer.</p>
	</div>
	
  </div>
</div>


<a href="http://quick.test/tradeoffer.php"><div class="tradeofferbutton">Send a trade offer</div></a>

<div class="maindiv_container" style="margin-top: 0px;">


<div class="maindiv_header" style=" margin-top: 50px;">
    Your unusuals
</div>

<div class="umaindiv">



<div class="fullscreen" id="overall" style="display:none;">
    <div class="fullscreen-text" id="overall-text"></div>
</div>';

if($steamrep == 'SCAMMER' && $own)
{
	echo '
<div class="fullscreen" id="overall">
    <div class="fullscreen-text" id="overall-text">You are banned. Reason: Steamrep \'SCAMMER\' tag. This ban is permanent</div>
</div>
';
}

$j = 0;

	foreach($items['result']['items'] as $item)
	{
		if($item['quality'] == 5)
	    {
		    if($item['defindex']==266)
			continue;
		    
			$hat = $IDHatArr[$item['defindex']];
		    $effect=$effNamebyIDArr[$item['attributes'][0]['float_value']];
			
			if($effect == NULL || $buyPriceArr[$hat][$effect] == 0 || $buyPriceArr[$hat][$effect] == 'TOO HIGH')
				continue;
			
		    echo '
			<div class="uimg popup" id="' .$j. '_unusual" choosed="false" offer="' .$buyPriceArr[$hat][$effect]. '" taken="false">
			
			    <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
			
			
			    <span>
				    <p>' .$buyPriceArr[$hat][$effect]. ' keys</p>
                </span>
			
			    <img src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;">
			</div>
			';
			
			
			$j ++;
		}
	}
	
	echo '
	</div>';

	
?>

<script>
$("document").ready(function() 
{
	var value = 0;
		
	$('#qboffer').text('Value: '+value+' keys');
	
	$('.uimg').click(function()
	{
		if($(this).attr('taken') === 'true')
		{
			return;
		}
		
		var checked = ($(this).attr('choosed') === 'true');
		var id = $(this).attr('id');
				
		if(!checked)
		{
			var adds = Number($(this).attr('offer'));
			
			if((value + adds) > <? echo $myKeysAmount; ?> )
			{
                $('#overall').fadeIn('fast');
				return;
			}
			
		    value += adds;
	        $('#qboffer').text('Value: '+value+' keys');
			$(this).attr('choosed', 'true');
			$(this).attr('class', 'uimg hidden');
			$('#'+id+'_taken').show();
			
			$('#our_offer').text(value);
			if(!($('#offer-text').is(':visible')))
				$('#offer-text').slideToggle();
		}
		else
		{
			var adds = Number($(this).attr('offer'));
		    value -= adds;
	        $('#qboffer').text('Value: '+value+' keys');
			$(this).attr('choosed', 'false');
			$(this).attr('class', 'uimg popup');
			$('#'+id+'_taken').hide();
			
			$('#our_offer').text(value);
			
			if(value == 0)
			    $('#offer-text').slideToggle();
				
		}
		
	});
	
	
	$('#overall').click(function()
	{
		$(this).hide();
	});
	
});	

	

</script>

<?
echo '
</div>';
$conn->close();
include 'footer.php';
?>



</body>
</html>