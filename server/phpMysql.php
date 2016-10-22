<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbName = "globalhack";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";

createServiceProviderTable($conn);

function createServiceProviderTable($conn){
  $sql = "CREATE TABLE service_provider (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255),
    emailAddress VARCHAR(255),
    phone VARCHAR(255),
    bedCount INT(10) UNSIGNED
  )";

  if ($conn->query($sql) === TRUE) {
    printf("Table service_provider successfully created.\n");
  }else{
    printf("Failed to create table");
  }

}

?>
