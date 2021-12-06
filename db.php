<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "kemuri";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  abort("Connection failed: " . $conn->connect_error, 500);
}