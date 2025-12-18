<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
require_once 'db_connect.php';


if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];

  $stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = :user_id ORDER BY timestamp DESC");
  $stmt->execute(['user_id' => $userId]);
  $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Fetch notifications for the logged-in user
  $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC"; 
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['user_id' => $userId]);
  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch pending request count for the logged-in user
    $sqlPendingRequests = "SELECT COUNT(*) as count FROM requests WHERE status = 'pending' AND user_id = :user_id";
    $stmtPendingRequests = $pdo->prepare($sqlPendingRequests);
    $stmtPendingRequests->execute(['user_id' => $userId]);
    $pendingRequestsCount = $stmtPendingRequests->fetchColumn();
  
    // Fetch approved request count for the logged-in user
    $sqlApprovedRequests = "SELECT COUNT(*) as count FROM requests WHERE status = 'approved' AND user_id = :user_id";
    $stmtApprovedRequests = $pdo->prepare($sqlApprovedRequests);
    $stmtApprovedRequests->execute(['user_id' => $userId]);
    $approvedRequestsCount = $stmtApprovedRequests->fetchColumn();
  
    // Fetch disapproved request count for the logged-in user
    $sqlRejectedRequests = "SELECT COUNT(*) as count FROM requests WHERE status = 'rejected' AND user_id = :user_id";
    $stmtRejectedRequests = $pdo->prepare($sqlRejectedRequests);
    $stmtRejectedRequests->execute(['user_id' => $userId]);
    $rejectedRequestsCount = $stmtRejectedRequests->fetchColumn();

} else {
  echo "Please log in to view your requests.";
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="static/core.css" />
    <link rel="stylesheet" href="static/theme-default.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
          <!-- Menu -->
          <?php include 'user_template/user_sidebar.php'; ?>

          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
  
           <?php  include 'user_template/user_navbar.php'; ?>
  
            <!-- / Navbar -->
  
            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->

  
              <div class="container-xxl flex-grow-1 container-p-y">
                

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


                <div class="row">
                    <div class="col-4 mb-3">
                        <div class="card bg-white">
                            <div class="card-header d-flex align-items-center fw-bold">
                                <div class="icon-box bg-success text-white rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                    <i class='bx bx-check-circle icon-large text-white'></i>
                                </div>
                                Approved Request
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($approvedRequestsCount); ?></h5>
                                <p class="card-text text-center">Number of approved requests</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-4 mb-3">
                        <div class="card bg-white">
                            <div class="card-header d-flex align-items-center fw-bold">
                                <div class="icon-box bg-warning text-dark rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                    <i class='bx bx-hourglass icon-large text-white'></i>
                                </div>
                                Pending Requests
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($pendingRequestsCount); ?></h5>
                                <p class="card-text text-center">Number of pending requests</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-4 mb-3">
                        <div class="card bg-white">
                            <div class="card-header d-flex align-items-center fw-bold">
                                <div class="icon-box bg-danger text-white rounded d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                    <i class='bx bx-x-circle icon-large'></i>
                                </div>
                                Denied Requests
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fs-3 text-center"><?php echo htmlspecialchars($rejectedRequestsCount); ?></h5>
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

                    <?php if (!empty($requests)): ?>
                        <table class="table table-hover" id="histTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Request Type</th>
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr data-status="<?php echo htmlspecialchars($request['status']); ?>">
                                        <td><?php echo htmlspecialchars($request['id']); ?></td>
                                        <td><?php echo htmlspecialchars($request['request_type']); ?></td>
                                        <td><?php 
                                            $dateTime = new DateTime($request['timestamp']);
                                            echo htmlspecialchars($dateTime->format('F j, Y - g:i A'));?>
                                        </td>
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
                    <?php else: ?>
                        <p>You have no requests yet.</p>
                    <?php endif; ?>
                </div>





              </div>
           
  
  
              <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>
  
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
      
    <script src="static/js/main.js"></script>

    <script src="static/js/menu.js"></script>

    <script>
        document.getElementById('filterDropdown').addEventListener('change', function() {
            const filterValue = this.value; // Get selected value
            const rows = document.querySelectorAll('#histTable tbody tr');

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