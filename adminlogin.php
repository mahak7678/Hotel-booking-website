<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $stmt = $con->prepare("SELECT * FROM adminlogin WHERE username = ? AND password = MD5(?)");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $_SESSION["admin"] = $username;
    header("Location: dashboard.php");
    exit();
  } else {
    echo "<script>alert('Invalid username or password'); window.location.href='adminlogin.php';</script>";
  }

  $stmt->close();
  $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - Luxury.com</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    .sidebar {
      width: 220px;
      height: 100vh;
      background-color: #343a40;
      padding-top: 60px;
      position: fixed;
      top: 0;
      left: 0;
      color: #fff;
    }

    .sidebar a {
      color: #adb5bd;
      padding: 12px 20px;
      display: block;
      text-decoration: none;
    }

    .sidebar a:hover {
      background-color: #495057;
      color: #fff;
    }

    .main-content {
      margin-left: 10px;
      padding-top: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-box {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-box h3 {
      margin-bottom: 25px;
      color: #333;
      text-align: center;
    }

    .luxury-heading {
      font-size: 24px;
      font-weight: bold;
      letter-spacing: 1px;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      .main-content {
        margin-left: 0;
        padding-top: 70px;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand luxury-heading" href="#"><i class="fas fa-hotel me-2"></i>Luxury.com</a>
</nav>


<script>
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = 'adminlogin.php'; // Redirect to logout.php if confirmed
  }
}
</script>

</div>

<!-- Login Panel -->
<div class="main-content">
  <div class="login-box">
    <h3>Admin Login</h3>
    <form method="POST" action="adminlogin.php">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" required />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required />
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
