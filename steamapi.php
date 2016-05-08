<?php

require_once 'steamauth/steamauth.php';
require_once 'connect.php';
require_once 'simple_html_dom.php';

$schema = json_decode(file_get_contents('http://quick.test/data/schema.json'), true);
//$items = json_decode(file_get_contents('http://quick.test/data/items.json'), true);
$bptfitems = json_decode(file_get_contents('http://quick.test/data/items_bptf.json'), true);
$avgEffArr = json_decode(file_get_contents('http://quick.test/data/effectAvgPrices.json'), true);
$effNameArr =json_decode(file_get_contents('http://quick.test/data/effectNames.json'), true);
$effNamebyIDArr = json_decode(file_get_contents('http://quick.test/data/effectIDs.json'), true);
$effIDbyNameArr = json_decode(file_get_contents('http://quick.test/data/effectIDbyName.json'), true);
$hatArr = json_decode(file_get_contents('http://quick.test/data/hatNames.json'), true);
$bpPricesArr = json_decode(file_get_contents('http://quick.test/data/bpPrice.json'), true);
$bpEffArr = json_decode(file_get_contents('http://quick.test/data/bpExstEff.json'), true);
$avgHatArr = json_decode(file_get_contents("http://quick.test/data/bpAvgHatPrice.json"), true);
$qPriceArr = json_decode(file_get_contents("http://quick.test/data/qPrice.json"), true);
$buyPriceArr = json_decode(file_get_contents("http://quick.test/data/buyPrice.json"), true);
$themedArr = json_decode(file_get_contents("http://quick.test/data/themed.json"), true);
$hatIDArr = json_decode(file_get_contents("http://quick.test/data/hatIDs.json"), true);
$IDHatArr = json_decode(file_get_contents("http://quick.test/data/IDHats.json"), true);
$classHatArr = json_decode(file_get_contents("http://quick.test/data/usedByClass.json"), true);
$tierArr = json_decode(file_get_contents("http://quick.test/data/tiers.json"), true);
$api_key = '5F5BB30E37DF4D3E9FA00ED5BE86D2CB';

function getQuality($id)
{
	if($id == 5)
		return "Unusual";
}

function sortByDate($a, $b)
{

    $t1 = strtotime($a['date']);
    $t2 = strtotime($b['date']);
    return $t2 - $t1;
}

function sortByPrice($a, $b)
{
    $t1 = $a['price'];
    $t2 = $b['price'];
    return $t1 - $t2;
}

function sortByProfit($a, $b)
{
	$t1 = $a['profit'];
    $t2 = $b['profit'];
    return $t2 - $t1;	
}

function sortByAvg($a, $b)
{
	global $avgHatArr;
	
	$t1 = $avgHatArr[$a];
	$t2 = $avgHatArr[$b];
	
	return $t2 - $t1;
}

function sortByAvgEff($a, $b)
{
	global $avgEffArr;
	
	$t1 = $avgEffArr[$a];
	$t2 = $avgEffArr[$b];
	
	return $t2 - $t1;
}

function sortByQPrice($a, $b)
{
	global $qPriceArr;
	
	$t1 = $qPriceArr[$a];
	$t2 = $qPriceArr[$b];
	
	return $t2 - $t1;
}

function genHatNums()
{
	
	global $hatArr, $schema, $hatIDArr, $IDHatArr;
	
	unset($IDHatArr);
		
	foreach($hatArr as $hat)
    {	
	    foreach($schema["result"]["items"] as $val)
	    {
		    if($val["item_name"] == $hat)
		    {
		    	$hatIDArr[$hat] = $val['defindex'];
				$IDHatArr[$val['defindex']] = $hat;
		    	echo $hat. " " .$val['defindex']. "<br>";
				break;
		    }
	    }
		
    }
	
	file_put_contents('data/IDHats.json', json_encode($IDHatArr));
}



function genHatIDs()
{
	
	global $hatArr, $schema, $hatIDArr, $IDHatArr;
	
	unset($hatIDArr);
	unset($IDHatArr);
	/*	
	foreach($hatArr as $hat)
    {	
	    foreach($schema["result"]["items"] as $i => $val)
	    {
		    if($val["item_name"] == $hat)
		    {
		    	$hatIDArr[$hat] = $i;
				$IDHatArr[$i] = $hat;
		    	echo $hat. " " .$i. "<br>";
				break;
		    }
	    }
		
    }*/
	
	foreach($schema["result"]["items"] as $i => $val)
	{
		$hatIDArr[$val["item_name"]] = $i;
		$IDHatArr[$val["defindex"]] = $val["item_name"];
		echo $val["item_name"]. " " .$i. "<br>";
	}
	
	file_put_contents('data/hatIDs.json', json_encode($hatIDArr));
	file_put_contents('data/IDHats.json', json_encode($IDHatArr));
}

