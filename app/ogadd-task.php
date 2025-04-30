<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && isset($_POST['due_date']) && $_SESSION['role'] == 'admin') {
        
        include "../DB_connection.php";

        function validate_input($data) {
            return htmlspecialchars(stripslashes(trim($data)));
        }

        $title = validate_input($_POST['title']);
        $description = validate_input($_POST['description']);
        $assigned_to = validate_input($_POST['assigned_to']);
        $due_date = validate_input($_POST['due_date']);
        $admin_id = $_SESSION['id']; // Store admin ID

        if (empty($title)) {
            $em = "Title is required";
            header("Location: ../create_task.php?error=$em");
            exit();
        } else if (empty($description)) {
            $em = "Description is required";
            header("Location: ../create_task.php?error=$em");
            exit();
        } else if ($assigned_to == 0) {
            $em = "Select a user";
            header("Location: ../create_task.php?error=$em");
            exit();
        } else {
            include "Model/Task.php";
            include "Model/Notification.php";

            // Insert the task
            $data = [$title, $description, $assigned_to, $due_date];
            insert_task($conn, $data);

            // Send notification to the employee
            $notif_emp = ["Task '$title' has been assigned to you. Please review and start working on it", $assigned_to, 'New Task Assigned'];
            insert_notification($conn, $notif_emp);

            // Send notification to the admin
            $notif_admin = ["You assigned '$title' to Employee ID: $assigned_to", $admin_id, 'Task Assigned'];
            insert_notification($conn, $notif_admin);

            $em = "Task created successfully";
            header("Location: ../create_task.php?success=$em");
            exit();
        }
    } else {
        $em = "Unknown error occurred";
        header("Location: ../create_task.php?error=$em");
        exit();
    }

} else { 
    $em = "First login";
    header("Location: ../create_task.php?error=$em");
    exit();
} 
?>
