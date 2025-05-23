<?php
// Database connection parameters
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'it_issues');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If you reach here, the connection is successful.
// You can include this file in other PHP scripts to use the $conn variable.
// For example, to close the connection later: $conn->close();
?>