function getSchema()
{
	global $schema, $api_key;
		
    $link = file_get_contents("http://api.steampowered.com/IEconItems_440/GetSchema/v0001/?language=en&key=" .$api_key);
    file_put_contents('data/schema.json', $link);
	$schema = json_decode(file_get_contents('http://quick.test/data/schema.json'), true);
}

function getBPData()
{
	global $bptfitems;
	$link = file_get_contents("http://backpack.tf/api/IGetPrices/v4/?key=56499ef5dea9e9c9397f77d8");
	
	$temp = json_decode($link, true);
	
	if($temp['response']['success'] == 1)
	{
        file_put_contents('data/items_bptf.json', $link);
	    unset($bptfitems);
	    $bptfitems = $temp;
	}
}

//Заполняю массив значениями эффектов + массив с именами эффектов

function getEffects()
{	
    global $avgHatArr, $qPriceArr, $hatArr, $bpPriceArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDbyNameArr, $bpEffArr, $effNamebyIDArr;
	
	unset($avgEffArr);
	unset($effNameArr);
	unset($effnamebyIDArr);
	unset($effIDbyNameArr);

    $source = file_get_html('http://backpack.tf/effects');
	
	foreach($source->find('ul.item-list li') as $effect)
	{
		$data = $effect->title;
		
		$price = $effect->find('span.bottom-right', 0)->plaintext;
		$price = str_replace("keys", "", $price);
		$price = str_replace("avg", "", $price);
		$price = str_replace(" ", "", $price);
		
		$priceNum = (float)$price;
				
		$avgEffArr[$data] = $priceNum;
		$effNameArr[] = $data;
	}
	
	//получаю defindex каждого эффекта из списка
	
	foreach($effNameArr as $effect)
	{
		foreach($schema['result']['attribute_controlled_attached_particles'] as $schemaEffect)
		{
			if($schemaEffect['name'] == $effect)
			{
				$effIDArr[$schemaEffect['id']] = $effect;
				break;
			}
		}
	}
	
	foreach($effNamebyIDArr as $key=>$value)
	{
		$effIDbyNameArr[$value] = $key;
	}
	
	file_put_contents('data/effectAvgPrices.json', json_encode($avgEffArr));
	file_put_contents('data/effectNames.json', json_encode($effNameArr));
	file_put_contents('data/effectIDs.json', json_encode($effNamebyIDArr));
	file_put_contents('data/effectIDbyName.json', json_encode($effIDbyNameArr));
	
}
	

//получаю названия шапок
	
function getHats()
{
	
	global $avgHatArr, $qPriceArr, $hatArr, $bpPriceArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr;
	
	unset($hatArr);
	
    $source = file_get_html('http://backpack.tf/unusuals');
	
	foreach($source->find('ul.item-list li') as $hat)
	{
		$data = $hat->title;
		$data = str_replace("Unusual ","", $data);
		$hatArr[] = $data;
	}
	
	file_put_contents('data/hatNames.json', json_encode($hatArr));
}
	
	
	

