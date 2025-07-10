<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: adminlogin.php');
    exit();
}

$conn = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Delete booking functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $delete_booking_id = mysqli_real_escape_string($conn, $_POST['delete_booking_id']);
    mysqli_query($conn, "DELETE FROM bookingform WHERE id = '$delete_booking_id'");
    header("Location: bookingmanage.php");
    exit();
}

// Update booking status functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking_status_id'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['update_booking_status_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    mysqli_query($conn, "UPDATE bookingform SET payment_status = '$new_status' WHERE id = '$booking_id'");
    header("Location: bookingmanage.php");
    exit();
}

// Fetch bookings for display
$query = "SELECT * FROM bookingform";
$bookings_result = mysqli_query($conn, $query);

// CSV Export functionality
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $export_result = mysqli_query($conn, $query);
    
    if ($export_result) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add header row
        fputcsv($output, ['Name', 'Contact', 'Email', 'Address', 'Zipcode', 'Check-in Date', 'Check-out Date', 'Adults', 'Children', 'Room ID', 'Payment Status', 'Order ID']);
        
        // Add data rows
        while ($booking = mysqli_fetch_assoc($export_result)) {
            fputcsv($output, $booking);
        }
        
        fclose($output);
        exit();
    } else {
        echo "Error fetching bookings for export: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Booking Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    .sidebar {
      background-color: var(--bs-primary);
      color: white;
      height: 100vh;
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
    .dashboard-header { font-size: 28px; font-weight: bold; margin-bottom: 20px; }

    .booking-table-container {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      padding: 20px;
      overflow-x: auto;
    }
    .booking-table th, .booking-table td {
      vertical-align: middle;
      padding: 10px 15px !important;
      font-size: 14px;
    }
    .booking-table th {
      background-color: #f7f9fc;
      color: #333;
    }
    .booking-table td {
      background-color: #fafafa;
    }
    .booking-table td form {
      margin: 0;
    }
  </style>
</head>
<body>

<header class="bg-primary text-white sticky-top shadow">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-hotel me-2"></i> Admin - Booking Management</a>
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
      <div class="dashboard-header">Bookings</div>

      <div class="booking-table-container">
        <h5 class="mb-3">All Bookings</h5>

        <!-- Search Bar -->
        <input type="text" class="form-control mb-3" id="searchInput" placeholder="Search by name, email, or room..." onkeyup="filterTable()" />

        <!-- Export Button -->
        <a href="bookings.php?export=csv" class="btn btn-success mb-3">Export to CSV</a>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered booking-table" id="bookingTable">
            <thead>
              <tr>
                <th onclick="sortTable(0)">Name</th>
                <!--<th onclick="sortTable(1)">Email</th>-->
                <th>Room ID</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Payment Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="bookingTableBody">
              <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                <tr>
                  <td><?= htmlspecialchars($booking['name']) ?></td>
                  <!--<td><?= htmlspecialchars($booking['email']) ?></td>-->
                  <td><?= htmlspecialchars($booking['room_id']) ?></td>
                  <td><?= htmlspecialchars($booking['check_in']) ?></td>
                  <td><?= htmlspecialchars($booking['check_out']) ?></td>
                  <td><?= ucfirst($booking['payment_status']) ?></td>	
                  <td>
                    <form method="POST" onsubmit="return confirm('Delete this booking?');">
                      <input type="hidden" name="delete_booking_id" value="<?= $booking['id'] ?>">
                      <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                    <form method="POST">
                      <input type="hidden" name="update_booking_status_id" value="<?= $booking['id'] ?>">
                      <select name="new_status" class="form-select form-select-sm">
                        <option value="Pending" <?= $booking['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Completed" <?= $booking['payment_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                      </select>
                      <button class="btn btn-sm btn-outline-primary">Update Status</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  // Sorting
  function sortTable(columnIndex) {
    const table = document.getElementById('bookingTable');
    const rows = Array.from(table.rows).slice(1); // Skip the header row
    const sortedRows = rows.sort((rowA, rowB) => {
      const cellA = rowA.cells[columnIndex].innerText;
      const cellB = rowB.cells[columnIndex].innerText;

      return cellA.localeCompare(cellB);
    });

    rows.forEach(row => row.remove()); // Remove existing rows
    sortedRows.forEach(row => table.appendChild(row)); // Append sorted rows
  }

  // Filter functionality
  function filterTable() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#bookingTableBody tr');

    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  }
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
