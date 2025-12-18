<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="static/img/school_logo.png" alt="DASHBOARD LOGO">
            </span>
        </a>

        <!-- RESPONSIVE NOT WORKING -->
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1 mt-4">
        
        <!-- Dashboard -->
        <li class="menu-item <?php echo $current_page == 'dashboard_user.php' ? 'active' : ''; ?>">
            <a href="dashboard_user.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-item  <?php if ($current_page == 'requests_user.php') {  echo 'active'; }?>">   
            <a href="requests_user.php" class="menu-link">
              <i class="menu-icon tf-icons bx bxs-file-pdf"></i>
              <div>Request</div>
            </a>
        </li>

        <li class="menu-item <?php if ($current_page == 'user_profile.php') {  echo 'active'; }?>">
            <a href="user_profile.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Profile</div>
            </a>
        </li>
        

        <li class="menu-item ">
            <a href="logout.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-exit"></i>
                <div>Logout</div>
            </a>
        </li>
    </ul>
</aside>
