<?php
require 'dbinfo.php';

session_start();
header('Content-Type: application/json');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "";
if (!$_SESSION['user_is_admin']) {
    // first see what tags this user has access to
    if ($_SESSION['user_id'] <= 0) {
        $_SESSION['user_id'] = 0;
    }
    
    // look up userid in tags
    $sql = "SELECT tag_id FROM todo_perms WHERE user_id in (" . $_SESSION['user_id'] . ", 0)";
    $result = $conn->query($sql);
    $accesses = array();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $accesses[] = $row['tag_id'];
        }
    } else {
        $conn->close();
        die(":(");
    }
    
    $sql = "SELECT * FROM todo_items WHERE id = " . intval($_GET['id']) . " AND tag_id in (".implode(",", $accesses).")";
} else {
    $sql = "SELECT * FROM todo_items WHERE id = " . intval($_GET['id']);
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo json_encode($row);
        //echo '{"id":' . $row['id']. ', "name":' . json_encode($row["name"]). ', "desc":' . json_encode($row['desc']) . ', "state":' . $row["state"]. ', "encrypted":' . $row['encrypted'] . ', "creator":' . $row['creator'] . "}\n";
    }
}
$conn->close();
?>