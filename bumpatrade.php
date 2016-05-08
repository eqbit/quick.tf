<? 
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

$id = $_POST['id'];


$sql = "SELECT * FROM trades WHERE id='" .$id. "' LIMIT 1";
$result = mysqli_query($conn, $sql);

$unixTimestamp = time();
$mysqlTimestamp = date("Y-m-d H:i:s", $unixTimestamp);

if (mysqli_num_rows($result) > 0) 
{
	$row = $result->fetch_assoc();
	
	$sql = "UPDATE trades SET bumped='" .$mysqlTimestamp. "' WHERE id=" .(int)$id;
	
	if ($conn->query($sql) === TRUE) 
	{
        echo "Success";
    } 
	else 
	{
        echo "Failed updating <br>
		" . $conn->error. "<br>
		uniqID: " .$uniqueid. ", price: " .$price. ", owner: " .$steamid. " id: " .$row['id'];
    }
}
else
{
	echo "Error with updating: listing not found";
}
$conn->close();
?>