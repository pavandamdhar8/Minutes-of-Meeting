<?php 
session_start();
include 'DB_connection.php'; 

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get user role
$role = $_SESSION['role'];
$username = $_SESSION['username'];

if ($role == "admin") {
    $sql = "SELECT * FROM meetings ORDER BY date_time DESC";
    $stmt = $conn->query($sql);
} else {
    $sql = "SELECT * FROM meetings WHERE FIND_IN_SET(:username, assigned_users) AND notes_shared = 1 ORDER BY date_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
}

$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meetings - MOM Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
    <?php include 'inc/nav.php'; ?>
    <?php include "inc/header.php"; ?>

    <div class="main-content">
        <h2><i class="fa fa-handshake"></i> Meetings</h2>

        <?php if ($role == "admin") { ?>
            <a href="create_meeting.php" class="btn btn-primary">
                <i class="fa fa-plus"></i> Create Meeting
            </a>
        <?php } ?>

        <table class="meeting-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Date & Time</th>
                    <th>Agenda</th>
                    <th>Members</th>
                    <th>Notes</th>
                    <th>Actions</th>
                    <th>Attendance</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 1;
                foreach ($meetings as $row) { ?>
                    <tr>
                        <td><?= $count++; ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($row['date_time'])); ?></td>
                        <td><?= htmlspecialchars($row['agenda']); ?></td>
                        <td>
                            <?php 
                            $users = explode(',', $row['assigned_users']);
                            foreach ($users as $user) {
                                $user = trim($user);
                                echo "<a href='create_task.php?user=" . urlencode($user) . "' class='assigned-user-badge'>" . htmlspecialchars($user) . "</a> ";
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if ($role == "admin" || $row['notes_shared'] == 1) { 
                                echo (!empty($row['notes'])) ? nl2br(htmlspecialchars($row['notes'])) : "<span style='color: grey;'>No notes available</span>";
                            } else {
                                echo "<span style='color: grey;'>Notes not shared</span>";
                            }
                            ?>

                            <?php if (!empty($row['notes_file']) && file_exists($row['notes_file']) && ($role == "admin" || $row['notes_shared'] == 1)): ?>
                                <br><strong>Attachment:</strong> 
                                <?php 
                                $file_path = $row['notes_file'];
                                $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                if (in_array($file_ext, ["jpg", "png", "gif"])) {
                                    echo "<br><a href='{$file_path}' target='_blank'><img src='{$file_path}' width='100' alt='Note Image'></a>";
                                } else {
                                    echo "<br><a href='{$file_path}' target='_blank'>View PDF</a>";
                                }
                                ?>
                            <?php endif; ?>

                            <?php if ($role == "admin") { ?>
                                <br>
                                <button class="btn btn-info" onclick="openNotesModal(<?= $row['id']; ?>, `<?= htmlspecialchars($row['notes']); ?>`, <?= $row['notes_shared']; ?>)">
                                    <i class="fa fa-pencil"></i> Edit Notes
                                </button>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($role == "admin") { ?>
                                <a href="edit_meeting.php?id=<?= $row['id']; ?>" class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="delete_meeting.php?id=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this meeting?');">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            <?php } ?>

                        </td>
                        <td>
                        <!-- Add Attendance Button -->
<button class="btn btn-primary" onclick="openAttendanceModal('<?= $row['id']; ?>', '<?= $row['assigned_users']; ?>')">
    <i class="fa fa-check-square-o"></i> Mark Attendance
</button>
</td>

                        <td>
                     <a href="generate_report.php?id=<?= $row['id']; ?>" class="btn btn-success">
                              <i class="fa fa-file-pdf-o"></i> Generate Report
    </a>
</td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Notes Modal -->
    <div id="notesModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeNotesModal()">&times;</span>
            <h3><i class="fa fa-pencil"></i> Meeting Notes</h3>

            <form id="notesForm" method="POST" action="update_notes.php" enctype="multipart/form-data">
                <input type="hidden" name="meeting_id" id="meetingId">
                
                <label><strong>Notes:</strong></label>
                <textarea name="notes" id="meetingNotes" rows="5" required></textarea>

                <label><strong>Share Notes:</strong></label>
                <select name="notes_shared" id="notes_shared">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>

                <label><strong>Upload File (PDF/Image):</strong></label>
                <input type="file" name="notes_file" id="notes_file" accept=".pdf,.jpg,.png,.gif">

                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Notes</button>
            </form>
        </div>
    </div>
    <!-- Attendance Modal -->
<div id="attendanceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAttendanceModal()">&times;</span>
        <h3><i class="fa fa-check-square-o"></i> Mark Attendance</h3>

        <form action="save_attendance.php" method="POST">
            <input type="hidden" name="meeting_id" id="attendanceMeetingId">
            <div id="userCheckboxList"></div>

            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Attendance</button>
        </form>
    </div>
</div>
<script>
    function openAttendanceModal(meetingId, assignedUsers) {
        document.getElementById('attendanceMeetingId').value = meetingId;

        let userList = assignedUsers.split(',');
        let html = '';
        userList.forEach(user => {
            let trimmedUser = user.trim();
            html += `<label><input type="checkbox" name="attended_users[]" value="${trimmedUser}"> ${trimmedUser}</label><br>`;
        });

        document.getElementById('userCheckboxList').innerHTML = html;
        document.getElementById('attendanceModal').style.display = 'block';
    }

    function closeAttendanceModal() {
        document.getElementById('attendanceModal').style.display = 'none';
    }
</script>


    <script>
        function openNotesModal(meetingId, notes, notesShared) {
            document.getElementById('meetingId').value = meetingId;
            document.getElementById('meetingNotes').value = notes;
            document.getElementById('notes_shared').value = notesShared;
            document.getElementById('notesModal').style.display = 'block';
        }

        function closeNotesModal() {
            document.getElementById('notesModal').style.display = 'none';
        }
    </script>

    <style>
        .btn-success { background: #28a745; padding: 10px 15px; font-size: 16px; }
        .btn-info { background: #3498db; padding: 8px 12px; }
        .modal { display: none; position: fixed; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); }
        .modal-content { background: white; padding: 20px; border-radius: 8px; width: 40%; margin: auto; }
    </style>

    <script>
        document.getElementById("notes_shared").addEventListener("change", function() {
            let selected = this.value;
            console.log("Notes sharing status changed to:", selected);
        });
    </script>


    <style>
        .main-content {
            margin-left: 250px;
            padding: 10px 20px;
        }

        .assigned-user-badge {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 19px;
            font-size: 15px;
            margin: 2px;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px 0;
            border: none;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary { background: #4CAF50; }
        .btn-info { background: #3498db; }
        .btn-warning { background: #f39c12; }
        .btn-danger { background: #e74c3c; }

        .meeting-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .meeting-table th, .meeting-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .meeting-table th { background: #3A3A5E; color: white; }

        .modal {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
<script type="text/javascript">
	var active = document.querySelector("#navList li:nth-child(2)");
	active.classList.add("active");
</script>
</body>
</html>
