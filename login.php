<?php 
session_start();

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $emails = mysqli_real_escape_string($con, $_POST['emails']);
    $passwords = mysqli_real_escape_string($con, $_POST['passwords']);

    $query = "SELECT * FROM register WHERE emails='$emails'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['passwords'];

        if (!password_verify($passwords, $hashed_password)) {
            echo "<script>alert('Incorrect password!'); window.location.href = 'login.php';</script>";
            exit();
        }

        // Set session and redirect
        $_SESSION['user_email'] = $user['emails'];
        $_SESSION['user'] = $user['firstname'];

        echo "<script>
            alert('Login successful! Redirecting to rooms page...');
            window.location.href = 'rooms.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('No account found with this email!'); window.location.href = 'register.php';</script>";
        exit();
    }
}
?>









	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Luxury Room.com - Login</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	</head>
	<style type="text/css">
		
		.navbar-nav .nav-link {
		color: white !important;
		transition: color 0.3s ease-in-out;
	}

	.navbar-nav .nav-link:hover {
		color: #FFD700 !important; /* Gold color on hover */
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

		
		<!-- Login Form -->
		<div class="container mt-5">
			<div class="row justify-content-center">
				<div class="col-md-6">
					<div class="card shadow-lg">
						<div class="card-body">
							<h3 class="text-center text-primary">Login</h3>
							<form action="login.php" method="POST">
								<div class="mb-3">
									<label for="email" class="form-label">Email</label>
									<div class="input-group">
										<span class="input-group-text"><i class="fas fa-envelope"></i></span>
										<input type="email" name="emails" class="form-control" id="email" placeholder="Enter your email" required>
									</div>
								</div>
								<div class="mb-3">
									<label for="password" class="form-label">Password</label>
									<div class="input-group">
										<span class="input-group-text"><i class="fas fa-lock"></i></span>
										<input type="password" name="passwords" class="form-control" id="password" placeholder="Enter your password" required>
									</div>
								</div>

								<input type="submit" class="btn btn-primary w-100" name="login_submit">
							</form>
							<div class="text-center mt-3">
							<a href="forgot_password.php" class="text-danger">Forgot Password?</a>
						</div>
						</div>
					</div>
				</div>	
			</div>
		</div>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	</body>
	</html>
