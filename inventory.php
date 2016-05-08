<? 
ini_set('max_execution_time', 120);
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

$profileid = $_GET['id'];

$sql = "SELECT * FROM users WHERE steamid=\"" .$profileid. "\" LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) 
{
	$row = $result->fetch_assoc();
	$username = htmlspecialchars($row['name']);
	$banned = $row['banned'];
	$tradeurl = $row['tradeofferurl'];
	
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
	
	if($row['vacban'] == 1)
	{
		$vacbanned = 'BANNED';
		$vaccolor = "red";
	}
	else
	{
		$vacbanned = 'OK';
		$vaccolor = "green";
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
$title = $username. '\'s inventory. quick.test';
?>

<?php include 'menu.php';

$steamid = $_SESSION['steam_steamid'].'';

if($steamid == $profileid)
	$own = true;
else
	$own = false;

$sql = "SELECT * FROM trades WHERE owner=" .$profileid;
$result = $conn->query($sql);
$numTrades = 0;

if ($result->num_rows > 0)
{
	$areTrades = true;
	while($row = $result->fetch_assoc())
    {
	    $trades[] = $row['uniqueid'];
		$numTrades ++;
    }
}
else
	$areTrades = false;


$keysAmount = 0;
$unusualsAmount = 0;
$response = file_get_contents("http://quick.test/getBP.php/?steamid=" .$profileid);
if($response == "Just updated")
	$color = "white";
else
	$color = "red";

if($steamrep == 'SCAMMER' && $own)
{
	echo '
<div class="fullscreen" id="overall">
    <div class="fullscreen-text" id="overall-text">You are banned. Reason: Steamrep \'SCAMMER\' tag. This ban is permanent</div>
</div>
';
}

if($own)
	echo '
<script>
function givePriceInput(suniq)
{
	$("#"+suniq+"_selldiv").fadeOut(\'fast\', function()
	{
	    $("#"+suniq+"_inputdiv").fadeIn(\'fast\', function()
		{
	        $("#"+suniq+"_input").focus();
		});
	});
}
function sendToTrades(sname, seffect, sowner, suniq, sorigin){
	
	var sprice = $(\'#\'+suniq+\'_input\').val();
	
	if(sprice>0)
	{
		$.post("http://quick.test/addatrade.php",
	    {
		    name: sname,
            effect: seffect,
		    steamid: sowner,
		    uniq: suniq,
		    origin: sorigin,
		    price: sprice
	    },
	    function(data){
			if(data == "Success")
			{
				$(\'#\'+suniq+\'_box\').fadeOut(\'slow\');
					
			}
			else 
			{
				$(\'#overall-text\').html(data);
                $(\'#overall\').fadeIn(\'slow\', function () {
                $(this).delay(3000).fadeOut(\'slow\');
                });
			}
	    });
	}
}

</script>';
?>
<div class="fullscreen" id="overall" style="display:none;">
    <div class="fullscreen-text" id="overall-text"></div>
</div>
<div class="prftagdiv">
    <a href="http://quick.test/profile/<?php echo $profileid; ?>"><div class="prftag">PROFILE</div></a>
    <div class="prftag" id="active">INVENTORY</div>
	<a href="http://quick.test/mytrades/<?php echo $profileid; ?>"><div class="prftag">LISTINGS</div></a>
	
	<input type="text" id="search-criteria" class="quick" style="float:right;" placeholder="Filter...">
	
</div>
<div class="umaindiv" id="list" style="max-width: 1150px; margin: auto; margin-top: 0px;">
<? 
if(!$own)
{
	echo '
<div style="width: 100%; margin-bottom: 30px; height: 30px; border: ' .$color. ' solid 1px; color: #fff; background: #c8c5c9; line-height: 30px;">' .$response. '</div>';
}
elseif($tradeurl != NULL)
{
	echo '
<div style="width: 100%; margin-bottom: 30px; height: 30px; border: ' .$color. ' solid 1px; color: #fff; background: #c8c5c9; line-height: 30px;">' .$response. '</div>';
}
else
{
	echo '
	<div style="width: 100%; margin-bottom: 30px; height: 30px; border: red solid 1px; background: linear-gradient(to bottom right, #f5f5f5, white); line-height: 30px;">To use quick.test listings, set your trade offer link in your <a href="http://quick.test/profile.php/?id=' .$steamid. '">profile</a></div>';
}

$items=json_decode(file_get_contents("data/profiles/" .$profileid. ".json"), true);

foreach($items['result']['items'] as $item)
{
	if($item['quality'] == 5)
	{
		$existedhats[] = $item['id'];
	}
}

if($areTrades)
{
	
	foreach($trades as $openone)
	{
		$existed = false;
	
		foreach($existedhats as $chance)
		{
			if($openone == $chance)
			{
				$existed = true;
				break;
			}
		}
	
		if($existed == false)
		{
			$sql = "DELETE FROM trades WHERE uniqueid=" .$openone. " AND owner=" .$profileid;
			$conn->query($sql);
			$numTrades --;
		}
	}
}

foreach($items['result']['items'] as $item)
{
	if($item['defindex'] == "5021")
	{
		$keysAmount++;
		continue;
	}
	
	if($own && $areTrades && $numTrades > 0)
	{
	    foreach($trades as $existedID)
	    {
		    if($item['id'] == $existedID)
		    {
		        $unusualsAmount++;
			    continue(2);
		    }
	    }
    }
	
	if($item['quality'] == 5)
	{
		if($item['defindex']==266)
			continue;
		
		$unusualsAmount++;
		
		$hat = $IDHatArr[$item['defindex']];
		$effect=$effNamebyIDArr[$item['attributes'][0]['float_value']];
		
		$regEffect = false;
		
		foreach($effNameArr as $bpEffect)
		{
			if($effect == $bpEffect)
			{
				$regEffect = true;
				break;
			}
		}
		if($regEffect)
			$effectSRC = "http://backpack.tf/images/440/particles/" .$effIDbyNameArr[$effect]. "_94x94.png";
		else
			continue; 
		
		$jhat = str_replace("'", "\'", $hat);
		$jeffect = str_replace("'", "\'", $effect);
		
		$bpPrice = $bpPricesArr[$hat][$effect];		
		$qPrice = $qPriceArr[$hat][$effect];
		
		
		$originID = $item['original_id'];
		$uniqID = $item['id'];
		
		echo '
		<div class="ubox" price="' .$qPriceArr[$hat][$effect]. '" name=" ' .$effect. ' ' .$hat.'" usedBy="' .$classHatArr[$hat]. '" id="' .$uniqID.'_box">
		    <div class="uimg" title="' .$effect. ' ' .$hat.'">
		        <img src="' .$effectSRC. '" style="height:80px; width:80px; position:absolute">
		        <img src=' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. ' style="height:80px; width:80px; position:relative; margin-top:10;">
		    </div>
		
		    <div class="utagul">
			<ul class="small">
		        <li class="small"><a target="_blank" href="http://backpack.tf/item/' .$originID. '">History</a></li>';
				if($bpPrice != 0)
					echo '
		            <li class="small" style="background:#57a2b8" title="suggested price - ' .$bpPrice. ' keys">' .$bpPrice. '</li>';
				if($qPrice != 0)
					echo '
				    <li class="small" style="background:#bb5cfd" title="generated price - ' .$qPrice. ' keys">' .$qPrice. '</li>';
				
				echo '
		        <li class="small" style="background:orange" title="Used by: ' .$classHatArr[$hat]. '">' .$classHatArr[$hat]. '</li>
			</ul>
			</div>
		
		    <div class="utxt" style="clear:left"><p>' .$effect. '</p><p>' .$hat.'</p></div>';
			
			if($own && $tradeurl != NULL && !$banned)
				echo '
			
			<div class="uboxpricetag" style="cursor:pointer" onclick="givePriceInput(\'' .$uniqID. '\')" id="' .$uniqID. '_selldiv" ><button class="ok" style="width: 80px;">SELL</button></div>
		
		    <div class="uboxpricetag" style="display:none;" id="' .$uniqID. '_inputdiv" >
				    <input class="quicklist" id="' .$uniqID. '_input" value="" type="number" min="5" max="10000" />
				    <button class="ok" onclick="sendToTrades(\'' .$jhat. '\', \'' .$jeffect. '\', \'' .$_SESSION['steam_steamid']. 'a\', \'' .$uniqID. '\', \'' .$originID. '\')" id="' .$uniqID. '_button" >ok</button>
			</div>';
			
			echo '
		</div>
';
	}
}

if($unusualsAmount == 0)
{
	echo 'USER HAS NO UNUSUALS';
}
?>
</div>"
<script>
$("document").ready(function() {
	$('#search-criteria').keyup(function(){
        $('.ubox').hide();
        var txt = $('#search-criteria').val();
        $('.ubox').each(function(i, e){
            if($(e).attr("name").toUpperCase().indexOf(txt.toUpperCase())>=0) $(e).show();
        });
    });
	
	$('#overall').click(function()
	{
		$(this).hide();
	});
});	
</script>
<?
$conn->close;
include 'footer.php';
?>

</body>
</html>