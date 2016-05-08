<? 
require_once 'steamauth/steamauth.php';
require("steamapi.php");

$effect = $_GET['eff'];

$title = $effect.'. Quick.tf';

include 'menu.php' ?>



<div class="prftagdiv"> 
    <input type="text" id="search-criteria" class="quick" placeholder="Filter...">
</div>

<div class="maindiv_container" style="margin-top: 0px;">

<div class="maindiv_header">
	<h3><? echo $effect; ?></h3>
</div>

<div class="umaindiv" id="effects">
<?
    foreach($hatArr as $hat)
	{
		if(in_array($effect, $bpEffArr[$hat]))
		{
			$extEff[] = $hat;
		}
	}
	
	foreach($extEff as $hat)
	{
		$thisEffPrices[$hat] = $qPriceArr[$hat][$effect];
	}
	
	arsort($thisEffPrices);
	
	foreach($thisEffPrices as $hat=>$value)
	{
		echo '
		<a href="#">
			<div class="uimg" title="' .$effect. ' ' .$hat. '">
		        <img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
			    <img src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px; margin-top:10;">';
				if($qPriceArr[$hat][$effect] > 0)
					echo '
		        <div class="utag">' .$qPriceArr[$hat][$effect]. '</div>';
				echo '
		    </div>
		</a>
		';
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
            if($(e).attr("title").toUpperCase().indexOf(txt.toUpperCase())>=0) $(e).show();
        });
    });
	
});	
</script>

<?
include 'footer.php'
?>

</body>
</html>