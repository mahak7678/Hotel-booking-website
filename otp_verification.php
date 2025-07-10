<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (isset($_GET['email']) && isset($_POST['submit_otp'])) {
    $email = $_GET['email'];
    $entered_otp = $_POST['otp'];

    $result = mysqli_query($con, "SELECT otp FROM register WHERE emails = '$email'");
    $row = mysqli_fetch_assoc($result);

    if ($row && $entered_otp === $row['otp']) {
        mysqli_query($con, "UPDATE register SET otp = NULL WHERE emails = '$email'");
        echo "<script>alert('Registration verified successfully.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .otp-container {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }

        .otp-container h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .otp-container input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .otp-container button {
            background-color: #0072ff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .otp-container button:hover {
            background-color: #005ecb;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <h2>Enter OTP sent to your email</h2>
        <form method="POST">
            <input type="text" name="otp" maxlength="6" required placeholder="Enter 6-digit OTP">
            <button type="submit" name="submit_otp">Verify OTP</button>
        </form>
    </div>
</body>
</html>
