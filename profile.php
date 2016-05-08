<? 
ini_set('max_execution_time', 120);
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

$profileid = $_GET['id'];

function themedTag($thm)
{
	if($thm == 1)
		echo '<li class="small" style="border: solid #0a7078 2px; color: #0a7078" title="generated price">THEMED</li>';
}

$responseBP = file_get_contents("http://quick.test/getBP.php/?steamid=" .$profileid);
$responseSum = file_get_contents("http://quick.test/getSum.php/?steamid=" .$profileid);

$sql = "SELECT * FROM users WHERE steamid=\"" .$profileid. "\" LIMIT 1";
$result = mysqli_query($conn, $sql);


		
if (mysqli_num_rows($result) > 0) 
{
	$row = $result->fetch_assoc();
	
	$numtrades = $row['trades_done'];
	$date = date("Y m d", $row['registered']);
	$avatar = $row['avatar'];
	$username = htmlspecialchars($row['name']);
	$profilepage = $row['profileurl'];
	$status = $row['status'];
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
$title = $username. '\'s profile. quick.test';

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


$sql = "SELECT * FROM trades WHERE owner=" .$profileid;
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
	$areTrades = true;
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
	}
	
    usort($trades, "sortByPrice");
}
else
	$areTrades = false;

include 'menu.php';


$steamid = $_SESSION['steam_steamid'].'';


if($steamid == $profileid)
	$own = true;
else
	$own = false;

if($own)
	echo '
