<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['reset_email'])) {
    echo "<script>alert('Unauthorized Access!'); window.location.href = 'login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $email = $_SESSION['reset_email'];

    $query = "UPDATE register SET passwords='$hashed_password' WHERE emails='$email'";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Password Reset Successfully!'); window.location.href = 'login.php';</script>";
        session_destroy();
    } else {
        echo "<script>alert('Error resetting password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3 class="text-center text-primary">Reset Password</h3>
                    <form action="reset_password.php" method="POST">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Enter New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <input type="submit" class="btn btn-success w-100" value="Reset Password">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
