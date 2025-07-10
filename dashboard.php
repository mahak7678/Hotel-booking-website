<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: adminlogin.php");
    exit();
}

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Total Bookings
$totalBookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bookingform"))['total'] ?? 0;

// Monthly Bookings (Chart Data)
$monthlyResult = mysqli_query($con, "SELECT MONTH(check_in) AS month, COUNT(*) AS bookings FROM bookingform GROUP BY MONTH(check_in)");
$monthlyData = [];
while ($row = mysqli_fetch_assoc($monthlyResult)) {
    $monthlyData[] = $row;
}

// Rooms Info
$totalRooms = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM rooms"))['total'] ?? 0;
$bookedRooms = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bookingform WHERE check_in <= CURDATE() AND check_out >= CURDATE()"))['total'] ?? 0;
$availableRooms = $totalRooms - $bookedRooms;

// Payment Status Count
$confirmed = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bookingform WHERE payment_status = 'Confirmed'"))['total'] ?? 0;
$canceled = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM bookingform WHERE payment_status = 'Canceled'"))['total'] ?? 0;
$pending = $totalBookings - ($confirmed + $canceled);

// Chart Labels
$months = array_map(fn($data) => date("F", mktime(0, 0, 0, $data['month'], 10)), $monthlyData);
$bookings = array_map(fn($data) => $data['bookings'], $monthlyData);

// Room Availability (Dummy Data)
$roomAvailabilityData = [
  'January' => 5, 'February' => 3, 'March' => 6, 'April' => 2,
  'May' => 7, 'June' => 4, 'July' => 6
];

// Payment Status for Chart
$paymentStatusData = [
  'Confirmed' => $confirmed,
  'Canceled' => $canceled,
  'Pending' => $pending
];

// Total Bookings by Room Data
$roomBookingsResult = mysqli_query($con, "SELECT room_id, COUNT(*) AS bookings FROM bookingform GROUP BY room_id");
$roomBookingsData = [];
while ($row = mysqli_fetch_assoc($roomBookingsResult)) {
    $roomBookingsData[] = $row;
}

// Room IDs and Bookings for Chart
$roomIds = array_map(fn($data) => "Room " . $data['room_id'], $roomBookingsData);
$roomBookings = array_map(fn($data) => $data['bookings'], $roomBookingsData);

// Recent Bookings
$recentBookings = mysqli_query($con, "SELECT name, room_id, check_in, check_out, payment_status FROM bookingform ORDER BY id DESC LIMIT 5");

// Monthly Tour Bookings (Chart Data)
$monthlyTourResult = mysqli_query($con, "SELECT MONTH(tour_date) AS month, COUNT(*) AS bookings FROM tour_bookings GROUP BY MONTH(tour_date)");
$monthlyTourData = [];
while ($row = mysqli_fetch_assoc($monthlyTourResult)) {
    $monthlyTourData[] = $row;
}


// Monthly Car Bookings (Chart Data)
$monthlyCarResult = mysqli_query($con, "SELECT MONTH(pickup_time) AS month, COUNT(*) AS bookings FROM car_bookings GROUP BY MONTH(pickup_time)");
$monthlyCarData = [];
while ($row = mysqli_fetch_assoc($monthlyCarResult)) {
    $monthlyCarData[] = $row;
}



// Feedback Counts by Rating
$feedbackResult = mysqli_query($con, "SELECT rating, COUNT(*) AS count FROM feedback GROUP BY rating");
$feedbackData = [];
while ($row = mysqli_fetch_assoc($feedbackResult)) {
    $feedbackData[] = $row;
}

// Total Tour Bookings
$totalTourBookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM tour_bookings"))['total'] ?? 0;

// Total Car Bookings
$totalCarBookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM car_bookings"))['total'] ?? 0;

// Total Registered Users
$totalUsers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM register"))['total'] ?? 0;

