<?php
// Start session at the beginning
session_start();

// Check if user is logged in
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user data from session
$username = $_SESSION["username"];
$email = $_SESSION["user_email"]; 
$user_id = $_SESSION["user_id"];
$profile_pic = $_SESSION["profile_pic_url"];


// // Default profile picture if none is found
// if (empty($profile_pic)) {
//     $profile_pic = "default-profile.jpg";
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById("current-time").innerText = now.toLocaleTimeString();
        }
        
        // Initialize time immediately, then update every second
        document.addEventListener("DOMContentLoaded", function() {
            updateTime();
            setInterval(updateTime, 1000);
        });
    </script>
</head>
<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">Home</span>
            <div class="d-flex">
                <a href="profile.php" class="btn btn-outline-light me-2">View Profile</a>
                <a href="logout.php" class="btn btn-light text-primary">Logout</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center p-4">
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" id="profile-pic" alt="Profile Picture" class="rounded-circle mb-3" width="100" height="100">
                        
                        <h3 class="text-primary">Welcome, <span id="user-name"><?php echo htmlspecialchars($username); ?></span>!</h3>
                        <p class="text-muted">Email: <span id="user-email"><?php echo htmlspecialchars($email); ?></span></p>
                        <h5 class="mt-4">Current Time:</h5>
                        <p id="current-time" class="fw-bold fs-4 text-primary">--:--:--</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


