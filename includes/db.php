<?php
$host = "localhost:3307";
$user = "root";
$pass = "";
$dbname = "workshop_db";

// establishing the connection to database
$conn = mysqli_connect($host, $user, $pass, $dbname);

// generic error handling
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>