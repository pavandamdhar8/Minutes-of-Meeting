<?php 
session_start();
require '../vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    if (
        isset($_POST['title']) && 
        isset($_POST['description']) && 
        isset($_POST['assigned_to']) && 
        isset($_POST['due_date']) && 
        $_SESSION['role'] == 'admin'
    ) {
        
        include "../DB_connection.php";
        include "Model/Task.php";
        include "Model/Notification.php";

        function validate_input($data) {
            return htmlspecialchars(stripslashes(trim($data)));
        }

        $title = validate_input($_POST['title']);
        $description = validate_input($_POST['description']);
        $assigned_to = validate_input($_POST['assigned_to']);
        $due_date = validate_input($_POST['due_date']);
        $admin_id = $_SESSION['id'];

        if (empty($title)) {
            header("Location: ../create_task.php?error=Title is required");
            exit();
        } else if (empty($description)) {
            header("Location: ../create_task.php?error=Description is required");
            exit();
        } else if ($assigned_to == 0) {
            header("Location: ../create_task.php?error=Select a user");
            exit();
        } else {
            // Insert the task
            $data = [$title, $description, $assigned_to, $due_date];
            insert_task($conn, $data);

            // Send notification to the employee
            $notif_emp = ["Task '$title' has been assigned to you. Please review and start working on it", $assigned_to, 'New Task Assigned'];
            insert_notification($conn, $notif_emp);

            // Send notification to the admin
            $notif_admin = ["You assigned '$title' to Employee ID: $assigned_to", $admin_id, 'Task Assigned'];
            insert_notification($conn, $notif_admin);

            // Fetch employee email
            $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$assigned_to]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            $employee_email = $employee['email'];

            // Send Email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mmcoepavan@gmail.com'; 
                $mail->Password = 'pksq dlpg nynj bwzx';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                
                // Sender and Recipient
                $mail->setFrom('mmcoepavan@gmail.com', 'MOM Tracker');
                $mail->addAddress($employee_email);
                
                // Email Content
                $mail->isHTML(true);
                $mail->Subject = "New Task Assigned: $title";
                $mail->Body = "
                    <h3>Dear Employee,</h3>
                    <p>You have been assigned a new task: <strong>$title</strong>.</p>
                    <p><strong>Description:</strong> $description</p>
                    <p><strong>Due Date:</strong> $due_date</p>
                    <p>Please check your dashboard for more details.</p>
                    <br>
                    <p>Best Regards,<br>MOM Tracker Team</p>
                ";

                $mail->send();
                header("Location: ../create_task.php?success=Task created and email sent");
                exit();
            } catch (Exception $e) {
                header("Location: ../create_task.php?error=Task created but email failed: " . $mail->ErrorInfo);
                exit();
            }
        }
    } else {
        header("Location: ../create_task.php?error=Unknown error occurred");
        exit();
    }

} else { 
    header("Location: ../login.php?error=First login");
    exit();
} 
?>
