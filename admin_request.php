<?php
session_start();

require 'db_connect.php'; 

$current_page = basename($_SERVER['PHP_SELF']);

try {
    // Query to fetch requests with user details, purpose, and academic year
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
    WHERE 
        r.status = 'pending'
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sql_2 = "
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
    WHERE 
        r.status = 'approved'
";
$stmt = $pdo->prepare($sql_2);
$stmt->execute();
$approved_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sql_3 = "
    SELECT 
        r.id, 
        r.request_type, 
        r.academic_year, 
        r.purpose, 
        r.timestamp, 
        r.status, 
        r.rejection_reason,  -- Add rejection_reason here
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
    WHERE 
        r.status = 'rejected'
";
$stmt = $pdo->prepare($sql_3);
$stmt->execute();
$rejected_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);




    // Query to fetch notifications for the logged-in user
    $userId = $_SESSION['user_id'];
    $sql = "
        SELECT * 
        FROM notifications 
        WHERE user_id = :user_id 
        ORDER BY created_at DESC
    "; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
}

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

  
          <!-- Layout container -->
          <div class="layout-page">  
          <?php include 'admin_template/admin_navbar.php'; ?>

  
  
            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
  
              <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-2"><span class="text-muted fw-light">Request Report /</span>  Manage Request</h4>


              <div class="row">
                <div class="col-xl-12">
                  <div class="nav-align-top mb-4">
                  <ul class="nav nav-pills mb-3" role="tablist">
                      <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                            Pending Request
                        </button>
                      </li>
                
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-approve" aria-controls="navs-pills-top-approve" aria-selected="false">
                            Approve Request
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-disapprove" aria-controls="navs-pills-top-disapprove" aria-selected="false">
                            Disapproved Request
                        </button>
                      </li>
    

                  </ul>
                  <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                  
                      <div class="input-group input-group-merge mb-3 search-input">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search Pending..."
                                aria-label="Search..."
                                id="searchPend"
                                onkeyup="searchTable('pendingTable', 'searchPend')"
                            />
                        </div>

                          <table class="table table-hover" id="pendingTable">
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
                                    <tr>
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


                      <div class="tab-pane fade show" id="navs-pills-top-approve" role="tabpanel">
                      <div class="input-group input-group-merge mb-3 search-input">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search Approve..."
                                aria-label="Search..."
                                id="searchapprove"
                                onkeyup="searchTable('approveTable', 'searchapprove')"
                            />
                        </div>

                          <table class="table table-hover" id="approveTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Requester Name</th>
                                    <th>Request Type</th>
                                    <th>Academic Year</th> <!-- New Column -->
                                    <th>Purpose</th> <!-- New Column -->
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approved_requests as $request): ?>
                                    <tr>
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
                                        <td>
                                            <?php if ($status === 'pending'): ?>
                                                <button type="button" class="btn btn-success btn-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#approveModal" 
                                                    onclick='populateApproveModal(<?php echo json_encode($request); ?>)'>
                                                    Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#disapproveModal" onclick="setRequestId(<?php echo htmlspecialchars($request['id']); ?>)">
                                                    Disapprove
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                        <div class="tab-pane fade show" id="navs-pills-top-disapprove" role="tabpanel">
                        <div class="input-group input-group-merge mb-3 search-input">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search Disapproved..."
                                aria-label="Search..."
                                id="searchdisapprove"
                                onkeyup="searchTable('disapproveTable', 'searchdisapprove')"
                            />
                        </div>

                          <table class="table table-hover" id="disapproveTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Requester Name</th>
                                    <th>Request Type</th>
                                    <th>Academic Year</th> <!-- New Column -->
                                    <th>Purpose</th> <!-- New Column -->
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rejected_requests as $request): ?>
                                    <tr>
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
                                    

                                        <td class="text-danger fw-bold">
                                            <?php echo htmlspecialchars($request['rejection_reason']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    

       


                    


                  </div>
                  </div>
                </div>

              </div>

       
              
                
          
              </div>
           
  
               <!-- Disapprove Modal -->
               <div class="modal fade" id="disapproveModal" tabindex="-1" aria-labelledby="disapproveModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="disapproveModalLabel">Disapprove Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="disapprove_request.php" method="POST" id="disapproveForm">
                                        <input type="hidden" name="request_id" id="request_id">
                                        <div class="mb-3">
                                            <label for="disapprove_reason" class="form-label">Reason for Disapproval</label>
                                            <select class="form-select" id="disapprove_reason" name="disapprove_reason" required>
                                                <option value="" disabled selected>Select a reason</option>
                                                <option value="Incomplete documentation">Incomplete documentation</option>
                                                <option value="Wrong information">Wrong information</option>
                                                <option value="Not eligible">Not eligible</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100">Submit Disapproval</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="approve_request.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input for request ID -->
                    <input type="hidden" name="request_id" id="approveRequestId">
                    
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="approveFullName" class="form-label">Full Name</label>
                        <input type="text" id="approveFullName" class="form-control" readonly>
                    </div>

                    <!-- Course -->
                    <div class="mb-3">
                        <label for="approveCourse" class="form-label">Course</label>
                        <input type="text" id="approveCourse" class="form-control" readonly>
                    </div>

                    <!-- Year & Section -->
                    <div class="mb-3">
                        <label for="approveYearSection" class="form-label">Year & Section</label>
                        <input type="text" id="approveYearSection" class="form-control" readonly>
                    </div>
                    
                    <!-- Student Number -->
                    <div class="mb-3">
                        <label for="approveStudentNo" class="form-label">Student Number</label>
                        <input type="text" id="approveStudentNo" class="form-control" readonly>
                    </div>

                    <!-- Request Type -->
                    <div class="mb-3">
                        <label for="approveRequestType" class="form-label">Request Type</label>
                        <input type="text" id="approveRequestType" class="form-control" readonly>
                    </div>

                    <!-- Academic Year -->
                    <div class="mb-3">
                        <label for="approveAcademicYear" class="form-label">Academic Year</label>
                        <input type="text" id="approveAcademicYear" class="form-control" readonly>
                    </div>

                    <!-- Purpose -->
                    <div class="mb-3">
                        <label for="approvePurpose" class="form-label">Purpose</label>
                        <textarea id="approvePurpose" class="form-control" rows="3" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Approve</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
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


    <script>
        function setRequestId(requestId) {
            document.getElementById('request_id').value = requestId;
        }
    </script>


<script>
function populateApproveModal(request) {
    document.getElementById('approveRequestId').value = request.id || '';
    document.getElementById('approveFullName').value = `${request.first_name || 'N/A'} ${request.last_name || 'N/A'}`;
    document.getElementById('approveCourse').value = request.course || 'N/A';
    document.getElementById('approveYearSection').value = `${request.year_level || 'N/A'} - ${request.section || 'N/A'}`;
    document.getElementById('approveStudentNo').value = request.student_no || 'N/A';
    document.getElementById('approveRequestType').value = request.request_type || 'N/A';
    document.getElementById('approveAcademicYear').value = request.academic_year || 'N/A';
    document.getElementById('approvePurpose').value = request.purpose || 'N/A';
}



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


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listeners to save the active tab in localStorage
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                const activeTab = e.target.getAttribute('data-bs-target');
                console.log("Saving active tab:", activeTab); // Debugging
                localStorage.setItem('activeTab', activeTab);
            });
        });

        // Retrieve the active tab from localStorage
        const activeTab = localStorage.getItem('activeTab');
        console.log("Stored active tab:", activeTab); // Debugging
        if (activeTab) {
            // Activate the stored tab if it exists
            const tabToActivate = document.querySelector(`[data-bs-target="${activeTab}"]`);
            if (tabToActivate) {
                const bootstrapTab = new bootstrap.Tab(tabToActivate); // Bootstrap's Tab instance
                bootstrapTab.show();
            } else {
                console.warn("Stored tab not found, activating the first tab as fallback.");
                activateFirstTab(); // Fallback to the first tab
            }
        } else {
            // Fallback to the first tab if no activeTab is stored
            console.warn("No active tab stored, activating the first tab.");
            activateFirstTab();
        }

        // Helper function to activate the first tab
        function activateFirstTab() {
            const firstTab = document.querySelector('[data-bs-toggle="tab"]');
            if (firstTab) {
                const bootstrapTab = new bootstrap.Tab(firstTab);
                bootstrapTab.show();
            }
        }
    });
</script>

</body>
</html>