<?php

require 'dbinfo.php';


session_start();
$perms = $_SESSION['user_view_perms'];
header('Content-Type: application/json');


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die('{"msg":"Connection failed: ' . $conn->connect_error . '"}');
} 

$accesses = array();

if (!$_SESSION['user_is_admin']) {
    // first see what tags this user has access to
    if ($_SESSION['user_id'] <= 0) {
        $_SESSION['user_id'] = 0;
    }
    
    // look up userid in tags
    $sql = "SELECT tag_id FROM todo_perms WHERE user_id in (" . $_SESSION['user_id'] . ", 0)";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $accesses[] = $row['tag_id'];
        }
    } else {
        echo "[]";
        $conn->close();
        die();
    }
    $sql = "SELECT * FROM todo_tags WHERE id in (" . implode(",", $accesses) . ")";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT * FROM todo_tags";
    $result = $conn->query($sql);
}

echo "[\n";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "\t{\"id\":" . $row['id'] . ", \"name\":" . json_encode($row["name"]) . "},\n";
    }
}

echo "\t{}\n]";
$conn->close();
?>
