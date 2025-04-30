<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Notification.php";

    // Fetch notifications for the logged-in user
    $notifications = get_all_my_notifications($conn, $_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .section-1 {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
        }

        .success {
            color: green;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .main-table th, .main-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .main-table th {
            background: #007bff;
            color: white;
            font-weight: bold;
        }

        .main-table tr:nth-child(even) {
            background: #f2f2f2;
        }

        .no-notifications {
            font-size: 18px;
            color: #555;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    
    <div class="body">
        <?php include "inc/nav.php"; ?>

        <section class="section-1">
            <h4 class="title">📢 All Notifications</h4>

            <?php if (isset($_GET['success'])) { ?>
                <div class="success" role="alert">
                    ✅ <?= stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>

            <?php if (!empty($notifications)) { ?>
                <table class="main-table">
                    <tr>
                        <th>#</th>
                        <th>Message</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                    <?php $i = 0; foreach ($notifications as $notification) { ?>
                        <tr>
                            <td><?= ++$i ?></td>
                            <td><?= htmlspecialchars($notification['message']); ?></td>
                            <td><?= htmlspecialchars($notification['type']); ?></td>
                            <td><?= htmlspecialchars($notification['date']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <div class="no-notifications">🚀 You have no new notifications.</div>
            <?php } ?>
        </section>
    </div>

    <script type="text/javascript">
        // Highlight the "Notifications" tab in the sidebar
        let activeTab = document.querySelector("#navList li a[href='notifications.php']");
        if (activeTab) {
            activeTab.parentElement.classList.add("active");
        }
    </script>

</body>
</html>

<?php 
} else { 
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
} 
?>
