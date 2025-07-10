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

// Delete subscriber
if (isset($_GET['delete'])) {
    $emailToDelete = $_GET['delete'];
    $delete_query = "DELETE FROM subscribe WHERE email = '$emailToDelete'";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Subscriber deleted successfully'); window.location.href='subscriber_management.php';</script>";
    } else {
        echo "<script>alert('Error deleting subscriber');</script>";
    }
}

// Fetch all subscribers
$subscribers = [];
$result = mysqli_query($con, "SELECT email FROM subscribe ORDER BY email ASC");
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $subscribers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subscriber Management - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    body, html {
      margin: 0;
      font-family: 'Roboto', sans-serif;
    }
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
    .main-content {
      padding: 40px;
      flex-grow: 1;
    }
    .dashboard-header {
      font-size: 36px;
      font-weight: bold;
      margin-bottom: 20px;
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
        <a class="navbar-brand" href="#"><i class="fas fa-envelope-open-text me-2"></i> Subscriber Management</a>
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
    window.location.href = 'logout.php'; // Redirect to logout.php if confirmed
  }
}
</script>

      </div>

      <!-- Main Content -->
      <div class="col-md-9 main-content">
        <div class="dashboard-header">
          Welcome, <?= htmlspecialchars($_SESSION["admin"]) ?>
        </div>

        <div class="card shadow">
          <div class="card-header bg-info text-white">
            Subscriber List
          </div>
          <div class="card-body">
            <?php if (count($subscribers) > 0): ?>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($subscribers as $subscriber): ?>
                    <tr>
                      <td><?= htmlspecialchars($subscriber['email']) ?></td>
                      <td>
                        <a href="?delete=<?= urlencode($subscriber['email']) ?>" class="btn btn-sm btn-delete">
                          <i class="fas fa-trash"></i> Delete
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p>No subscribers found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>