//получаю new цену бекпака
function getNewBPPrices()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPricesArr, $buyPriceArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDbyNameArr, $effNamebyIDArr, $bpEffArr, $conn;
		
	$sql = '';
	
	foreach($hatArr as $hat)
	{
		foreach($bpEffArr[$hat] as $effect)
		{
			$cur = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['currency'];
			
			if($cur == 'usd')
			{
			    $temp1 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value_high'];
				$temp1 = round($temp1 / 2.33, 2);
			    $temp2 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value'];
                $temp2 = round($temp2 / 2.33, 2);
			}
			else
			{
				$temp1 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value_high'];
			    $temp2 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value'];
			}
			
			if($temp1 != NULL)
				$final = ($temp1 + $temp2) / 2;
			else
				$final = $temp2;
			
			echo '<p>' .$effect. ' ' .$hat. ' - new price = ' .$final. '<p>';
			echo 'old price - ' .$bpPricesArr[$hat][$effect]. '<br>';
			
			if($buyPriceArr[$hat][$effect] > ($final - $final*0.15))
			{
				$temp = $final;
					
				if($temp > 120)
			    {
					echo 'Too High to buy<br>';
			        $buyPriceArr[$hat][$effect] = "TOO HIGH";
			        continue;
			    }
					
					$bPrice = 7;
					
					if($temp > $qPriceArr[$hat][$effect])
					{
					    $temp = $qPriceArr[$hat][$effect];
					}
					
					if($temp < 9)
						$bPrice = 7;
					elseif($temp < 11)
					    $bPrice = 8;
					elseif($temp < 25)
					    $bPrice = $temp * 0.85;
					elseif($temp < 40)
					    $bPrice = $temp * 0.82;
					elseif($temp < 55)
					    $bPrice = $temp * 0.8;
					elseif($temp < 90)
			 		   $bPrice = $temp * 0.72;
					else
					{
						echo 'Too High to buy<br>';
						$buyPriceArr[$hat][$effect] = "TOO HIGH";
						$sql .= "UPDATE hats SET buyprice=\"TOO HIGH\" WHERE name=\"" .$effect. " " .$hat. "\";";
						continue;
					}
					
				echo 'New buyPrice = ' .round($bPrice). '<br>';
					
				$buyPriceArr[$hat][$effect] = round($bPrice);
				$sql .= "UPDATE hats SET buyprice=\"" .$buyPriceArr[$hat][$effect]. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
				
				
				
			    $bpPricesArr[$hat][$effect] = $final;
			    $sql .= "UPDATE hats SET bptfprice=\"" .$final. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
			
			}
		}
	}
	
	if($sql != '')
	{
	    if ($conn->multi_query($sql) === TRUE) 
	    {
		    echo "DONE!";
        } 
	    else 
	    {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
	}
	
	file_put_contents('data/bpPrice.json', json_encode($bpPricesArr));
	file_put_contents("data/buyPrice.json", json_encode($buyPriceArr));
	
}
	
	
//получаю цену бекпака
function getBPPrices()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPriceArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDbyNameArr, $effNamebyIDArr, $bpEffArr, $conn;
	
	unset($bpPricesArr);
	
	$sql = '';
	
	foreach($hatArr as $hat)
	{
		echo "<p>" .$hat. "</p>";
		foreach($bpEffArr[$hat] as $effect)
		{
			$cur = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['currency'];
			
			if($cur == 'usd')
			{
			    $temp1 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value_high'];
				$temp1 = round($temp1 / 2.33, 2);
			    $temp2 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value'];
                $temp2 = round($temp2 / 2.33, 2);
			}
			else
			{
				$temp1 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value_high'];
			    $temp2 = $bptfitems['response']['items'][$hat]['prices'][5]['Tradable']['Craftable'][$effIDbyNameArr[$effect]]['value'];
			}
			
			if($temp1 != NULL)
				$final = ($temp1 + $temp2) / 2;
			else
				$final = $temp2;
			
			$bpPricesArr[$hat][$effect] = $final;
			$sql .= "UPDATE hats SET bptfprice=\"" .$final. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
			
			echo $effect. "- id: " .$effIDbyNameArr[$effect]. " temp1: " .$temp1. " temp2: " .$temp2. " bpPrice: " .$bpPricesArr[$hat][$effect]. "<br>";
		}
	}
	
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
	file_put_contents('data/bpPrice.json', json_encode($bpPricesArr));
	
}


function getExtEffects()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPriceArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr;
	
	unset($bpEffArr);
	
	foreach($hatArr as $hatName)
	{
		echo $hatName. "<br>";
		
		
		$url = str_replace(' ', '%20', $hatName);
		$source = file_get_html("http://backpack.tf/unusual/" .$url);
		
		foreach($source->find('ul.item-list li') as $hat)
		{
			$data = $hat->title;
		    $data = str_replace(' '.$hatName, '', $data);
			
			if($data == "Community Sparkle")
			continue;
		
			$bpEffArr[$hatName][] = $data;
			echo $data. "<br>";
		}
	}
	
	file_put_contents('data/bpExstEff.json', json_encode($bpEffArr));
	
}

$usblEffArr = array("Sunbeams", "Green Energy", "Purple Energy", "Haunted Ghosts", "Vivid Plasma", "Searing Plasma", "Circling Peace Sign", "Circling TF Logo", "Disco Beat Down", "Miami Nights", "Stormy Storm", "Blizzardy Storm", "Smoking", "Steaming", "Aces High", "Dead Presidents");


$mult = 0.0175;

