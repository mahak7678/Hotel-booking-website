<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emails = mysqli_real_escape_string($con, $_POST['emails']);
    $security_answer = mysqli_real_escape_string($con, $_POST['security_answer']);

    // Using prepared statement for better security
    $query = "SELECT security_answer FROM register WHERE emails=?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $emails);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_answer = $row['security_answer'];

        if (password_verify($security_answer, $hashed_answer)) {
            $_SESSION['reset_email'] = $emails;
            echo "<script>alert('Identity Verified! Please reset your password.'); window.location.href = 'reset_password.php';</script>";
        } else {
            echo "<script>alert('Incorrect answer! Try again.'); window.location.href = 'forgot_password.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found! Please try again.'); window.location.href = 'forgot_password.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
.navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
        }
</style>
<body>
<header class="bg-primary text-white p-3 sticky-top shadow">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-hotel"></i> Luxury Room.com</a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="mhk.php" class="nav-link"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a href="rooms.php" class="nav-link"><i class="fas fa-bed"></i> Rooms</a></li>
                    <li class="nav-item"><a href="facilities.php" class="nav-link"><i class="fas fa-cogs"></i> Facilities</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                                                <li class="nav-item"><a href="wishlist.php" class="nav-link"> <i class="fas fa-heart"></i>Wishlist</a></li>					
                        <li class="nav-item"><a href="yourbookingpage.php" class="nav-link"><i class="fas fa-book"></i> My Bookings</a></li>
                        <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="register.php" class="nav-link"><i class="fas fa-user-plus"></i> Register</a></li>
                        <li class="nav-item"><a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3 class="text-center text-primary">Forgot Password</h3>
                    <form action="forgot_password.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Enter Your Email</label>
                            <input type="email" name="emails" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="security_answer" class="form-label">What is your Favorite Food Name?</label>
                            <input type="text" name="security_answer" class="form-control" required>
                        </div>
                        <input type="submit" class="btn btn-primary w-100" value="Verify">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
