<?php
session_start();
$host = "localhost";
$user = "root";
$password = ""; //h5wZeYOT/Vp[.dDn
$dbname = "ewip";

try {
    // Attempt to create the MySQLi connection
    $conn = new mysqli($host, $user, $password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
