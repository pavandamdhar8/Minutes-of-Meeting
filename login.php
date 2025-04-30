<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | MOM Tracker</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

  <style>
    /* Background */
    body {
      background: url('back.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: flex-start; /* Align to the top */
      height: 100vh;
      margin: 0;
      transition: 0.3s;
    }

    /* Back Button */
    .back-button {
      position: absolute;
      top: 20px;
      left: 20px;
      background: rgba(255, 255, 255, 0.8);
      color: #222;
      border: none;
      border-radius: 6px;
      padding: 8px 12px;
      text-decoration: none;
      font-size: 14px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      transition: background 0.3s, transform 0.2s;
    }
    .back-button:hover {
      background: rgba(255, 255, 255, 1);
      transform: translateY(-2px);
    }

    /* Flip Container */
    .flip-container {
      width: 400px;
      perspective: 1000px;
      position: relative;
      margin-top: 120px; /* Added margin to push it down */
    }

    /* Inner flipping box */
    .flipper {
      width: 100%;
      transition: transform 0.6s;
      transform-style: preserve-3d;
      position: relative;
    }

    /* Flip on active class */
    .flip-container.flip .flipper {
      transform: rotateY(180deg);
    }

    /* Both forms */
    .login-container, .register-container {
      width: 100%;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      position: absolute;
      backface-visibility: hidden;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: rgba(255, 255, 255, 0.95);
      color: #222; /* Dark text for contrast */
    }

    /* Register form (hidden by default) */
    .register-container {
      transform: rotateY(180deg);
    }

    .form-control {
      border-radius: 8px;
    }

    .btn-login {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      font-weight: bold;
    }

    .toggle-link {
      margin-top: 10px;
      font-size: 14px;
      cursor: pointer;
      color: #007bff;
      text-decoration: underline;
      text-align: center;
    }

    .toggle-link:hover {
      color: #0056b3;
    }

    /* Ensure content stays centered */
    @media (max-width: 450px) {
      .flip-container {
        width: 90%;
      }
    }
  </style>
</head>
<body>

  <!-- Back Button -->
  <a href="home.html" class="back-button">
    <i class="fa fa-arrow-left"></i>
  </a>

  <div class="flip-container" id="flipContainer">
    <div class="flipper">
      
      <!-- Login Form -->
      <div class="login-container">
        <h3 class="text-center">Welcome Back</h3>
        <form method="POST" action="app/login.php">
          <div class="mb-3 text-start">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="user_name" autocomplete="off" required>
          </div>
          <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" autocomplete="off" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                <i class="fa fa-eye" id="toggleIcon"></i>
              </button>
            </div>
          </div>
          <div class="form-check text-start">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label" for="rememberMe">Remember me</label>
          </div>
          <button type="submit" class="btn btn-primary btn-login mt-3">Login</button>
        </form>
        <p class="toggle-link" onclick="flipCard()">Don't have an account? Register</p>
      </div>

      <!-- Registration Form -->
      <div class="register-container">
        <h3 class="text-center">Create an Account</h3>
        <form method="POST" action="app/register.php">
          <div class="mb-3 text-start">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" required>
          </div>
          <div class="mb-3 text-start">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3 text-start">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="user_name" required>
          </div>
          <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary btn-login mt-3">Register</button>
        </form>
        <p class="toggle-link" onclick="flipCard()">Already have an account? Login</p>
      </div>

    </div>
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

    function flipCard() {
      document.getElementById('flipContainer').classList.toggle('flip');
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