function genAvgHatPrices()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPricesArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr, $mult;
	
	$effSumArr = array();
	$num = 0;
	
	unset($avgHatArr);
	
	foreach($hatArr as $hat)
	//for($cir =0; $cir<100; $cir++)
	{
		//$hat = $hatArr[$cir];
		
		$i = 0;
		$sum = 0;
		$effSum = 0;
		
		foreach($usblEffArr as $uEffect)
		{
			if($bpPricesArr[$hat][$uEffect] > 0)
			{
				$sum += (float)$bpPricesArr[$hat][$uEffect];
				$i++;
				$effSum += $avgEffArr[$uEffect];
			}
		}
		
		if($i > 7)
		{
			$avgHatArr[$hat] = round($sum/$i);
		    $effSumArr[] = ($sum/$i)/$effSum;
		    $num ++;
		}
		else
			$avgHatArr[$hat] = 0;
		
		
		
	}
	
	file_put_contents("data/bpAvgHatPrice.json", json_encode($avgHatArr));
}

//генерирую цены
function genQPrices()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPricesArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr, $mult, $conn, $classHatArr;
	
	$sqlF = "SELECT * FROM hats";
	$result = $conn->query($sqlF);
	
	while($row = $result->fetch_assoc())
	{
		echo $row['name']. '<br>';
	}
	
	
	unset($qPriceArr);
	$qPriceArr = array();
	
	foreach($hatArr as $hat)
	{
		$highTierHat = false;
		$lowTierHat = false;
		
		if($avgHatArr[$hat] == 0)
			continue;
		
		if($avgHatArr[$hat] > 50)
		{
			$highTierHat = true;
		}
		
		if($avgHatArr[$hat] < 20)
		{
			$lowTierHat = true;
		}
		
		echo '<h1>' .$hat. '</h1>';
		
		foreach($bpEffArr[$hat] as $effect)
		{
			if(!isset($data[$effect. ' ' .$hat]))
			{
				$sqlS = "INSERT INTO hats (name, bptfprice, qprice, buyprice, themed, tier) VALUES (\"" .$effect. ' ' .$hat. "\", \"" .$bpPriceArr[$hat][$effect]. "\", 0, 0, 0, 0)";
				$conn->query($sqlS);
			}
			
			$effMult = 1;
			
			if($lowTierHat)
			{
				if($avgEffArr[$effect] < 20)
					$effMult = 1.6;
				elseif($avgEffArr[$effect] < 40)
				$effMult = 1.2;
				
				if($avgEffArr[$effect] > 150)
					$effMult = 0.77;
				elseif($avgEffArr[$effect] > 80)
					$effMult = 0.85;
			}
			elseif($highTierHat)
			{
				if($avgEffArr[$effect] < 40)
				{
					if($classHatArr[$hat] == 'Multi')
						$effMult = 1;
					else
						$effMult = 0.82;
				}
				
				if($avgHatArr[$hat]>100)
				{
					if($avgEffArr[$effect] > 150)
					    $effMult = 1.5;
				    elseif($avgEffArr[$effect] > 80)
					    $effMult = 1.2;
				}
				elseif($avgHatArr[$hat]>75)
				{
					if($avgEffArr[$effect] > 150)
					    $effMult = 1.2;
				    elseif($avgEffArr[$effect] > 80)
					    $effMult = 1.1;
				}
				else
				{
					if($avgEffArr[$effect] > 150)
					    $effMult = 1.15;
				    elseif($avgEffArr[$effect] > 80)
					    $effMult = 1.05;
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
			
			echo $effect. ' - ' .$classHatArr[$hat]. ' - ' .$temp. '<br>
			';
			
			$qPriceArr[$hat][$effect] = $temp;
			
			$sql .= "UPDATE hats SET qprice=\"" .$temp. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
		}	       		
	}
	
	file_put_contents("data/qPrice.json", json_encode($qPriceArr));
	
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
}

//генерирую цены, по которым покупаю
function genBuyPrices()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPricesArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr, $buyPriceArr, $conn;
	
	unset($buyPriceArr);
	$buyPriceArr = array();
	
	$sql = "SELECT * FROM hats";
	$result = $conn->query($sql);
	
	while($row = $result->fetch_assoc())
	{
		$data[$row['name']] = $row['buyprice'];
	}
	
	$sql = '';
	
	foreach($hatArr as $hat)
	{
		if ($avgHatArr[$hat] == 0)
		    continue;
		
		echo '<p></p><p><H2 style="color:purple">' .$hat. '</H2><p>';
		foreach($bpEffArr[$hat] as $effect)
		{
			echo '<p><H3>' .$effect. '</H3><p>';
			$temp = $bpPricesArr[$hat][$effect];
			echo 'backpack price is: ' .$temp. '</br>';
			
			if($temp > 120)
			{
				$buyPriceArr[$hat][$effect] = "TOO HIGH";
				echo $effect. ' backpackprice is: ' .$temp. '. too high. continue...<br>';
				continue;
			}
			
			$bPrice = 7;
			
			if($temp == 0)
			{
				$temp = $qPriceArr[$hat][$effect];
				echo 'backpackprice is not available for effect: "' .$effect. '". Taking qprice, which is: ' .$qPriceArr[$hat][$effect]. '<br>';
			}
			elseif($temp > $qPriceArr[$hat][$effect])
			{
			    $temp = $qPriceArr[$hat][$effect];
				echo 'backpackprice is unreasonable high (' .$bpPricesArr[$hat][$effect]. ') for effect: "' .$effect. '", taking qprice which is: ' .$qPriceArr[$hat][$effect]. '<br>';
			}
				
			if($temp < 9)
				$bPrice = 7;
			elseif($temp < 11)
			    $bPrice = 8;
			elseif($temp < 15)
			    $bPrice = $temp * 0.8;
			elseif($temp < 25)
			    $bPrice = $temp * 0.76;
			elseif($temp < 40)
			    $bPrice = $temp * 0.73;
			elseif($temp < 55)
			    $bPrice = $temp * 0.70;
			elseif($temp < 90)
			    $bPrice = $temp * 0.65;
			else
			{
				$buyPriceArr[$hat][$effect] = "TOO HIGH";
				echo 'price is too high to offer. continue<br>';
				continue;
			}
			
			echo 'preoffer for effect ' .$effect. ' is: ' .$bPrice. '<br>';
			
			$buyPriceArr[$hat][$effect] = round($bPrice);
			
			echo 'final offer for effect ' .$effect. ' is: ' .$buyPriceArr[$hat][$effect]. '<br>';
			
			if($buyPriceArr[$hat][$effect] < $data[$effect. ' ' .$hat] || $data[$effect. ' ' .$hat] == 0)
				$sql .= "UPDATE hats SET buyprice=\"" .$buyPriceArr[$hat][$effect]. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
			else
				$buyPriceArr[$hat][$effect] = $data[$effect. ' ' .$hat];
		}
	}
	
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
	file_put_contents("data/buyPrice.json", json_encode($buyPriceArr));
}

