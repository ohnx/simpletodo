<?php
require 'dbinfo.php';

session_start();
$perms = $_SESSION['user_view_perms'];
header('Content-Type: application/json');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, name, state, security, encrypted FROM todo_items";
$result = $conn->query($sql);

echo '['."\n";
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if ($row['security'] == 0 || $_SESSION['user_is_admin'] || ($perms & $row['security']) == $row['security']) {
        echo "\t".'{"id":' . $row['id']. ', "name":"' . $row["name"]. '", "state":' . $row["state"]. ', "encrypted":' . $row['encrypted'] . "},\n";
	}
    }
}
echo "\t{}\n]";
$conn->close();
?>