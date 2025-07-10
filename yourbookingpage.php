<?php
session_start();

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get logged-in user's email from session
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Please log in to view your bookings.'); window.location.href='login.php';</script>";
    exit;
}
$user_email = $_SESSION['user_email'];

// Cancel room
if (isset($_POST['cancel_booking'])) {
    $id = $_POST['cancel_id'];
    mysqli_query($con, "DELETE FROM bookingform WHERE id = $id AND email = '$user_email'");
    echo "<script>alert('Room booking cancelled!'); window.location.href='yourbookingpage.php';</script>";
    exit;
}

// Cancel tour
if (isset($_POST['cancel_tour'])) {
    $id = $_POST['tour_id'];
    mysqli_query($con, "DELETE FROM tour_bookings WHERE id = $id AND email = '$user_email'");
    echo "<script>alert('Tour booking cancelled!'); window.location.href='yourbookingpage.php';</script>";
    exit;
}

// Cancel car
if (isset($_POST['cancel_car'])) {
    $id = $_POST['car_id'];
    mysqli_query($con, "DELETE FROM car_bookings WHERE id = $id AND email = '$user_email'");
    echo "<script>alert('Car booking cancelled!'); window.location.href='yourbookingpage.php';</script>";
    exit;
}

// Queries — only fetch bookings for the logged-in user
$room_result = mysqli_query($con, "SELECT bookingform.*, rooms.image_path, rooms.price 
                                   FROM bookingform 
                                   LEFT JOIN rooms ON bookingform.room_id = rooms.id 
                                   WHERE bookingform.email = '$user_email' 
                                   ORDER BY bookingform.id DESC");

$tour_query = "SELECT id, name, email, destination, tour_date, total_amount 
               FROM tour_bookings 
               WHERE email = '$user_email' 
               ORDER BY id DESC";

$tour_result = mysqli_query($con, $tour_query);
if (!$tour_result) {
    die("Tour Query Failed: " . mysqli_error($con));
}

$car_query = "SELECT id, name, pickup_location, drop_location, pickup_time, total_amount 
              FROM car_bookings 
              WHERE email = '$user_email' 
              ORDER BY id DESC";

$car_result = mysqli_query($con, $car_query);
if (!$car_result) {
    die("Car Query Failed: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        h2, h3 {
            margin-top: 40px;
            text-align: center;
            color: #333;
        }
        table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        .btn-download {
            margin: 15px 0;
        }
        .search-box {
            margin-bottom: 10px;
        }
        img {
            border-radius: 6px;
        }
		 .navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
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
<div class="container">
    <!-- Room Bookings -->
    <h2>Your Room Bookings</h2>
    <input type="text" class="form-control search-box" id="roomSearch" placeholder="Search room bookings...">
    <button class="btn btn-success btn-download" onclick="downloadTable('roomTable')">Download CSV</button>
    <table class="table table-bordered" id="roomTable">
        <thead>
        <tr>
            <th>Name</th><th>Contact</th><th>Address</th>
            <th>Check-in</th><th>Check-out</th><th>Adults</th><th>Children</th><th>Image</th><th>Price</th><th>Payment</th><th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($room_result)): ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['contact'] ?></td><td><?= $row['address'] ?></td>
                <td><?= $row['check_in'] ?></td><td><?= $row['check_out'] ?></td>
                <td><?= $row['adults'] ?></td><td><?= $row['childrens'] ?></td>
                <td><img src="admin/<?= $row['image_path'] ?>" width="80" height="60"></td>
				<td><?= $row['price'] ?></td>
               <td>
    <?= $row['payment_status'] ?>
    <?php if ($row['payment_status'] === 'Pending'): ?>
        <br>
        <form method="GET" action="paymentpage.php">
            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn btn-primary btn-sm mt-1">Pay Now</button>
        </form>
    <?php endif; ?>
</td>

                <td>
                    <form method="POST" onsubmit="return confirm('Cancel room booking?');">
                        <input type="hidden" name="cancel_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Tour Bookings -->
	<div>
<h3>Your Tour Bookings</h3>
<input type="text" class="form-control search-box" id="tourSearch" placeholder="Search tour bookings...">
<button class="btn btn-success btn-download" onclick="downloadTable('tourTable')">Download CSV</button>
<table class="table table-bordered" id="tourTable">
    <thead>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Destination</th><th>Tour Date</th><th>Amount</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php while ($tour = mysqli_fetch_assoc($tour_result)): ?>
        <tr>
            <td><?= $tour['id'] ?></td><td><?= $tour['name'] ?></td><td><?= $tour['email'] ?></td>
            <td><?= $tour['destination'] ?></td><td><?= $tour['tour_date'] ?></td>
            <td><?= $tour['total_amount'] ?></td> <!-- Tour Amount -->
            <td>
                <form method="POST" onsubmit="return confirm('Cancel tour booking?');">
                    <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                    <button type="submit" name="cancel_tour" class="btn btn-danger btn-sm">Cancel</button>
                </form>
				 <form method="GET" action="tour_payment.php" style="display:inline-block; margin-left: 5px;">
 <input type="hidden" name="booking_id" value="<?= $tour['id'] ?>">

        <button type="submit" class="btn btn-primary btn-sm">Pay Now</button>
    </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<!-- Car Bookings -->
<<!-- Car Bookings -->
<h3>Your Car Bookings</h3>
<input type="text" class="form-control search-box" id="carSearch" placeholder="Search car bookings...">
<button class="btn btn-success btn-download" onclick="downloadTable('carTable')">Download CSV</button>

<?php
// Debugging output
echo "<!-- Logged-in user email: $user_email -->";

// Updated query to ensure case-insensitive email match
$car_query = "SELECT id, name, pickup_location, drop_location, pickup_time, total_amount 
              FROM car_bookings 
              WHERE LOWER(email) = LOWER('$user_email') 
              ORDER BY id DESC";
$car_result = mysqli_query($con, $car_query);

if (!$car_result) {
    echo "<div class='alert alert-danger'>Car Query Failed: " . mysqli_error($con) . "</div>";
} elseif (mysqli_num_rows($car_result) === 0) {
    echo "<div class='alert alert-warning'>No car bookings found for your account.</div>";
}
?>

<table class="table table-bordered" id="carTable">
    <thead>
        <tr><th>ID</th><th>Name</th><th>Pickup</th><th>Drop</th><th>Pickup Time</th><th>Amount</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php if ($car_result && mysqli_num_rows($car_result) > 0): ?>
        <?php while ($car = mysqli_fetch_assoc($car_result)): ?>
            <tr>
                <td><?= $car['id'] ?></td>
                <td><?= $car['name'] ?></td>
                <td><?= $car['pickup_location'] ?></td>
                <td><?= $car['drop_location'] ?></td>
                <td><?= $car['pickup_time'] ?></td>
                <td><?= $car['total_amount'] ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Cancel car booking?');" style="display:inline;">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <button type="submit" name="cancel_car" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                    <form method="GET" action="car_payment.php" style="display:inline-block; margin-left: 5px;">
                        <input type="hidden" name="booking_id" value="<?= $car['id'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Pay Now</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Scripts -->
<script>
    function addSearch(inputId, tableId) {
        document.getElementById(inputId).addEventListener("keyup", function () {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll(`#${tableId} tbody tr`);
            rows.forEach(row => {
                row.style.display = [...row.children].some(cell =>
                    cell.textContent.toLowerCase().includes(filter)) ? "" : "none";
            });
        });
    }

    addSearch("roomSearch", "roomTable");
    addSearch("tourSearch", "tourTable");
    addSearch("carSearch", "carTable");

    function downloadTable(tableId) {
        let table = document.getElementById(tableId);
        let rows = table.querySelectorAll("tr");
        let csv = [];
        rows.forEach(row => {
            let cols = row.querySelectorAll("td, th");
            let rowData = [];
            cols.forEach(col => rowData.push(`"${col.innerText}"`));
            csv.push(rowData.join(","));
        });

        let blob = new Blob([csv.join("\n")], {type: 'text/csv'});
        let link = document.createElement("a");
        link.download = `${tableId}.csv`;
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }
</script>
</body>
</html>

<?php mysqli_close($con); ?>
