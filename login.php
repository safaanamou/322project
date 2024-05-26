<?php
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    require_once "database.php";

    // Validate input
    if (empty($email) || empty($password)) {
        echo "<div class='alert alert-danger'>All fields are required</div>";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "<div class='alert alert-danger'>Something went wrong</div>";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $passwordCheck = password_verify($password, $row['password']);
                if ($passwordCheck == false) {
                    echo "<div class='alert alert-danger'>Wrong password</div>";
                } elseif ($passwordCheck == true) {
                    // Set session variables and redirect to main HTML page
                    $_SESSION['user'] = $row['email'];
                    header("Location: main.html");
                    exit();
                }
            } else {
                echo "<div class='alert alert-danger'>No user found with that email</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="ggg.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
