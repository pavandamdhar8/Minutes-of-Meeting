<?php
session_start();
require_once 'DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meeting_id = $_POST['meeting_id'];
    $attended_users = $_POST['attended_users'] ?? [];

    if (!$meeting_id) {
        die("Meeting ID is required.");
    }

    try {
        // Delete existing records
        $stmt = $conn->prepare("DELETE FROM attendance WHERE meeting_id = ?");
        $stmt->execute([$meeting_id]);

        // Insert new records
        $insert = $conn->prepare("INSERT INTO attendance (meeting_id, username, attended) VALUES (?, ?, 1)");

        foreach ($attended_users as $username) {
            $insert->execute([$meeting_id, $username]);
        }

        $_SESSION['message'] = "Attendance saved successfully.";
        header("Location: meetings.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
