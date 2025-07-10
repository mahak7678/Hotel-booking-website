<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

// Redirect if no booking data
if (!isset($_SESSION['total_amount']) || !isset($_SESSION['booking_type'])) {
    header("Location: index.php");
    exit;
}

$totalAmount = $_SESSION['total_amount'];
$bookingType = $_SESSION['booking_type'];

// Handle Payment Confirmation
$paymentSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_done'])) {
    if ($bookingType === 'car') {
        $query = "UPDATE car_bookings SET payment_status='Paid' ORDER BY id DESC LIMIT 1";
    } elseif ($bookingType === 'tour') {
        $query = "UPDATE tour_bookings SET payment_status='Paid' ORDER BY id DESC LIMIT 1";
    }

      if (mysqli_query($con, $query)) {
        $paymentSuccess = true;
        session_unset();
        session_destroy();
    } else {
        $error = "Payment status update failed: " . mysqli_error($con);
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - Luxury Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
        }
        .payment-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .payment-method img {
            width: 150px;
        }
    </style>
</head>
<body>

<?php if (!$paymentSuccess): ?>
<div class="payment-container text-center">
    <h2>Complete Your Booking</h2>
    <p>Booking Type: <strong><?= ucfirst($bookingType) ?></strong></p>
    <p>Total Amount: <span class="fw-bold text-success">₹<?= $totalAmount ?></span></p>

    <div class="my-4">
        <h5>Pay via UPI</h5>
         <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=luxuryroom@upi" alt="UPI QR Code" />
        <p><strong>UPI ID:</strong> 8700844056@ibl</p>
    </div>

    <div class="my-3">
        <a href="https://paytm.com/pay" target="_blank" class="btn btn-primary">Pay Now with Paytm</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
    <a href="facilities.php" class="btn btn-outline-primary mt-3">Back</a>
    </form>
</div>
<?php else: ?>
<div class="payment-container text-center">
    <h2 class="text-success">Payment Successful!</h2>
    <p>Your booking has been confirmed.</p>
    <a href="mhk.php" class="btn btn-outline-primary mt-3">Back to Home</a>
</div>
<?php endif; ?>

</body>
</html>
