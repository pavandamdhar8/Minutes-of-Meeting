<?php 
session_start();
include "../DB_connection.php"; 
require '../vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function validate_input($data) {
    return trim(stripslashes(htmlspecialchars($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_name']) && isset($_POST['password'])) {

    $user_name = validate_input($_POST['user_name']);
    $password = validate_input($_POST['password']);

    if (empty($user_name)) {
        header("Location: ../login.php?error=" . urlencode("User name is required"));
        exit();
    } elseif (empty($password)) {
        header("Location: ../login.php?error=" . urlencode("Password is required"));
        exit();
    }

    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
        $_SESSION['lockout_time'] = 0;
    }

    if ($_SESSION['failed_attempts'] >= 5) {
        $lockout_duration = 10; // 5 minutes
        if (time() - $_SESSION['lockout_time'] < $lockout_duration) {
            header("Location: ../login.php?error=" . urlencode("Too many failed attempts. Try again later."));
            exit();
        } else {
            $_SESSION['failed_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
        }
    }

    // Check user credentials
    $sql = "SELECT id, username, password, role, email FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['role'] = $user['role'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['failed_attempts'] = 0;

        // If the user is an employee, check for due tasks
        if ($_SESSION['role'] == 'employee') {
            $employee_id = $_SESSION['id'];
            $sql = "SELECT title, due_date FROM tasks WHERE assigned_to = ? AND due_date <= DATE_ADD(NOW(), INTERVAL 2 DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$employee_id]);
            $tasks_due = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($tasks_due) {
                $_SESSION['task_alerts'] = [];
                foreach ($tasks_due as $task) {
                    $title = $task['title'];
                    $due_date = $task['due_date'];
                    $_SESSION['task_alerts'][] = "Task '$title' is due on $due_date.";

                    // Send email notification
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'mmcoepavan@gmail.com';
                        $mail->Password = 'pksq dlpg nynj bwzx';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        $employee_email = $_SESSION['email']; // Employee's email

                        // Sender and Recipient
                        $mail->setFrom('mmcoepavan@gmail.com', 'MOM Tracker');
                        $mail->addAddress($employee_email);

                        // Email Content
                        $mail->isHTML(true);
                        $mail->Subject = "Task Reminder: $title is Due Soon!";
                        $mail->Body = "
                            <h3>Dear Employee,</h3>
                            <p>Your assigned task <strong>$title</strong> is due on <strong>$due_date</strong>.</p>
                            <p>Please ensure timely completion.</p>
                            <br>
                            <p>Best Regards,<br>MOM Tracker Team</p>
                        ";

                        $mail->send();
                    } catch (Exception $e) {
                        // Log email errors if necessary
                    }
                }
            }
        }

        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['failed_attempts']++;
        if ($_SESSION['failed_attempts'] >= 5) {
            $_SESSION['lockout_time'] = time();
        }
        header("Location: ../login.php?error=" . urlencode("Invalid username or password"));
        exit();
    }
} else {
    header("Location: ../login.php?error=" . urlencode("Unknown error occurred"));
    exit();
}
?>
