<?php
$crc = hash('crc32', $_GET['username']);
echo "hash: " . hash('sha512', $_GET['password'].", also, ".$crc). "\n";
echo "salt: " . $crc;
?>
