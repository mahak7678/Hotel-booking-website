<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: adminlogin.php");
    exit();
}

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Delete booking
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM tour_bookings WHERE id = $id";
    if (mysqli_query($con, $deleteQuery)) {
        echo "<script>alert('Tour booking deleted successfully'); window.location.href='tour_booking_management.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to delete booking');</script>";
    }
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="tour_bookings.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['Name', 'Email', 'Destination', 'Tour Date', 'Booking Time','total_amount']);

    $search = $_GET['search'] ?? '';
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';

    $conditions = [];
    if (!empty($search)) {
        $safe_search = mysqli_real_escape_string($con, $search);
        $conditions[] = "(name LIKE '%$safe_search%' OR email LIKE '%$safe_search%' OR destination LIKE '%$safe_search%')";
    }
    if (!empty($from) && !empty($to)) {
        $conditions[] = "tour_date BETWEEN '$from' AND '$to'";
    }
    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $result = mysqli_query($con, "SELECT name, email, destination, tour_date, booking_time,total_amount FROM tour_bookings $where");
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Fetch bookings
$search = $_GET['search'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$conditions = [];
if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($con, $search);
    $conditions[] = "(name LIKE '%$safe_search%' OR email LIKE '%$safe_search%' OR destination LIKE '%$safe_search%')";
}
if (!empty($from) && !empty($to)) {
    $conditions[] = "tour_date BETWEEN '$from' AND '$to'";
}
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$query = "SELECT * FROM tour_bookings $where ORDER BY booking_time DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Tour Booking Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .sidebar {
        background-color: #0d6efd;
        height: 100vh;
        color: white;
        padding: 20px;
    }
    .sidebar a {
        display: block;
        color: white;
        margin-bottom: 15px;
        text-decoration: none;
    }
    .sidebar a:hover {
        text-decoration: underline;
    }
    .main-content {
        padding: 30px;
    }
    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
    }
    .btn-delete:hover {
        background-color: #c82333;
    }
  </style>
</head>
<body>
  <header class="bg-primary text-white sticky-top shadow">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fas fa-plane-departure me-2"></i>Tour Booking Management</a>
      </div>
    </nav>
  </header>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
      <h3>Admin Panel</h3>
      <a href="dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
	   <a href="feedback_management.php"><i class="fas fa-comments me-2"></i>Feedback Management</a>
      <a href="add_room.php"><i class="fas fa-door-open me-2"></i>Room Management</a>
      <a href="user.php"><i class="fas fa-credit-card me-2"></i>User management</a>
      <a href="bookingmanage.php"><i class="fas fa-tags me-2"></i>Room Booking management</a>
	  	  <a href="tour_booking_management.php"><i class="fas fa-plane-departure me-2"></i>Tour Booking Management</a>
	  	  <a href="car_booking_management.php"><i class="fas fa-car me-2"></i> Car Booking Management</a>
	  <a href="subscriber_management.php"><i class="fas fa-envelope-open-text me-2"></i>Subscriber Management</a>
      <a href="adminmange.php"><i class="fas fa-users me-2"></i>Admin Management</a>
	    <a href="javascript:void(0);" onclick="confirmLogout()"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>

<script>
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = 'adminlogin.php'; // Redirect to logout.php if confirmed
  }
}
</script>

    </div>

    <!-- Main Content -->
    <div class="col-md-9 main-content">
      <h2 class="mb-4">Tour Booking Management</h2>

      <!-- Filter Form -->
      <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control" placeholder="Search (Name, Email, Destination)" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
          <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-2">
          <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
        </div>
        <div class="col-md-3 text-end">
          <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" class="btn btn-success">
            <i class="fas fa-file-csv me-1"></i>Export to CSV
          </a>
        </div>
      </form>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Destination</th>
              <th>Tour Date</th>
              <th>Booking Time</th>
			  <th>Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['destination']) ?></td>
                  <td><?= htmlspecialchars($row['tour_date']) ?></td>
                  <td><?= htmlspecialchars($row['booking_time']) ?></td>
				  <td><?= htmlspecialchars($row['total_amount']) ?></td>
                  <td>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center">No records found</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
