<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch only TOR requests for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = :user_id AND status = 'pending'");
    $stmt->execute(['user_id' => $userId]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = :user_id AND status = 'approved'");
    $stmt->execute(['user_id' => $userId]);
    $approvedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = :user_id AND status = 'rejected'");
    $stmt->execute(['user_id' => $userId]);
    $rejectedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch notifications for the logged-in user
    $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>User Request</title>
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
                <div class="col-xl-12">
                  <div class="nav-align-top mb-4">
                  <ul class="nav nav-pills mb-3" role="tablist">
                      <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-pending" aria-controls="navs-pills-top-home" aria-selected="true">
                            Pending Record
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-pending-approved" aria-controls="navs-pills-pending-seller" aria-selected="false">
                            Approved Record
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-pending-disapp" aria-controls="navs-pills-pending-seller" aria-selected="false">
                            Disapproved Record
                        </button>
                      </li>

                    

    

                  </ul>
                  <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-pills-pending" role="tabpanel">

                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-header px-0">Request Records</h3>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestFormModal">
                                Request Form
                            </button>
                        </div>

                        <div class="input-group input-group-merge mb-3 search-input">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search Pending..."
                                aria-label="Search..."
                                id="searchPending"
                                onkeyup="searchTable('pendingTable', 'searchPending')"
                            />
                        </div>

                        <?php if (!empty($requests)): ?>
                            <table class="table table-hover" id="pendingTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Request Type</th>
                                        <th>Timestamp</th>
                                        <th>Status</th>
                                        <th>Purpose</th>
                                        <th>Academic Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $request): ?>
                                        <tr>
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

                                            <td>
                                                <?php if ($request['purpose']): ?>
                                                    <?php echo htmlspecialchars($request['purpose']); ?>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>   
                                            <td><?php echo htmlspecialchars($request['academic_year']); ?></td>

                                        
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center fs-4 pt-4">You have no Pending requests.</p>
                        <?php endif; ?>

                  
                    
                      </div>

                      <div class="tab-pane fade" id="navs-pills-pending-approved" role="tabpanel">


                      <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-header px-0">Approved Records</h3>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestFormModal">
                                Request Form
                            </button>
                        </div>

                        <div class="input-group input-group-merge mb-3 search-input">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search Pending..."
                                aria-label="Search..."
                                id="searchApp"
                                onkeyup="searchTable('approveTable', 'searchApp')"
                            />
                        </div>

                        <?php if (!empty($approvedRequests)): ?>
                            <table class="table table-hover" id="approveTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Request Type</th>
                                        <th>Timestamp</th>
                                        <th>Status</th>
                                        <th>Purpose</th>
                                        <th>Academic Year</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($approvedRequests as $request): ?>
                                        <tr>
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

                                            <td>
                                                <?php if ($request['purpose']): ?>
                                                    <?php echo htmlspecialchars($request['purpose']); ?>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>   
                                            <td><?php echo htmlspecialchars($request['academic_year']); ?></td>

                                            <td>
                                                <?php if ($request['status'] === 'approved'): ?>
                                                    <a href="download_pdf.php?request_id=<?php echo $request['id']; ?>" class="btn btn-success">
                                                        View PDF
                                                    </a>
                                                <?php else: ?>
                                                    Not Available
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center fs-4 pt-4">You have no Approved requests.</p>
                        <?php endif; ?>


                        </div>


                      <div class="tab-pane fade" id="navs-pills-pending-disapp" role="tabpanel">

                      <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-header px-0">Disapproved Records</h3>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestFormModal">
                                Request Form
                            </button>
                        </div>
                            
                        <div class="input-group input-group-merge mb-3 search-input">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search Pending..."
                                aria-label="Search..."
                                id="searchDiss"
                                onkeyup="searchTable('dissTable', 'searchDiss')"
                            />
                        </div>

                        <?php if (!empty($rejectedRequests)): ?>
                            <table class="table table-hover" id="dissTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Request Type</th>
                                        <th>Timestamp</th>
                                        <th>Status</th>
                                        <th>Purpose</th>
                                        <th>Academic Year</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rejectedRequests as $request): ?>
                                        <tr>
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

                                            <td>
                                                <?php if ($request['purpose']): ?>
                                                    <?php echo htmlspecialchars($request['purpose']); ?>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>   
                                            <td><?php echo htmlspecialchars($request['academic_year']); ?></td>

                                            <td class="text-danger fw-bold">
                                            <?php echo htmlspecialchars($request['rejection_reason']); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center fs-4 pt-4">You have no Disapproved requests.</p>
                        <?php endif; ?>


                      </div>



                    


                  </div>
                  </div>
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

<!-- Modal Structure -->
<div class="modal fade" id="requestFormModal" tabindex="-1" aria-labelledby="requestFormModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestFormModalLabel">Request TOR Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form action="user_request.php" method="POST">
                    <!-- User Name -->
                    <div class="mb-3">
                        <label for="userDetails" class="form-label">Name and Student Number</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="userDetails" 
                            name="userDetails" 
                            value="<?php 
                                echo isset($_SESSION['name'], $_SESSION['student_no']) 
                                    ? htmlspecialchars($_SESSION['name']) . ' (' . htmlspecialchars($_SESSION['student_no']) . ')' 
                                    : 'Guest';
                            ?>" 
                            readonly
                        >
                    </div>

                    <!-- Form Type -->
                    <div class="mb-3">
                        <label for="formType" class="form-label">Select Form Type</label>
                        <select class="form-select" id="formType" name="formType" required>
                            <option value="" disabled selected>Select a form</option>
                            <option value="TOR">Transcript of Records (TOR)</option>
                            <option value="COR">Certificate of Registration (COR)</option>
                            <option value="COE">Certificate of Enrollment (COE)</option>
                            <option value="COG">Certificate of Grades (COG)</option>
                            <option value="DIPLOMA">Certificate of Graduation</option>
                        </select>
                    </div>

                    <!-- Year Range -->
                    <div class="mb-3">
                        <label for="yearRange" class="form-label">Select Academic Year</label>
                        <select class="form-select" id="yearRange" name="yearRange" required>
                            <option value="" disabled selected>Select a year</option>
                            <option value="2020-2021">2020-2021</option>
                            <option value="2021-2022">2021-2022</option>
                            <option value="2022-2023">2022-2023</option>

                            <option value="2023-2024">2023-2024</option>
                        </select>
                    </div>

                    <!-- Purpose of Request -->
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose of Request (Optional)</label>
                        <textarea 
                            class="form-control" 
                            id="purpose" 
                            name="purpose" 
                            rows="3" 
                            placeholder="Enter the purpose of your request (e.g., Scholarship, Employment, etc.)">
                        </textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
      
    <script src="static/js/main.js"></script>

    <script src="static/js/menu.js"></script>



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