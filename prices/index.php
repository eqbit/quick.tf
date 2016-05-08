<? 
require_once 'menu.php'
?>



<script>
$(document).ready(function(){
	$("#unusual").click(function(){
		$("#unusual").fadeOut(function(){
			$("#unusualbuy").fadeIn();
		    $("#unusualquicksell").fadeIn();
			$("#unusualsell").fadeIn();
		});
	});
});
</script>

<div class="maindiv" id="firstmaindiv" style="height: 300px; margin-top: 100px;">
    <a href="#"><div class="mainpagebox" id="unusual">ENTER</div></a>	
	
    <a href="http://quick.tf/trades" title="Buy an unusual"><div class="choosebox" id="unusualbuy" style="display:none">BUY</div></a>
	<?
	if(isset($_SESSION['steamid']))
    {
	    echo '<a href="http://quick.tf/profile/' .$_SESSION['steam_steamid']. '" title="Create a listing"><div class="choosebox" id="unusualsell" style="display:none">SELL</div></a>
		';
    }
	else
	{
		echo '<a href="#" title="Sign in to access listings"><div class="choosebox" id="unusualsell" style="display:none; background: linear-gradient(to bottom right, #d2d2d2, #e7e7e7, #d2d2d2);">SELL</div></a>
		';
	}
	?>
	<a href="http://quick.tf/qb" title="QuickSell your unusual"><div class="choosebox" id="unusualquicksell" style="display:none">QUICKSELL</div></a>
</div>


<?php
include 'footer.php';
?>



</body>
</html>