<script>
function givePriceInput(suniq)
{
	$("#"+suniq+"_selldiv").fadeOut(\'fast\', function(){
	$("#"+suniq+"_inputdiv").fadeIn(\'fast\', function(){
	$("#"+suniq+"_input").focus();});});
}
function updateTheTrade(sowner, suniq){
	
	var sprice = $(\'#\'+suniq+\'_input\').val();
	
	if(sprice>0)
	{
		$.post("http://quick.test/updateatrade.php",
	    {
		    steamid: sowner,
		    uniq: suniq,
		    price: sprice
	    },
	    function(data){
			if(data == "Success")
			{
				$(\'#\'+suniq+\'_selldiv\').text(sprice+" KEYS");
				$(\'#\'+suniq+\'_inputdiv\').fadeOut(\'fast\', function(){
					$(\'#\'+suniq+\'_selldiv\').fadeIn(\'fast\');
				});
					
			}
			else 
			{
				$(\'#overall\').html(data);
                $(\'#overall\').fadeIn(\'slow\', function () {
                $(this).delay(5000).fadeOut(\'slow\');
                });
			}
	    });
	}
}
function removeTheTrade(suniq, sid){
		
		$.post("http://quick.test/removeatrade.php",
	    {
		    id: sid
	    },
	    function(data){
			if(data == "Success")
			{
				$(\'#\'+suniq+\'_box\').fadeOut(\'slow\');					
			}
			else 
			{
				$(\'#overall\').html(data);
                $(\'#overall\').fadeIn(\'slow\', function () {
                $(this).delay(5000).fadeOut(\'slow\');
                });
			}
	    });
	
}
function bumpTheTrade(suniq, sid){
		
		$.post("http://quick.test/bumpatrade.php",
	    {
		    id: sid
	    },
	    function(data){
			if(data == "Success")
			{
				$(\'#\'+suniq+\'_bumper\').fadeOut(\'slow\');					
			}
			else 
			{
				$(\'#overall\').html(data);
                $(\'#overall\').fadeIn(\'slow\', function () {
                $(this).delay(5000).fadeOut(\'slow\');
                });
			}
	    });
	
}

</script>';



$keysAmount = 0;
$unusualsAmount = 0;
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

if(count($existedhats) == 0)
    $areTrades = false;

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
		}
	}
}







echo '

<div class="fullscreen" id="overall" style="display: none;">
    <div class="overall_selldiv" id="bp_selldiv">
	
	    <div class="closebutton" id="close">
		    x
		</div>
	
	<h3><center><span id="selldiv_name"></span></center></h3>
	<br />
	<input class="quicklist" id="selldiv_price" type="number" min="0" max="2000" /><div class="quicklist_block">Type your price in keys  </div>
	
   <textarea class="quicklist_comment" placeholder="Add your description here" id="selldiv_comment" cols="40" rows="3"></textarea>
   
   <button class="submitbutton" id="submit">Submit</button>
	
	</div>
</div>

<div class="maindiv_container" style="margin-top: 0px;">

<div class="maindiv_header">
    <H1>' .$username. ' - profile</H1>
</div>

<div class="umaindiv">';





if($status == 0)
{
	$status = 'offline';
	$statuscolor = '#838383';
}
else
{
	$status = 'online';
	$statuscolor = '#02b43b';
}
?>

<div class="fullscreen" id="overall" style="display:none;">
    <div class="fullscreen-text" id="overall-text"></div>
</div>

<?

if($steamrep == 'SCAMMER' && $own)
{
	echo '
<div class="fullscreen" id="overall">
    <div class="fullscreen-text" id="overall-text-banned">You are banned. Reason: Steamrep \'SCAMMER\' tag. This ban is permanent</div>
</div>
';
}


	echo '
	    <div style="display: inline-block; float: left; margin-left: 100px;">
		    <img src="' .$avatar. '" style="border: 1px solid ' .$statuscolor. '; border-radius:5px; float:left; margin-bottom:20px;">
		    <div style="float: left">
			    <p class="profile" style="background: none; margin-top: 20px; margin-left:10px; clear: none; width: 160px; color: ' .$srcolor. '">Steamrep: <span style="float: right">' .$steamrep. '</span></p>
				<p class="profile" style="background: none; color: ' .$tradecolor. ';margin-top: 5px; margin-left:10px; clear:none; width: 160px;">Trade status: <span style="float: right">' .$tradeban. '</span></p>
				<p class="profile" style="background: none; color: ' .$vaccolor. ';margin-top: 5px; margin-left:10px; clear: none; width: 160px;">VAC: <span style="float: right">' .$vacbanned. '</span></p>
			</div>';
		if($profileid == $steamid) 
        { 
	        echo "<div style=\"clear: both; margin-top:15px\">";
	        logoutbutton();
			echo "</div>";
        }
	
	echo "
	        <div class='comm_ico_cont'>
	        <a href='" .$profilepage. "' target='_blank'><div class='comm_ico'><img src='http://quick.test/content/img/steam_ico.png' /><span style='float: right; margin-top: 7px; margin-right: 5px;'>STEAM</span></div></a>
	        <a href='http://backpack.tf/profiles/" .$profileid. "' target='_blank'><div class='comm_ico'><img src='http://quick.test/content/img/bptf_ico.png' /><span style='float: right; margin-top: 7px; margin-right: 5px;'>BPTF</span></div></a>
	        <a href='http://www.tf2outpost.com/user/" .$profileid. "' target='_blank'><div class='comm_ico'><img src='http://quick.test/content/img/tf2op_ico.png' /><span style='float: right; margin-top: 7px; margin-right: 5px;'>TF2OP</span></div></a>
	        <a href='http://steamrep.com/profiles/" .$profileid. "' target='_blank'><div class='comm_ico'><img src='http://quick.test/content/img/steamrep_ico.png' /><span style='float: right; margin-top: 7px; margin-right: 5px;'>SR</span></div></a>
	        </div>
		</div>
		
	
	
	    <div style='display: inline-block; float: right; margin-right: 100px;'>
	
	        <p class='profile'>Status:<span style='color:" .$statuscolor. "'> " .$status."</span></p>
	        <p class='profile'>Steam user since: " .gmdate("Y m d", $timecreated). "</p>
	        <p class='profile'>Unusuals: " .$unusualsAmount."</p>
	        <p class='profile'>Keys: " .$keysAmount. "</p>
	        <p class='profile'>Open listings: " .$tradesNum."</p>
	        <p class='profile'>Total listings: " .$numtrades."</p>";
	
	if($own)
	{
		echo "<div style='width:410px;'><input id='trinp' style='float: left; width:366px; height: 23px;' ";
            if($tradeurl != NULL)
                echo "placeholder='" .$tradeurl. "'";
            else	
                echo "placeholder='Your trade offer link'";
            echo "><button class='ok' steamid='" .$steamid. "a' id='trbtn'>OK</button></div>";
	}
	
	echo '</div>
	
</div>';
	
$backpack = file_get_contents("data/profiles/" .$profileid. ".json");

if($backpack != NULL)
{
	$items=json_decode($backpack, true);
    
    echo '
	<div class="maindiv_panel" id="backpackPanel">
	    <h3>Backpack</h3>
	</div>
    <div class="umaindiv" id="backpack">';
	
	foreach($items['result']['items'] as $item)
	{
		if($item['quality'] == 5)
	    {
		    if($item['defindex']==266)
			continue;
		    
			$hat = $IDHatArr[$item['defindex']];
		    $effect=$effNamebyIDArr[$item['attributes'][0]['float_value']];
			$originID = $item['original_id'];
		    $uniqID = $item['id'];
			
			if($effect == NULL)
				continue;
			
		    echo '
			<div class="uimg tooltip bp_item" effect="' .$effect. '" hat="' .$hat. '" uniq_id="' .$uniqID. '" or_id="' .$originID. '">';
				echo '
			
			    <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
			
			
			<span>';
				
				if($bpPricesArr[$hat][$effect] != 0)
					echo '
				<p>Suggested price: ' .$bpPricesArr[$hat][$effect]. ' keys</p>';
				
				if($qPriceArr[$hat][$effect] != 0 && $themedArr[$hat][$effect] == 0)
					echo '
				<p>Recommended price: ' .$qPriceArr[$hat][$effect]. ' keys</p>';
				
			echo '
            </span>
			<span style="margin-top:86px; text-align: center; background: gray; color: #eaeaea" >
                <strong>' .$effect. ' ' .$hat. '</strong>
			</span>
			';
				echo'
			    <img src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;">
			</div>
			';
		}
	}
	
	echo '
	</div>';

}


if($areTrades)
{
	echo '
	<div class="maindiv_panel" id="tradesPanel">
	    <h3>Trades</h3>
	</div>
    <div class="umaindiv" id="trades">';
	
    foreach($trades as $item)
        {
            
                
						$history = $item['org_id'];
						$effect = $item['effect'];
						$name = $item['name'];
						$sellPrice = $item['price'];
						$id = $item['id'];
						$uniqID = $item['uniqueid'];
						
						
						$bptfprice = $data[$effect. " " .$name]["bp"];
                        if($bptfprice == 0)
							$bptfprice = "?";
						$qprice = $data[$effect. " " .$name]["qp"];
						if($qprice == 0)
							$qprice = "?";
						$themed = $data[$effect. " " .$name]["themed"];
						$class = $classHatArr[$name];
						
						$goodDeal = false;
							
						if($data[$effect. " " .$name]["bp"] == 0)
						{
							if($data[$effect. " " .$name]["qp"] != 0)
								if($sellPrice < $data[$effect. " " .$name]["qp"])
								{
									$goodDeal = true;
								}
						}
						elseif($data[$effect. " " .$name]["qp"] == 0)
						{
							if($sellPrice < $data[$effect. " " .$name]["bp"])
							{
								$goodDeal = true;
							}
						}
						else
						{
							if($sellPrice < $data[$effect. " " .$name]["bp"] && $sellPrice < $data[$effect. " " .$name]["qp"])
							{
								$goodDeal = true;
							}
						}
						
                        echo '
						<div class="ubox" price="' .$qprice. '" usedBy="' .$class. '" name=" ' .$effect. ' ' .$name.'" id="' .$uniqID.'_box" ';
						
						if($goodDeal)
							{
								echo 'style="box-shadow: 0px 0px 5px green;"';
							}
							echo '>
						
						    <a href="http://quick.test/trade/' .$id. '">
							<div class="uimg">
							
							    <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute">
							
							    <img src=' .$schema["result"]["items"][$hatIDArr[$name]]["image_url"]. ' style="height:80px; width:80px; position:relative; margin-top:10;">
							</div>
							</a>
							
							<div class="utagul">
							    <ul class="small">';
								
							    if($item['owner'] == $steamid)
                                {
                                    echo '
									<li class="small" id="' .$uniqID. '_bumper" style="background:#2343e1; color: white; padding-left:7px; padding-right:7px; cursor:pointer" onclick="bumpTheTrade(\'' .$uniqID. '\', \'' .$id. '\')">BUMP</li>
							        <li class="small" style="background:#fb0000;color: white; padding-left:7px; padding-right:7px; cursor:pointer" onclick="removeTheTrade(\'' .$uniqID. '\', \'' .$id. '\')">CLOSE</li>';
								}									
								
							        echo '
							        <li class="small" style="background: white"><a target="_blank" href="http://backpack.tf/item/' .$history. '">History</a></li>';
									if($item['owner'] != $steamid)
									    echo '
									<li class="small" style="background:white" title="Seller\'s profile page"><a href="http://quick.test/profile/' .$item['owner']. '">SELLER</a></li>';
									if($bptfprice != 0)
										echo'
							        <li class="small" style="background:#b0e0e6" title="suggested price - ' .$bptfprice. '  keys">' .$bptfprice. '</li>';
									if($qprice != 0)
										echo '
							        <li class="small" style="background:#bb5cfd" title="recommended price - '  .round($qprice/100*90). '-' .round($qprice/100*110).  ' keys">' .$qprice. '</li>';
									echo '
							        <li class="small" style="background:orange" title="Used by: ' .$class. '">' .$class. '</li>';themedTag($themed); echo '</ul>
							</div>
							
							<div class="utxt" style="clear:left">
							    <p>' .$effect. '</p>
								<p>' .$name.'</p>
							</div>';
							
							if($item['owner'] == $steamid)
							{
								echo '
								
								<div class="uboxpricetag" style="cursor:pointer" onclick="givePriceInput(\'' .$uniqID. '\')" id="' .$uniqID. '_selldiv" >' .$sellPrice. ' KEYS</div>
		
		                        <div class="uboxpricetag" style="display:none;" id="' .$uniqID. '_inputdiv" >
				                    <input class="quicklist" id="' .$uniqID. '_input" value="" placeholder="' .$sellPrice. '" type="number" min="0" max="2000" />
				                    <button class="ok" onclick="updateTheTrade(\'' .$_SESSION['steam_steamid']. 'a\', \'' .$uniqID. '\')" id="' .$uniqID. '_button" >ok</button>
								</div>';
							}
							else
							{
								echo '
    						    <div class="uboxpricetag"><a href="' .$tradeurl. '" target="_blank">' .$sellPrice. ' KEYS</a></div>';
							}
							
							echo '
						</div>
						';
                        
					
                
            
        }
		
		echo '
		</div>';
}

	
?>

<script>
$("document").ready(function() 
{
	var name;
	var originID;
	var uniqID;
	var comment;
    var temp;
	var price;
	<? if($own)
	echo "
    var steamid = '" .$profileid. "';
	
	$('#trbtn').click(function()
	{
        var link = $('#trinp').val();
		var id = $('#trbtn').attr('steamid');
		var act = 'update_tradeurl';
		
		if(link != 0)
		{
			$.post('http://quick.test/updateBD.php',
			{
				steamid: id,
				trade_url: link,
				action: act
			},
			function(data) 
			{
				if(data == 'Success')
			    {
				    $('#trinp').attr('placeholder', link);
					$('#trinp').val('');
			    }
			    else 
			    {
				    $('#overall-text').html(data);
                    $('#overall').fadeIn('slow', function () 
					{
                    $(this).delay(5000).fadeOut('slow');
                    });
				}
			});
        }
    });
		
	$('.bp_item').click(function()
	{
		hat = $(this).attr('hat');
		effect = $(this).attr('effect');
		uniqID = $(this).attr('uniq_id');
		originID = $(this).attr('or_id');
		
		temp = effect+' '+hat;
		
		if(temp != name)
		{
			name = temp;
			$('#selldiv_comment').val('');
			$('#selldiv_price').val('');
		    $('#selldiv_name').text(name);
		}
		
		$('#overall').fadeIn('fast');
	});
	
	
	$('#close').click(function()
	{
		$('#overall').fadeOut('fast');
	});
	
	$('#submit').click(function()
	{
		price = $('#selldiv_price').val();
		comment = $('#selldiv_comment').val();
		if(price != 0)
		{
			$.post('http://quick.test/addatrade.php',
			{
				in_hat: hat,
				in_effect: effect,
				in_price: price,
				in_steamid: steamid,
				in_uniq: uniqID,
				in_origin: originID,
				in_comment: comment				
			},
			function(data) 
			{
				if(data == 'Success')
			    {
				    alert('success');
			    }
			    else 
			    {
				    alert(data);
				}
			});
        }
	});
	
	";?>
	
	
	$('#backpackPanel').click(function(){
	    $('#backpack').slideToggle();
    });
	
	$('#tradesPanel').click(function(){
	    $('#trades').slideToggle();
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