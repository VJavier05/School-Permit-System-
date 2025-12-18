<?php
session_start();

require 'db_connect.php'; 

$current_page = basename($_SERVER['PHP_SELF']);

try {
    $sql = "
    SELECT 
        id, 
        CONCAT(first_name, ' ', last_name) AS name, 
        age, 
        gender, 
        phone, 
        course, 
        CONCAT(region, ', ', province, ', ', city, ', ', barangay) AS address 
    FROM users 
    WHERE role = 'user' AND is_approved = 'pending'
  ";

$stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sqlDisapproved = "
    SELECT 
        id, 
        CONCAT(first_name, ' ', last_name) AS name, 
        age, 
        gender, 
        phone, 
        course, 
        CONCAT(region, ', ', province, ', ', city, ', ', barangay) AS address 
    FROM users 
    WHERE role = 'user' AND is_approved = 'disapproved'
  ";
    $stmtDisapproved = $pdo->prepare($sqlDisapproved);
    $stmtDisapproved->execute();
    $disapprovedStudents = $stmtDisapproved->fetchAll(PDO::FETCH_ASSOC);


    $userId = $_SESSION['user_id'];

  // Fetch notifications for the logged-in user
  $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC"; // Ensure you have a created_at field
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
              <h4 class="fw-bold py-3 mb-2"><span class="text-muted fw-light">Admin Action /</span>  Manage Registration</h4>


              <div class="row">
                <div class="col-xl-12">
                  <div class="nav-align-top mb-4">
                  <ul class="nav nav-pills mb-3" role="tablist">
                      <li class="nav-item">
                      <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                          Pending User
                      </button>
                      </li>
                      <li class="nav-item">
                      <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-pending-seller" aria-controls="navs-pills-pending-seller" aria-selected="false">
                          Disapproved User
                      </button>
                      </li>

    

                  </ul>
                  <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                  
                          <div class="input-group input-group-merge mb-3 search-input">
                              <span class="input-group-text"><i class="bi bi-search"></i></span>
                              <input
                              type="text"
                              class="form-control"
                              placeholder="Search Pending..."
                              aria-label="Search..."
                              id="searchPending"
                              onkeyup="searchTable('pendingTable', 'searchPending')"/>
                          </div>

                          
                          <table class="table table-hover" id="studentsTable">
                              <thead class="table-dark">
                                  <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">Name</th>
                                      <th scope="col">Age</th>
                                      <th scope="col">Gender</th>
                                      <th scope="col">Mobile No.</th>
                                      <th scope="col">Course</th>
                                      <th scope="col">Address</th>
                                      <th scope="col">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php if (empty($students)): ?>
                                      <tr>
                                          <td colspan="8" class="text-center">No students found.</td>
                                      </tr>
                                  <?php else: ?>
                                      <?php foreach ($students as $student): ?>
                                      <tr>
                                          <th scope="row"><?= htmlspecialchars($student['id']) ?></th>
                                          <td><?= htmlspecialchars($student['name']) ?></td>
                                          <td><?= htmlspecialchars($student['age']) ?></td>
                                          <td><?= htmlspecialchars($student['gender']) ?></td>
                                          <td><?= htmlspecialchars($student['phone']) ?></td>
                                          <td><?= htmlspecialchars($student['course']) ?></td>
                                          <td><?= htmlspecialchars($student['address']) ?></td>
                                          <td>
                                          <a href="update_student_status.php?id=<?= htmlspecialchars($student['id']) ?>&action=approve" 
                                          class="btn btn-sm btn-success">
                                              Approve
                                          </a>

                                          <a href="update_student_status.php?id=<?= htmlspecialchars($student['id']) ?>&action=disapprove" 
                                          class="btn btn-sm btn-danger">
                                              Disapprove
                                          </a>
                                      </td>
                                      </tr>
                                      <?php endforeach; ?>
                                  <?php endif; ?>
                              </tbody>
                          </table>

                      </div>


                      <!-- PENDING SELLER -->
                      <div class="tab-pane fade" id="navs-pills-pending-seller" role="tabpanel">
                      
              
                      
                      <div class="input-group input-group-merge mb-3 search-input">
                              <span class="input-group-text"><i class="bi bi-search"></i></span>
                              <input
                              type="text"
                              class="form-control"
                              placeholder="Search Pending..."
                              aria-label="Search..."
                              id="searchPending"
                              onkeyup="searchTable('pendingTable', 'searchPending')"/>
                          </div>

                          
                          <table class="table table-hover" id="studentsTable">
                              <thead class="table-dark">
                                  <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">Name</th>
                                      <th scope="col">Age</th>
                                      <th scope="col">Gender</th>
                                      <th scope="col">Mobile No.</th>
                                      <th scope="col">Course</th>
                                      <th scope="col">Address</th>
                                      <th scope="col">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php if (empty($disapprovedStudents)): ?>
                                      <tr>
                                          <td colspan="8" class="text-center">No Disapproved students found.</td>
                                      </tr>
                                  <?php else: ?>
                                      <?php foreach ($disapprovedStudents as $student): ?>
                                      <tr>
                                          <th scope="row"><?= htmlspecialchars($student['id']) ?></th>
                                          <td><?= htmlspecialchars($student['name']) ?></td>
                                          <td><?= htmlspecialchars($student['age']) ?></td>
                                          <td><?= htmlspecialchars($student['gender']) ?></td>
                                          <td><?= htmlspecialchars($student['phone']) ?></td>
                                          <td><?= htmlspecialchars($student['course']) ?></td>
                                          <td><?= htmlspecialchars($student['address']) ?></td>
                                          <td>
                                          <a href="update_student_status.php?id=<?= htmlspecialchars($student['id']) ?>&action=return" 
                                              class="btn btn-sm btn-warning">
                                                  Return
                                              </a>
                                          </td>
                                      </tr>
                                      <?php endforeach; ?>
                                  <?php endif; ?>
                              </tbody>
                          </table>


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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


    
</body>
</html>