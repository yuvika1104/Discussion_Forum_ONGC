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
CREATE TABLE IF NOT EXISTS user (
    cpf_no CHAR(5) PRIMARY KEY,                         
    email VARCHAR(255) NOT NULL UNIQUE,                 
    phone_number VARCHAR(15),                           
    name VARCHAR(100) NOT NULL,                         
    designation VARCHAR(100),                           
    department VARCHAR(100),                            
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    
    bio TEXT,                                          
    profile_photo_path VARCHAR(255),                   
    hashed_password VARCHAR(255) NOT NULL,              
    role TINYINT NOT NULL DEFAULT 0 ,                    
    active TINYINT NOT NULL DEFAULT 1                
)
";

if ($conn->query($sql) === TRUE) {
    echo "Table created or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Done
$conn->close();
?>
