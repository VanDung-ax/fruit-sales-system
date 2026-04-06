<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "appbantraicay";

$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
