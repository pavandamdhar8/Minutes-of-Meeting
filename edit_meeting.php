<?php 
session_start();
include 'DB_connection.php';

// Check if admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: meetings.php");
    exit();
}

// Fetch meeting details
if (isset($_GET['id'])) {
    $meeting_id = $_GET['id'];
    $sql = "SELECT * FROM meetings WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $meeting_id, PDO::PARAM_INT);
    $stmt->execute();
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meeting) {
        echo "Meeting not found!";
        exit();
    }
}

// Fetch all users for the checkbox list
$sql_users = "SELECT username FROM users";
$stmt_users = $conn->query($sql_users);
$all_users = $stmt_users->fetchAll(PDO::FETCH_COLUMN);

// Update meeting
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date_time = $_POST['date_time'];
    $agenda = $_POST['agenda'];
    $assigned_users = implode(",", $_POST['assigned_users']); // Convert array to string

    $update_sql = "UPDATE meetings SET title = :title, date_time = :date_time, agenda = :agenda, assigned_users = :assigned_users WHERE id = :id";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bindParam(':title', $title);
    $stmt_update->bindParam(':date_time', $date_time);
    $stmt_update->bindParam(':agenda', $agenda);
    $stmt_update->bindParam(':assigned_users', $assigned_users);
    $stmt_update->bindParam(':id', $meeting_id, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        header("Location: meetings.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating meeting!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meeting - MOM Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            overflow-x: hidden; /* Prevent horizontal scroll */
        }
        .card {
    max-width: 800px; /* Keeps the form from stretching too wide */
    margin: 20px auto 20px auto; /* 70px top margin, centered horizontally */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

    </style>
</head>
<body>

<!-- Navigation -->
<?php include 'inc/nav.php'; ?>
<?php include "inc/header.php" ?>

<div class="container-fluid mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3><i class="fa fa-edit"></i> Edit Meeting</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><strong>Title:</strong></label>
                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($meeting['title']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Date & Time:</strong></label>
                    <input type="datetime-local" class="form-control" name="date_time" value="<?= date('Y-m-d\TH:i', strtotime($meeting['date_time'])); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Agenda:</strong></label>
                    <textarea class="form-control" name="agenda" rows="3" required><?= htmlspecialchars($meeting['agenda']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Assign Users:</strong></label><br>
                    <div class="row">
                        <?php foreach ($all_users as $user) { ?>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="assigned_users[]" value="<?= $user; ?>" 
                                        <?= in_array($user, explode(',', $meeting['assigned_users'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label"><?= $user; ?></label>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="meetings.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
	var active = document.querySelector("#navList li:nth-child(2)");
	active.classList.add("active");
</script>

</body>
</html>
