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

// check if user is admin
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
        echo "[{}]";
        $conn->close();
        die();
    }

    // now actually look
    $sql = "SELECT id, name, tag_id, state, encrypted, creator, duedate FROM todo_items WHERE tag_id in (".implode(",", $accesses).")";
} else {
    $sql = "SELECT id, name, tag_id, state, encrypted, creator, duedate FROM todo_items"; // no restrictions
}
$result = $conn->query($sql);

echo '['."\n";
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "\t".'{"id":' . $row['id']. ', "name":' . json_encode($row["name"]) . ', "state":' . $row["state"]. ', "encrypted":' . $row['encrypted'] . ', "tag_id":' . $row['tag_id'] . ', "duedate":"' . $row['duedate'] . '"' . "},\n";
    }
}
echo "\t{}\n]";
$conn->close();
?>
