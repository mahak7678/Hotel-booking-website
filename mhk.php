<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Booking Availability Check
if (isset($_POST['submit'])) {
    $check_in = $_POST['checkin'];
    $check_out = $_POST['checkout'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];

    $checkQuery = "SELECT COUNT(*) AS total_bookings FROM bookings WHERE check_in <= '$check_out' AND check_out >= '$check_in'";
    $result = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($result);

    if ($row['total_bookings'] < 3) {
        $_SESSION['checkin'] = $check_in;
        $_SESSION['checkout'] = $check_out;
        $_SESSION['adults'] = $adults;
        $_SESSION['children'] = $children;

        echo "<script>
                if (confirm('Room is available! You must be registered or logged in before booking.')) {
                    window.location.href = 'register.php';
                }
              </script>";
    } else {
        echo "<script>alert('Sorry, no rooms are available for the selected dates.');</script>";
    }
}

// Feedback Form Submission
if (isset($_POST['submit_feedback'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rating = $_POST['rating'];
    $comments = $_POST['review']; // 'review' from form maps to 'comments' column

    $query = "INSERT INTO feedback (name, email, rating, comments) VALUES ('$name', '$email', '$rating', '$comments')";

    if (mysqli_query($con, $query)) {
        echo "<script>alert('Thank you! Your feedback has been submitted.');  window.location.href = 'mhk.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Room.com</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    body {
        font-size: 16px;
        line-height: 1.5;
        color: #333;
    }
    .navbar-nav .nav-link {
        color: white !important;
        transition: color 0.3s ease-in-out;
    }
    .navbar-nav .nav-link:hover {
        color: #FFD700 !important;
    }
    #roomImageSlider,
    #roomImageSlider .carousel-inner,
    #roomImageSlider .carousel-item,
    #roomImageSlider .carousel-item img {
        height: 400px !important;
        max-height: 400px;
        object-fit: cover;
        width: 100%;
    }
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        padding: 15px;
    }
 .hero {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    color: white;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
}

.slider-bg {
    display: flex;
    height: 100%;
    width: 400%;
    animation: heroSlide 18s infinite ease-in-out;
}

.slider-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    flex-shrink: 0;
    opacity: 0.9;
}

