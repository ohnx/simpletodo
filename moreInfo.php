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

$sql = "SELECT * FROM todo_items WHERE id = " . intval($_GET['id']);
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if ($row['security'] == 0 || $_SESSION['user_is_admin'] || ($perms & $row['security']) == $row['security']) {
        echo '{"id":' . $row['id']. ', "name":' . json_encode($row["name"]). ', "desc":' . json_encode($row['desc']) . ', "state":' . $row["state"]. ', "encrypted":' . $row['encrypted'] . ', "creator":' . $row['creator'] . "}\n";
	}
    }
}
$conn->close();
?>