<? 
require_once 'steamauth/steamauth.php';
require("steamapi.php");

$title = 'Info, generations, tiers, recommended prices and more about unusuals. Quick.tf';

include 'menu.php' ?>



<div class="prftagdiv"> 
    <input type="text" id="search-criteria" class="quick" placeholder="Filter...">
</div>

<div class="maindiv_container" style="margin-top: 0px;">

<div class="maindiv_panel" id="effectsPanel">
	<h3>Effects</h3>
</div>

<div class="umaindiv" id="effects" style="display: none;">
<?
usort($effNameArr, "sortByAvgEff");
	
    foreach($effNameArr as $effect)
	{
		echo '<div class="uimg" title="' .$effect. '">
		<a href="#">
		<img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
		</a>
		<div class="utag">' .$avgEffArr[$effect]. '</div>
		</div>
		';
	}
	?>
</div>

<div class="maindiv_panel" id="cosmeticsPanel">
	<h3>Cosmetics</h3>
</div>

<div class="umaindiv" id="hats">
<?
    usort($hatArr, "sortByAvg");
	
    foreach($hatArr as $hat)
	{
		if(strpos($hat, 'Taunt') !== false)
			continue;
		
		echo '<div class="uimg" title="' .$hat. '" id="' .$hatIDArr[$hat]. '">
		<a href="http://quick.test/stats/' .$hat. '">
		<img  src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:relative; margin:5;">
		</a>
		<div class="utag">' .$avgHatArr[$hat]. '</div>
		</div>
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
	
	$('#effectsPanel').click(function(){
	    $('#effects').slideToggle();
    });
	
	$('#cosmeticsPanel').click(function(){
	    $('#hats').slideToggle();
    });
});	
</script>

<?
include 'footer.php'
?>

</body>
</html>