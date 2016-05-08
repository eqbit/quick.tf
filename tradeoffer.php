<?
$counter_name = "count_offers.txt";

if (!file_exists($counter_name)) {
  $f = fopen($counter_name, "w");
  fwrite($f,"0");
  fclose($f);
}

$f = fopen($counter_name,"r");
$counterVal = fread($f, filesize($counter_name));
fclose($f);
 

$counterVal++;
$f = fopen($counter_name, "w");
fwrite($f, $counterVal);
fclose($f); 

$tradelink = "https://steamcommunity.com/tradeoffer/new/?partner=82672773&token=_M1MSnJd";

header ("Location: " .$tradelink);
?>