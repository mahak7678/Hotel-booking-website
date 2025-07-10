<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (!$con) die("Connection failed: " . mysqli_connect_error());

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_submit'])) {
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $emails = mysqli_real_escape_string($con, $_POST['emails']);
    $passwords = $_POST['passwords'];
    $confirm_password = $_POST['confirm_password'];
    $security_answer = mysqli_real_escape_string($con, $_POST['security_answer']);

    if ($passwords !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        $hashed_password = password_hash($passwords, PASSWORD_DEFAULT);
        $hashed_answer = password_hash($security_answer, PASSWORD_DEFAULT);

        $query = "INSERT INTO register (firstname, emails, passwords, security_answer) 
                  VALUES ('$firstname', '$emails', '$hashed_password', '$hashed_answer')";
        if (mysqli_query($con, $query)) {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            mysqli_query($con, "UPDATE register SET otp = '$otp' WHERE emails = '$emails'");

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'luxurycom43@gmail.com';
                $mail->Password   = 'fqtpcyfnfuajrlsb';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail->setFrom('mahakbishnoi5@gmail.com', 'Luxury Room');
                $mail->addAddress($emails, $firstname);
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP for Luxury Room Registration';
                $mail->Body = "<h2>Hello $firstname!</h2><p>Your OTP: <strong>$otp</strong></p>";

                $mail->send();
                $registrationSuccess = true;
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('Registration failed.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register | Luxury Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; }
        .register-form { max-width: 400px; margin: 50px auto; padding: 30px; box-shadow: 0 0 10px #ccc; border-radius: 10px; }
        .password-toggle { cursor: pointer; position: absolute; right: 15px; top: 38px; }
		.navbar-nav .nav-link {
		color: white !important;
		transition: color 0.3s ease-in-out;
	}

	.navbar-nav .nav-link:hover {
		color: #FFD700 !important; /* Gold color on hover */
	}
.register-form { 
    max-width: 700px;   /* Increased width */
    margin: 50px auto; 
    padding: 15px 30px; /* Decreased top/bottom padding (from 30px to 15px) */
    box-shadow: 0 0 10px #ccc; 
    border-radius: 10px; 
}

    </style>
</head>
<body>

<?php if ($registrationSuccess): ?>
<script>
    alert("OTP sent! Check your email.");
    window.location.href = 'otp_verification.php?email=<?= urlencode($emails) ?>';
</script>
<?php endif; ?>
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
<div class="register-form bg-white position-relative">
    <h3 class="mb-4 text-center">Create Account</h3>
    <form method="POST" id="registerForm" novalidate>
        <div class="mb-3">
            <label for="firstname" class="form-label">Full Name</label>
            <input type="text" id="firstname" name="firstname" class="form-control" required placeholder="Your full name" />
        </div>

        <div class="mb-3">
            <label for="emails" class="form-label">Email</label>
            <input type="email" id="emails" name="emails" class="form-control" required placeholder="email@example.com" />
        </div>

        <div class="mb-3 position-relative">
            <label for="passwords" class="form-label">Password</label>
            <input type="password" id="passwords" name="passwords" class="form-control" required minlength="6" placeholder="Password" />
            <i class="fas fa-eye password-toggle" onclick="togglePassword('passwords', this)"></i>
        </div>

        <div class="mb-3 position-relative">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6" placeholder="Confirm Password" />
            <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)"></i>
        </div>

        <div class="mb-3">
            <label for="security_answer" class="form-label">Security Question: What is your favorite food?</label>
            <input type="text" id="security_answer" name="security_answer" class="form-control" required placeholder="Answer" />
        </div>

        <button type="submit" name="register_submit" class="btn btn-primary w-100">Register</button>
    </form>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    function togglePassword(id, icon) {
        const field = document.getElementById(id);
        if (field.type === "password") {
            field.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.getElementById("registerForm").addEventListener("submit", function(e) {
        if (document.getElementById("passwords").value !== document.getElementById("confirm_password").value) {
            alert("Passwords do not match!");
            e.preventDefault();
        }
    });
</script>

</body>
</html>
