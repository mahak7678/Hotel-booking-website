<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit_subscribe'])) {
    $email = $_POST['email'];

    // Insert subscriber's email into database
    $query = "INSERT INTO Subscribe(email) VALUES ('$email')";
    $execute = mysqli_query($con, $query);

    if ($execute) {
        // Initialize PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'mahakbishnoi5@gmail.com'; // Your Gmail
            $mail->Password = 'YOUR_APP_PASSWORD_HERE'; // Use App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Sender & recipient
            $mail->setFrom('mahakbishnoi5@gmail.com', 'Luxury Room');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Thank You for Subscribing!';
            $mail->Body = "
                <h2>Welcome to Luxury Room!</h2>
                <p>Thank you for subscribing to our newsletter. Stay updated with the latest offers and news.</p>
                <br>
                <p>Best Regards,</p>
                <p><strong>Luxury Room Team</strong></p>
            ";

            $mail->send();
            echo "<script>alert('Subscription successful! Check your email.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Subscription successful, but email not sent. Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Failed to subscribe. Please try again.');</script>";
    }
}
?>
