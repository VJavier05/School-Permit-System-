<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

require 'db_connect.php'; 

// Fetch data from the database
try {
    $sql = "SELECT id, first_name, last_name, age, gender, phone, course, region, province, city, barangay FROM users WHERE role = 'user' AND is_approved = 'approved'"; // Assuming 0 is false
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sqlDisapproved = "SELECT id, first_name, last_name, age, gender, phone, course, region, province, city, barangay FROM users WHERE role = 'user' AND is_approved = 'archieve'";
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
        <div class="layout-container">
          <!-- Menu -->
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
                
              <h4 class="fw-bold py-3 mb-2"><span class="text-muted fw-light">User Management /</span>  Manage Accounts</h4>

              <div class="row">
              <div class="col-xl-12">
                  <div class="nav-align-top mb-4">
                  <ul class="nav nav-pills mb-3" role="tablist">
                      <li class="nav-item">
                      <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                          Manage User
                      </button>
                      </li>
                      <li class="nav-item">
                      <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-pending-seller" aria-controls="navs-pills-pending-seller" aria-selected="false">
                          Archieve User
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
                              placeholder="Search User..."
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
                                        <td><?= htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) ?></td>
                                        <td><?= htmlspecialchars($student['age']) ?></td>
                                        <td><?= htmlspecialchars($student['gender']) ?></td>
                                        <td><?= htmlspecialchars($student['phone']) ?></td>
                                        <td><?= htmlspecialchars($student['course']) ?></td>
                                        <td><?= htmlspecialchars($student['region']) . ', ' . htmlspecialchars($student['province']) . ', ' . htmlspecialchars($student['city']) . ', ' . htmlspecialchars($student['barangay']) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success" onclick="openUpdateModal(<?= htmlspecialchars(json_encode($student)) ?>)">
                                                Update
                                            </button>
                                            <a href="update_student_status.php?id=<?= htmlspecialchars($student['id']) ?>&action=archieve" class="btn btn-sm btn-danger">
                                                Delete
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
                              placeholder="Search User..."
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
                                        <td colspan="8" class="text-center">No Archived students found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($disapprovedStudents as $student): ?>
                                    <tr>
                                        <th scope="row"><?= htmlspecialchars($student['id']) ?></th>
                                        <td><?= htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) ?></td>
                                        <td><?= htmlspecialchars($student['age']) ?></td>
                                        <td><?= htmlspecialchars($student['gender']) ?></td>
                                        <td><?= htmlspecialchars($student['phone']) ?></td>
                                        <td><?= htmlspecialchars($student['course']) ?></td>
                                        <td><?= htmlspecialchars($student['region']) . ', ' . htmlspecialchars($student['province']) . ', ' . htmlspecialchars($student['city']) . ', ' . htmlspecialchars($student['barangay']) ?></td>
                                        <td>
                                            <a href="update_student_status.php?id=<?= htmlspecialchars($student['id']) ?>&action=return_approved" 
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


<!-- Update Student Modal -->
<div class="modal fade" id="updateStudentModal" tabindex="-1" aria-labelledby="updateStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_student.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudentModalLabel">Update Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="studentId">
                    <div class="mb-3">
                        <label for="studentFirstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="studentFirstName" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentLastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="studentLastName" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentAge" class="form-label">Age</label>
                        <input type="number" class="form-control" id="studentAge" name="age" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentGender" class="form-label">Gender</label>
                        <input type="text" class="form-control" id="studentGender" name="gender" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentPhone" class="form-label">Mobile No.</label>
                        <input type="text" class="form-control" id="studentPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentCourse" class="form-label">Course</label>
                        <input type="text" class="form-control" id="studentCourse" name="course" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentRegion" class="form-label">Region</label>
                        <input type="text" class="form-control" id="studentRegion" name="region" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentProvince" class="form-label">Province</label>
                        <input type="text" class="form-control" id="studentProvince" name="province" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentCity" class="form-label">City</label>
                        <input type="text" class="form-control" id="studentCity" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentBarangay" class="form-label">Barangay</label>
                        <input type="text" class="form-control" id="studentBarangay" name="barangay" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    function openUpdateModal(student) {
        // Set the form fields with the student's data
        document.getElementById('studentId').value = student.id;
        document.getElementById('studentFirstName').value = student.first_name;
        document.getElementById('studentLastName').value = student.last_name;
        document.getElementById('studentAge').value = student.age;
        document.getElementById('studentGender').value = student.gender;
        document.getElementById('studentPhone').value = student.phone;
        document.getElementById('studentCourse').value = student.course;
        document.getElementById('studentRegion').value = student.region;
        document.getElementById('studentProvince').value = student.province;
        document.getElementById('studentCity').value = student.city;
        document.getElementById('studentBarangay').value = student.barangay;
        
        // Show the modal
        var updateStudentModal = new bootstrap.Modal(document.getElementById('updateStudentModal'));
        updateStudentModal.show();
    }
</script>


</body>
</html>