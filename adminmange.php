<?php
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['admin'])) {
    header('Location: adminlogin.php');
    exit();
}

$conn = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Handle adding new admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_username'], $_POST['add_password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['add_username']);
    $password = password_hash($_POST['add_password'], PASSWORD_BCRYPT); // Hash the password before storing

    $query = "INSERT INTO adminlogin (username, password) VALUES ('$username', '$password')";
    
    if (mysqli_query($conn, $query)) {
        $add_success = "New admin added successfully.";
    } else {
        $add_error = "Error: " . mysqli_error($conn);
    }
}

// Handle removing an admin
if (isset($_POST['remove_admin_id'])) {
    $admin_id = mysqli_real_escape_string($conn, $_POST['remove_admin_id']);
    
    // Prevent removing the current logged-in admin
    if ($_SESSION['admin_id'] == $admin_id) {
        $remove_error = "You cannot remove your own admin account.";
    } else {
        // Delete admin from the database
        $query = "DELETE FROM adminlogin WHERE id = '$admin_id'";
        if (mysqli_query($conn, $query)) {
            $remove_success = "Admin removed successfully.";
        } else {
            $remove_error = "Error: " . mysqli_error($conn);
        }
    }
}

// Fetch all admins to display for removal
$query = "SELECT * FROM adminlogin";
$result = mysqli_query($conn, $query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Admins</title>
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
            <div class="dashboard-header">
                Welcome to Manage Admin Section, <?= htmlspecialchars($_SESSION["admin"]) ?>
            </div>

            <!-- Add Admin Section -->
            <div class="mt-4">
                <h5>Add New Admin</h5>
                <?php if (isset($add_success)) echo "<div class='alert alert-success'>$add_success</div>"; ?>
                <?php if (isset($add_error)) echo "<div class='alert alert-danger'>$add_error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="add_username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="add_username" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="add_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Admin</button>
                </form>
            </div>

            <!-- Remove Admin Section -->
            <div class="mt-4">
                <h5>Remove Admin</h5>
                <?php if (isset($remove_success)) echo "<div class='alert alert-success'>$remove_success</div>"; ?>
                <?php if (isset($remove_error)) echo "<div class='alert alert-danger'>$remove_error</div>"; ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Admin ID</th>
                            <th>Username</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($admin = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $admin['id']; ?></td>
                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="remove_admin_id" value="<?php echo $admin['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this admin?')">Remove</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
