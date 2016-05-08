<? 
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
    $tradeurl = $row['tradeofferurl'];	
}
$title = $username. '\'s listings. quick.test';

include 'menu.php';
function getPlaceHolder($q, $b)
{
	$q = str_replace(" KEYS", "", $q);
	$b = str_replace(" KEYS", "", $b);
	
	if($q == "NONE")
	{
		if($b == "NONE")
			return "...";
		else
			return $b;
	}
	else
		return $q;
}
$steamid = $_SESSION['steam_steamid'].'';

if($steamid == $profileid)
	$own = true;
else
	$own = false;


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





function themedTag($thm)
{
	if($thm == 1)
		echo '<li class="small" style="border: solid #0a7078 2px; color: #0a7078" title="generated price">THEMED</li>';
}

$response = file_get_contents("http://quick.test/getBP.php/?steamid=" .$profileid);
if($response == "Just updated")
	$color = "white";
else
	$color = "red";


if($own)
	echo '
<script>
function givePriceInput(suniq)
{
	$("#"+suniq+"_selldiv").fadeOut(\'fast\', function(){
	$("#"+suniq+"_inputdiv").fadeIn(\'fast\');});
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
				$(\'#\'+suniq+\'_selldiv\').text(sprice);
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
?>
<div class="fullscreen" id="overall" style="display:none;"></div>
<div class="prftagdiv">
    <a href="http://quick.test/profile/<?echo $profileid; ?>"><div class="prftag">PROFILE</div></a>
    <a href="http://quick.test/inventory/<?echo $profileid; ?>"><div class="prftag">INVENTORY</div></a>
	<div class="prftag" id="active">LISTINGS</div>
	
	<input type="text" id="search-criteria" class="quick" style="float:right;" placeholder="Filter...">
</div>
<div class="umaindiv" id="list" style="max-width: 1150px; margin: auto; margin-top: 0px">

<div style="width: 100%; margin-bottom: 30px; height: 30px; border: <? echo $color; ?> solid 1px; color: white; background: #c8c5c9; line-height: 30px;">
<?php

echo $response;
echo "</div>";
$items=json_decode(file_get_contents("data/profiles/" .$profileid. ".json"), true);

if($areTrades)
{
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
						$awesomedeal = false;
							
						if($data[$effect. " " .$name]["bp"] == 0)
						{
							if($data[$effect. " " .$name]["qp"] != 0)
								if($sellPrice < $data[$effect. " " .$name]["qp"])
								{
									$goodDeal = true;
									if($sellPrice < ($data[$effect. " " .$name]["qp"])/100*82)
										$awesomedeal = true;
								}
						}
						elseif($data[$effect. " " .$name]["qp"] == 0)
						{
							if($sellPrice < $data[$effect. " " .$name]["bp"])
							{
								$goodDeal = true;
								if($sellPrice < ($data[$effect. " " .$name]["bp"])/100*82)
										$awesomedeal = true;
							}
						}
						else
						{
							if($sellPrice < $data[$effect. " " .$name]["bp"] && $sellPrice < $data[$effect. " " .$name]["qp"])
							{
								$goodDeal = true;
								if($sellPrice < ($data[$effect. " " .$name]["qp"])/100*82 && $sellPrice < ($data[$effect. " " .$name]["bp"])/100*82)
										$awesomedeal = true;
							}
						}
						
                        echo '
						<div class="ubox" price="' .$qprice. '" usedBy="' .$class. '" name=" ' .$effect. ' ' .$name.'" id="' .$uniqID.'_box" ';
						
						if($goodDeal)
							{
								
								echo 'style="box-shadow: 0px 0px 14px green;';
								if($awesomedeal)
									echo 'background: linear-gradient(to right bottom,#e7fbea, #4be765, #b8f2c2);';
								echo '"';
							}
							echo '>
						
						    <a href="#">
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
							        <li class="small" style="background:#bb5cfd" title="generated price - ' .$qprice. ' keys">' .$qprice. '</li>';
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
}
else
	echo 'USER HAS NO LISTINGS';



echo "</div>";
?>
<script>
$("document").ready(function() {
	$('#search-criteria').keyup(function(){
        $('.ubox').hide();
        var txt = $('#search-criteria').val();
        $('.ubox').each(function(i, e){
            if($(e).attr("name").toUpperCase().indexOf(txt.toUpperCase())>=0) $(e).show();
        });
    });
});	
</script>
<?
$conn->close;
include 'footer.php';
?>

</body>
</html>