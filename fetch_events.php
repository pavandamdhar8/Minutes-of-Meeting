<?php
session_start();
require 'DB_connection.php'; // Ensure this file initializes a PDO connection

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_SESSION['username'];
$events = [];

try {
    // Fetch User Info (Check if Admin)
    $userQuery = "SELECT id, role FROM users WHERE username = :username";
    $stmt = $conn->prepare($userQuery);
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userRow) {
        echo json_encode(["error" => "User not found"]);
        exit();
    }

    $userId = $userRow['id'];
    $isAdmin = ($userRow['role'] === 'admin'); // Check if user is an admin

    // 🟢 Fetch Meetings
    if ($isAdmin) {
        // Admin: See all meetings
        $meetingsQuery = "SELECT id, title, date_time AS start, agenda AS description FROM meetings";
    } else {
        // Regular User: See only assigned meetings
        $meetingsQuery = "SELECT id, title, date_time AS start, agenda AS description 
                          FROM meetings WHERE FIND_IN_SET(:userId, assigned_users) > 0";
    }

    $stmt = $conn->prepare($meetingsQuery);

    if (!$isAdmin) { // Bind userId only for regular users
        $stmt->bindValue(":userId", $userId, PDO::PARAM_STR);
    }

    $stmt->execute();
    $meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($meetings as $row) {
        $row['color'] = "#007bff"; // Blue for meetings
        $row['textColor'] = "white";
        $row['type'] = "Meeting";
        $events[] = $row;
    }

    // 🟢 Fetch Tasks
    if ($isAdmin) {
        // Admin: See all tasks
        $tasksQuery = "SELECT id, title, due_date AS start, description FROM tasks";
    } else {
        // Regular User: See only assigned tasks
        $tasksQuery = "SELECT id, title, due_date AS start, description FROM tasks WHERE assigned_to = :userId";
    }

    $stmt = $conn->prepare($tasksQuery);

    if (!$isAdmin) { // Bind userId only for regular users
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
    }

    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tasks as $row) {
        $row['color'] = "#28a745"; // Green for tasks
        $row['textColor'] = "white";
        $row['type'] = "Task";
        $events[] = $row;
    }

    // Return JSON data
    header('Content-Type: application/json');
    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
