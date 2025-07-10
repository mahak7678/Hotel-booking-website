<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get booking ID from GET parameter
if (!isset($_GET['booking_id'])) {
    die("Booking ID is missing.");
}

$booking_id = intval($_GET['booking_id']); // sanitize

// Fetch booking details along with room price
$sql = "SELECT b.*, r.price AS room_price FROM bookingform b 
        LEFT JOIN rooms r ON b.room_id = r.id WHERE b.id = $booking_id";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Booking not found.");
}

$booking = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - Luxury Room</title>
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
    h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    .qr-code {
      text-align: center;
      margin: 20px 0;
    }
    .btn-group {
      display: flex;
      justify-content: space-between;
    }
    .btn-cancel {
      background-color: #dc3545;
      color: white;
    }
  </style>
</head>
<body>

<div class="payment-container">
  <h2><i class="fas fa-credit-card"></i> Complete Your Payment</h2>

  <p><strong>Booking for:</strong> <?= htmlspecialchars($booking['name']); ?></p>
  <p><strong>Room ID:</strong> <?= htmlspecialchars($booking['room_id']); ?></p>
  <p><strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in']); ?></p>
  <p><strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out']); ?></p>
  <p><strong>Amount to Pay:</strong> ₹<?= htmlspecialchars($booking['room_price']); ?></p>

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
    <a href="yourbookingpage.php" class="btn btn-success w-100 me-2">I've Paid</a>
    <a href="yourbookingpage.php?cancel=true" class="btn btn-cancel w-100 ms-2">Cancel</a>
  </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>