//генерирую высокие цены, по которым покупаю
function genBuyPricesImproved()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPricesArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr, $buyPriceArr, $conn;
	
	unset($buyPriceArr);
	$buyPriceArr = array();
	
	$sql = "SELECT * FROM hats";
	$result = $conn->query($sql);
	
	while($row = $result->fetch_assoc())
	{
		$data[$row['name']] = $row['buyprice'];
	}
	
	$sql = '';
	
	foreach($hatArr as $hat)
	{
		if ($avgHatArr[$hat] == 0)
		    continue;
		
		echo '<p></p><p><H2 style="color:purple">' .$hat. '</H2><p>';
		foreach($bpEffArr[$hat] as $effect)
		{
			echo '<p><H3>' .$effect. '</H3><p>';
			$temp = $bpPricesArr[$hat][$effect];
			echo 'backpack price is: ' .$temp. '</br>';
			
			if($temp > 120)
			{
				$buyPriceArr[$hat][$effect] = "TOO HIGH";
				echo $effect. ' backpackprice is: ' .$temp. '. too high. continue...<br>';
				continue;
			}
			
			
			$bPrice = 7;
			
			if($temp == 0)
			{
				$temp = $qPriceArr[$hat][$effect];
				echo 'backpackprice is not available for effect: "' .$effect. '". Taking qprice, which is: ' .$qPriceArr[$hat][$effect]. '<br>';
			}
			elseif($temp > $qPriceArr[$hat][$effect])
			{
			    $temp = $qPriceArr[$hat][$effect];
				echo 'backpackprice is unreasonable high (' .$bpPricesArr[$hat][$effect]. ') for effect: "' .$effect. '", taking qprice which is: ' .$qPriceArr[$hat][$effect]. '<br>';
			}
				
			if($temp < 9)
				$bPrice = 7;
			elseif($temp < 11)
			    $bPrice = 8;
			elseif($temp < 25)
			    $bPrice = $temp * 0.85;
			elseif($temp < 40)
			    $bPrice = $temp * 0.82;
			elseif($temp < 55)
			    $bPrice = $temp * 0.8;
			elseif($temp < 90)
			    $bPrice = $temp * 0.72;
			else
			{
				$buyPriceArr[$hat][$effect] = "TOO HIGH";
				echo 'price is too high to offer. continue<br>';
				continue;
			}
			
			echo 'preoffer for effect ' .$effect. ' is: ' .$bPrice. '<br>';
			
			$buyPriceArr[$hat][$effect] = round($bPrice);
			
			echo 'final offer for effect ' .$effect. ' is: ' .$buyPriceArr[$hat][$effect]. '<br>';
			
			$sql .= "UPDATE hats SET buyprice=\"" .$buyPriceArr[$hat][$effect]. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
		}
	}
	
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
	file_put_contents("data/buyPrice.json", json_encode($buyPriceArr));
}


