<? 
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

$owner = $_POST['steamid'];
$uniqueid = $_POST['uniq'];
$price = $_POST['price'];

$steamid = str_replace("a", "", $owner);


$sql = "SELECT * FROM trades WHERE uniqueid='" .$uniqueid. "' AND owner='" .$steamid. "' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) 
{
	$row = $result->fetch_assoc();
	
	$sql="UPDATE trades SET price='" .$price. "' WHERE id='" .(int)$row['id']. "'";
	
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