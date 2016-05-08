<? 
require_once 'steamauth/steamauth.php'; 
require_once "connect.php";
require_once 'steamapi.php';

$id = $_POST['id'];


$sql = "SELECT * FROM trades WHERE id='" .$id. "' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) 
{
	$row = $result->fetch_assoc();
	
	$sql = "DELETE FROM trades WHERE id=" .$id;
	
	if ($conn->query($sql) === TRUE) 
	{
        echo "Success";
    } 
	else 
	{
        echo "Failed <br>
		" . $conn->error;
    }
}
else
{
	echo "Error removing: listing not found";
}
$conn->close();
?>