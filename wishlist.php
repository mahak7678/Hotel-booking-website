<?php
session_start();
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get rooms from wishlist
$sessionId = session_id();
$query = "SELECT r.* FROM rooms r JOIN wishlist w ON r.id = w.room_id WHERE w.session_id = '$sessionId'";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
    $roomId = $_POST['room_id'];
    $removeQuery = "DELETE FROM wishlist WHERE session_id = '$sessionId' AND room_id = '$roomId'";
    mysqli_query($con, $removeQuery);
    header("Location: wishlist.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Wishlist - Luxury Room.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
        }
        .card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-body {
            flex-grow: 1;
        }
        .remove-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .remove-btn:hover {
            background-color: #ff1a1a;
        }
        .card-img-top {
            height: 200px; /* Set a fixed height for the images */
            object-fit: cover; /* Ensure images fill the space without distortion */
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
                        <li class="nav-item"><a href="wishlist.php" class="nav-link"><i class="fas fa-heart"></i> Wishlist</a></li>
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
    <h2>Your Wishlist</h2>
    <div class="row">
        <?php while ($room = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="admin/<?php echo htmlspecialchars($room['image_path']); ?>" class="card-img-top" alt="Room Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                        <p><strong>₹<?php echo number_format($room['price'], 2); ?> per night</strong></p>
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            <button type="submit" name="remove" class="remove-btn">Remove from Wishlist</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>
