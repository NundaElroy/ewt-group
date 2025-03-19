<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(htmlspecialchars($_POST["email"]), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        try {
            include "databasecredentials.php";
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $token = bin2hex(random_bytes(32));
                $_SESSION["reset_email"] = $email;
                $_SESSION["reset_token"] = $token;

                $updateStmt = $conn->prepare("UPDATE users SET reset_code = :token WHERE email = :email");
                $updateStmt->bindParam(':token', $token, PDO::PARAM_STR);
                $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
                $updateStmt->execute();

                
                require_once("mailer.php");
                if (send_email($token,$email)) {
                    header("Location: verify-token.php");
                } else {
                    $message = "Failed to send email.";
                }
            } else {
                $message = "Email not found.";
            }
        } catch (PDOException $e) {
            $message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center text-primary">Forgot Password</h2>
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-info text-center"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Enter Your Email:</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Send Reset Code</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php" class="text-secondary">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
