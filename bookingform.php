<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$booking_success = false;
$payment_section = false;

if (isset($_POST['booking_submit'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $zipcode = $_POST['zipcode'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = $_POST['adults'];
    $childrens = $_POST['childrens'];

    // Registered user's email (from session)
    $email = $_SESSION['user_email'] ?? '';

    // Optional: Fetch registered name from register table
    $registered_name = $name; // fallback to entered name
    if ($email != '') {
        $res = mysqli_query($con, "SELECT firstname FROM register WHERE emails='$email' LIMIT 1");
        if ($res && mysqli_num_rows($res) > 0) {
            $reg_row = mysqli_fetch_assoc($res);
            $registered_name = $reg_row['firstname'];
        }
    }

    $query = "SELECT id FROM rooms WHERE id NOT IN 
              (SELECT room_id FROM bookingform WHERE check_in < '$check_out' AND check_out > '$check_in') LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $room = mysqli_fetch_assoc($result);
        $room_id = $room['id'];

        $query = "INSERT INTO bookingform (name, contact, address, zipcode, check_in, check_out, adults, childrens, room_id, payment_status, email) 
                  VALUES ('$name', '$contact', '$address', '$zipcode', '$check_in', '$check_out', '$adults', '$childrens', '$room_id', 'Pending', '$email')";
        if (mysqli_query($con, $query)) {
            $booking_success = true;
            $payment_section = true;
			

            // Get room price
            $price_query = "SELECT price FROM rooms WHERE id = $room_id LIMIT 1";
            $price_result = mysqli_query($con, $price_query);
            if ($price_result && mysqli_num_rows($price_result) > 0) {
                $room_data = mysqli_fetch_assoc($price_result);
                $room_price = $room_data['price'];
            } else {
                $room_price = "N/A";
            }

            // Send confirmation email using PHPMailer
            if ($email != '') {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'luxurycom43@gmail.com'; // Replace with your Gmail
                    $mail->Password   = 'fqtpcyfnfuajrlsb';   // Use your app-specific password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('yourgmail@gmail.com', 'Luxury Room');
                    $mail->addAddress($email, $registered_name);

                    $mail->isHTML(false);
                    $mail->Subject = 'Booking Confirmation - Luxury Room';
                    $mail->Body    = "Dear $registered_name,\n\nThank you for booking with us.\n\n"
                                   . "Booking Details:\nCheck-in: $check_in\nCheck-out: $check_out\n"
                                   . "Room Price: ₹$room_price\n\nPlease pay via UPI: 8700844056@ibl\n\n"
                                   . "Regards,\nLuxury Room Team";

                    $mail->send();
                    echo '<div class="alert alert-success">Confirmation email sent to your registered email!</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">Mailer Error: ' . $mail->ErrorInfo . '</div>';
                }
            }
                   
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($con) . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">No rooms available for the selected dates.</div>';
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Room.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            height: 100%;
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .row {
            margin-bottom: 20px;
        }
        .navbar-nav .nav-link {
            color: white !important;
            transition: color 0.3s ease-in-out;
        }

        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
        }

        /* Custom styles for booking form */
        .booking-form label {
            font-weight: bold;
        }
        .booking-form .form-control {
            margin-bottom: 15px;
        }
		.tour-car-img {
    height: 180px;
    object-fit: cover;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.btn-book-now {
    background: linear-gradient(45deg, #007bff, #00c6ff);
    color: white;
    font-weight: bold;
    border-radius: 30px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.btn-book-now:hover {
    background: linear-gradient(45deg, #0056b3, #0096c7);
    transform: scale(1.05);
    color: #fff;
}
.tour-car-img {
    height: 180px;
    object-fit: cover;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.btn-book-now {
    background: linear-gradient(45deg, #007bff, #00c6ff);
    color: white;
    font-weight: bold;
    border-radius: 30px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.btn-book-now:hover {
    background: linear-gradient(45deg, #0056b3, #0096c7);
    transform: scale(1.05);
    color: #fff;
}
.payment-section {
  max-width: 400px;
  margin: 40px auto;
  padding: 25px;
  background-color: #f9f9f9;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.payment-section h2 {
  text-align: center;
  margin-bottom: 25px;
  color: #333;
  font-weight: 700;
}

.payment-section label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.payment-section input[type="text"],
.payment-section input[type="email"],
.payment-section input[type="number"],
.payment-section select {
  width: 100%;
  padding: 10px 12px;
  margin-bottom: 18px;
  border: 1.8px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
  transition: border-color 0.3s ease;
}

.payment-section input[type="text"]:focus,
.payment-section input[type="email"]:focus,
.payment-section input[type="number"]:focus,
.payment-section select:focus {
  border-color: #007bff;
  outline: none;
}

.payment-section button {
  width: 100%;
  padding: 12px 0;
  background-color: #007bff;
  border: none;
  border-radius: 6px;
  color: #fff;
  font-size: 17px;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.payment-section button:hover {
  background-color: #0056b3;
}
html {
  scroll-behavior: smooth;
}


    </style>
</head>
<body>
<header class="bg-primary text-white p-3 sticky-top shadow">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-hotel"></i> Luxury Room.com</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="mhk.php" class="nav-link"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a href="rooms.php" class="nav-link"><i class="fas fa-list"></i> Rooms</a></li>
                    <li class="nav-item"><a href="facilities.php" class="nav-link"><i class="fas fa-money-bill"></i> Facilities</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
					                             <li class="nav-item"><a href="wishlist.php" class="nav-link"> <i class="fas fa-heart"></i>Wishlist</a></li>
                        <li class="nav-item"><a href="yourbookingpage.php" class="nav-link"><i class="fas fa-book"></i> My Bookings</a></li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a href="register.php" class="nav-link"><i class="fas fa-user-plus"></i> Register</a></li>
                        <li class="nav-item"><a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container my-4">
<h2 class="mb-4"><i class="fas fa-calendar-check"></i> Hotel Booking Form</h2>

    <div class="row">
       <div class="col-md-6 d-flex flex-column">
	   <?php if (!$payment_section): ?>
            <form class="booking-form p-4 border rounded bg-light shadow-sm" method="POST" enctype="multipart/form-data" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label"><i class="fas fa-user"></i> Name</label>
                        <input type="text" id="name" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="contact" class="form-label"><i class="fas fa-phone"></i> Contact No.</label>
                        <input type="number" id="contact" class="form-control" name="contact" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Address</label>
                        <input type="text" id="address" class="form-control" name="address" required>
                    </div>
                    <div class="col-md-6">
                        <label for="zip-code" class="form-label"><i class="fas fa-map-pin"></i> Zip-code</label>
                        <input type="number" id="zip-code" class="form-control" name="zipcode" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="checkin" class="form-label"><i class="fas fa-sign-in-alt"></i> Check-in</label>
                        <input type="date" id="checkin" class="form-control" name="check_in" required>
                    </div>
                    <div class="col-md-6">
                        <label for="checkout" class="form-label"><i class="fas fa-sign-out-alt"></i> Check-out</label>
                        <input type="date" id="checkout" class="form-control" name="check_out" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="adults" class="form-label"><i class="fas fa-user-friends"></i> Adults</label>
                        <input type="number" id="adults" class="form-control" min="1" name="adults" required>
                    </div>
                    <div class="col-md-6">
                        <label for="children" class="form-label"><i class="fas fa-child"></i> Children</label>
                        <input type="number" id="children" class="form-control" min="0" name="childrens" required>
                    </div>
                </div>
                
                <input type="submit" class="btn btn-primary mt-3 w-100 btn-book-now" name="booking_submit" value="Pay Now">
            </form>
			<?php else: ?>
    <!-- Payment Section -->
    <div class="payment-section" id="payment-section">
        <h3><i class="fas fa-check-circle"></i> Booking Successful!</h3>
        <p>Your room is confirmed. Please complete your payment to finalize your booking.</p>
            <p><strong>Room Price:</strong> ₹<?= isset($room_price) ? $room_price : 'N/A' ?></p>
        <h5>Pay Using UPI</h5>
        <p><strong>UPI ID:</strong> 8700844056@ibl</p>
        
        <!-- Replace this image with your actual UPI QR code -->
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=luxuryroom@upi" alt="UPI QR Code" />
        
       <a href="upi://pay?pa=8700844056@ibl&pn=YourNameOrBusiness&cu=INR" class="btn btn-success mt-3">
            <i class="fas fa-mobile-alt"></i> Pay via UPI App
        </a>

        <p class="mt-3">After completing payment, you can <a href="yourbookingpage.php">check your booking status here</a>.</p>
    </div>
<?php endif; ?>
			<!-- Room Booking Cards -->
<h4 class="mb-3"><i class="fas fa-bed"></i> Room Booking</h4>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" class="card-img-top tour-car-img" alt="Room 1">
            <div class="card-body text-center">
                <h5 class="card-title">Deluxe Room</h5>
                <p class="card-text">Spacious and modern room with all amenities for a relaxing stay.</p>
                <a href="rooms.php" class="btn btn-book-now"><i class="fas fa-door-open"></i> Book Room</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80" class="card-img-top tour-car-img" alt="Room 2">
            <div class="card-body text-center">
                <h5 class="card-title">Suite Room</h5>
                <p class="card-text">Enjoy premium luxury with a king-size bed and private balcony.</p>
                <a href="rooms.php" class="btn btn-book-now"><i class="fas fa-hotel"></i> Book Room</a>
            </div>
        </div>
    </div>
</div>

        </div>

       <div class="col-md-6 d-flex flex-column">
        <div class="p-4 border rounded bg-light shadow-sm flex-grow-1 d-flex flex-column justify-content-between">
            <div>
        <h4 class="mb-4"><i class="fas fa-car"></i> Car Booking</h4>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <img src="images/car2.jpg" class="card-img-top tour-car-img" alt="Car 1">
                    <div class="card-body text-center">
                        <h5 class="card-title">Luxury Sedan</h5>
                        <p class="card-text">Ride in style and comfort with our premium sedans.</p>
                        <a href="rooms.php" class="btn btn-book-now"><i class="fas fa-car-side"></i> Book Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <img src="images/car3.jpg" class="card-img-top tour-car-img" alt="Car 2">
                    <div class="card-body text-center">
                        <h5 class="card-title">SUV Adventure</h5>
                        <p class="card-text">Perfect for rugged terrain and family trips.</p>
                        <a href="rooms.php" class="btn btn-book-now"><i class="fas fa-car"></i> Book Now</a>
                    </div>
                </div>
            </div>
        </div>
		</div>
		
		
  <h4 class="mb-4"><i class="fas fa-suitcase-rolling"></i> Tour Booking</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <img src="images/goa3.webp" class="card-img-top tour-car-img" alt="Tour 1">
                    <div class="card-body text-center">
                        <h5 class="card-title">City Explorer</h5>
                        <p class="card-text">Explore the hidden gems of the city with our guided tour.</p>
                        <a href="rooms.php" class="btn btn-book-now"><i class="fas fa-map-marked-alt"></i> Book Tour</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" class="card-img-top tour-car-img" alt="Tour 2">
                    <div class="card-body text-center">
                        <h5 class="card-title">Mountain Escape</h5>
                        <p class="card-text">Reconnect with nature through a serene mountain tour.</p>
                        <a href="rooms.php" class="btn btn-book-now"><i class="fas fa-hiking"></i> Book Tour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<footer class="bg-dark text-white mt-5 pt-5 pb-3">
  <div class="container">
    <div class="row">
      <!-- About Us -->
      <div class="col-md-4 mb-4">
        <h5 class="text-uppercase"><i class="fas fa-hotel"></i> Luxury Room.com</h5>
        <p>Experience luxury like never before. Book premium rooms, cars, and tours with ease.</p>
      </div>

      <!-- Quick Links -->
      <div class="col-md-4 mb-4">
        <h5 class="text-uppercase"><i class="fas fa-link"></i> Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="mhk.php" class="text-white text-decoration-none"><i class="fas fa-home me-2"></i>Home</a></li>
          <li><a href="rooms.php" class="text-white text-decoration-none"><i class="fas fa-bed me-2"></i>Rooms</a></li>
          <li><a href="facilities.php" class="text-white text-decoration-none"><i class="fas fa-swimming-pool me-2"></i>Facilities</a></li>
          <li><a href="yourbookingpage.php" class="text-white text-decoration-none"><i class="fas fa-calendar-check me-2"></i>My Bookings</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="col-md-4 mb-4">
        <h5 class="text-uppercase"><i class="fas fa-envelope"></i> Contact</h5>
        <p><i class="fas fa-map-marker-alt me-2"></i>123 Luxury St, New Delhi, India</p>
        <p><i class="fas fa-phone me-2"></i>+91 98765 43210</p>
        <p><i class="fas fa-envelope me-2"></i>support@luxuryroom.com</p>
        <div>
          <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>
    </div>
    <hr class="bg-white">
    <div class="text-center">
      <p class="mb-0">&copy; 2025 Luxury Room.com | Designed with ❤️ by Mahak</p>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
