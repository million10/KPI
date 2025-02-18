<?php
session_start();
ob_start();
include 'includes/db.php'; // Assumes a separate db.php file handles database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email and password from the POST request
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // No need to escape password for verification

    // Query to check if the user exists
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password using password_verify()
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
                    header("Location: evaluator/evaluator_dashboard.php");
                    break;
                case 'employee':
                    header("Location: employee/employee_dashboard.php");
                    break;
                default:
                    echo "Invalid role.";
            }
            exit();
        } else {
            // Password is incorrect
            $_SESSION['error'] = "የይለፍ ቃል የተሳሳተ ነው በድጋሚ ይሞክሩ።";
            header("Location: index.php");
            exit();
        }
    } else {
        // No user found with the given email
        $_SESSION['error'] = "መለያስሞ ወይም የይለፍ ቃል የተሳሳተ ነው በድጋሚ ይሞክሩ።";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
ob_end_flush();
?>
