<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "gym_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "";
}
?>
