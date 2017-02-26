<?php
require 'dbinfo.php';

session_start();
$perms = $_SESSION['user_view_perms'];
header('Content-Type: application/json');

if ($_SESSION['user_id'] <= 0 ) {
die('{"msg":"error"}');
}

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
}

// user has no rights to edit/create a todo under this category
if (!$_SESSION['user_is_admin'] && !in_array(intval($_POST['tag_id']), $accesses)) {
    die('{"msg":"error"}');
}

// just creating a new one
if (intval($_POST['id']) < 1) {
    $sql = "INSERT INTO `todo_items` (`name`, `desc`, `tag_id`, `state`, `encrypted`, `creator`, `duedate`) VALUES ('" .
        mysqli_real_escape_string($conn, $_POST['name']) . "', '" . mysqli_real_escape_string($conn, $_POST['desc']) . "', '".
        intval($_POST['tag_id']) ."', '". intval($_POST['state']) ."', 0, '" . $_SESSION['user_id'] . "', '" .
        date("Y-m-d H:i:s", strtotime($_POST["duedate"])) . "');";
    $result = $conn->query($sql);
    $conn->close();
    die('{"msg":"ok"}');
}

// get the old tag id... if the user is switching it, they need edit perms for both.
// user is admin, it's ok.
if ($_SESSION['user_is_admin']) {}
else {
    $sqlSelect = "SELECT tag_id FROM todo_items WHERE id = " . intval($_POST['id']);
    $result = $conn->query(sqlSelect);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if (in_array(intval($row['tag_id']), $accesses)) {
                break; // user is ok. they have edit perms for the old tag
            }
        }
        $conn->close();
        die('{"msg":"error"}');
    } else {
        $conn->close();
        die('{"msg":"error"}');
    }
}

// expecting input:
// name: name
// desc: desc
// tag_id: tag id
// state: state
// duedate: due date

$sqlUpdate = "UPDATE  `todo_items` SET  `name` =  '" . mysqli_real_escape_string($conn, $_POST['name']) .
             "', `desc` =  '" . mysqli_real_escape_string($conn, $_POST['desc']) . "', `tag_id`='" .
             intval($_POST['tag_id']) . "', `state`='".intval($_POST['state']) . "', `duedate` = '" .
             date("Y-m-d H:i:s", strtotime($_POST["duedate"])) . "' WHERE  `id` = " . intval($_POST['id']);

$result2 = $conn->query($sqlUpdate);
$conn->close();
die('{"msg":"ok"}');
?>
{"msg":"error"}