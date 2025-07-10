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

// Fetch all feedback
$feedbacks = [];
$query = "SELECT id, name, email, rating, comments, submitted_at FROM feedback ORDER BY submitted_at DESC";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $feedbacks[] = $row;
    }
}

// Delete feedback
if (isset($_GET['delete'])) {
    $feedback_id = $_GET['delete'];
    $delete_query = "DELETE FROM feedback WHERE id = $feedback_id";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Feedback deleted successfully'); window.location.href='feedback_management.php';</script>";
    } else {
        echo "<script>alert('Error deleting feedback');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Feedback Management - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
    .feedback-table th, .feedback-table td {
      vertical-align: middle;
    }
    .btn-delete {
      color: white;
      background-color: #dc3545;
      border: none;
      cursor: pointer;
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
      <a class="navbar-brand" href="#"><i class="fas fa-comments me-2"></i> Feedback Management</a>
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
        Welcome to Feedback Management, <?= htmlspecialchars($_SESSION["admin"]) ?>
      </div>

      <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">Feedback List</div>
                <div class="card-body">
                    <?php if (count($feedbacks) > 0): ?>
                    <table class="table table-bordered feedback-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Rating</th>
                                <th>Comments</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedbacks as $feedback): ?>
                                <tr>
                                    <td><?= htmlspecialchars($feedback['name']) ?></td>
                                    <td><?= htmlspecialchars($feedback['email']) ?></td>
                                    <td><?= $feedback['rating'] ?></td>
                                    <td><?= nl2br(htmlspecialchars($feedback['comments'])) ?></td>
                                    <td><?= date('Y-m-d H:i:s', strtotime($feedback['submitted_at'])) ?></td>
                                    <td>
                                        <a href="?delete=<?= $feedback['id'] ?>" class="btn-delete btn btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p>No feedback available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
