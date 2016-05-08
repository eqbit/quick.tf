<? 	
require_once 'steamapi.php';

$hat = $_GET["hat"];
$jHat = str_replace("'", "\'", $hat);
$page_exists = false;
	
foreach($hatArr as $existed)
{
	if($hat == $existed)
	{
		$page_exists = true;
		break;
	}
}
	
if(!$page_exists)
{
	header ("Location: http://quick.test/qsearch.php/?request=" .$hat);
}

require_once 'steamauth/steamauth.php';
require_once 'connect.php';

   
		
	
	$hatU = str_replace("'", "", $hat);
	
	$lowest = 10;
	
	$sql = "SELECT * FROM hats WHERE name LIKE \"%" .$hat. "%\"";
	$result = $conn->query($sql);
	
	
	while($row = $result->fetch_assoc())
	{
		$data[$row['name']]['bp'] = $row['bptfprice'];
		$data[$row['name']]['themed'] = $row['themed'];
		
		if(!$data[$row['name']]['themed'])
			$data[$row['name']]['qp'] = $row['qprice'];
		else
			$data[$row['name']]['qp'] = 0;
		
		$data[$row['name']]['buyp'] = $row['buyprice'];
		
		if($row['buyprice'] < $lowest)
			$lowest = $row['buyprice'];
	}

$title = $hat. ' - QB pricelist. Quick.tf';
	
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
?>

<script>
function setDetails(name, bpPrice, qPrice, buyPrice, effect, themed, effID) 
{
	
	var color = "#00d283";
	if(buyPrice == "TOO HIGH" || buyPrice == 0)
	{
		buyPrice = "NOT BUYING";
		color = "gray";
	}
	else
	{
		buyPrice = buyPrice+" KEYS";
	}
	if(themed != 1) {
		document.getElementById("name").innerHTML = name+' <? echo $jHat; ?>';
		$('#span_themed').hide();
	}
	else {
		document.getElementById("name").innerHTML = name+' <? echo $jHat; ?>';
		$('#span_themed').fadeIn();
	}
<?
if($adminMode)
{
	echo '
	var temp = buyPrice.replace(" KEYS", "");
	
	if(temp == "NOT BUYING")
		temp = "TOO HIGH";
	 
	$("#HatName").val(name);
	$("#changeBuyPrice").attr("placeholder", temp);
	$("#buyOrderInput").val(temp+" keys for the "+name+" . Also buying other effects. Full pricelist: quick.test/qoffer/"+name.replace(effect+" ",""));
	$("#buyLink").attr("href", "http://backpack.tf/classifieds/buy/Unusual/' .$hat. '/Tradable/Craftable/"+effID);
	if(themed == 1) {
		document.getElementById("themedchk").checked = true;
	}
	else
	{
		document.getElementById("themedchk").checked = false;
	}
	$("#changeBuyPrice").val("");
	$("#changeBuyPrice").focus();
	
	';
} ?>

    if(bpPrice==0)
		bpPrice="NONE";
	else
		bpPrice=bpPrice+" keys";
	
	if(qPrice==0)
		qPrice="NONE";
	else
		qPrice=Math.round(qPrice/100*90)+"-"+Math.round(qPrice/100*110)+" keys";
	
	document.getElementById("bpPrice").innerHTML ="Suggested price: "+bpPrice;
	document.getElementById("qPrice").innerHTML = "Recommended price: "+qPrice;
	document.getElementById("buyPrice").innerHTML = "QB: "+buyPrice;
	
	var newSRC = document.getElementById(effect).src;
	
	if(!$("#detaileffect").is(":visible"))
	{
	    $('#detaileffect').attr('src', newSRC);
	    $("#detaileffect").fadeIn(300);
	}
	else
		$("#detaileffect").fadeOut(200, function() {	
	        $('#detaileffect').attr('src', newSRC);
			$("#detaileffect").fadeIn(200);
		});
}

