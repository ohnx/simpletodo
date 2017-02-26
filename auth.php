<?php
require 'dbinfo.php';

header('Content-Type: application/json');

$name = $_POST['username'];
$pass = $_POST['password'];
$mode = $_POST['mode'];


if(isset($mode)) {
                session_start();
                $_SESSION['user_id'] = 0;
                $_SESSION['user_view_perms'] = 0;
                $_SESSION['user_is_admin'] = 0;
                $_SESSION['user_name'] = "";
                die('{"status":1,"msg":"ok"}');
                session_end();
}

if( isset($name) || isset($pass) ) {
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = 'SELECT id,username,password,salt,admin FROM todo_users WHERE username = "' . $name .'"';
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
         if (hash('sha512', $pass . ", also, ".$row['salt']) === $row['password']) {
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_view_perms'] = $row['permissions'];
                $_SESSION['user_is_admin'] = $row['admin'];
                $_SESSION['user_name'] = $_POST['username'];
                die('{"status":1,"msg":"ok"}');
         }
    }
echo '{"msg":"Failed login"}';
} else {
echo '{"msg":"Failed login"}';
}
} else {
echo '{"msg":"Missing information"}';
}
?>