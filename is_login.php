<?php
header('Content-Type: application/json');

session_start();
                if ($_SESSION['user_view_perms'] > 0) {
echo '{"status":0, "username":"'.$_SESSION['user_name'].  '", "uid":' . $_SESSION['user_id'] . ', "is_admin":' .$_SESSION['user_is_admin']. '}';
} else {
echo '{"status":-1}';
}
?>