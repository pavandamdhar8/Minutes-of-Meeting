<?php  
$sName = "localhost:3307"; // Database server and port
$uName = "root";           // Database username
$pass  = "";               // Database password (empty in XAMPP default)
$db_name = "task_management_db"; // Database name

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$sName;dbname=$db_name;charset=utf8", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { // Corrected spelling
    die("Connection failed: " . $e->getMessage());
}
