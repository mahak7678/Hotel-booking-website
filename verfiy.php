<?php
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = mysqli_real_escape_string($con, $_GET['email']);
    $token = mysqli_real_escape_string($con, $_GET['token']);

    $query = "SELECT is_verified FROM register WHERE emails='$email' AND verification_token='$token'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['is_verified'] == 1) {
            echo "<script>alert('Email is already verified. Please login.'); window.location='login.php';</script>";
        } else {
            $update = "UPDATE register SET is_verified = 1, verification_token = NULL WHERE emails = '$email'";
            mysqli_query($con, $update);
            echo "<script>alert('Email verified successfully. You can now log in.'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid verification link.'); window.location='register.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location='register.php';</script>";
}
?>
