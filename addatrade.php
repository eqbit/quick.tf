<? 
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

$hat = $_POST['in_hat'];
$effect = $_POST['in_effect'];
$steamid = $_POST['in_steamid'];
$uniqueid = $_POST['in_uniq'];
$originalid = $_POST['in_origin'];
$price = $_POST['in_price'];
$comment = $_POST['in_comment'];


$sql = "SELECT * FROM trades WHERE uniqueid='" .$uniqueid. "' AND owner='" .$steamid. "'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) 
{
	echo "Listing already existed";
}
else
{
	if($price>10000)
	{
		echo 'Incorect price. Highest allowed price: 10000 keys';
		$conn->close;
		exit();
	}
	
	if($price<5)
	{
		echo 'Incorrect price. Lowest allowed price for an unusual hat: 5 keys<br>
		      TIP: any unusual would be instantly sold for 5-6 keys';
		$conn->close();
		exit();
	}
	$unixTimestamp = time();
    $mysqlTimestamp = date("Y-m-d H:i:s", $unixTimestamp);
	
	$sql="INSERT INTO trades(name, effect, uniqueid, originalid, price, owner, desk, Bumped) VALUES (\"" .$hat. "\", \"" .$effect. "\", \"" .$uniqueid. "\", \"" .$originalid. "\", \"" .$price. "\", \"" .$steamid. "\", \"" .$comment. "\", \"" .$mysqlTimestamp. "\")";
	
	if ($conn->query($sql) === TRUE) 
	{
		$sql = "SELECT * FROM users WHERE steamid=\"" .$steamid. "\" LIMIT 1";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) 
        {
	        $row = $result->fetch_assoc();
			$numtrades = (int)$row['trades_done'];
			$numtrades ++;
			
			$sql = "UPDATE users SET trades_done=" .$numtrades. " WHERE steamid=" .$steamid;
			$conn->query($sql);
        }
		
        echo "Success";
    } 
	else 
	{
        echo "Failed adding to database <br>
		name: " .$hat. ", effect: " .$effect. ", ID: " .$uniqueid. ", original ID: " .$originalid. ", price: " .$price. ", owner: " .$steamid. " comment: " .$comment;
		echo $conn->error;
    }
}
$conn->close();
?>