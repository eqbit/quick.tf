<? 
require_once 'steamapi.php';

$title = 'Listings. quick.test';

include 'menu.php';


$page = $_GET['page']-1;

if(!isset($_GET['page']))
	$page = 0;
else
	$page = $_GET['page']-1;

$numresults = 60;

$sql = "SELECT * FROM hats";
$result = $conn->query($sql);	
	
while($row = $result->fetch_assoc())
{
	$data[$row['name']]['bp'] = $row['bptfprice'];
	$data[$row['name']]['buyp'] = $row['buyprice'];
	$data[$row['name']]['themed'] = $row['themed'];
	
	
		if(!$data[$row['name']]['themed'])
			$data[$row['name']]['qp'] = $row['qprice'];
		else
			$data[$row['name']]['qp'] = 0;
	
	$data[$row['name']]['class'] = $row['class'];
	$data[$row['name']]['tier'] = $row['tier'];
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
}

usort($trades, "sortByDate");

$steamid = $_SESSION['steam_steamid'].'';

if($_SESSION['steam_steamid'] == '76561198042938501')
{
	$adminMode = true;
}
else
{
	$adminMode = false;
}

function themedTag($thm)
{
	if($thm == 1)
		echo '<li class="small" style="border: solid #0a7078 2px; color: #0a7078">THEMED</li>';
}

