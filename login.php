<?php
// Start session 
session_start();

// Database connection
include "databasecredentials.php";


$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $email = filter_var(htmlspecialchars($_POST["email"]) , FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"]; 
    
    // validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (empty($password)) {
        $message = "Please enter your password.";
    } else {
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Prepare SQL statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT id, username, email,profile_pic ,password FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                 
                // Verify password
                if (password_verify($password, $user["password"])) {
                    // Password is correct, set session variables
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["user_email"] = $user["email"];
                    $_SESSION["profile_pic_url"] = $user["profile_pic"];
                    $_SESSION["logged_in"] = true;
                    
                    // Redirect to dashboard or home page
                    header("Location: home.php");
                    exit();
                } else {
                    $message = "Invalid email or password.";
                }
            } else {
                $message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $message = "An error occurred during login. Please try again.";
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center text-primary">Login</h2>
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger text-center"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" value="" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="register.php" class="text-primary">Sign up</a></p>
                            <p><a href="forgot-password.php" class="text-secondary">Forgot Password?</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>