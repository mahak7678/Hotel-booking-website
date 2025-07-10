<?php 
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$searchQuery = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$priceFilter = isset($_GET['price_range']) ? (int)$_GET['price_range'] : 0;

$query = "SELECT * FROM rooms WHERE (name LIKE '%$searchQuery%' OR facilities LIKE '%$searchQuery%')";
if ($priceFilter) {
    $query .= " AND price <= $priceFilter";
}

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Handle AJAX Room Search
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    while ($room = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col-md-4">
            <div class="card mb-3 img-hover-trigger">
                <img src="admin/<?php echo htmlspecialchars($room['image_path']); ?>" class="card-img-top" alt="Room Image">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h3>
                    <p><strong>₹<?php echo number_format($room['price'], 2); ?> per night</strong></p>
                    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                    <p><strong>Facilities:</strong> <?php echo htmlspecialchars($room['facilities']); ?></p>
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="bookingform.php" method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            <input type="hidden" name="room_image" value="<?php echo 'uploads/' . htmlspecialchars($room['image_path']); ?>">
                            <button type="submit" class="btn btn-primary w-100">Book Now</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary w-100">Login to Book</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    exit(); // Only return room cards for AJAX request
}

// Handle Newsletter Subscription
if (isset($_POST['subscribe_btn'])) {
    $email = mysqli_real_escape_string($con, $_POST['subscriber_email']);
    $query = "INSERT INTO subscribe (email) VALUES ('$email')";
    mysqli_query($con, $query);
    $_SESSION['subscribed'] = true;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get rooms to display
$roomsQuery = "SELECT * FROM rooms";
$roomsResult = mysqli_query($con, $roomsQuery);
?>


<!-- HTML PART -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Luxury Room.com - Rooms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<!-- AOS Animation Library -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <style>
        .card {
            height: 100%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            z-index: 2;
        }
        .navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
        }
        .filter-container {
            margin-bottom: 30px;
        }
	.carousel .card {
    background: #f9f9f9;
    border-radius: 15px;
}
#map {
    width: 100%;
    height: 450px;
    border: none;
    margin-bottom: 50px;
}
.wishlist-btn i {
    color: red; /* This will make the heart icon red */
}

