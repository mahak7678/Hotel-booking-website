<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// ----------------------
// Car & Tour Price Maps
// ----------------------
$car_prices = [
    "Delhi" => ["Agra" => 2000, "Jaipur" => 3000],
    "Agra" => ["Delhi" => 2000, "Jaipur" => 2500],
    "Jaipur" => ["Delhi" => 3000, "Agra" => 2500]
];

$tour_prices = [
    "Delhi" => 1500,
    "Agra" => 1800,
    "Jaipur" => 2200
];

// ----------------------
// Handle Tour Booking
// ----------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tour_date'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $destination = mysqli_real_escape_string($con, $_POST['destination']);
    $tour_date = mysqli_real_escape_string($con, $_POST['tour_date']);

    $total_amount = isset($tour_prices[$destination]) ? $tour_prices[$destination] : 0;

    $query = "INSERT INTO tour_bookings (name, email, destination, tour_date, total_amount) 
              VALUES ('$name', '$email', '$destination', '$tour_date', '$total_amount')";

    if (mysqli_query($con, $query)) {
        $_SESSION['booking_type'] = 'tour';
        $_SESSION['booking_id'] = mysqli_insert_id($con);
        $_SESSION['total_amount'] = $total_amount;

        header("Location: payment.php");
        exit();
    } else {
        echo "<script>alert('Error booking tour!');</script>";
    }
}

// ----------------------
// Handle Car Booking
// ----------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pickup_time'])) {
    if (!isset($_SESSION['user_email'])) {
        echo "<script>alert('Please log in to book a car.'); window.location.href = 'login.php';</script>";
        exit();
    }

    $email = $_SESSION['user_email'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $pickup = mysqli_real_escape_string($con, $_POST['pickup_location']);
    $drop = mysqli_real_escape_string($con, $_POST['drop_location']);
    $pickup_time = mysqli_real_escape_string($con, $_POST['pickup_time']);

    $total_amount = isset($car_prices[$pickup][$drop]) ? $car_prices[$pickup][$drop] : 0;

    $query = "INSERT INTO car_bookings (name, pickup_location, drop_location, pickup_time, total_amount, email) 
              VALUES ('$name', '$pickup', '$drop', '$pickup_time', '$total_amount', '$email')";

    if (mysqli_query($con, $query)) {
        $_SESSION['booking_type'] = 'car';
        $_SESSION['booking_id'] = mysqli_insert_id($con);
        $_SESSION['total_amount'] = $total_amount;

        header("Location: payment.php");
        exit();
    } else {
        echo "<script>alert('Error booking car!');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Luxury Room.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        .service-section {
            padding: 60px 0;
        }
       .service-card {
    background-color: #f9f9f9;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .service-form {
            padding: 20px;
        }
	#facilitiesCarousel {
    background-color: #f8f9fa; /* Light background for the carousel */
    border-radius: 15px;
    padding: 20px;
}

#facilitiesCarousel .carousel-inner img {
    height: 300px; /* Adjust the size of the slider images */
    object-fit: cover; /* Ensures the image covers the entire space */
}

.carousel-control-prev-icon, .carousel-control-next-icon {
    background-color: #000; /* Change the control button colors */
}
.navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
        }

   body {
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .service-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
        transform: scale(1.03);
        background-color: #0056b3;
    }
    </style>
</head>
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
<!-- Our Top Facilities Section with Icons -->
<section class="container my-5" data-aos="fade-up">
    <h3 class="text-center text-primary mb-4">Our Top Facilities</h3>
    <div class="row g-4">
        <!-- Facility 1 -->
        <div class="col-md-4">
            <div class="service-card text-center p-4">
                <h5><i class="fas fa-swimming-pool fa-3x text-primary mb-3"></i></h5>
                <h5 class="card-title">Swimming Pool</h5>
                <p class="card-text">Relax and unwind in our luxurious swimming pool, equipped with the best amenities for your comfort.</p>
            </div>
        </div>
        <!-- Facility 2 -->
        <div class="col-md-4">
            <div class="service-card text-center p-4">
                <h5><i class="fas fa-spa fa-3x text-primary mb-3"></i></h5>
                <h5 class="card-title">Spa and Wellness</h5>
                <p class="card-text">Indulge in a rejuvenating spa experience with therapies designed to refresh and relax you.</p>
            </div>
        </div>
        <!-- Facility 3 -->
        <div class="col-md-4">
            <div class="service-card text-center p-4">
                <h5><i class="fas fa-utensils fa-3x text-primary mb-3"></i></h5>
                <h5 class="card-title">Restaurant</h5>
                <p class="card-text">Enjoy a variety of gourmet dishes in our world-class restaurant, offering both local and international cuisines.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Top Facilities Section with Smaller Slider and Background -->
