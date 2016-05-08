<? 
require_once 'steamauth/steamauth.php';
require("steamapi.php");

$title = 'Guide for unusual prices - Quick.tf';

include 'menu.php' ?>





<div class="maindiv_container" style="margin-top: 0px;">

<div class="maindiv_header">
	<h3>Generated data</h3>
	<input type="text" id="search-criteria" class="quick" placeholder="Filter...">
</div>

<div class="maindiv_panel" id="effectsPanel">
	<h3>Effects</h3>
</div>

<div class="umaindiv" id="effects" style="display: none;">
<?
usort($effNameArr, "sortByAvgEff");
	
    foreach($effNameArr as $effect)
	{
		echo '<div class="uimg tooltip" name="' .$effect.'">
		<a href="http://quick.test/effect/' .$effect. '">
		<img src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
		</a>
		<span style="width: auto; margin-top:86px; margin-left:-50px; background: gray; color: #eaeaea" >
            <strong>' .$effect. '</strong>
		</span>';
		if($avgEffArr[$effect] > 0 && $adminMode)
			echo '
		<div class="utag">' .$avgEffArr[$effect]. '</div>';
		echo '
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
		
		echo '<div class="uimg tooltip" name="' .$hat.'">
		<a href="http://quick.test/hat/' .$hat. '">
		<img  src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:relative; margin:5;">
		</a>
		<span style="width: auto; margin-top:86px; margin-left:-85px; background: gray; color: #eaeaea" >
            <strong>' .$hat. '</strong>
		</span>';
		if($avgHatArr[$hat] > 0)
			echo '
		<div class="utag">' .$avgHatArr[$hat]. '</div>';
		echo '
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
            if($(e).attr("name").toUpperCase().indexOf(txt.toUpperCase())>=0) $(e).show();
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