@keyframes heroSlide {
    0%   { transform: translateX(0%); }
    25%  { transform: translateX(-100%); }
    50%  { transform: translateX(-200%); }
    75%  { transform: translateX(-300%); }
    100% { transform: translateX(0%); }
}

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 20px;
        animation: fadeInUp 1s ease-out forwards;
    }
    .hero p {
        font-size: 1.3rem;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .luxury-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }
    .why-choose-box {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

/* Common Hover Pop Effect */
.pop-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pop-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

/* For Why Choose Us cards specifically */
.why-choose-box {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.why-choose-box:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}
/* Match feedback form background to navbar's bg-primary color */
.feedback-form {
    background-color:  #2c3e50 /* Bootstrap primary color */
    color: white; /* Ensuring the text is visible */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
<section class="hero position-relative">
    <div class="slider-bg" id="heroSlider">
        <img src="https://images.pexels.com/photos/457882/pexels-photo-457882.jpeg" alt="Room 1">
        <img src="https://images.pexels.com/photos/1008155/pexels-photo-1008155.jpeg" alt="Room 2">
        <img src="https://images.pexels.com/photos/358528/pexels-photo-358528.jpeg" alt="Room 3">
        <img src="https://images.unsplash.com/photo-1548013146-72479768bada" alt="Room 4">
    </div>
    <div class="hero-content text-white text-center">
        <h1>Find your next stay</h1>
        <p>Search low prices on hotels, rooms, and much more...</p>
    </div>
</section>


<section class="container my-5">
    <h2 class="text-center mb-4">Check Booking Availability</h2>
    <form id="bookingForm" class="row g-3 bg-light p-4 rounded shadow" method="POST">
        <div class="col-md-3">
            <label for="checkin" class="form-label">Check-in</label>
            <input type="date" name="checkin" id="checkin" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label for="checkout" class="form-label">Check-out</label>
            <input type="date" name="checkout" id="checkout" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label for="adults" class="form-label">Adult</label>
            <select id="adults" name="adults" class="form-select">
                <option value="1">1 Adult</option>
                <option value="2">2 Adults</option>
                <option value="3">3 Adults</option>
                <option value="4">4 Adults</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="children" class="form-label">Children</label>
            <select id="children" name="children" class="form-select">
                <option value="0">0 Children</option>
                <option value="1">1 Child</option>
                <option value="2">2 Children</option>
                <option value="3">3 Children</option>
            </select>
        </div>
        <div class="col-12 text-center">
            <input type="submit" class="btn btn-primary" name="submit">
        </div>
    </form>
</section>

<section class="container my-5">
    <h2 class="mb-4 text-center">Explore Our Rooms</h2>
    <div id="roomImageSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
        <div class="carousel-inner rounded shadow">
            <div class="carousel-item active">
                <img src="admin/uploads/pic2.jpg"class="d-block w-100" alt="pic1">
            </div>
            <div class="carousel-item">
                <img src="admin/uploads/img4.jpg" class="d-block w-100" alt="Executive Suite">
            </div>
            <div class="carousel-item">
                <img src="admin/uploads/pic5.jpg" class="d-block w-100" alt="Standard Room">
            </div>
            <div class="carousel-item">
                <img src="admin/uploads/room4.jpg" class="d-block w-100" alt="Travel View">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#roomImageSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#roomImageSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<section class="container my-5">
    <h2 class="text-center mb-4">Experience the Luxury</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <img src="https://images.pexels.com/photos/271743/pexels-photo-271743.jpeg" class="img-fluid rounded shadow luxury-image pop-hover" alt="Spa">
        </div>
        <div class="col-md-4">
            <img src="https://images.pexels.com/photos/2102587/pexels-photo-2102587.jpeg" class="img-fluid rounded shadow luxury-image pop-hover" alt="Swimming Pool">
        </div>
        <div class="col-md-4">
            <img src="https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg" class="img-fluid rounded shadow luxury-image pop-hover" alt="Dining Experience">
        </div>
    </div>
</section>

<section class="container my-5">
    <h2 class="text-center mb-4">Why Choose Us?</h2>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="p-4 shadow rounded why-choose-box pop-hover">
                <i class="fas fa-concierge-bell fa-3x text-primary mb-3"></i>
                <h5>Exceptional Service</h5>
                <p>Our trained staff is available 24/7 to make your stay comfortable and memorable.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded why-choose-box pop-hover">
                <i class="fas fa-bed fa-3x text-primary mb-3"></i>
                <h5>Luxury Rooms</h5>
                <p>Enjoy spacious rooms with world-class amenities and premium furnishings.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded why-choose-box pop-hover">
                <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                <h5>Prime Locations</h5>
                <p>Our hotels are located in the heart of the city, close to top attractions and transport hubs.</p>
            </div>
        </div>
    </div>
</section>

<section class="container my-5">
    <h2 class="text-center mb-4">We Value Your Feedback</h2>
  <form method="POST" class="row g-3 bg-light p-4 rounded shadow">
    <div class="col-md-6">
        <label for="name" class="form-label">Your Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Your Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="col-md-12">
        <label for="rating" class="form-label">Rate Your Stay</label>
        <select name="rating" id="rating" class="form-select" required>
            <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
            <option value="4">⭐⭐⭐⭐ - Very Good</option>
            <option value="3">⭐⭐⭐ - Good</option>
            <option value="2">⭐⭐ - Fair</option>
            <option value="1">⭐ - Poor</option>
        </select>
    </div>
    <div class="col-md-12">
        <label for="review" class="form-label">Your Review</label>
        <textarea name="review" id="review" rows="4" class="form-control" required></textarea>
    </div>
    <div class="col-12 text-center">
        <button type="submit" name="submit_feedback" class="btn btn-success">Submit Review</button>
    </div>
</form>

</section>


<footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container text-md-left">
        <div class="row text-md-left">
            <!-- Brand and About -->
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Luxury Room.com</h5>
                <p>Experience luxury, comfort, and unforgettable moments in the heart of the city. We’re here to make your stay exceptional.</p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Quick Links</h5>
                <p><a href="rooms.php" class="text-white" style="text-decoration: none;">Rooms</a></p>
                <p><a href="facilities.php" class="text-white" style="text-decoration: none;">Facilities</a></p>
                <p><a href="register.php" class="text-white" style="text-decoration: none;">Register</a></p>
                <p><a href="login.php" class="text-white" style="text-decoration: none;">Login</a></p>
            </div>

            <!-- Contact -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Contact</h5>
                <p><i class="fas fa-home mr-3"></i> Delhi, India</p>
                <p><i class="fas fa-envelope mr-3"></i> luxury43@gmail.com</p>
                <p><i class="fas fa-phone mr-3"></i> +91 9876543210</p>
            </div>

            <!-- Social Media -->
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mt-3 text-center">
                <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Follow Us</h5>
                <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                <a href="#" class="text-white"><i class="fab fa-youtube fa-2x"></i></a>
            </div>
        </div>

        <hr class="mb-4 mt-4">

        <div class="row align-items-center">
            <div class="col-md-8 col-lg-8">
                <p class="text-center text-md-start">© 2025 Luxury Room.com | All Rights Reserved</p>
            </div>
        </div>
    </div>
</footer>


<script>
document.getElementById("bookingForm").addEventListener("submit", function(event) {
    var checkin = document.getElementById("checkin").value;
    var checkout = document.getElementById("checkout").value;
    if (new Date(checkout) <= new Date(checkin)) {
        alert("Check-out date must be after the check-in date.");
        event.preventDefault();
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
