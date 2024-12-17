<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- SEARCH FORM -->

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                Hello {{auth()->user()->first_name}}  <i class="fas fa-user-shield nav-icon border-1 border-solid" style="color:#007bff; border: 2px solid;border-radius: 55%;padding: 6px;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">Profile Setting</span>
                <div class="dropdown-divider"></div>
                <a href="{{ url(auth()->user()->role.'/update/profile') }}" class="dropdown-item">
                    <i class="far fa-user-circle mr-2"></i> Manage Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="#"   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item dropdown-footer">Welcome Back {{auth()->user()->first_name}}!</a>
            </div>
        </li>

    </ul>
</nav>
