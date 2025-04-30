<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Pro Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Sidebar -->
    <nav class="side-bar">
        <div class="user-profile">
            <img src="img/user.png" alt="User Profile">
            <h4>@<?= $_SESSION['username'] ?></h4>
            <button id="toggleSidebar"><i class="fa fa-bars"></i></button>
        </div>

        <ul id="navList">
            <?php 
                $currentPage = basename($_SERVER['PHP_SELF']);
                $menuItems = [
                    "employee" => [
                        ["index.php", "fa-tachometer", "Dashboard"],
                        ["my_task.php", "fa-tasks", "My Task"],
                        ["profile.php", "fa-user", "Profile"],
                        ["notifications.php", "fa-bell", "Notifications"]
                    ],
                    "admin" => [
                        ["index.php", "fa-tachometer", "Dashboard"],
                        ["user.php", "fa-users", "Manage Users"],
                        ["create_task.php", "fa-plus", "Create Task"],
                        ["tasks.php", "fa-tasks", "All Tasks"]
                    ]
                ];

                $role = $_SESSION['role'];
                foreach ($menuItems[$role] as $item) {
                    $activeClass = ($currentPage == $item[0]) ? 'active' : '';
                    echo "<li class='nav-item $activeClass'>
                            <a href='{$item[0]}'>
                                <i class='fa {$item[1]}'></i>
                                <span>{$item[2]}</span>
                            </a>
                          </li>";
                }
            ?>

            <li class="nav-item">
                <a href="logout.php">
                    <i class="fa fa-sign-out"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>

        <!-- Dark Mode Toggle -->
        <div class="theme-switch">
            <i id="themeIcon" class="fa fa-moon"></i>
        </div>
    </nav>

    <script>
        // Toggle Sidebar
        document.getElementById("toggleSidebar").addEventListener("click", function() {
            document.querySelector(".side-bar").classList.toggle("collapsed");
        });

        // Dark Mode Toggle
        const themeIcon = document.getElementById("themeIcon");
        themeIcon.addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            themeIcon.classList.toggle("fa-moon");
            themeIcon.classList.toggle("fa-sun");
        });
    </script>

</body>
</html>

<style>
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

/* Light & Dark Mode */
body {
    transition: 0.3s ease-in-out;
    background: #f4f4f4;
    color: #333;
}
body.dark-mode {
    background: #222;
    color: #fff;
}
body.dark-mode .side-bar {
    background: #333;
}
body.dark-mode .nav-item {
    color: #ddd;
}
body.dark-mode .nav-item:hover, body.dark-mode .nav-item.active {
    background: #555;
    border-left: 5px solid #f4a261;
}

/* Sidebar Styling */
.side-bar {
    width: 260px;
    height: 100vh;
    background: linear-gradient(135deg,rgb(126, 177, 233),rgb(100, 135, 173));
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
    position: fixed;
    transition: 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
}

/* User Profile Section */
.user-profile {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    color: white;
}
.user-profile img {
    width: 80px;
    border-radius: 50%;
    margin-bottom: 10px;
    border: 2px solid white;
}
.user-profile h4 {
    font-size: 18px;
}

/* Toggle Sidebar Button */
#toggleSidebar {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

/* Sidebar Navigation */
#navList {
    list-style: none;
    padding: 0;
    margin: 0;
}
.nav-item {
    padding: 14px 20px;
    display: flex;
    align-items: center;
    transition: 0.3s;
}
.nav-item a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
    width: 100%;
    transition: 0.3s ease-in-out;
}
.nav-item i {
    margin-right: 12px;
    font-size: 18px;
}
.nav-item:hover, .nav-item.active {
    background: rgba(255, 255, 255, 0.2);
    border-left: 5px solid #ffeb3b;
}

/* Theme Switch */
.theme-switch {
    position: absolute;
    bottom: 20px;
    left: 20px;
    cursor: pointer;
    font-size: 24px;
    color: white;
}

/* Responsive - Collapsible Sidebar */
@media (max-width: 768px) {
    .side-bar {
        width: 60px;
    }
    .side-bar.collapsed {
        width: 250px;
    }
    .user-profile h4 {
        display: none;
    }
    .nav-item span {
        display: none;
    }
    .side-bar.collapsed .user-profile h4,
    .side-bar.collapsed .nav-item span {
        display: inline;
    }
}
</style>
