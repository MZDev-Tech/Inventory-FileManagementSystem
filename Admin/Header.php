<?php
// Enable output buffering
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_name("ADMIN_SESSION");
    session_start();
}

include '../vendor/autoload.php';
include('../connection.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Code to verify JWT token generated after admin login
$secret_key = "Zarnat12$&10";
$show_refreshToken = false; // Initialize the variable

if (isset($_COOKIE["access_token"])) {
    $token = $_COOKIE['access_token'];

    try {
        // Decode & verify the token
        $decoded_token = JWT::decode($token, new Key($secret_key, 'HS256'));

        // Get expiration time for the token
        $expiration_time = $decoded_token->exp;

        // Check if the token is about to expire (within 10 minutes)
        if ($expiration_time - time() <= 600) {
            $show_refreshToken = true;
        }
        if ($expiration_time < time()) {
            $id = $_SESSION['id'];
            $role = 'admin';
            $query = "UPDATE user SET status='inactive' where id='$id' && role='$role'";
            mysqli_query($con, $query);
            session_destroy();
            setcookie('access_token', time() - 3600, "/");

            $_SESSION['message'] = 'Session expired. Please log in again.';
            header('Location:../admin-login.php');
            exit();
        }
    } catch (Exception $e) {
        // Token is invalid or expired
        $_SESSION['message'] = 'Session expired. Please log in again.';
        header('Location:../admin-login.php');
        exit();
    }
} else {
    // No token found in cookies
    $_SESSION['message'] = "Unauthorized access. Please log in first.";
    header('Location:../admin-login.php');
    exit();
}

// Flush the output buffer
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- External CSS File Link -->
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="sweetalert2.min.css">

    <!-- Font Icons Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!----------------Header Section--------------------->
    <header id="header-part">
        <div class="dashboard">
            <form method="POST" action="">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="search" name="search" style="border:none" placeholder="Search here..">
                </div>
            </form>
        </div>

        <div class="header-icons">
            <button id="refreshTokenBtn" class="btn token-btn btn-sm">Refresh Token</button>
            <div class="icon1 menuBar">
                <i class="fas fa-bars" id="menuIcon"></i>
            </div>

            <div class="icon1 messIcon">
                <i class="fa-solid fa-envelope"></i>
                <div class="bg-wrapper">
                    <span class="notification">1</span>
                </div>
            </div>

            <div class="icon2 messIcon">
                <i class="fa-solid fa-bell"></i>
                <div class="bg-wrapper wrapper1">
                    <span class="notification">2</span>
                </div>
            </div>



            <?php
            include('../connection.php');
            $query = "SELECT * FROM admin WHERE id='1'";
            $result = mysqli_query($con, $query);
            while ($row = mysqli_fetch_array($result)) {
            ?>
                <div class="admin">
                    <a href="admin-profile.php">
                        <?php if (!empty($row['image']) && file_exists('../Images/' . $row['image'])) {
                            echo '<img src="../Images/' . $row['image'] . '">';
                        } else {
                            echo '<img src="../Images/user-profile.jpg">';
                        } ?> <div class="bg-wrapper1">
                            <span></span>
                        </div>
                    </a>

                    <div class="header-dropdown">
                        <!-- Dropdown Container -->
                        <div class="dropdown">
                            <!-- Dots Icon (Dropdown Toggle) -->
                            <a href="#" class="dropdown-toggle no-btn" id="dotsDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-angle-down angleicon"></i>
                            </a>

                            <!-- Dropdown Menu -->
                            <ul class="dropdown-menu" aria-labelledby="dotsDropdown">
                                <li>
                                    <a class="dropdown-item" href="Admin-profile.php">
                                        <i class="far fa-user"></i> Manage Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="update-profile.php">
                                        <i class="fas fa-unlock-alt"></i> Handle Password
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="confirmLogout(event)">
                                        <i class="fas fa-arrow-right-from-bracket"></i> Logout
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>


        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const header = document.getElementById('header-part');
            const menuIcon = document.getElementById('menuIcon');
            let isMenuActive = false; // Track menu state

            // Toggle menu event
            menuIcon.addEventListener('click', () => {
                isMenuActive = !isMenuActive;
                adjustHeaderOnScroll();
            });

            // Scroll event listener
            window.addEventListener('scroll', adjustHeaderOnScroll);
            window.addEventListener('resize', adjustHeaderOnScroll); // Adjust on screen resize

            function adjustHeaderOnScroll() {
                const scrolled = window.scrollY > 10;
                const isSmallScreen = window.innerWidth <= 768; // Adjust threshold as needed

                if (isSmallScreen) {
                    // Small screen styles
                    header.style.borderRadius = '0';
                    header.style.top = '0';
                    header.style.borderBottom = '1px solid rgb(43, 144, 151)';
                    header.style.left = '0';
                    header.style.width = '100%';
                    header.style.position = 'fixed';
                    header.style.zIndex = '999';

                    if (isMenuActive) {
                        // Keep header fixed when sidebar is over it
                        header.style.left = '0';
                        header.style.width = '100%';
                    }
                } else {
                    // Large screen styles
                    if (scrolled) {
                        header.style.borderRadius = '0';
                        header.style.top = '0';
                        header.style.borderBottom = '1px solid rgb(43, 144, 151)';

                        if (isMenuActive) {
                            header.style.left = '0';
                            header.style.width = '100%';
                        } else {
                            header.style.left = '245px';
                            header.style.width = 'calc(100% - 245px)';
                        }
                    } else {
                        header.style.borderRadius = '4px';
                        header.style.top = '8px';
                        header.style.borderBottom = 'none';

                        if (isMenuActive) {
                            header.style.left = '40px';
                            header.style.width = 'calc(100% - 80px)';
                        } else {
                            header.style.left = '260px';
                            header.style.width = 'calc(100% - 280px)';
                        }
                    }
                }
            }

            adjustHeaderOnScroll(); // Apply styles on page load
        });

        // Pass token expiration details from PHP to JS
        const expirationTime = <?php echo isset($expiration_time) ? $expiration_time : 0; ?>;

        // Script to show if refresh token button needs to be shown
        window.addEventListener('DOMContentLoaded', () => {
            const refreshButton = document.getElementById('refreshTokenBtn');

            const checkTokenTime = () => {
                const currentTimeClientSide = Math.floor(Date.now() / 1000); // Convert to seconds
                const timeLeftClientSide = expirationTime - currentTimeClientSide;

                // 600 seconds = 10 minutes
                if (timeLeftClientSide <= 600 && timeLeftClientSide > 0) {
                    refreshButton.style.display = "inline-block";
                } else {
                    refreshButton.style.display = "none";
                }
            };

            checkTokenTime();
            // Check token remaining time every minute
            setInterval(checkTokenTime, 60000);

            // Refresh Token Button Script
            refreshButton.addEventListener('click', () => {
                // Send AJAX request to refresh token
                $.ajax({
                    type: 'POST',
                    url: 'refresh_token.php',
                    success: function(response) {
                        console.log("Response from server:", response); // Log the response
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                title: "Good job!",
                                text: "Token refreshed successfully!",
                                icon: "success"
                            }).then(() => {
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Error refreshing token.",
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Error refreshing token.",
                        });
                    }
                });
            });
        });
        //sweet alert for logout

        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancel",
                confirmButtonText: "Yes, Logout!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "logout.php";
                }
            });
        }
    </script>

    <!-- External JS File Link -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>
</body>

</html>