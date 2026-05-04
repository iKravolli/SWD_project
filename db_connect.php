<?php
// Temporary db_connect.php for User Management testing
// Based on your signup.php connection pattern

$host = "localhost";
$username = "root";
$password = "";
$database = "workshopbooking";

$db_connection = mysqli_connect($host, $username, $password, $database);

if(!$db_connection)
{
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Note: Sultan will replace this with his final version
?>