<section class="container my-5"data-aos="fade-up">
    <h3 class="text-center text-primary mb-4">Our Top Facilities</h3>
    <div id="facilitiesCarousel" class="carousel slide" data-bs-ride="carousel" style="background-color: #f0f0f0; border-radius: 15px; padding: 20px;">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/delhi5.jpg" class="d-block w-100" alt="Facility 1" style="height: 300px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="images/car2.jpg" class="d-block w-100" alt="Facility 2" style="height: 300px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="images/delhi3.jpg" class="d-block w-100" alt="Facility 3" style="height: 300px; object-fit: cover;">
            </div>
			 <div class="carousel-item">
                <img src="images/car5.jpg" class="d-block w-100" alt="Facility 3" style="height: 300px; object-fit: cover;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#facilitiesCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#facilitiesCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<section class="container my-5"data-aos="fade-up">
    <h3 class="text-center text-primary mb-4">Explore Tour Booking</h3>
    <div class="row g-4 align-items-stretch">
        <!-- Tour Booking Form -->
        <div class="col-md-6 d-flex">
            <div class="service-card w-100">
                <div class="service-form">
                        <h4>Tour Booking</h4>
                    <form action="" method="POST" onsubmit="return validateTourBooking();">
                        <input type="text" class="form-control mb-2" name="name" placeholder="Your Name" required>
                        <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>

                        <!-- Destination Dropdown -->
                        <select name="destination" id="destination" class="form-control mb-2" onchange="updateTourAmount()" required>
                            <option value="">Select Destination</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Agra">Agra</option>
                            <option value="Jaipur">Jaipur</option>
                        </select>

                        <input type="date" class="form-control mb-2" name="tour_date" required>

                        <!-- Show calculated amount -->
                        <div id="tour-amount-display" class="mb-2 text-success fw-bold">Amount: ₹0</div>

                        <!-- Hidden input to send total_amount -->
                        <input type="hidden" name="total_amount" id="tour-amount">

                        <button type="submit" class="btn btn-primary w-100">Book Tour</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Tour Booking Images -->
        <div class="col-md-6">
            <div class="row row-cols-2 g-2 h-100">
                <div class="col"><img src="images/delhi1.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Tour 1"></div>
                <div class="col"><img src="images/delhi2.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Tour 2"></div>
                <div class="col"><img src="images/delhi3.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Tour 3"></div>
                <div class="col"><img src="images/delhi5.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Tour 4"></div>
            </div>
        </div>
    </div>
</section>


<section class="container my-5">
    <h3 class="text-center text-primary mb-4">Explore Car Booking</h3>
    <div class="row g-4 align-items-stretch">
        <!-- Car Booking Form -->
        <div class="col-md-6 d-flex">
            <div class="service-card w-100">
                <div class="service-form">
                    <h4>Car Booking</h4>
                    <form action="" method="POST">
                        <input type="text" class="form-control mb-2" name="name" placeholder="Your Name" required>
                  <label>Pickup Location:</label>
<select name="pickup_location" id="pickup_location" class="form-control mb-2" required>
  <option value="">Select Pickup</option>
  <option value="Delhi">Delhi</option>
  <option value="Jaipur">Jaipur</option>
  <option value="Agra">Agra</option>
</select>

<label>Drop Location:</label>
<select name="drop_location" id="drop_location" class="form-control mb-2" required>
  <option value="">Select Drop</option>
  <option value="Delhi">Delhi</option>
  <option value="Jaipur">Jaipur</option>
  <option value="Agra">Agra</option>
</select>
<label>Pickup Time:</label>
<input type="datetime-local" name="pickup_time" class="form-control mb-2" required>

<label>Total Amount (INR):</label>
<input type="text" name="total_amount" id="total_amount" class="form-control mb-2" readonly>




                        <button type="submit" class="btn btn-primary w-100">Book Car</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Car Booking Images -->
        <div class="col-md-6">
            <div class="row row-cols-2 g-2 h-100">
                <div class="col"><img src="images/car1.png" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Car 1"></div>
                <div class="col"><img src="images/car3.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Car 2"></div>
                <div class="col"><img src="images/car5.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Car 3"></div>
                <div class="col"><img src="images/car2.jpg" class="img-fluid rounded w-100 h-100 object-fit-cover" alt="Car 4"></div>
            </div>
        </div>
    </div>
