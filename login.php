<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM yango_user WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            // Redirect to index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yango Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff5f5;
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #dc3545;
        }
        h2 {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary:hover {
            background-color: #bd2130;
            border-color: #bd2130;
        }
        .yango-logo {
            display: block;
            margin: 0 auto 15px auto;
            max-width: 100px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Yango Logo -->
        <img src="data:image/jpeg;base64,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" alt="Yango Logo" class="yango-logo">
        <h2>Login to Yango</h2>
        
        <!-- Error Message -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">Don't have an account? <a href="signup.php" class="text-danger">Signup</a></p>
    </div>
</body>
</html>
