<!-- Header -->
<!-- Sidebar -->
<nav class="side-bar">
    <div class="user-p">
        <img src="img/user.png">
        <h4>@<?=$_SESSION['username']?></h4>
    </div>

    <?php if($_SESSION['role'] == "employee"){ ?>
        <ul id="navList">
            <li><a href="index.php"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>
            <li><a href="meetings.php"><i class="fa fa-handshake-o"></i> <span>My Meetings</span></a></li>
            <li><a href="my_task.php"><i class="fa fa-tasks"></i> <span>My Task</span></a></li>
            <li><a href="profile.php"><i class="fa fa-user"></i> <span>Profile</span></a></li>
            <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
            <li><a href="calendar.php"><i class="fa fa-calendar"></i> <span>Calendar</span></a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
        </ul>
    <?php } else { ?>
        <ul id="navList">
            <li><a href="index.php"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>
            <li><a href="meetings.php"><i class="fa fa-handshake-o"></i> <span>Meetings</span></a></li>
            <li><a href="create_task.php"><i class="fa fa-plus"></i> <span>Create Task</span></a></li>
            <li><a href="tasks.php"><i class="fa fa-tasks"></i> <span>All Tasks</span></a></li>
            <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
            <li><a href="calendar.php"><i class="fa fa-calendar"></i> <span>Calendar</span></a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
        </ul>
    <?php } ?>
</nav>

<!-- Main Content -->
<div class="main-content">
</div>

<style>
    /* Header Styling */
    .main-header {
        width: 100%;
        height: 60px;
        background: rgb(4, 4, 55); /* Darker blue */
        color: white;
        display: flex;
        align-items: center;
        padding-left: 20px;
        font-size: 1.5em;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    /* Sidebar Styling */
    .side-bar {
        width: 250px;
        height: calc(100vh - 60px); /* Leaves space for the header */
        background: rgb(6, 6, 72); /* Dark theme */
        position: fixed;
        top: 60px; /* Starts below the header */
        left: 0;
        color: white;
        box-shadow: 3px 0 10px rgba(14, 14, 14, 0.2);
        overflow-y: auto; /* Allows scrolling */
    }

    /* Sidebar User Profile */
    .user-p {
        text-align: center;
        padding: 20px 0;
    }

    .user-p img {
        width: 80px;
        border-radius: 50%;
        border: 3px solid rgb(104, 88, 212); /* Soft Purple */
    }

    .user-p h4 {
        margin-top: 10px;
        font-size: 1.2em;
        color: white;
    }

    /* Navigation List */
    #navList {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #navList li {
        width: 100%;
    }

    #navList li a {
        display: flex;
        align-items: center;
        padding: 8px 15px;
        color: white;
        text-decoration: none;
        font-size: 1em;
        transition: background 0.1s, padding-left 0.3s;
    }

    #navList li a i {
        margin-right: 10px;
        font-size: 1.2em;
    }

    /* Hover Effect */
    #navList li a:hover {
        background:rgb(94, 94, 146); /* Slightly brighter */
        padding-left: 25px;
    }

    /* Main Content - Fix Overlap Issue */
    .main-content {
        margin-left: 250px; /* Leaves space for sidebar */
        padding: 80px 20px 20px; /* Prevents overlap with header */
    }

    /* Responsive Sidebar */
    @media (max-width: 768px) {
        .side-bar {
            width: 200px;
        }

        .main-content {
            margin-left: 200px; /* Adjust for smaller sidebar */
        }

        #navList li a {
            font-size: 0.9em;
            padding: 10px 15px;
        }

        .user-p img {
            width: 60px;
        }

        .user-p h4 {
            font-size: 1em;
        }
    }

    @media (max-width: 480px) {
        .side-bar {
            width: 80px;
        }

        .main-content {
            margin-left: 80px;
        }

        #navList li a {
            font-size: 0.8em;
            padding: 8px 10px;
        }

        .user-p img {
            width: 50px;
        }

        .user-p h4 {
            display: none;
        }

        /* Hide text labels in sidebar */
        #navList li a span {
            display: none;
        }
    }
</style>
