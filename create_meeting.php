<?php 
session_start();
include 'DB_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error_message = "";
$admin_username = $_SESSION['username'];

// Fetch admin's email
$adminEmailQuery = "SELECT email FROM users WHERE username = ?";
$adminEmailStmt = $conn->prepare($adminEmailQuery);
$adminEmailStmt->execute([$admin_username]);
$adminEmail = $adminEmailStmt->fetchColumn(); // Get admin's email

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date_time = $_POST['date_time'];
    $agenda = $_POST['agenda'];
    $assigned_users = isset($_POST['assigned_users']) ? $_POST['assigned_users'] : [];

    // Ensure admin is always included
    if (!in_array($admin_username, $assigned_users)) {
        $assigned_users[] = $admin_username;
    }

    if (empty($assigned_users)) {
        $error_message = "Error: Please select at least one user.";
    } else {
        $assigned_users_str = implode(',', $assigned_users);

        // Insert into meetings table
        $sql = "INSERT INTO meetings (title, date_time, agenda, assigned_users, created_at) 
                VALUES (:title, :date_time, :agenda, :assigned_users, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':date_time', $date_time);
        $stmt->bindParam(':agenda', $agenda);
        $stmt->bindParam(':assigned_users', $assigned_users_str);

        if ($stmt->execute()) {
            // Fetch emails of assigned users
            $placeholders = rtrim(str_repeat('?,', count($assigned_users)), ',');
            $emailQuery = "SELECT email FROM users WHERE username IN ($placeholders)";
            $emailStmt = $conn->prepare($emailQuery);
            $emailStmt->execute($assigned_users);
            $emails = $emailStmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($emails)) {
                sendMeetingEmails($emails, $adminEmail, $title, $date_time, $agenda);
            }

            header("Location: meetings.php");
            exit();
        } else {
            $error_message = "Error: Could not create the meeting.";
        }
    }
}

// Function to send emails using PHPMailer
function sendMeetingEmails($emails, $adminEmail, $title, $date_time, $agenda) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mmcoepavan@gmail.com'; 
        $mail->Password = 'pksq dlpg nynj bwzx';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender
        $mail->setFrom($adminEmail, 'MOM Tracker');

        // Add Recipients
        foreach ($emails as $recipientEmail) {
            $mail->addAddress($recipientEmail);
        }

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Meeting Scheduled: $title";
        $mail->Body = "
            <html>
            <head>
                <title>New Meeting Scheduled</title>
            </head>
            <body>
                <h2>A new meeting has been scheduled.</h2>
                <p><strong>Title:</strong> $title</p>
                <p><strong>Date & Time:</strong> $date_time</p>
                <p><strong>Agenda:</strong> $agenda</p>
                <p>Please check your dashboard for more details.</p>
            </body>
            </html>
        ";

        // Send the email
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Meeting</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            overflow-x: hidden;
            background-color: #f4f4f4;
        }
        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -175px; /* Moves container slightly higher */
        }
        .form-container {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        .user-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .checkbox-label {
            background: #ddd;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .checkbox-label input {
            margin-right: 5px;
        }
        .disabled-checkbox {
            background: #ccc !important;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Navigation -->
    <?php include 'inc/nav.php'; ?>
    <?php include "inc/header.php"; ?>

    <!-- Centered Content (Moved slightly higher) -->
    <div class="content-wrapper">
        <div class="card form-container">
            <div class="card-header bg-primary text-white text-center">
                <h3><i class="fa fa-plus"></i> Create Meeting</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($error_message)) { ?>
                    <div class="alert alert-danger"><?= $error_message; ?></div>
                <?php } ?>

                <form method="POST" id="meetingForm">
                    <div class="mb-3">
                        <label class="form-label"><strong>Title:</strong></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Date & Time:</strong></label>
                        <input type="datetime-local" class="form-control" name="date_time" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Agenda:</strong></label>
                        <textarea class="form-control" name="agenda" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Add employee:</strong></label>
                        <div class="user-list">
                            <!-- Admin is always checked and disabled -->
                            <label class="checkbox-label disabled-checkbox">
                                <input type="checkbox" name="assigned_users[]" value="<?= $admin_username; ?>" checked disabled>
                                <?= $admin_username; ?> (Admin)
                            </label>

                            <?php 
                            // Fetch non-admin users from the database
                            $stmt = $conn->query("SELECT username FROM users WHERE username != '$admin_username'");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<label class='checkbox-label'>
                                        <input type='checkbox' name='assigned_users[]' value='{$row['username']}'>
                                        {$row['username']}
                                      </label>";
                            }
                            ?>
                        </div>
                        <div id="userError" class="text-danger mt-2" style="display: none;">Please select at least one user.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="meetings.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Create Meeting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("meetingForm").addEventListener("submit", function(event) {
    let checkboxes = document.querySelectorAll("input[name='assigned_users[]']:checked");
    if (checkboxes.length === 0) {
        document.getElementById("userError").style.display = "block";
        event.preventDefault(); // Prevent form submission
    } else {
        document.getElementById("userError").style.display = "none";
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

