<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    include "app/Model/Notification.php"; 

    $notification_count = count_notification($conn, $_SESSION['id']); 

    if ($_SESSION['role'] == "admin") {
        $todaydue_task = count_tasks_due_today($conn);
        $overdue_task = count_tasks_overdue($conn);
        $nodeadline_task = count_tasks_NoDeadline($conn);
        $num_task = count_tasks($conn);
        $num_users = count_users($conn);
        $pending = count_pending_tasks($conn);
        $in_progress = count_in_progress_tasks($conn);
        $completed = count_completed_tasks($conn);
    } else {
        $num_my_task = count_my_tasks($conn, $_SESSION['id']);
        $overdue_task = count_my_tasks_overdue($conn, $_SESSION['id']);
        $nodeadline_task = count_my_tasks_NoDeadline($conn, $_SESSION['id']);
        $pending = count_my_pending_tasks($conn, $_SESSION['id']);
        $in_progress = count_my_in_progress_tasks($conn, $_SESSION['id']);
        $completed = count_my_completed_tasks($conn, $_SESSION['id']);

        // Fetch upcoming tasks due within 2 days
        $sql = "SELECT title, due_date FROM tasks WHERE assigned_to = ? AND due_date <= DATE_ADD(NOW(), INTERVAL 2 DAY)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['id']]);
        $tasks_due = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['task_alerts'] = [];
        foreach ($tasks_due as $task) {
            $_SESSION['task_alerts'][] = "Task '{$task['title']}' is due on {$task['due_date']}.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: #f4f6f9;
            margin: 0;
        }

        .side-bar {
            width: 260px;
            height: 100vh;
            background: #1a1a2e;
            color: white;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .side-bar ul {
            padding: 0;
            list-style: none;
        }

        .side-bar ul li {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .side-bar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: 0.3s;
            font-size: 16px;
        }

        .side-bar ul li a i {
            margin-right: 12px;
            font-size: 18px;
        }

        .side-bar ul li:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .body-content {
            margin-left: 260px;
            padding: 20px;
            flex-grow: 1;
        }

        .alert-box {
            background:rgb(224, 41, 41);
            color: #333;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 80%;
            position: relative;
            left: 0;
            font-weight: bold;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .dashboard-item {
            background: linear-gradient(135deg, rgb(37, 75, 145), #2a5298);
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            color: white;
            transition: 0.3s;
        }

        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .dashboard-item i {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .dashboard-item span {
            font-size: 18px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .side-bar {
                width: 220px;
            }
            .body-content {
                margin-left: 220px;
            }
        }
    </style>
</head>
<body>

    <?php include 'inc/header.php'; ?>

    <div class="side-bar">
        <?php include "inc/nav.php"; ?>
    </div>

    <div class="body-content">
        <?php if ($_SESSION['role'] == "employee" && isset($_SESSION['task_alerts']) && count($_SESSION['task_alerts']) > 0) { ?>
            <div class="alert-box">
                <?php foreach ($_SESSION['task_alerts'] as $alert) {
                    echo "<p>$alert</p>";
                } ?>
            </div>
            <?php unset($_SESSION['task_alerts']); ?>
        <?php } ?>

        <section class="section-1">
            <?php if ($_SESSION['role'] == "admin") { ?>
                <div class="dashboard">
                    <div class="dashboard-item">
                        <i class="fa fa-users"></i>
                        <span><?=$num_users?> Employees</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-tasks"></i>
                        <span><?=$num_task?> All Tasks</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-window-close-o"></i>
                        <span><?=$overdue_task?> Overdue</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-clock-o"></i>
                        <span><?=$nodeadline_task?> No Deadline</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span><?=$todaydue_task?> Due Today</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-bell"></i>
                        <span><?=$notification_count?> Notifications</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-square-o"></i>
                        <span><?=$pending?> Pending</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-spinner"></i>
                        <span><?=$in_progress?> In Progress</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-check-square-o"></i>
                        <span><?=$completed?> Completed</span>
                    </div>
                </div>
            <?php }else{ ?>
                <div class="dashboard">
                    <div class="dashboard-item">
                        <i class="fa fa-tasks"></i>
                        <span><?=$num_my_task?> My Tasks</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-window-close-o"></i>
                        <span><?=$overdue_task?> Overdue</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-clock-o"></i>
                        <span><?=$nodeadline_task?> No Deadline</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-bell"></i>
                        <span><?=$notification_count?> Notifications</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-square-o"></i>
                        <span><?=$pending?> Pending</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-spinner"></i>
                        <span><?=$in_progress?> In Progress</span>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-check-square-o"></i>
                        <span><?=$completed?> Completed</span>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>

<script>
    document.querySelector("#navList li:nth-child(1)").classList.add("active");
</script>

</body>
</html>
<?php } else { 
   header("Location: login.php?error=First login");
   exit();
} ?>
