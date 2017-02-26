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

$sql = "";
if (!$_SESSION['user_is_admin']) {
    // first see what tags this user has access to
    if ($_SESSION['user_id'] <= 0) {
        $_SESSION['user_id'] = 0;
    }
    
    // look up userid in tags
    $sql = "SELECT tag_id, access_level FROM todo_perms WHERE user_id in (" . $_SESSION['user_id'] . ", 0)";
    $result = $conn->query($sql);
    $accesses = array();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['access_level'] > 0) {
                $accesses[] = $row['tag_id'];
            }
        }
    } else {
        $conn->close();
        die('{"msg":"error"}');
    }
    $sql = "DELETE FROM todo_items WHERE id = " . intval($_POST['id']) . " AND tag_id in (".implode(",", $accesses).")";
} else {
    // admin bypass
    $sql = "DELETE FROM todo_items WHERE id = " . intval($_POST['id']);
}

if (!$_SESSION['user_is_admin'] && count($accesses) == 0) {
    die('{"msg":"error"}');
}

// delete where the id is right, but also where the user can access.
$result = $conn->query($sql);
die('{"msg":"ok"}');
$conn->close();
?>
{"msg":"error"}