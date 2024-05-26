<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="registration-container">
        <img src="main_logo.png" alt="Logo"> <!-- Ensure the path to your logo is correct -->
        <h1>Welcome to Famagusta Municipality</h1>
        <form action="registration1.php" method="post">
            <?php
            if (isset($_POST["submit"])) {
                $fullName = $_POST["fullname"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeat_password"];
                
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();
                
                if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
                    array_push($errors, "All fields are required");
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Email is not valid");
                }
                if (strlen($password) < 8) {
                    array_push($errors, "Password must be at least 8 characters long");
                }
                if ($password !== $passwordRepeat) {
                    array_push($errors, "Password does not match");
                }

                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $rowCount = mysqli_num_rows($result);
                if ($rowCount>0) {
                    array_push($errors,"Email already exists!");
                   }

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                } else {
                    
                    $sql = "INSERT INTO users (full_name, email, password) VALUES ( ?, ?, ? )";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                    if ($prepareStmt){
                        mysqli_stmt_bind_param($stmt,"sss",$fullName, $email, $passwordHash);
                        mysqli_stmt_execute($stmt);
                        echo "<div class='alert alert-success'>You are registered successfully.</div>";
                    }else{
                        die("Something went wrong");
                    }
                }
            }
            ?>
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <input type="password" name="repeat_password" placeholder="Repeat your password" required>
            </div>
            <button type="submit" name="submit">Register</button>
        </form>
        <a href="#">Forgot your password?</a>
    </div>
</body>
</html>
