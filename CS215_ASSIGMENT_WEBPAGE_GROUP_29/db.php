<?php
// db.php
$db_host = "mysqlusr.cs.uregina.ca"; // Use the remote host from your bash command
$db_user = "tbz507";                 // Your username
$db_pwd = "opklnm9090";     // Use the password that worked in the terminal
$db_db = "tbz507-0";                 // Use the -0 database name

$charset = 'utf8mb4';
$attr = "mysql:host=$db_host;dbname=$db_db;charset=$charset";
$conn = new mysqli($db_host, $db_user, $db_pwd, $db_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>