<?php
session_start();
include 'includes/db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email and password from the POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement with placeholders to prevent SQL injection
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Check if a user is found
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("Location: Admin/admin_dashboard.php");
                    break;
                case 'evaluator':
                    header("Location: evaluator_dashboard.php");
                    break;
                case 'employee':
                    header("Location: employee_dashboard.php");
                    break;
                default:
                    echo "Invalid role.";
                    exit();
            }
        } else {
            // Password is incorrect
            $_SESSION['error'] = "Incorrect email or password.";
            header("Location: index.php");
            exit();
        }
    } else {
        // No user found with the given email
        $_SESSION['error'] = "Incorrect email or password.";
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect to login page if accessed directly
    header("Location: ff.php");
    exit();
}
?>
