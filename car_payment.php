<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_GET['booking_id'])) {
    die("Booking ID is missing.");
}

$booking_id = intval($_GET['booking_id']);

// Correct table and column names
$sql = "SELECT * FROM car_bookings WHERE id = ?";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Car booking not found.");
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - Car Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .payment-container {
      max-width: 600px;
      margin: 50px auto;
      padding: 30px;
      background-color: #f8f9fa;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 25px; }
    .qr-code { text-align: center; margin: 20px 0; }
    .btn-group { display: flex; justify-content: space-between; }
    .btn-cancel { background-color: #dc3545; color: white; }
  </style>
</head>
<body>

<div class="payment-container">
  <h2><i class="fas fa-credit-card"></i> Complete Your Payment</h2>

  <p><strong>Booking for:</strong> <?= htmlspecialchars($booking['name']); ?></p>
  <p><strong>Pickup Location:</strong> <?= htmlspecialchars($booking['pickup_location']); ?></p>
  <p><strong>Drop Location:</strong> <?= htmlspecialchars($booking['drop_location']); ?></p>
  <p><strong>Pickup Time:</strong> <?= htmlspecialchars($booking['pickup_time']); ?></p>
  <p><strong>Amount to Pay:</strong> ₹<?= htmlspecialchars($booking['total_amount']); ?></p>

  <hr>

  <div class="qr-code">
    <h5>Pay via UPI</h5>

    <p>UPI ID: <strong>8700844056@ibl</strong></p>
  </div>

  <div class="qr-code">
    <h5>Or Pay via Paytm</h5>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=luxuryroom@upi" alt="UPI QR Code" />	
    <p>Paytm Number: <strong>8700844056</strong></p>
  </div>

  <div class="btn-group mt-4">
    <a href="yourbooking.php" class="btn btn-success w-100 me-2">I've Paid</a>
    <a href="yourbookingpage.php?cancel=true" class="btn btn-cancel w-100 ms-2">Cancel</a>
  </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>