function fillUnusualsDB()
{
	global $avgHatArr, $qPriceArr, $hatArr, $bpPricesArr, $usblEffArr, $schema, $bptfitems, $avgEffArr, $effNameArr, $effIDArr, $bpEffArr, $buyPriceArr, $conn, $themedArr;
		
	$sql = '';
	foreach($hatArr as $hat)
	{
		foreach($bpEffArr[$hat] as $effect)
		{
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
				}
					
			$sql .= 'INSERT INTO hats (name, bptfprice, qprice, buyprice, themed) VALUES ("' .$effect. ' ' .$hat. '", "' .$bpPricesArr[$hat][$effect]. '", "' .$qPriceArr[$hat][$effect]. '", "' .$buyPriceArr[$hat][$effect]. '", "' .$themedArr[$hat][$effect]. '");';
			
		}
	}
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}



function genClassArr()
{
	global $classHatArr, $hatArr, $hatIDArr, $schema, $avgHatArr, $conn;
	
	$sqlF = "SELECT * from hats_advanced";
	$result = $conn->query($sqlF);
	while($row = $result->fetch_assoc())
		$data[$row['name']] = 1;
	 
	unset($classHatArr);
	$sql = '';
	
	foreach($hatArr as $hat)
	{
		if(!isset($data[$hat]))
		{
			$sqlS = "INSERT INTO hats_advanced (name, avg, class) VALUES (\"" .$hat. "\", \"" .$avgHatArr[$hat]. "\", 0)";
			$conn->query($sqlS);
			echo $hat. ' - done<br>';
		}
		
		if($schema["result"]["items"][$hatIDArr[$hat]]["used_by_classes"] == NULL or $schema["result"]["items"][$hatIDArr[$hat]]["used_by_classes"][1] != NULL)
		{
			$classHatArr[$hat] = "Multi";
			$sql .= "UPDATE hats_advanced set class=\"Multi\" WHERE name=\"" .$hat. "\";";
		}
		else
		{
			$temp = $schema["result"]["items"][$hatIDArr[$hat]]["used_by_classes"][0];
			$classHatArr[$hat] = $temp;
			$sql .= "UPDATE hats_advanced set class=\"" .$temp. "\" WHERE name=\"" .$hat. "\";";
		}
	}
	
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
	file_put_contents("data/usedByClass.json", json_encode($classHatArr)); 
}

function changeHat()
{
	global $conn;
	if(isset($_POST["hatName"]))
	{
		if(isset($_POST["newHatPrice"]))
		{
			$sql = "UPDATE hats SET buyprice='" .$_POST['newHatPrice']. "' WHERE name='" .$_POST['hatName']. "'";
			$conn->query($sql);
			$sql =  "UPDATE hats SET themed='" .$_POST['themed']. "' WHERE name='" .$_POST['hatName']. "'";
			$conn->query($sql);
			
			print_r($_POST);
		}
	}
}

function editHatTags()
{
	global $conn, $hatArr, $bpEffArr, $avgHatArr, $avgEffArr, $themedArr;
	/*
	
	$sql = "UPDATE hats set qprice='0' WHERE themed=1";
	$conn->query($sql);
	
	$sql = "SELECT * FROM hats WHERE themed=1";
	$result = $conn->query($sql);
	
	
	while($row = $result->fetch_assoc())
	{
		$data[$row['name']]['themed'] = $row['themed'];
	}
	
	foreach($hatArr as $hat)
	{
		foreach($bpEffArr[$hat] as $effect)
		{
			if($data[$effect. ' ' .$hat]['themed'])
				$themedArr[$hat][$effect] = 1;
			else
				$themedArr[$hat][$effect] = 0;
		}
	}
	
	file_put_contents("data/themed.json", json_encode($themedArr));*/
	
	
	
	echo 'DONE';
}

