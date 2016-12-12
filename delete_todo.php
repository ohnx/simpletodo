<?php
require 'dbinfo.php';


session_start();
$perms = $_SESSION['user_view_perms'];
header('Content-Type: application/json');

if ($_SESSION['user_id'] < 0 ) {
die('{"msg":"error"}');
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die('{"msg":"Connection failed: ' . $conn->connect_error . '"}');
} 

$sql = "SELECT security, creator FROM todo_items WHERE id = " . intval($_POST['id']);
$result = $conn->query($sql);

$sqlUpdate = "DELETE FROM `todo_items` WHERE  `id` = " . intval($_POST['id']);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if ($_SESSION['user_is_admin'] || (($perms & $row['security']) == $row['security']) && ($row['creator'] == $_SESSION['user_id'])) {
// user has perms to do this
$result2 = $conn->query($sqlUpdate);
$conn->close();
die('{"msg":"ok"}');
	}
    }
}
$conn->close();
?>
{"msg":"error"}