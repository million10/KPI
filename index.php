<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - performance indicators system</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
           background-color: #f4f7fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container img {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-weight: 700;
            color: #4e54c8;
        }
        .form-control {
            border-radius: 50px;
            padding: 15px 20px;
        }
        .btn-login {
            background-color: #4e54c8;
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: bold;
            color: #ffffff;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #6c63ff;
        }
        .form-icon {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #4e54c8;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Logo Image -->
    <img src="assets/OIP.jpg" alt="Company Logo">
    
    <h2>JJU Performance Indicator System</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="input-group">
            <span class="form-icon"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control pl-5" placeholder="username" name="email" required>
        </div>
        <div class="input-group">
            <span class="form-icon"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control pl-5" placeholder="password " name="password" required>
        </div>
        <button type="submit" class="btn btn-login btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
     <!--   <div class="text-center mt-3">
            <a href="forgot_password.php" style="color: #4e54c8;">የይለፍ ቃል እረሳው?</a>
        </div> -->
    </form>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
