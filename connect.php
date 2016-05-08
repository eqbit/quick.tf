<?php;
    $servername = "localhost";
    $username = "quicktfDB";
    $password = "456456";
    $dbname = "quicktfDB";
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
} 
?>