function regenerateHat()
{
	global $conn, $bpEffArr, $avgHatArr, $qPriceArr, $bpPricesArr, $buyPriceArr, $mult, $avgEffArr;
		
	if(isset($_POST["hat"]))
	{
		$sql = '';
		$hat = $_POST["hat"];
		if(isset($_POST["newAvg"]))
		{
			$avg = $_POST["newAvg"];
			$avgHatArr[$hat] = (float)$avg;
			
			foreach($bpEffArr[$hat] as $effect)
			{
				$sqlS = "SELECT * FROM hats WHERE name=\"" .$effect. " " .$hat. "\"";
				$result = $conn->query($sqlS);
				if($result->num_rows == 0)
				{
					$sqlF = "INSERT INTO hats (name, bptfprice, qprice, buyprice, themed, tier) VALUES (\"" .$effect. " " .$hat. "\", \"" .$bpPriceArr[$hat][$effect]. "\", \"0\", \"0\", \"" .$themedArr[$hat][$effect]. "\", \"0\")";
					$conn->query($sqlF);
				}
					
				
				
				$temp = round($avgHatArr[$hat]*$mult*$avgEffArr[$effect]);
			    if($temp < 9)
			    {
				    if($effect == "Nuts n' Bolts") {
				    	$temp = 8;
				    } elseif($effect == "Massed Flies") { 
				        $temp = 8;
				    } elseif($effect == "Orbiting Planets") {
					    $temp = 8;
				    } elseif($effect == "Bubbling") {
					    $temp = 8;
				    }
				    else
					    $temp = 9;
			    }
			
			    $qPriceArr[$hat][$effect] = $temp;
				
				$temp = $bpPricesArr[$hat][$effect];
			
			    if($temp > 120)
			    {
				    $buyPriceArr[$hat][$effect] = "TOO HIGH";
				    continue;
			    }
			
			    $bPrice = 7;
			
			    if($temp == 0 or $temp > $qPriceArr[$hat][$effect])
				    $temp = $qPriceArr[$hat][$effect];
					
				
				echo"<p>" .$effect. " bpPrice: " .$temp. " qPrice: " .$qPriceArr[$hat][$effect]. " avgHatArr: " .$avgHatArr[$hat]. " mult: " .$mult. " avgEffArr: " .$avgEffArr[$effect]. "<br>";
				
			    if($temp < 9)
				    $bPrice = 7;
			    elseif($temp < 11)
			        $bPrice = 8;
			    elseif($temp < 15)
			        $bPrice = $temp * 0.8;
			    elseif($temp < 25)
			        $bPrice = $temp * 0.76;
			    elseif($temp < 40)
			        $bPrice = $temp * 0.73;
			    elseif($temp < 55)
			        $bPrice = $temp * 0.70;
			    elseif($temp < 90)
			        $bPrice = $temp * 0.65;
			    else
			    {
				    $bPrice = "TOO HIGH";
			    }
			    
				if($bPrice != "TOO HIGH")
					$buyPriceArr[$hat][$effect] = round($bPrice);
				else
					$buyPriceArr[$hat][$effect] = $bPrice;
				
				echo $effect. " bpPrice: " .$temp. " qPrice: " .$qPriceArr[$hat][$effect]. " avgHatArr: " .$avgHatArr[$hat]. " mult: " .$mult. " avgEffArr: " .$avgEffArr[$effect]. " buyPrice: " .$buyPriceArr[$hat][$effect]. "<br>";
				
				
			    $sql .= "UPDATE hats SET buyprice='" .$buyPriceArr[$hat][$effect]. "' WHERE name='" .$effect. " " .$hat. "'";
			    $sql .=  "UPDATE hats SET qprice='" .$qPriceArr[$hat][$effect]. "' WHERE name='" .$effect. " " .$hat. "'";
			    
			}
			
			$conn->multi_query($sql);
			
			file_put_contents("data/buyPrice.json", json_encode($buyPriceArr));
			file_put_contents("data/qPrice.json", json_encode($qPriceArr));
			file_put_contents("data/bpAvgHatPrice.json", json_encode($avgHatArr));
			
			
		}
	}
}

function fillClassDB()
{
	global $conn, $hatArr, $avgHatArr, $classHatArr;
	
	foreach($hatArr as $hat)
	{
		$sql = "INSERT INTO hats_advanced (name, avg, class) VALUES (\"" .$hat. "\", \"" .$avgHatArr[$hat]. "\", \"" .$classHatArr[$hat]. "\")";
		$conn->query($sql);
		echo $hat. " done<br>";
	}
}


