<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - MOM Tracker</title>

    <!-- FullCalendar & Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* General Page Styling */
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar Spacing */
        .content-container {
            margin-top: 40px; /* Ensures navbar does not overlap */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
        }

        /* Calendar Container */
        .calendar-container {
            width: 90%;
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* FullCalendar Customization */
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: bold;
        }

        .fc-button {
            background-color: #007bff !important;
            border: none !important;
            color: white !important;
        }

        .fc-button:hover {
            background-color: #0056b3 !important;
        }

        /* Responsive Layout */
        @media (max-width: 768px) {
            .calendar-container {
                width: 95%;
            }
        }
    </style>
</head>
<body>

<!-- Include navigation -->
<?php include 'inc/nav.php'; ?>
<?php include 'inc/header.php'; ?>

<!-- Main Content -->
<div class="content-container">
    <div class="calendar-container">
        <h3 class="text-center mb-3"><i class="fa fa-calendar"></i> Meeting & Task Calendar</h3>
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar & JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: "auto",
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: 'fetch_events.php',
        eventClick: function(info) {
            alert('Event: ' + info.event.title + '\nDate: ' + info.event.start.toLocaleString());
        },
        eventColor: '#007bff',
        eventTextColor: 'white'
    });

    calendar.render();
});
</script>
<script type="text/javascript">
	var active = document.querySelector("#navList li:nth-child(6)");
	active.classList.add("active");
</script>

</body>
</html>
