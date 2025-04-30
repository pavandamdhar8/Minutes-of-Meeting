<?php
session_start();
include 'DB_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_id = $_POST['meeting_id'];
    $notes = $_POST['notes'];
    $notes_shared = isset($_POST['notes_shared']) ? 1 : 0; // ✅ Fixed: Capture share status
    $upload_dir = "uploads/";
    $file_path = "";

    // Ensure the upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle file upload
    if (!empty($_FILES['notes_file']['name'])) {
        $file_ext = strtolower(pathinfo($_FILES['notes_file']['name'], PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf"];

        if (in_array($file_ext, $allowed_types)) {
            // Generate unique file name
            $new_file_name = uniqid("note_", true) . "." . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            if (!move_uploaded_file($_FILES['notes_file']['tmp_name'], $file_path)) {
                echo "File upload failed!";
                exit();
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, GIF, and PDF allowed.";
            exit();
        }
    }

    // Update the database
    if ($file_path) {
        $sql = "UPDATE meetings SET notes = :notes, notes_file = :file_path, notes_shared = :notes_shared WHERE id = :meeting_id";
    } else {
        $sql = "UPDATE meetings SET notes = :notes, notes_shared = :notes_shared WHERE id = :meeting_id";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':notes_shared', $notes_shared);
    if ($file_path) {
        $stmt->bindParam(':file_path', $file_path);
    }
    $stmt->bindParam(':meeting_id', $meeting_id);

    if ($stmt->execute()) {
        header("Location: meetings.php");
        exit();
    } else {
        echo "Error updating notes!";
    }
} else {
    echo "Invalid request!";
}
?>
