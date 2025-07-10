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

// Delete user functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_email'])) {
    $delete_email = mysqli_real_escape_string($conn, $_POST['delete_user_email']);
    mysqli_query($conn, "DELETE FROM register WHERE emails = '$delete_email'");
    header("Location: user.php");
    exit();
}

// Fetch users from the database for both the table and export
$query = "SELECT * FROM register";
$users_result = mysqli_query($conn, $query);

// CSV Export functionality
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    // Reset the result set for export
    $export_result = mysqli_query($conn, $query);
    
    if ($export_result) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="users.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add header row
        fputcsv($output, ['First Name', 'Email', 'Password (Encrypted)', 'Security Answer']);
        
        // Add data rows
        while ($user = mysqli_fetch_assoc($export_result)) {
            fputcsv($output, $user);
        }
        
        fclose($output);
        exit();
    } else {
        echo "Error fetching users for export: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Management</title>
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

    .user-table-container {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      padding: 20px;
      overflow-x: auto;
    }
    .user-table th, .user-table td {
      vertical-align: middle;
      padding: 10px 15px !important;
      font-size: 14px;
    }
    .user-table th {
      background-color: #f7f9fc;
      color: #333;
    }
    .user-table td {
      background-color: #fafafa;
    }
    .user-table td form {
      margin: 0;
    }
  </style>
</head>
<body>

<header class="bg-primary text-white sticky-top shadow">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-users me-2"></i> Admin - User Management</a>
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
      <div class="dashboard-header">Registered Users</div>

      <div class="user-table-container">
        <h5 class="mb-3">All Users</h5>

        <!-- Search Bar -->
        <input type="text" class="form-control mb-3" id="searchInput" placeholder="Search by name or email..." onkeyup="filterTable()" />

        <!-- Export Button -->
        <a href="user.php?export=csv" class="btn btn-success mb-3">Export to CSV</a>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered user-table" id="userTable">
            <thead>
              <tr>
                <th onclick="sortTable(0)">Name</th>
                <th onclick="sortTable(1)">Email</th>
                <th>Password (Encrypted)</th>
                <th>Security Answer</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                <tr>
                  <td><?= htmlspecialchars($user['firstname']) ?></td>
                  <td><?= htmlspecialchars($user['emails']) ?></td>
                  <td style="word-break: break-all; max-width: 200px;">
                    <?= htmlspecialchars($user['passwords']) ?>
                  </td>
                  <td><?= htmlspecialchars($user['security_answer']) ?></td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Delete this user?');">
                      <input type="hidden" name="delete_user_email" value="<?= $user['emails'] ?>">
                      <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav>
          <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<script>
  // Sorting
  function sortTable(columnIndex) {
    const table = document.getElementById('userTable');
    const rows = Array.from(table.rows).slice(1); // Skip the header row
    const sortedRows = rows.sort((rowA, rowB) => {
      const cellA = rowA.cells[columnIndex].innerText;
      const cellB = rowB.cells[columnIndex].innerText;

      return cellA.localeCompare(cellB);
    });

    rows.forEach(row => row.remove()); // Remove existing rows
    sortedRows.forEach(row => table.appendChild(row)); // Append sorted rows
  }

  const rowsPerPage = 5;
  let currentPage = 1;
  let tableRows = Array.from(document.querySelectorAll('#userTable tbody tr'));

  function displayPage(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    tableRows.forEach((row, index) => {
      row.style.display = (index >= start && index < end) ? '' : 'none';
    });

    renderPagination();
  }

  function renderPagination() {
    const totalPages = Math.ceil(tableRows.length / rowsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
      pagination.innerHTML += `
        <li class="page-item ${i === currentPage ? 'active' : ''}">
          <a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>
        </li>
      `;
    }
  }

  function goToPage(page) {
    currentPage = page;
    displayPage(page);
  }

  function filterTable() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const filtered = tableRows.filter(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
      return text.includes(filter);
    });

    tableRows = filtered.length > 0 ? filtered : Array.from(document.querySelectorAll('#userTable tbody tr'));
    currentPage = 1;
    displayPage(currentPage);
  }

  // Initial display
  displayPage(currentPage);
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
