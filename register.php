<?php
require 'DB_connection.php'; // Include the database connection

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email'])); // Capture email
    $password = trim($_POST['password']);
    $role = 'employee'; // Default role

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } 
    // Validate password strength (Minimum 6 characters)
    elseif (strlen($password) < 3) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password

        try {
            // Check if the username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                $error_message = "Username or email already exists! Try a different one.";
            } else {
                // Insert user into the database
                $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");

                if ($stmt->execute([$full_name, $username, $email, $hashed_password, $role])) {
                    // Redirect to login page with success message
                    header("Location: login.php?success=Registration successful! You can now log in.");
                    exit();
                } else {
                    $error_message = "Something went wrong! Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MOM Tracker</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: url('back.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }
        .login-container {
            background: rgb(202, 212, 220);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .global-back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgb(179, 195, 211);
            color: #222;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background 0.3s, transform 0.2s;
        }
        .global-back-button:hover {
            background: #ccc;
            transform: scale(1.05);
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Global Back Button -->
    <a href="index.php" class="global-back-button"><i class="fa fa-arrow-left"></i> Back</a>

    <div class="login-container">
        <h3 class="text-center">Register</h3>
        
        <!-- Display Error Message -->
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3 text-start">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                        <i class="fa fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <small>Password must be at least 6 characters.</small>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
    </div>

    <script>
        function togglePassword() {
            var password = document.getElementById('password');
            var toggleIcon = document.getElementById('toggleIcon');

            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
