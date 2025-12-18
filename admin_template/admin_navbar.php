<?php
// Ensure session variables are accessible
if (session_status() === PHP_SESSION_NONE) {
    session_start();

}
$user_id = $_SESSION['user_id'];

// Fetch user data from the database, including the profile picture path
$stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id); 
$stmt->execute();
$userProfilePicture = $stmt->fetchColumn();



// Define a default value for $notifications if it's not set
$notifications = isset($notifications) ? $notifications : [];
?>

<nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bi bi-list bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto gap-5">

            <!-- Notification Dropdown -->
            <li>
         <div class="dropdown">
    <button class="btn btn-primary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class='bx bx-bell'></i> Notifications
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?php echo count($notifications) > 99 ? '99+' : count($notifications); ?>
            <span class="visually-hidden">unread notifications</span>
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end w-auto mt-1" aria-labelledby="notificationDropdown">
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <li class="dropdown-item p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-2">
                            <i class='bx bx-info-circle'></i>
                        </div>
                        <div>
                            <p class="mb-0"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <small class="text-muted"><?php echo date('F j, Y, g:i A', strtotime($notification['created_at'])); ?></small>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="clear_notif_admin.php" method="POST" style="margin: 0;">
                    <button type="submit" class="dropdown-item text-danger">Clear All Notifications</button>
                </form>
            </li>
        <?php else: ?>
            <li class="dropdown-item text-center">No notifications</li>
        <?php endif; ?>
    </ul>
</div>

            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                    <img src="<?php echo isset($userProfilePicture) && !empty($userProfilePicture) ? htmlspecialchars($userProfilePicture) : 'static/img/default_pic.png'; ?>" alt="Profile Picture" class="rounded-circle w-40 h-40 object-fit-cover" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                    <img src="<?php echo isset($userProfilePicture) && !empty($userProfilePicture) ? htmlspecialchars($userProfilePicture) : 'static/img/default_pic.png'; ?>" alt class="rounded-circle w-40 h-40 object-fit-cover" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?php echo $_SESSION['name'] ?? 'Guest'; ?></span>
                                    <small class="text-muted text-capitalize"><?php echo $_SESSION['role'] ?? 'Guest'; ?></small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="logout.php">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