</section>




<!-- Footer Section -->
<footer class="bg-dark text-white text-center py-5 mt-4">
    <div class="container">
        <div class="row">
            <!-- Mini About Us Section -->
            <div class="col-md-4 mb-4">
                <h5 class="text-primary">About Luxury Room</h5>
                <p>Luxury Room provides the best accommodation experience with top-tier services. Enjoy a comfortable and luxurious stay at our properties.</p>
            </div>

            <!-- Contact and Social Links -->
            <div class="col-md-4 mb-4">
                <h5 class="text-primary">Get In Touch</h5>
                <p><i class="fas fa-envelope"></i> support@luxuryroom.com</p>
                <p><i class="fas fa-phone-alt"></i> +91 9876543210</p>
                <div class="social-icons mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin fa-2x"></i></a>
                </div>
            </div>

            <!-- Quick Call to Action -->
            <div class="col-md-4 mb-4">
                <h5 class="text-primary">Ready to Book?</h5>
                <p>Get the best rooms and offers by booking with us today!</p>
                <a href="rooms.php" class="btn btn-light">Book Now</a>
            </div>
        </div>

        <!-- Payment Methods & Privacy Links -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="payment-methods">
                    <h5 class="text-primary">We Accept</h5>
                    <div class="d-flex justify-content-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Paytm_logo.png/640px-Paytm_logo.png" alt="UPI" width="50" class="me-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/0e/UPI.jpg" alt="PayPal" width="50">
                    </div>
                </div>
                <div class="privacy-links mt-3">
                    <a href="privacy-policy.php" class="text-white me-4">Privacy Policy</a>
                    <a href="terms-of-service.php" class="text-white">Terms of Service</a>
                </div>
            </div>
        </div>

        <!-- Copyright Section -->
        <div class="mt-4">
            <p>&copy; 2025 Luxury Room. All rights reserved.</p>
        </div>
    </div>
</footer>
<script>
  const rates = {
    "Delhi-Jaipur": 2000,
    "Delhi-Agra": 1800,
    "Jaipur-Agra": 2200,
    "Jaipur-Delhi": 2000,
    "Agra-Delhi": 1800,
    "Agra-Jaipur": 2200
  };

  const pickup = document.getElementById("pickup_location");
  const drop = document.getElementById("drop_location");
  const totalAmount = document.getElementById("total_amount");

  function calculateAmount() {
    const from = pickup.value;
    const to = drop.value;

    if (from === "" || to === "") {
      totalAmount.value = "";
      return;
    }

    if (from === to) {
      totalAmount.value = "0";
      alert("Pickup and drop location cannot be the same.");
    } else {
      const key = `${from}-${to}`;
      totalAmount.value = rates[key] || "Not Available";
    }
  }

  pickup.addEventListener("change", calculateAmount);
  drop.addEventListener("change", calculateAmount);
  
  function updateTourAmount() {
    const destination = document.getElementById('destination').value;
    const amountDisplay = document.getElementById('tour-amount-display');
    const amountInput = document.getElementById('tour-amount');

    const prices = {
        "Delhi": 1500,
        "Agra": 1800,
        "Jaipur": 2200
    };

    const amount = prices[destination] || 0;
    amountDisplay.textContent = `Amount: ₹${amount}`;
    amountInput.value = amount;
}

function validateTourBooking() {
    const destination = document.getElementById('destination').value;
    if (!destination) {
        alert("Please select a destination.");
        return false;
    }
    return true;
}

    function validateCarBooking() {
        const pickup = document.getElementById('pickup_location').value;
        const drop = document.getElementById('drop_location').value;

        if (!pickup) {
            alert('Please select a pickup location.');
            return false;
        }
        if (!drop) {
            alert('Please select a drop location.');
            return false;
        }
        if (pickup === drop) {
            alert('Pickup and drop locations cannot be the same.');
            return false;
        }
        const amount = parseInt(document.getElementById('car-amount').value);
        if (amount <= 0) {
            alert('Invalid amount for car booking.');
            return false;
        }
        return true;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>

</body>
</html>