.wishlist-btn:hover i {
    color: darkred	; /* Changes the color on hover for a nice effect */
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card[data-aos] {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 1s ease forwards;
}

.card:hover {
    transform: scale(1.05) rotate(-0.5deg);
    transition: all 0.4s ease-in-out;
    z-index: 2;
}

.wishlist-btn i {
    transition: transform 0.3s ease;
}

.wishlist-btn:hover i {
    transform: scale(1.2);
    color: darkred;
}

.btn-primary, .btn-outline-secondary {
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn:hover {
    transform: scale(1.02);
}

.card-title {
    transition: color 0.3s ease;
}

.card-title:hover {
    color: #0d6efd;
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


<div class="container my-4">
    <!-- Filter Form -->
<div class="filter-container">
    <form id="searchForm" method="GET" action="rooms.php">
        <div class="d-flex">
            <input 
                type="text" 
                name="search" 
                class="form-control me-2" 
                placeholder="Search rooms" 
                id="searchQuery" 
                value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">

            <input 
                type="number" 
                name="price_range" 
                class="form-control me-2" 
                id="priceFilter" 
                placeholder="Enter max price" 
                value="<?php echo isset($priceFilter) ? htmlspecialchars($priceFilter) : ''; ?>">

            <select 
                id="currencySelector" 
                name="currency" 
                class="form-control me-2">
                <option value="INR" <?php echo (isset($_GET['currency']) && $_GET['currency'] === 'INR') ? 'selected' : ''; ?>>INR</option>
                <option value="USD" <?php echo (isset($_GET['currency']) && $_GET['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                <option value="EUR" <?php echo (isset($_GET['currency']) && $_GET['currency'] === 'EUR') ? 'selected' : ''; ?>>EUR</option>
            </select>

            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
</div>

<script>
    // Auto-submit on currency or price change
    document.getElementById('currencySelector').addEventListener('change', function () {
        document.getElementById('searchForm').submit();
    });

    document.getElementById('priceFilter').addEventListener('change', function () {
        document.getElementById('searchForm').submit();
    });
</script>


    <!-- Room Listings -->
    <div id="room-list" class="row g-3">
        <?php while ($room = mysqli_fetch_assoc($result)): ?>
    <div class="col-md-4 d-flex" data-aos="fade-up">
        <div class="card mb-3 img-hover-trigger d-flex flex-column w-100">
            <img src="admin/<?php echo htmlspecialchars($room['image_path']); ?>" class="card-img-top" alt="Room Image">
            <div class="card-body d-flex flex-column">
                <h3 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h3>
                <p><strong>₹<?php echo number_format($room['price'], 2); ?> per night</strong></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                <p><strong>Facilities:</strong> <?php echo htmlspecialchars($room['facilities']); ?></p>
                <div class="mt-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="bookingform.php" method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            <input type="hidden" name="room_image" value="<?php echo 'uploads/' . htmlspecialchars($room['image_path']); ?>">
                            <button type="submit" class="btn btn-primary w-100">Book Now</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary w-100">Login to Book</a>
                    <?php endif; ?>

                    <!-- Add to Wishlist Button -->
                    <button class="btn btn-outline-secondary w-100 mt-2 wishlist-btn" data-room-id="<?php echo $room['id']; ?>"> <i class="fas fa-heart"></i>Add to Wishlist</button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>

</div>

<!-- Guest Reviews Section (No Carousel) -->
<div class="container my-5">
    <h2 class="text-center mb-4">What Our Guests Say</h2>
    <div class="row g-4">
        <?php
        $fakeReviews = [
            ['name' => 'Priya Sharma', 'review' => 'Absolutely loved the stay! The rooms were clean and luxurious.', 'rating' => 5],
            ['name' => 'Aman Verma', 'review' => 'Great value for money. The location is perfect for city travel.', 'rating' => 4],
            ['name' => 'Sneha Kapoor', 'review' => 'The food service was amazing and the staff were very polite.', 'rating' => 5],
            ['name' => 'Rahul Khanna', 'review' => 'Had a pleasant experience. Would definitely come again.', 'rating' => 4],
            ['name' => 'Nisha Mehta', 'review' => 'Room view was breathtaking. Enjoyed every bit of the stay.', 'rating' => 5],
            ['name' => 'Karan Singh', 'review' => 'Decent stay but expected more in room service.', 'rating' => 3],
        ];

        foreach ($fakeReviews as $review):
        ?>
        <div class="col-md-6 col-lg-4" data-aos="zoom-in">

            <div class="card h-100 p-3 shadow-sm">
                <h5 class="text-center"><?php echo $review['name']; ?></h5>
                <div class="text-center mb-2">
                    <?php
                    for ($i = 0; $i < 5; $i++) {
                        echo $i < $review['rating']
                            ? '<i class="fas fa-star text-warning"></i>'
                            : '<i class="far fa-star text-muted"></i>';
                    }
                    ?>
                </div>
                <p class="text-center mb-0"><?php echo $review['review']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>


<!-- Google Map Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Our Location</h2>
    <div class="ratio ratio-16x9">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14011.905780465136!2d77.1034909!3d28.7040591!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d015aa8a64ae1%3A0xa7ab9b3e5e1b13e4!2sBadli%2C%20Delhi%2C%20110042!5e0!3m2!1sen!2sin!4v1681111111111"
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>


<footer class="bg-primary text-white pt-5 pb-4 mt-4 position-relative">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Luxury Room.com</h5>
                <p>Your best choice for luxury and comfort. Book your dream stay today.</p>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="mhk.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="rooms.php" class="text-white text-decoration-none">Rooms</a></li>
                    <li><a href="facilities.php" class="text-white text-decoration-none">Facilities</a></li>
                    <li><a href="yourbookingpage.php" class="text-white text-decoration-none">My Bookings</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Contact</h5>
                <p><i class="fas fa-envelope me-2"></i> support@luxuryroom.com</p>
                <p><i class="fas fa-phone me-2"></i> +91 9876543210</p>
                <p><i class="fas fa-map-marker-alt me-2"></i> Delhi, India</p>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Subscribe</h5>
                <form method="POST">
                    <div class="mb-2">
                        <input type="email" name="subscriber_email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" name="subscribe_btn" class="btn btn-light btn-sm w-100">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="text-center mt-3">
            <p class="mt-3 mb-0">&copy; 2025 Luxury Room.com | All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
    const searchQueryInput = document.getElementById('searchQuery');
    const priceFilterSelect = document.getElementById('priceFilter');

    searchQueryInput.addEventListener('input', searchRooms);
    priceFilterSelect.addEventListener('change', searchRooms);

    function searchRooms() {
        const search = searchQueryInput.value;
        const price = priceFilterSelect.value;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'rooms.php?search=' + encodeURIComponent(search) + '&price_range=' + price, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('room-list').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
	  const currencyRates = {
        INR: 1,
        USD: 0.012, // Example rate
        EUR: 0.011 // Example rate
    };

    const currencySymbol = {
        INR: '₹',
        USD: '$',
        EUR: '€'
    };

    document.getElementById('currencySelector').addEventListener('change', function () {
        const selectedCurrency = this.value;
        const priceElements = document.querySelectorAll('.card .card-body p strong');

        priceElements.forEach(priceElement => {
            const inrText = priceElement.textContent.match(/₹([\d,]+)/);
            if (inrText) {
                const inrValue = parseFloat(inrText[1].replace(/,/g, ''));
                const converted = (inrValue * currencyRates[selectedCurrency]).toFixed(2);
                priceElement.textContent = `${currencySymbol[selectedCurrency]}${converted} per night`;
            }
        });
    });
</script>


<?php
if (isset($_SESSION['subscribed'])) {
    echo "<script>alert('Thank you for subscribing!');</script>";
    unset($_SESSION['subscribed']);
}
mysqli_close($con);
?>
<script>
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function () {
            const roomId = this.getAttribute('data-room-id');

            // Send AJAX request to add room to wishlist
            fetch('add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'room_id=' + roomId
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // You can replace this with a toast message or modal
            });
        });
    });
</script>

<!-- AOS JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>

</body>
</html>
