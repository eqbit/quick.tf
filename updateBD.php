<? 
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';


$action = $_POST['action'];


if($action == 'update_tradeurl')
{
	
    $tradeurl = $_POST['trade_url'];
	$steamid = str_replace("a", "", $_POST['steamid']);
	if(strpos($tradeurl, 'https://steamcommunity.com/tradeoffer/new/?partner=') !== false)
	{
		if(strlen($tradeurl) < 80)
		{
			$sql="UPDATE users SET tradeofferurl='" .$tradeurl. "' WHERE steamid=\"" .$steamid. "\"";
			if ($conn->query($sql) === TRUE) 
	        {
                echo "Success";
            } 
	        else 
	        {
                echo "Failed<br>
		        " .$conn->error;
            }
		}
		else 
			echo '<p>Incorrect URL</p><p>Get a correct one <a href="http://steamcommunity.com/my/tradeoffers/privacy#footer_spacer" target="_blank" style="color: #6495ed">here</a>';
	}
	else 
		echo '<p>Incorrect URL</p><p>Get a correct one <a href="http://steamcommunity.com/my/tradeoffers/privacy#footer_spacer" target="_blank" style="color: #6495ed">here</a>';
}

if($action == 'update_a_hat')
{
	$newBuyPrice = $_POST['newPrice'];
	$name = $_POST['hatName'];
	
	
	$sql = "UPDATE hats SET buyPrice=\"" .$newBuyPrice. "\" WHERE name=\"" .$name. "\"";
	if ($conn->query($sql) === TRUE) 
	{
        echo "Success";
    } 
	else 
	{
        echo "Failed<br>
		" .$conn->error;
    }
	
}

if($action == 'update_themed_attr')
{
	$themed = $_POST['themed'];
	$name = $_POST['hatName'];
	
	
	$sql = "UPDATE hats SET themed='" .$themed. "' WHERE name=\"" .$name. "\"";
	if ($conn->query($sql) === TRUE) 
	{
        echo "Success";
    } 
	else 
	{
        echo "Failed<br>
		" .$conn->error;
    }
	
}

