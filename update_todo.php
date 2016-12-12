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

$security = (isset($_POST['public']) && ($_POST['public'] == 'true')) ? 0 : $perms;

if (intval($_POST['id']) < 1) {
$sql = "INSERT INTO `todo_items` (`name`, `desc`, `security`, `state`, `encrypted`, `creator`) VALUES ('" . mysqli_real_escape_string($conn, $_POST['name']) . "', '" . mysqli_real_escape_string($conn, $_POST['desc']) . "', '".$security ."', '". intval($_POST['state']) ."', 0, '" . $_SESSION['user_id'] . "');";
$result = $conn->query($sql);
$conn->close();
die('{"msg":"ok"}');
}

$sql = "SELECT security, creator FROM todo_items WHERE id = " . intval($_POST['id']);
$result = $conn->query($sql);

if (isset($_POST['update_security']) && $_POST['update_security']) {
$sqlUpdate = "UPDATE  `todo_items` SET  `name` =  '" . mysqli_real_escape_string($conn, $_POST['name']) . "', `desc` =  '" . mysqli_real_escape_string($conn, $_POST['desc']) . "', `security` = '" . $security . "', `state`='".intval($_POST['state'])."' WHERE  `id` = " . intval($_POST['id']);
} else {
$sqlUpdate = "UPDATE  `todo_items` SET  `name` =  '" . mysqli_real_escape_string($conn, $_POST['name']) . "', `desc` =  '" . mysqli_real_escape_string($conn, $_POST['desc']) . "', `state`='".intval($_POST['state'])."' WHERE  `id` = " . intval($_POST['id']);
}

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