<?
if($adminMode)
{
	echo "

function changeHat()
{
	
	var sprice = $('#changeBuyPrice').val();
	var sname = $('#HatName').val();
	var act = 'update_a_hat';
	var path = sname.replace(' ', '_');
	

	while(path.indexOf(' ') > -1)
	{
	    path = path.replace(' ', '_');
	}
	
	while(path.indexOf('\\'') > -1)
	{
	    path = path.replace('\\'', '');
	}
	

	
	if(sprice>0)
	{
		$.post('http://quick.test/updateBD.php',
	    {
		    action: act,
		    hatName: sname,
		    newPrice: sprice
	    },
	    function(data)
		{
			if(data == 'Success')
			{
				$('#'+path+'_buyTag').text(sprice);
				$('#changeBuyPrice').val('')
			}
			else 
			{
				$('#overall').html(data);
                $('#overall').fadeIn('slow', function () {
                $(this).delay(5000).fadeOut('slow');
                });
			}
	    });
	}
}

function regenerateHat()
{
    var sNewAvg = $('#hatAvgPrice').val();
	var sHat = $('#globalHatName').val();
	var act = 'regenerate_a_hat';
		
	if(1)
	{
		$.post('http://quick.test/updateBD.php',
		{
			hat: sHat,
			newAvg: sNewAvg,
			action: act
		},
		function(data) 
		{
			if(data == 'Success')
			{
				location.reload();
			}
			else 
			{
				$('#overall-text').html(data);
                $('#overall').fadeIn('slow', function () 
				{
                $(this).delay(15000).fadeOut('slow');
                });
			}
		});
    }
	else
	{
		alert('FUCK');
	}
}
	
$('#overall').click(function()
{
	$(this).hide();
});

";
}
?>
</script>

<div class="detail_header">
	<span id="name"><? echo $hat ?></span>
</div>

<div class="detail" style="height: 200px; margin: auto; width: 800px; margin-top: 0px;">
  <div style="width: 20%; float: left;">
    <div class="uimg_big">
	    <img src="" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; display:none;" id="detaileffect" >
	    <img src="<?echo $schema["result"]["items"][$hatIDArr[$hat]]["image_url"] ?>" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:3;">
	</div>
  </div>
  
  <div style="width: 79%; float: left;">
	
	<div class="detail-text">
		<a href="http://quick.test/search/<? echo $hat; ?>"><span style='color:gray; margin-left:20px; padding:5px;border: 2px gray solid'>SEARCH</span></a>
		<span style='display: none; color:#0a7078; margin-left:20px; padding:5px;border: 2px #0a7078 solid' id='span_themed'>THEMED</span>
		<h3>
		<p class='details' id="bpPrice">| </p>
		<p class='details' id="qPrice"> |</p>
		<p class='details' id="buyPrice"> |</p>
		</h3>
		
		
	</div>
  </div>
</div>

<?
if($adminMode)
{
	echo "

<div class=\"detail-container\">
	<div class=\"separator\"></div>
    <div class=\"admin-panel\" style=\"height: 40px; width: 550px; display: inline-block\">
	    <input type=\"text\" value=\"\" id=\"HatName\" style=\"float:left; margin:10px;\"/>
	    <form onSubmit=\"changeHat(); return false\">
	        <input type=\"text\" value=\"\" name=\"newBuyPrice\" id=\"changeBuyPrice\" style=\"float:left; margin:10px; width: 70px\" placeholder=\"\" />
	    </form>
	    <button style=\"float:left; margin:10px; width: 40px; text-align:center; cursor: pointer\" onclick=\"changeHat()\">OK</button>
		
	    <div><div style='float:left; padding-left: 2px'> THEMED?</div> <div style='float:left;'><input type=\"checkbox\" id=\"themedchk\"/style=\"width: 40px; margin-top: 15px\"></div></div>
		
	</div>
	
	<div class=\"admin-panel\" style=\"height: 40px; display: inline-block\">
	    <input type=\"text\" value=\"" .$hat. "\" id=\"globalHatName\" style=\"float:left; margin:10px;\" readonly/>
	    <input type=\"text\" value=\"\" id=\"hatAvgPrice\" style=\"float:left; margin:10px; width: 70px\" placeholder=\"" .$avgHatArr[$hat]. "\"/>
	    <button style=\"float:left; margin:10px; width: 40px; text-align:center; cursor: pointer\" onclick=\"regenerateHat()\">OK</button>	    
	</div>
	
	<div class=\"admin-panel\" style=\"height: 100px; width: 1000px\">
	    <input type=\"text\" id=\"buyOrderInput\" value=\"\" style=\"float:left; margin:10px; width: 90%\"/>
	    <a id=\"buyLink\" href=\"http://backpack.tf/classifieds/buy/Unusual/" .$hat. "/Tradable/Craftable/" .$effNum. "\" target=\"_blank\" style=\"padding 10px; padding-left: 10px; padding-right: 10px; border: 1px #998cff solid; float: left; margin: 20px;\">BuyLink</a>
		<a href=\"http://backpack.tf/classifieds/?item=" .$hat. "&quality=5&tradable=1&craftable=1&australium=-1\" target=\"_blank\" style=\"padding 10px; padding-left: 10px; padding-right: 10px; border: 1px #998cff solid; float: left; margin: 20px;\">Listings</a>
		
	</div>
	
</div>
	
<script>
$('#themedchk').change(function() {
    if(this.checked) 
	{
        var sthemed = 1;
	}
	else
	{
		var sthemed = 0;
	}
		
		var act = 'update_themed_attr';
		var sname = $('#HatName').val()+' " .$hat. "';
		
        if(sname != 0)
		{
			$.post('http://quick.test/updateBD.php',
	        {
		        action: act,
		        hat: sname,
				effect: seffect,
		        themed: sthemed
	        },
	        function(data)
		    {
			    if(data == 'Success')
			    {					
					$('#span_themed').fadeIn();
			    }
			    else 
			    {
				    $('#overall').html(data);
                    $('#overall').fadeIn('slow', function () 
					{
                        $(this).delay(5000).fadeOut('slow');
                    });
			    }
	        });
		}
});	
</script>

	";
}

	
?>


<div class="maindiv_container" style="margin-top: 0px;">

<div class="maindiv_header">
	<h3>Unusual <? echo $hat; ?> - recommended prices</h3>
    <input type="text" id="search-criteria" class="quick" placeholder="Filter...">
</div>

<div class="umaindiv" id="tinylist">
<?
	
	foreach($bpEffArr[$hat] as $effect)
	{
		$jEffect = str_replace("'", "\'", $effect);
		$buyPrice = $data[$effect. " " .$hat]["buyp"];
		$qPrice = $data[$effect. " " .$hat]["qp"];
		$pHat = str_replace(" ", "_", $hat);
		$pHat = str_replace("'", "", $pHat);
		$pEffect = str_replace(" ", "_", $effect);
		$pEffect = str_replace("'", "", $pEffect);
		
		echo "
		
		<div class=\"uimg\" effect=\"" .$effect. "\" title=\"" .$effect. " " .$hat. "\" style=\"margin:3\" onclick=\"setDetails('" .$jEffect. "', '" .$bpPricesArr[$hat][$effect]. "', '" .$qPrice. "', '" .$buyPrice. "', '" .$jEffect. "', '" .$data[$effect. " " .$hat]["themed"]. "', '" .$effIDbyNameArr[$effect]. "')\">
		    <img id=\"". $effect. "\" src=\"http://backpack.tf/images/440/particles/" .$effIDbyNameArr[$effect]. "_94x94.png\" style=\"height:80px; width:80px; position:absolute; top: 3px; left: 3px;\">
		    <img src=\"" .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. "\" style=\"height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;\">";
			
			if($qPrice > 0)
				echo"
			<div class=\"utag\" id=\"" .$pEffect."_" .$pHat. "_buyTag\">" .$qPrice. "</div>";
			
		echo "
		</div>
		";
	}
?>
</div>
</div>
<script>



$("document").ready(function() {
	$('#search-criteria').keyup(function(){
        $('.uimg').hide();
        var txt = $('#search-criteria').val();
        $('.uimg').each(function(i, e){
            if($(e).attr("effect").toUpperCase().indexOf(txt.toUpperCase())>=0) $(e).show();
        });
    });
	
	$('#infoPanel').click(function(){
	    $('#fullInfo').slideToggle();
    });
	
	$("#buyOrderInput").on("click", function () {
        $(this).select();
    });
});	

</script>
<?
$conn->close;
include 'footer.php';
?>


</body>
</html>