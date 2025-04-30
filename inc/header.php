<header class="header">
    <h2 class="u-name"> MOM <b>Tracker</b>
        <label for="checkbox">
            <i id="navbtn" class="fa fa-bars"></i>
        </label>
    </h2>
    <span class="notification" id="notificationBtn">
        <i class="fa fa-bell"></i>
        <span id="notificationNum" class="notification-count"></span>
    </span>
</header>

<div class="notification-bar" id="notificationBar">
    <ul id="notifications"></ul>
</div>

<style>
    /* General Styles */
    body {
        background: #f8f9fa;
        font-family: Arial, sans-serif;
        padding-top: 70px; /* Prevents content from being hidden under the fixed header */
    }
    

    /* Fixed Header */
    .header {
        background: #1E3A8A; /* Deep Blue */
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #3B82F6; /* Lighter Blue */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: white;
        position: fixed; /* Keeps header in place */
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000; /* Ensures it stays above everything */
    }

    .u-name {
        font-size: 1.6em;
        font-weight: bold;
    }

    .u-name b {
        color: #60A5FA; /* Light Blue */
    }

    /* Notification Icon */
    .notification {
        position: relative;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .notification i {
        font-size: 1.5em;
        color: white;
        transition: color 0.3s, transform 0.3s;
    }

    .notification:hover i {
        color: #3B82F6; /* Bright Blue */
        transform: scale(1.1);
    }

    .notification-count {
        background: red;
        color: white;
        font-size: 0.8em;
        border-radius: 50%;
        padding: 3px 6px;
        position: absolute;
        top: -5px;
        right: -8px;
    }

    /* Notification Dropdown */
    .notification-bar {
        display: none;
        position: absolute;
        right: 20px;
        top: 60px;
        width: 280px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        z-index: 1000;
    }

    .notification-bar ul {
        list-style: none;
        padding: 10px;
        margin: 0;
    }

    .notification-bar ul li {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        transition: background 0.3s;
    }

    .notification-bar ul li:hover {
        background: #f1f1f1;
    }

    /* Responsive Header */
    @media (max-width: 768px) {
        .header {
            padding: 12px;
        }

        .u-name {
            font-size: 1.3em;
        }

        .notification i {
            font-size: 1.3em;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function refreshNotifications() {
            $("#notificationNum").load("app/notification-count.php");
            $("#notifications").load("app/notification.php");
        }

        $("#notificationBtn").click(function(event) {
            $("#notificationBar").fadeToggle();
            event.stopPropagation();
        });

        $(document).click(function() {
            $("#notificationBar").fadeOut();
        });

        setInterval(refreshNotifications, 10000);
    });
</script>
