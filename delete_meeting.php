<?php
session_start();
include 'DB_connection.php';

// Check if admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: meetings.php");
    exit();
}

if (isset($_GET['id'])) {
    $meeting_id = $_GET['id'];

    $delete_sql = "DELETE FROM meetings WHERE id = :id";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bindParam(':id', $meeting_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: meetings.php");
        exit();
    } else {
        echo "Error deleting meeting!";
    }
} else {
    echo "Invalid request!";
}
?>
