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

// Add room functionality
// Add room functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && !isset($_POST['delete_room_id'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $facilities = mysqli_real_escape_string($conn, $_POST['facilities']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $availability_status = mysqli_real_escape_string($conn, $_POST['availability_status']);
    
    // Set default status as 'Available' (you can modify as needed)
    $status = 'Available';

    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = 'uploads/' . $image_name;
        move_uploaded_file($image_tmp, $image_path);
    }

    $query = "INSERT INTO rooms (name, room_type, price, status, description, facilities, image_path, availability_status) 
              VALUES ('$name', '$room_type', '$price', '$status', '$description', '$facilities', '$image_path', '$availability_status')";
    mysqli_query($conn, $query);

    header("Location: add_room.php");
    exit();
}



// Delete room functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_room_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_POST['delete_room_id']);

    $img_result = mysqli_query($conn, "SELECT image_path FROM rooms WHERE id = $delete_id");
    if ($img_row = mysqli_fetch_assoc($img_result)) {
        $img_path = $img_row['image_path'];
        if (file_exists($img_path)) {
            unlink($img_path); // delete image file
        }
    }

    mysqli_query($conn, "DELETE FROM rooms WHERE id = $delete_id");

    // Redirect after deletion
    header("Location: add_room.php");
    exit();
}

// Fetch all rooms
$result = mysqli_query($conn, "SELECT * FROM rooms ORDER BY id DESC");
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Add Room</title>
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
    canvas { max-height: 300px; }

    .room-img {
      width: 120px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
    }
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
    window.location.href = 'logout.php'; // Redirect to logout.php if confirmed
  }
}
</script>

    </div>

    <!-- Main Content -->
    <div class="col-md-9 main-content">
      <div class="dashboard-header">
        Welcome to Add & Delete Section, <?= htmlspecialchars($_SESSION["admin"]) ?>
      </div>

      <!-- Add Room Form -->
      <div class="form-section mb-4">
        <h4>Add New Room</h4>
        <form method="POST" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">Room Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="price" class="form-label">Price (₹)</label>
              <input type="number" name="price" class="form-control" required>
            </div>
            <div class="col-12">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" rows="3" class="form-control" required></textarea>
            </div>
			<div class="col-md-6">
              <label for="image" class="form-label">Facilites</label>
              <input type="text" name="facilities" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="image" class="form-label">Room Image</label>
              <input type="file" name="image" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="availability_status" class="form-label">Availability</label>
              <select name="availability_status" class="form-select" required>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
              </select>
            </div>
          </div>
          <button class="btn btn-primary mt-3">Add Room</button>
        </form>
      </div>

      <!-- Room List -->
      <div class="table-section">
        <h4>Existing Rooms</h4>
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Status</th>
              <th>Delete</th>
              <!-- Future: Add Edit here -->
            </tr>
          </thead>
          <tbody>
            <?php while ($room = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><img src="<?= htmlspecialchars($room['image_path']) ?>" class="room-img" alt="room image"></td>
                <td><?= htmlspecialchars($room['name']) ?></td>
                <td>₹<?= number_format($room['price']) ?></td>
                <td><?= ucfirst(htmlspecialchars($room['availability_status'])) ?></td>
                <td>
                  <form method="POST" onsubmit="return confirm('Delete this room?');">
                    <input type="hidden" name="delete_room_id" value="<?= $room['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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

<?php mysqli_close($conn); ?>