$totalsub = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM subscribe"))['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - Luxury.com</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Roboto', sans-serif;
    }
    .sidebar {
      background-color: var(--bs-primary);
      color: white;
      height: 200vh;
      padding: 30px 20px;
    }
    .sidebar h2 { font-size: 28px; margin-bottom: 40px; }
    .sidebar a {
      color: white;
      display: block;
      margin-bottom: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    .sidebar a:hover { text-decoration: underline; }
    .main-content { padding: 40px; flex-grow: 1; }
    .dashboard-header { font-size: 36px; font-weight: bold; margin-bottom: 20px; }
    canvas { max-height: 300px; }
  </style>
</head>
<body>
<header class="bg-primary text-white sticky-top shadow">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
    </div>
  </nav>
</header>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar d-none d-md-block">
      <h2>Admin Panel</h2>
      <a href="dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
      <a href="feedback_management.php"><i class="fas fa-comments me-2"></i>Feedback Management</a>
      <a href="add_room.php"><i class="fas fa-door-open me-2"></i>Room Management</a>
      <a href="user.php"><i class="fas fa-credit-card me-2"></i>User Management</a>
      <a href="bookingmanage.php"><i class="fas fa-tags me-2"></i>Room Booking Management</a>
      <a href="tour_booking_management.php"><i class="fas fa-plane-departure me-2"></i>Tour Booking Management</a>
      <a href="car_booking_management.php"><i class="fas fa-car me-2"></i>Car Booking Management</a>
      <a href="subscriber_management.php"><i class="fas fa-envelope-open-text me-2"></i>Subscriber Management</a>
      <a href="adminmange.php"><i class="fas fa-users me-2"></i>Admin Management</a>
      <a href="javascript:void(0);" onclick="confirmLogout()"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>
	<script>
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = 'logout.php'; // Redirect to logout.php if confirmed
  }
}
</script>


    <!-- Main Content -->
    <div class="col-md-9 main-content">
      <div class="dashboard-header">
        Welcome to Admin Dashboard, <?= htmlspecialchars($_SESSION["admin"]) ?>
      </div>

      <div class="row mb-4">
     
        <!-- Total Bookings Counter -->
<div class="col-md-6 mb-4">
  <div class="card shadow text-center">
    <div class="card-header bg-info text-white">
      <i class="fas fa-calendar-check me-2"></i>Total Bookings
    </div>
    <div class="card-body">
      <h1 class="display-1">
        <i class="fas fa-book me-2 text-info"></i><?= $totalBookings ?>
      </h1>
      <p class="lead">Bookings so far</p>
    </div>
  </div>
</div>

        <!-- Available Rooms Counter -->
      <div class="col-md-6 mb-4">
  <div class="card shadow text-center">
    <div class="card-header bg-success text-white">
      <i class="fas fa-bed me-2"></i>Available Rooms
    </div>
    <div class="card-body">
      <h1 class="display-1">
        <i class="fas fa-door-open me-2 text-success"></i><?= $availableRooms ?>
      </h1>
      <p class="lead">Rooms available right now</p>
    </div>
  </div>
</div>

<!-- Tour Bookings Counter -->
<div class="col-md-6 mb-4">
  <div class="card shadow text-center">
    <div class="card-header bg-warning text-white">
      <i class="fas fa-plane-departure me-2"></i>Total Tour Bookings
    </div>
    <div class="card-body">
      <h1 class="display-1">
        <i class="fas fa-globe me-2 text-warning"></i><?= $totalTourBookings ?>
      </h1>
      <p class="lead">Tours booked so far</p>
    </div>
  </div>
</div>

<!-- Car Bookings Counter -->
<div class="col-md-6 mb-4">
  <div class="card shadow text-center">
    <div class="card-header bg-primary text-white">
      <i class="fas fa-car me-2"></i>Total Car Bookings
    </div>
    <div class="card-body">
      <h1 class="display-1">
        <i class="fas fa-taxi me-2 text-primary"></i><?= $totalCarBookings ?>
      </h1>
      <p class="lead">Cars booked so far</p>
    </div>
  </div>
</div>

<!-- Registered Users Counter -->
<div class="col-md-6 mb-4">
  <div class="card shadow text-center">
    <div class="card-header bg-dark text-white">
      <i class="fas fa-users me-2"></i>Registered Users
    </div>
    <div class="card-body">
      <h1 class="display-1">
        <i class="fas fa-user-friends me-2 text-dark"></i><?= $totalUsers ?>
      </h1>
      <p class="lead">Users registered so far</p>
    </div>
  </div>
</div>