if($action == 'regenerate_a_hat')
{
	$hat = $_POST['hat'];
	$avg = $_POST['newAvg'];
	
	
	$sql = '';
	
	$avgHatArr[$hat] = $avg;
	
	if($avg == 0)
	{
		foreach($bpEffArr[$hat] as $effect)
		{
			$qPriceArr[$hat][$effect] = 0;
			$sql .= "UPDATE hats SET qprice=\"0\" WHERE name=\"" .$effect. " " .$hat. "\";";
		}
		
		file_put_contents("data/qPrice.json", json_encode($qPriceArr));
	    file_put_contents("data/bpAvgHatPrice.json", json_encode($avgHatArr));
	
	    if ($conn->multi_query($sql) === TRUE) 
	    {
		    echo "Success";
        } 
	    else 
	    {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
		
		return;
		
	}
	
	$ultraHighTierHat = false;
	$highTierHat = false;
	$lowTierHat = false;
	
	if($avgHatArr[$hat] > 250)
	{
		$GodTierHat = true;
	}
	elseif($avgHatArr[$hat] > 190)
	{
		$specialTierHat = true;
	}
	elseif($avgHatArr[$hat] > 150)
	{
		$ultraHighTierHat = true;
	}
	elseif($avgHatArr[$hat] > 105)
	{
		$almostUltraHighTierHat = true;
	}
	elseif($avgHatArr[$hat] > 50)
	{
		$highTierHat = true;
	}
	elseif($avgHatArr[$hat] < 20)
	{
		$lowTierHat = true;
	}
	
	
	foreach($bpEffArr[$hat] as $effect)
	{
		$effMult = 1;
			
			if($GodTierHat)
			{
				
				if($avgEffArr[$effect] < 25)
				{
					$effMult = 0.3;
				}
				elseif($avgEffArr[$effect] < 37)
				{
					$effMult = 0.5;
				}
				elseif($avgEffArr[$effect] < 94)
				{
					$effMult = 0.7;
				}
				elseif($avgEffArr[$effect] < 160)
				{
					$effMult = 1.2;
				}
				else
				{
					$effMult = 1.6;
				}
				
			}
			elseif($specialTier)
			{
				
				if($avgEffArr[$effect] < 25)
				{
					$effMult = 0.45;
				}
				elseif($avgEffArr[$effect] < 37)
				{
					$effMult = 0.5;
				}
				elseif($avgEffArr[$effect] < 50)
				{
					$effMult = 0.6;
				}
				elseif($avgEffArr[$effect] < 80)
				{
					$effMult = 0.7;
				}
				elseif($avgEffArr[$effect] < 100)
				{
					$effMult = 0.8;
				}
				else
				{
					if($avgEffArr[$effect] > 150)
					    $effMult = 1.6;
				}
				
			}
			elseif($lowTierHat)
			{
				if($avgEffArr[$effect] < 20)
					$effMult = 1.6;
				elseif($avgEffArr[$effect] < 40)
				$effMult = 1.2;
				
				if($avgEffArr[$effect] > 120)
					$effMult = 0.77;
				elseif($avgEffArr[$effect] > 80)
					$effMult = 0.85;
			}
			elseif($highTierHat)
			{
				if($avgEffArr[$effect] < 25)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 0.82;
					else
						$effMult = 0.80;
				}
				elseif($avgEffArr[$effect] < 36)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 0.8;
					else
						$effMult = 0.77;
				}
				elseif($avgEffArr[$effect] < 40)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 1.4;
					else
						$effMult = 1.2;
				}
				elseif($avgEffArr[$effect] < 90)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 1.1;
					else
						$effMult = 1;
				}
				elseif($avgEffArr[$effect] < 95)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 1.1;
					else
						$effMult = 0.8;
				}
				elseif($avgEffArr[$effect] < 150)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 1.2;
					else
						$effMult = 1.1;
				}
				else
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 1.25;
					else
						$effMult = 1.2;
				}
			}
			elseif($almostUltraHighTierHat)
			{
				if($avgEffArr[$effect] < 25)
				{
					$effMult = 0.5;
				}
				elseif($avgEffArr[$effect] < 37)
				{
					$effMult = 0.75;
				}
				elseif($avgEffArr[$effect] < 50)
				{
					$effMult = 0.78;
				}
				elseif($avgEffArr[$effect] < 80)
				{
					$effMult = 0.8;
				}
				else
				{
					if($avgEffArr[$effect] > 150)
					    $effMult = 1.6;
				}
				
			}
			elseif($ultraHighTierHat)
			{
				if($avgEffArr[$effect] < 25)
				{
					$effMult = 0.45;
				}
				elseif($avgEffArr[$effect] < 50)
				{
					$effMult = 0.7;
				}
				elseif($avgEffArr[$effect] < 150)
				{
					$effMult = 0.8;
				}
				else
				{
					if($avgEffArr[$effect] > 150)
					    $effMult = 1.6;
				}
				
			}
			else
			{
				if($classHatArr[$hat] == 'Multi')
					if($avgEffArr[$effect] < 40)
						$effMult = 1.2;
			}
			
			$temp = round($avgHatArr[$hat]*$mult*$effMult*$avgEffArr[$effect]);
			
			if($temp < 9)
			{
				if($effect == "Nuts n' Bolts") {
					$temp = 8;
				} elseif($effect == "Massed Flies") { 
				    $temp = 8;
				} elseif($effect == "Orbiting Planets") {
					$temp = 8;
				}
				else
					$temp = 9;
			}
			
			$qPriceArr[$hat][$effect] = $temp;
			$sql .= "UPDATE hats SET qprice=\"" .$temp. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
	}
	
	file_put_contents("data/qPrice.json", json_encode($qPriceArr));
	file_put_contents("data/bpAvgHatPrice.json", json_encode($avgHatArr));
	
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "Success";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
}


$conn->close();
?>