?>
<script>
function givePriceInput(suniq)
{
	$("#"+suniq+"_selldiv").fadeOut('fast', function()
	{
	    $("#"+suniq+"_inputdiv").fadeIn('fast', function()
	    {
	        $("#"+suniq+"_input").focus();
	    });
	});
}
function updateTheTrade(sowner, suniq){
	
	var sprice = $('#'+suniq+'_input').val();
	
	if(sprice>0)
	{
		$.post("http://quick.test/updateatrade.php",
	    {
		    steamid: sowner,
		    uniq: suniq,
		    price: sprice
	    },
	    function(data)
		{
			if(data == "Success")
			{
				$('#'+suniq+'_selldiv').text(sprice+' KEYS');
				$('#'+suniq+'_input').val('');
				$('#'+suniq+'_inputdiv').fadeOut('fast', function(){
					$('#'+suniq+'_selldiv').fadeIn('fast');
				});
					
			}
			else 
			{
				$('#overall-text').html(data);
                $('#overall').fadeIn('slow', function () {
                $(this).delay(5000).fadeOut('slow');
                });
			}
	    });
	}
        else
        {
            $('#'+suniq+'_inputdiv').fadeOut('fast', function(){
		 $('#'+suniq+'_selldiv').fadeIn('fast');
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
				$('#'+suniq+'_box').fadeOut('slow');					
			}
			else 
			{
				$('#overall-text').html(data);
                $('#overall').fadeIn('slow', function () {
                $(this).delay(5000).fadeOut('slow');
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
				$('#'+suniq+'_bumper').hide();
                $('#'+suniq+'_bumper_inactive').show();				
			}
			else 
			{
				$('#overall-text').html(data);
                $('#overall').fadeIn('slow', function () {
                $(this).delay(5000).fadeOut('slow');
                });
			}
	    });
	
}

</script>

<div class="fullscreen" id="overall" style="display:none;">
    <div class="fullscreen-text" id="overall-text"></div>
</div>

<!--
<div style="width: 90%; margin:auto">
 <div class="searchclsdiv"> 

    <input type="text" id="search-criteria" class="quick"  style="margin-top: 6px; margin-left: 6px" placeholder="Filter...">
	
	<div class="classBtn" id="" style="background: url(http://quick.test/content/img/tokens/All.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="goodDealBtn" id="GoodDeal" style="background: url(http://quick.test/content/img/tokens/GoodDeals.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Multi" style="background: url(http://quick.test/content/img/tokens/Multi.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Scout" style="background: url(http://quick.test/content/img/tokens/Scout.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Soldier" style="background: url(http://quick.test/content/img/tokens/Soldier.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Pyro" style="background: url(http://quick.test/content/img/tokens/Pyro.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Demoman" style="background: url(http://quick.test/content/img/tokens/Demoman.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Heavy" style="background: url(http://quick.test/content/img/tokens/Heavy.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Engineer" style="background: url(http://quick.test/content/img/tokens/Engineer.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Medic" style="background: url(http://quick.test/content/img/tokens/Medic.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Sniper" style="background: url(http://quick.test/content/img/tokens/Sniper.png); background-size: contain; background-repeat: no-repeat;"></div>
	<div class="classBtn" id="Spy" style="background: url(http://quick.test/content/img/tokens/Spy.png); background-size: contain; background-repeat: no-repeat;"></div>
</div>
-->

<div class="maindiv_container">

    <div class="maindiv_header">
    <H2>
        <a style="color: #eaeaea" href="http://quick.test/trades">Listings</a>
	    <? if($page > 0) echo '<span style="float: right;">Page ' .($page + 1). '</span>'; ?>
    </H2>
    </div>
	
	
    <div class="umaindiv" id="list">
		<?
		$j=0;
		foreach($trades as $fff)
		    $j++;
			
        for($i = $page*$numresults; $i < ($page*$numresults+$numresults); $i ++)
        {
			if($i == $j)
				break;
			
            $item = $trades[$i];
                
						$history = $item['org_id'];
						$effect = $item['effect'];
						$name = $item['name'];
						$sellPrice = $item['price'];
						$id = $item['id'];
						$uniqID = $item['uniqueid'];
						$bump = $item['date'];
						$allowBump = false;
						$owner = $item['owner'];
						
						
						$sql = "SELECT * FROM users WHERE steamid=" .$owner;
						$result = $conn->query($sql);

    					if ($result->num_rows > 0)
    					{
							$row = $result->fetch_assoc();
	    					$tradeurl = $row['tradeofferurl'];
							$username = $row['name'];
						}
						
						if((time() - strtotime($bump)) > 1800)
							$allowBump = true;
						
						$bptfprice = $data[$effect. " " .$name]["bp"];
                        if($bptfprice == 0)
							$bptfprice = "?";
						$qprice = $data[$effect. " " .$name]["qp"];
						if($qprice == 0)
							$qprice = "?";
						$themed = $data[$effect. " " .$name]["themed"];
						$tier = $tierArr[$name][$effect];
						$class = $classHatArr[$name];
						
						$goodDeal = 0;
						$awesomedeal = false;
							
						if($bptfprice == 0)
						{
							if($qprice != 0)
								if($sellPrice < $qprice)
								{
									$goodDeal = 1;
								}
						}
						elseif($qprice == 0)
						{
							if($sellPrice < $bptfprice)
							{
								$goodDeal = 1;
								if($sellPrice < ($bptfprice)/10*9)
										$awesomedeal = true;
							}
						}
						else
						{
							if($sellPrice < $bptfprice && $qprice)
							{
								$goodDeal = 1;
								if($sellPrice < ($qprice)/10*9 && $sellPrice < ($bptfprice)/10*9)
										$awesomedeal = true;
							}
						}
						
                        echo '
						<div class="ubox" price="' .$qprice. '" goodDeal="' .$goodDeal. '" usedBy="' .$class. '" name="' .$effect. ' ' .$name.'" id="' .$uniqID.'_box" style=" background-image: url(\'http://quick.test/content/img/' .$class. 'Back.png\');';
						
						if($goodDeal)
							{
								
								echo 'box-shadow: 0px 0px 5px green;';
							}
						echo '">
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
									if($allowBump)
									{
										echo '
									<li class="small" id="' .$uniqID. '_bumper" style="background:#2343e1; color: white; padding-left:7px; padding-right:7px; cursor:pointer" onclick="bumpTheTrade(\'' .$uniqID. '\', \'' .$id. '\')">BUMP</li>';
									}
									else
									{
										echo '
									<li class="small" id="' .$uniqID. '_bumper" style="background:gray; opacity: 0.5; color: white; padding-left:7px; padding-right:7px;">BUMP</li>';
									}
									echo '
									<li class="small" id="' .$uniqID. '_bumper_inactive" style="background:gray; opacity: 0.5; color: white; padding-left:7px; padding-right:7px; display: none">BUMP</li>
									
							        <li class="small" style="background:#fb0000;color: white; padding-left:7px; padding-right:7px; cursor:pointer" onclick="removeTheTrade(\'' .$uniqID. '\', \'' .$id. '\')">CLOSE</li>';
								}
                                else	
								{
									if($adminMode)
									{
										echo '
										<li class="small" style="background:#ff6700; color: white; padding-left:7px; padding-right:7px; cursor:pointer" onclick="removeTheTrade(\'' .$uniqID. '\', \'' .$id. '\')">CLOSE</li>';
									}
								}							
								
							        echo '
							        <li class="small" style="background: white"><a target="_blank" href="http://backpack.tf/item/' .$history. '">History</a></li>';
									if($item['owner'] != $steamid)
									    echo '
									<li class="small" style="background:white"><a href="http://quick.test/profile/' .$item['owner']. '" class="tooltip">SELLER
									
									<span>
                                    <strong>Blah Blah</strong><br />
                                      Test
                                    </span>
									<span style="margin-top:86px; text-align: center; background: gray; color: #eaeaea" >
                                        <strong>' .$username. '</strong>
			                        </span>
									
									</a></li>';
									if($bptfprice != 0)
										echo'
							        <li class="small" style="background:#b0e0e6" title="suggested price: ' .$bptfprice. ' keys">' .$bptfprice. '</li>';
									if($qprice != 0)
										echo '
							        <li class="small" style="background:#bb5cfd" title="recommended price: ' .round($qprice/100*90). '-' .round($qprice/100*110). ' keys">' .$qprice. '</li>';
									if($tier != 0)
									{
										echo '
							            <li class="small" style="background:#fff" title="' .$tier. ' tier hat">' .$tier. '</li>';
									}
									themedTag($themed); echo '</ul>
									
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
								    <form onSubmit="updateTheTrade(\'' .$_SESSION['steam_steamid']. 'a\', \'' .$uniqID. '\'); return false">
				                    <input class="quicklist" id="' .$uniqID. '_input" value="" placeholder="' .$sellPrice. '" type="number" min="0" max="2000" />
									</form>
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
 
    </div>
</div>
<div style="text-align: center; clear: both; margin-bottom: 60px;">';
if($page > 1)
{
	echo '
	<a href="http://quick.test/trades/page/1"><div class="page"><img src="http://quick.test/content/img/left_arrow_end.png" /></div></a>';
}
else
{
	echo '
	<div class="page-inactive"><img src="http://quick.test/content/img/left_arrow_end.png" /></div>';
}

if($page > 0)
{
	echo '
	<a href="http://quick.test/trades/page/' .$page. '"><div class="page"><img src="http://quick.test/content/img/left_arrow.png" /></div></a>';
}
else
{
	echo '
	<div class="page-inactive"><img src="http://quick.test/content/img/left_arrow.png" /></div>';
}

if(($page+1)*$numresults < $j)
{
	echo '
	<a href="http://quick.test/trades/page/' .($page+2). '"><div class="page"><img src="http://quick.test/content/img/right_arrow.png" /></div></a>';
}
else
{
	echo '
	<div class="page-inactive"><img src="http://quick.test/content/img/right_arrow.png" /></div>';
}

$numPages = (int)($j/$numresults);

if(($page+1) < $numPages)
{
	echo '
	<a href="http://quick.test/trades/page/' .($numPages + 1). '"><div class="page"><img src="http://quick.test/content/img/right_arrow_end.png" /></div></a>';
}
else
{
	echo '
	<div class="page-inactive"><img src="http://quick.test/content/img/right_arrow_end.png" /></div>';
}

        echo '
</div>
';

		
		
		?>
 <?
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
	
	$('.classBtn').click(function(){
	    $('.ubox').hide();
            var cls = $(this).attr("id");
            $('.ubox').each(function(i, e){
                if($(e).attr("usedBy").toUpperCase().indexOf(cls.toUpperCase())>=0) $(e).fadeIn();
            });
	});
	
	$('.goodDealBtn').click(function(){
	        $('.ubox').hide();
                $('.ubox').each(function(i, e){
                    if($(e).attr("goodDeal") == '1')
                    {
                        $(e).fadeIn();
                    }
                });
            
	});
	
	$('#overall').click(function()
	{
		$(this).hide();
	});
});	
</script>

<?

$conn->close();
include 'footer.php'
?>


</body>
</html>