<!-- Registered Users Counter -->
<div class="col-md-6 mb-4">
  <div class="card shadow text-center">
    <div class="card-header bg-danger text-white">
      <i class="fas fa-users me-2"></i>Subscriber User
    </div>
    <div class="card-body">
      <h1 class="display-1">
        <i class="fas fa-user-friends me-2 text-danger"></i><?= $totalsub ?>
      </h1>
      <p class="lead">Users registered so far</p>
    </div>
  </div>
</div>


        <!-- Recent Bookings Table -->
        <div class="col-md-12 mb-4">
          <div class="card shadow">
            <div class="card-header bg-secondary text-white">Recent Bookings</div>
            <div class="card-body table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="table-light">
                  <tr><th>Name</th><th>Room</th><th>Check-In</th><th>Check-Out</th><th>Status</th></tr>
                </thead>
                <tbody>
                  <?php while ($row = mysqli_fetch_assoc($recentBookings)) : ?>
                    <tr>
                      <td><?= htmlspecialchars($row['name']) ?></td>
                      <td><?= "Room " . htmlspecialchars($row['room_id']) ?></td>
                      <td><?= htmlspecialchars($row['check_in']) ?></td>
                      <td><?= htmlspecialchars($row['check_out']) ?></td>
                      <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

<script>
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = 'logout.php';
  }
}

const ctxMonthly = document.getElementById('monthlyBookingsChart').getContext('2d');
const monthlyBookingsChart = new Chart(ctxMonthly, {
  type: 'line',
  data: {
    labels: <?= json_encode($months) ?>,
    datasets: [{
      label: 'Bookings',
      data: <?= json_encode($bookings) ?>,
      borderColor: 'rgb(54, 162, 235)',
      backgroundColor: 'rgba(54, 162, 235, 0.2)',
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  }
});

const ctxPayment = document.getElementById('paymentStatusChart').getContext('2d');
const paymentStatusChart = new Chart(ctxPayment, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_keys($paymentStatusData)) ?>,
    datasets: [{
      label: 'Payment Status',
      data: <?= json_encode(array_values($paymentStatusData)) ?>,
      backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
      hoverOffset: 10
    }]
  },
  options: {
    responsive: true
  }
});

const ctxRoomBookings = document.getElementById('roomBookingsChart').getContext('2d');
const roomBookingsChart = new Chart(ctxRoomBookings, {
  type: 'bar',
  data: {
    labels: <?= json_encode($roomIds) ?>,
    datasets: [{
      label: 'Bookings',
      data: <?= json_encode($roomBookings) ?>,
      backgroundColor: 'rgba(75, 192, 192, 0.7)',
      borderColor: 'rgba(75, 192, 192, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true, stepSize: 1 }
    }
  }
});

const ctxFeedback = document.getElementById('feedbackChart').getContext('2d');
const feedbackChart = new Chart(ctxFeedback, {
  type: 'pie',
  data: {
    labels: <?= json_encode($ratings) ?>,
    datasets: [{
      label: 'Feedback Ratings',
      data: <?= json_encode($feedbackCounts) ?>,
      backgroundColor: [
        '#4caf50',
        '#2196f3',
        '#ff9800',
        '#f44336',
        '#9c27b0'
      ]
    }]
  },
  options: {
    responsive: true
  }
});

const ctxMonthlyTour = document.getElementById('monthlyTourBookingsChart').getContext('2d');
const monthlyTourBookingsChart = new Chart(ctxMonthlyTour, {
  type: 'line',
  data: {
    labels: <?= json_encode($monthsTour) ?>,
    datasets: [{
      label: 'Tour Bookings',
      data: <?= json_encode($bookingsTour) ?>,
      borderColor: 'rgb(255, 99, 132)',
      backgroundColor: 'rgba(255, 99, 132, 0.2)',
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  }
});

const ctxMonthlyCar = document.getElementById('monthlyCarBookingsChart').getContext('2d');
const monthlyCarBookingsChart = new Chart(ctxMonthlyCar, {
  type: 'line',
  data: {
    labels: <?= json_encode($monthsCar) ?>,
    datasets: [{
      label: 'Car Bookings',
      data: <?= json_encode($bookingsCar) ?>,
      borderColor: 'rgb(255, 206, 86)',
      backgroundColor: 'rgba(255, 206, 86, 0.2)',
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>
</body>
</html>
