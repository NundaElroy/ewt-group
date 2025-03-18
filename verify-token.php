<?php
session_start();
include "databasecredentials.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION["reset_email"] ?? null;
    $token = $_POST["token"];

    if (!$email) {
        $message = "Session expired. Try again.";
    } else {
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND reset_code = :reset_code");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':reset_code', $token);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $_SESSION["reset_verified"] = true;
                header("Location: reset-password.php");
                exit();
            } else {
                $message = "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            $message = "Error occurred. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Token</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center text-primary">Verify Token</h2>
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger text-center"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="token" class="form-label">Enter Reset Token:</label>
                                <input type="text" id="token" name="token" class="form-control" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Verify</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="forgot-password.php" class="text-secondary">Resend Token</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