$classes = array("Scout", "Soldier", "Pyro", "Demoman", "Heavy", "Engineer", "Medic", "Sniper", "Spy", "Multi");

$title = '[b]Buying unusual [u]quicksells[/u].[/b]

Paying with TF2 and CSGO keys,

and

Paypal: listed amount of TF2 keys * $1.7 = amount I\'m willing to pay. Sending as gift/family

Best way to contact me is to send a trade offer. I always check them first
However, feel free to add me or post your question here.

Only looking for listed effects. 

Prices are actual for clean hats. If your hat is duped, i will pay less

Here we go:

';

function genOPFiles() 
{
	global $classes, $hatArr, $classHatArr, $avgHatArr, $title, $bpEffArr, $buyPriceArr, $conn;
	
	$sql = "SELECT * FROM hats";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
		{
			$buyPrice[$row['name']] = $row['buyprice'];
		}
	}
	
	
	echo '<div style="width:40%;margin:auto">';
	
	foreach($classes as $class)
	{
		
		foreach($hatArr as $hat)
		{
			if($classHatArr[$hat] == $class)
			{
				$data[] = $hat;
			}
		}
	}
	
	
	foreach($data as $hat)
	{
		if($avgHatArr[$hat] != 0)
		{
			$temp[] = $hat;
		}
	}
	
	unset($data);
	
	$num = count($temp);
	$num = $num/8;
	
	for($i=0; $i<$num; $i++)
	{
		
		$myfile = fopen("prices/".$i.".txt", "w");
		fwrite($myfile, $title);
		
		for($k=0; $k<8; $k++)
		{
			$hat = $temp[$k+$i*8];
			echo $hat. "<br>";
			fwrite($myfile, '
			
			[color=#8104ba][b]' .$hat. ':[/b] [/color]

');
			unset($data);
			foreach($bpEffArr[$hat] as $effect)
			{
				if($buyPriceArr[$hat][$effect] == 0)
					continue;
				$csPrice = $buyPrice[$effect. ' ' .$hat]/10*9;
				$data[] = $effect. " - [b]" .round($buyPrice[$effect. ' ' .$hat]). " TF2 keys[/b] or [b]" .round($csPrice). " CSGO keys[/b]
";
			}
			
			usort($data, "strnatcmp");
			
			foreach($data as $text)
			{
				fwrite($myfile, $text);
			}
		}
		
		fclose($myfile);
	}
	
	echo '</div>';
}

function genTier()
{
	global $hatArr, $bpEffArr, $avgHatArr, $avgEffArr, $tierArr, $conn;
	
	$sql = '';
	
	foreach($hatArr as $hat)
	{
		foreach($bpEffArr[$hat] as $effect)
		{
			
			if($avgHatArr[$hat] == 0)
			{
				$tier = '?';
			}
			elseif($avgHatArr[$hat] < 30)
			{
				if($avgEffArr[$effect] < 26)
                    $tier = "humble";
				elseif($avgEffArr[$effect] < 60)
				    $tier = "low";
				else
					$tier = "mid";				
			}
			elseif($avgHatArr[$hat] < 50)
			{
				if($avgEffArr[$effect] < 26)
                    $tier = "low";
				elseif($avgEffArr[$effect] < 60)
				    $tier = "mid";
				else
					$tier = "high";				
			}
			elseif($avgHatArr[$hat] < 100)
			{
				if($avgEffArr[$effect] < 26)
                    $tier = "mid";
				elseif($avgEffArr[$effect] < 80)
				    $tier = "high";
				elseif($avgEffArr[$effect] < 120)
					$tier = "special";
				else
					$tier = "GOD";
			}
			else
			{
				if($avgEffArr[$effect] < 26)
                    $tier = "mid";
				elseif($avgEffArr[$effect] < 100)
				    $tier = "high";
				else
					$tier = "GOD";				
			}
			
			$sql .= "UPDATE hats set tier=\"" .$tier. "\" WHERE name=\"" .$effect. " " .$hat. "\";";
			$tierArr[$hat][$effect] = $tier;
			echo $hat. ' ' .$effect. ' - ' .$tier. '<br>';
		} 
	}
		
	if ($conn->multi_query($sql) === TRUE) 
	{
		echo "DONE!";
    } 
	else 
	{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
	
	file_put_contents("data/tiers.json", json_encode($tierArr));
}

