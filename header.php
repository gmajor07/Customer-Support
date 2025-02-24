<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit(); // Stop script execution after redirection
}
?>

  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Customer Support</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    
    <link href="cardstyle.css" rel="stylesheet">
   
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />

    <!-- jQuery (if needed) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  </head>
  <body>
    <div class="container-scroller">
      <div class="row p-0 m-0 proBanner" id="proBanner">
        <div class="col-md-12 p-0 m-0">
          <div class="card-body card-body-padding d-flex align-items-center justify-content-between">
            <div class="ps-lg-3">
              <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0 font-weight-medium me-3 buy-now-text">Free 24/7 customer support, updates, and more with service!</p>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <a href="send_messages.php"><i class="mdi mdi-home me-3 text-white"></i></a>
              <button id="bannerClose" class="btn border-0 p-0">
                <i class="mdi mdi-close text-white mr-0"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="send_messages.php">NARET CAMPANY</a>
                  <a class="navbar-brand brand-logo-mini" href="send_messages.php"><img src="assets/images/logo.png" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
       
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="nav-profile-img">
                  <img src="assets/images/faces/face1.png" alt="image">
                  <span class="availability-status online"></span>
                </div>
                <div class="nav-profile-text">
                  <p class="mb-1 text-black"><?php echo ucfirst($_SESSION['username']); ?></p>
                </div>
              </a>
              <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="admin.php">
                  <i class="mdi mdi-cached me-2 text-success"></i> Change Password</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                  <i class="mdi mdi-logout me-2 text-primary"></i> Sign out </a>
              </div>
            </li>
            <li class="nav-item d-none d-lg-block full-screen-link">
              <a class="nav-link">
                <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
              </a>
            </li>
        
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="nav-profile-image">
                  <img src="assets/images/faces/face1.png" alt="profile" />
                  <span class="login-status online"></span>
                  <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2"><?php echo ucfirst($_SESSION['username']); ?></span>
                  <span class="text-secondary text-small">Company Manager</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
              </a>
            </li>
           
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Message</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-crosshairs-gps menu-icon"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="add_template.php">Add Template Message</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="templates.php">View Message Templates</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="send_messages.php">Send Message</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="periodically_messages.php">Send Periodically Message</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="terminate.php">View Periodically Message</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Customers</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="customer_registration.php">Register New Customer</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="send_messages.php">Send Messages</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="customers.php">Customers</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="customer_management.php">Customers Management</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
                <span class="menu-title">Admin</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-contacts menu-icon"></i>
              </a>
              <div class="collapse" id="icons">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="register.php">Registration</a>
                  </li>
                </ul>
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="admin_panel.php">Admin Panel</a>
                  </li>
                </ul>
              </div>
              <div class="collapse" id="icons">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="logout.php">Log out</a>
                  </li>

                  <li><a class="dropdown-item" href="admin.php">
                  <i class="mdi mdi-cached me-2 text-success"></i> Change Password</a> </li>
                </ul>

                
                
              </div>
            </li>
           
          </ul>
        </nav>

        <script>
      $(document).ready(function () {
    $("#search").on("keyup", function () {
        let searchText = $(this).val();

        $.ajax({
            url: "search_users.php",
            type: "POST",
            data: { query: searchText },
            success: function (response) {
                $("#dataTable tbody").html(response);

                // Reattach Bootstrap modal functionality after updating table
                $('[data-bs-toggle="modal"]').off("click").on("click", function () {
                    let targetModal = $(this).attr("data-bs-target");
                    $(targetModal).modal("show");
                });
            }
        });
    });

    // Ensure modals work for both static and dynamic content
    $(document).on("click", "[data-bs-toggle='modal']", function () {
        let targetModal = $(this).attr("data-bs-target");
        $(targetModal).modal("show");
    });
});



      </script>


<style>
        .navbar-brand {
            font-weight: bold;
            color: #3498db; /* Change the color to your preference */
            text-decoration: none; /* Remove the underline */
            padding: 10px 20px; /* Add some padding */
            transition: color 0.3s; /* Add a transition for the color change */
        }

        .navbar-brand:hover {
            color: #2980b9; /* Color on hover */
        }

        .navbar-brand:active {
            color: #1abc9c; /* Color when clicked */
        }
    </style>


       