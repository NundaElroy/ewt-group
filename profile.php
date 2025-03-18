<?php
session_start();
include "databasecredentials.php";

// Redirect if not logged in
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
$email = $_SESSION["user_email"];
$profile_pic = $_SESSION["profile_pic_url"];

$message = "";

// Fetch user data from the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT username, email, profile_pic FROM users WHERE id = :user_id");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $username = htmlspecialchars($user["username"]);
        $email = htmlspecialchars($user["email"]);
        $profile_pic = $user["profile_pic"];
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_profile"])) {
        $new_username = htmlspecialchars($_POST["username"]);
        $new_email = filter_var(htmlspecialchars($_POST["email"]), FILTER_SANITIZE_EMAIL);

        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email WHERE id = :user_id");
            $stmt->bindParam(":username", $new_username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $new_email, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION["username"] = $new_username;
            $username = $_SESSION["username"];
            $_SESSION["user_email"] = $new_email;
            $email = $_SESSION["user_email"];
            $message = "Profile updated successfully!";
        }
    }

    // Handle profile picture update
    if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type
        if (in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
            
            $stmt = $conn->prepare("UPDATE users SET profile_pic = :profile_pic WHERE id = :user_id");
            $stmt->bindParam(":profile_pic", $target_file, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION["profile_pic_url"] = $target_file;
            $message = "Profile picture updated!";
        } else {
            $message = "Invalid image format.";
        }
    }

    // Handle account deletion
    if (isset($_POST["delete_account"])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        session_destroy();
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">View Profile</span>
            <div class="d-flex">
                <a href="home.php" class="btn btn-outline-light me-2">Home</a>
                <a href="logout.php" class="btn btn-light text-primary">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center p-4">
                        
                        <!-- Profile Picture -->
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" id="profile-pic" alt="Profile Picture" class="rounded-circle mb-3" width="100" height="100">
                        

                        <!-- Update Profile Picture -->
                        <form action="profile.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Change Profile Picture</label>
                                <input type="file" name="profile_pic" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Picture</button>
                        </form>

                        <hr>

                        <!-- Update Profile Form -->
                        <form action="profile.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary w-100">Update Profile</button>
                        </form>

                        <hr>

                        <!-- Delete Account Button -->
                        <form action="profile.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                            <button type="submit" name="delete_account" class="btn btn-danger w-100">Delete Account</button>
                        </form>

                        <?php if ($message): ?>
                            <p class="mt-3 text-danger"><?php echo $message; ?></p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
