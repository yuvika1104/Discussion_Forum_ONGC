<?php
$servername = "localhost";
$username = "root";
$password = "";              // Default password in XAMPP is empty
$dbname = "discussion_forum"; 

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected to MySQL successfully<br>";

// Creating database if it does not exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' created or already exists<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Step 3: Select the database
$conn->select_db($dbname);

//  Create tables if they do not exist
$sql = "
ALTER TABLE user 
ADD COLUMN role TINYINT NOT NULL DEFAULT 0 ;
";

if ($conn->query($sql) === TRUE) {
    echo "Table created or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Done
$conn->close();
?>
