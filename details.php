<? 
require_once 'steamauth/steamauth.php';
require_once 'steamapi.php';

$hat = $_GET['hat'];
$effect = $_GET['effect'];

$title = $effect. ' ' .$hat. ' - details. quick.test';

$links = '
<script src="https://cdnjs.com/libraries/chart.js"></script>
';

include 'menu.php';

?>


<script type="text/javascript">
$(function () {
	//Better to construct options first and then pass it as a parameter
	var options = {
		title: {
			text: "Spline Chart using jQuery Plugin"
		},
                animationEnabled: true,
		data: [
		{
			type: "spline", //change it to line, area, column, pie, etc
			dataPoints: [
				{ x: 10, y: 10 },
				{ x: 20, y: 12 },
				{ x: 30, y: 8 },
				{ x: 40, y: 14 },
				{ x: 50, y: 6 },
				{ x: 60, y: 24 },
				{ x: 70, y: -4 },
				{ x: 80, y: 10 }
			]
		}
		]
	};

	$("#chartContainer").CanvasJSChart(options);

});
</script>

<div class="umaindiv" id="tinylist">
<?
    echo '<div class="uimg" title="' .$hat. '" id="' .$hatIDArr[$hat]. '">
	    <a href="http://quick.test/qoffer/' .$hat. '">
	        <img  src="' .$schema["result"]["items"][$hatIDArr[$hat]]["image_url"]. '" style="height:80px; width:80px; position:relative; margin:5;">
			<img id="'. $effect. '" src="http://backpack.tf/images/440/particles/' .$effIDbyNameArr[$effect]. '_94x94.png" style="height:80px; width:80px; position:absolute; top: 3px; left: 3px;">
		</a>
	</div>
		';
?>

    <div style="width: 90%; margin: auto;">
	    <? echo '<H1>' .strtoupper($effect). ' ' .strtoupper($hat). '</H1>';?>
	</div>
		
	    <div style="clear:both; margin-top: 80px;">
		    <div style="width: 40%; float: left">
		        <p class="profile">Blah Naaahhaha</p>
		        <p class="profile">Blah Naaahhaha</p>
		        <p class="profile">Blah Naaahhaha</p>
		        <p class="profile">Blah Naaahhaha</p>
		        <p class="profile">Blah Naaahhaha</p>
	        </div>
		
		    <div style="width: 55%; float: left">
		        <div id="chartContainer" style="height: 300px; width: 100%;"></div>
		    </div>
		</div>
	
	
</div>



<?
include 'footer.php'
?>


</body>
</html>