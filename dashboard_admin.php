<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

require 'db_connect.php';


// Approved Users Count
$sqlApprovedUsers = "SELECT COUNT(*) as count FROM users WHERE role = 'user' AND is_approved = 'approved'";
$stmtApproved = $pdo->prepare($sqlApprovedUsers);
$stmtApproved->execute();
$approvedUsersCount = $stmtApproved->fetchColumn();

// Pending Users Count
$sqlPendingUsers = "SELECT COUNT(*) as count FROM users WHERE role = 'user' AND is_approved = 'pending'";
$stmtPending = $pdo->prepare($sqlPendingUsers);
$stmtPending->execute();
$pendingUsersCount = $stmtPending->fetchColumn();

// Denied Requests Count
$sqlDeniedRequests = "SELECT COUNT(*) as count FROM requests WHERE status = 'rejected'";
$stmtDenied = $pdo->prepare($sqlDeniedRequests);
$stmtDenied->execute();
$deniedRequestsCount = $stmtDenied->fetchColumn();

// Pending Requests Count
$sqlPendingRequests = "SELECT COUNT(*) as count FROM requests WHERE status = 'pending'";
$stmtPendingRequests = $pdo->prepare($sqlPendingRequests);
$stmtPendingRequests->execute();
$pendingRequestsCount = $stmtPendingRequests->fetchColumn();


$userId = $_SESSION['user_id'];

$sql = "
    SELECT 
        r.id, 
        r.request_type, 
        r.academic_year, 
        r.purpose, 
        r.timestamp, 
        r.status, 
        CONCAT(u.first_name, ' ', u.last_name) AS full_name,
        u.first_name, 
        u.last_name, 
        u.course, 
        u.section, 
        u.year_level, 
        u.student_no
    FROM 
        requests r
    JOIN 
        users u 
    ON 
        r.user_id = u.id
    ORDER BY 
        r.timestamp DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);





  // Fetch notifications for the logged-in user
  $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC"; // Ensure you have a created_at field
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['user_id' => $userId]);
  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="static/core.css" />
    <link rel="stylesheet" href="static/theme-default.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>
<body>
    <div class="layout-wrapper layout-content-navbar">


    <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="layout-container">
          <!-- Menu -->
  
          <?php include 'admin_template/admin_sidebar.php'; ?>
          <!-- / Menu -->
  
          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
  
            <?php include 'admin_template/admin_navbar.php'; ?>
  
            <!-- / Navbar -->
  
            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
  
              <div class="container-xxl flex-grow-1 container-p-y">
                
     
              
              <div class="row">
                <div class="col-md-3">
                    <div class="card bg-white mb-3">
                    <div class="card-header d-flex align-items-center fw-bold">
                            <div class="icon-box bg-success text-white rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                <i class='bx bx-check-circle icon-large text-white'></i>
                            </div>
                            Approved Users
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($approvedUsersCount); ?></h5>
                            <p class="card-text text-center">Number of approved users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white mb-3">
                    <div class="card-header d-flex align-items-center fw-bold">
                            <div class="icon-box bg-warning text-dark rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                <i class='bx bx-time-five icon-large text-white'></i>
                            </div>
                            Pending Users
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($pendingUsersCount); ?></h5>
                            <p class="card-text text-center">Number of pending users</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-white mb-3">
                    <div class="card-header d-flex align-items-center fw-bold">
                            <div class="icon-box bg-info text-dark rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                <i class='bx bx-hourglass icon-large text-white'></i>
                            </div>
                            Pending Requests
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($pendingRequestsCount); ?></h5>
                            <p class="card-text text-center">Number of pending req</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white mb-3">
                    <div class="card-header d-flex align-items-center fw-bold">
                            <div class="icon-box bg-danger text-white rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                <i class='bx bx-x-circle icon-large'></i>
                            </div>
                            Denied Requests
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($deniedRequestsCount); ?></h5>
                            <p class="card-text text-center">Number of denied requests</p>
                        </div>
                    </div>
                </div>
             
            </div>



              <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="card-header p-0">Request History</h3>
                </div>

                    <!-- Filter and Search Section -->
                    <div class="row align-items-center mb-3">
                        <!-- Search Bar -->
                        <div class="col-md-9">
                            <div class="input-group input-group-merge search-input">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Search Requests..."
                                    aria-label="Search..."
                                    id="searchHistory"
                                    onkeyup="searchTable('histTable', 'searchHistory')"
                                />
                            </div>
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="col-md-3 text-end">
                            <label for="filterDropdown" class="me-2">Filter by Req Type:</label>
                            <select id="filterDropdown" class="form-select d-inline-block" style="width: 150px;">
                                <option value="all">All</option>
                                <option value="approved">Approved</option>
                                <option value="pending">Pending</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <table class="table table-hover" id="histTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Requester Name</th>
                                    <th>Request Type</th>
                                    <th>Academic Year</th> <!-- New Column -->
                                    <th>Purpose</th> <!-- New Column -->
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr data-status="<?php echo htmlspecialchars($request['status']); ?>">
                                        <td><?php echo htmlspecialchars($request['id']); ?></td>
                                        <td><?php echo htmlspecialchars($request['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['request_type']); ?></td>
                                        <td><?php echo htmlspecialchars($request['academic_year']); ?></td> <!-- Display Academic Year -->
                                        <td><?php echo htmlspecialchars($request['purpose'] ?: 'N/A'); ?></td> <!-- Display Purpose or 'N/A' -->
                                        <td><?php echo date('F j, Y, g:i A', strtotime($request['timestamp'])); ?></td>
                                        <td>
                                            <?php 
                                            $status = htmlspecialchars($request['status']);
                                            switch ($status) {
                                                case 'approved':
                                                    echo '<span class="badge bg-success">' . $status . '</span>';
                                                    break;
                                                case 'pending':
                                                    echo '<span class="badge bg-warning text-dark">' . $status . '</span>';
                                                    break;
                                                case 'rejected':
                                                    echo '<span class="badge bg-danger">' . $status . '</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">' . $status . '</span>'; // For any other status
                                            }
                                            ?>
                                        </td>
                                    
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
           
  
  
              
              
                <div class="content-backdrop fade"></div>
            </div>

            </div>

            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>
  
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    document.getElementById('filterDropdown').addEventListener('change', function() {
    const filterValue = this.value; // Get selected value
    const rows = document.querySelectorAll('#histTable tbody tr'); // Change this line to match the correct table ID

    rows.forEach(row => {
        const status = row.getAttribute('data-status'); // Get the status of the row
        if (filterValue === 'all' || status === filterValue) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
});

    </script>

    <script>
        function searchTable(tableId, inputId) {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById(inputId);
    filter = input.value.toLowerCase();
    table = document.getElementById(tableId);
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows (skipping the header row)
    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td");
        var rowVisible = false;

        // Loop through each cell in the row
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;

                // Check if the search term matches any column, including the timestamp
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    rowVisible = true;
                    break;
                }
            }
        }

        // Show or hide the row based on the search input
        if (rowVisible) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}

    </script>


</body>
</html>