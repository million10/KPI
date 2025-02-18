<?php
session_start();
include '../includes/db.php'; // Include database connection

// Initialize variables
$user_id = $_SESSION['user_id']; // Assuming you have stored the user's ID in the session
$profile_picture = ''; // To store the current profile picture path

// Fetch user data including current profile picture
$query = "SELECT profile_picture FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
if ($result) {
    $user = mysqli_fetch_assoc($result);
    $profile_picture = $user['profile_picture'];
}

// Handle form submission for profile picture upload and password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $file_name = $_FILES['profile_picture']['name'] ?? '';
    $file_size = $_FILES['profile_picture']['size'] ?? 0;
    $file_tmp = $_FILES['profile_picture']['tmp_name'] ?? '';
    $file_type = $_FILES['profile_picture']['type'] ?? '';

    // Check if the file is an image
    $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!empty($file_name) && !in_array($file_ext, $allowed_extensions)) {
        $errors[] = "Invalid file type. Please upload an image (jpeg, png, gif).";
    }

    // Check file size (5MB limit)
    if ($file_size > 5242880) {
        $errors[] = "File size must be less than 5MB.";
    }

    // If there are no errors, proceed with the upload
    if (empty($errors)) {
        // Define upload directory
        $upload_dir = '../uploads/profile_pictures/'; // Make sure this directory exists and is writable
        $new_file_name = time() . '_' . basename($file_name); // To avoid naming conflicts

        // Move the uploaded file
        if (!empty($file_name) && move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            // Update the user's profile picture in the database
            $query_update = "UPDATE users SET profile_picture = '$new_file_name' WHERE id = '$user_id'";
            if (mysqli_query($conn, $query_update)) {
                $_SESSION['message'] = "Profile picture updated successfully!";
                header("Location: settings.php"); // Redirect to the same page to show the updated picture
                exit();
            } else {
                $errors[] = "Error updating the profile picture in the database: " . mysqli_error($conn);
            }
        } else {
            $errors[] = "Failed to move the uploaded file.";
        }
    }

    // Handle password change
    if (!empty($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } else {
            // Update password in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query_update_password = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
            if (mysqli_query($conn, $query_update_password)) {
                $_SESSION['message'] = "Password changed successfully!";
                header("Location: settings.php");
                exit();
            } else {
                $errors[] = "Error updating the password in the database: " . mysqli_error($conn);
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
    <title>Profile Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
    </style>
    <script>
        function checkPasswords() {
            var newPassword = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            var message = document.getElementById("password_message");
            if (newPassword !== confirmPassword) {
                message.textContent = "Passwords do not match.";
                message.style.color = "red";
            } else {
                message.textContent = "Passwords match.";
                message.style.color = "green";
            }
        }
    </script>
</head>
<body>
<!-- Include the common sidebar -->
<?php include 'sidebar.php'; ?>
<div class="container">
    <h2 class="text-center">profile settings</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo $error . '<br>'; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mb-4">
        <img src="../uploads/profile_pictures/<?php echo $profile_picture ?: 'default.jpg'; ?>" alt="Profile Picture" class="rounded-circle" width="150">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profile_picture">update Profile photo:</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control-file">
        </div>

        <div class="form-group">
            <label for="new_password">change Password (optional):</label>
            <input type="password" name="new_password" id="new_password" class="form-control" onkeyup="checkPasswords()">
        </div>

        <div class="form-group">
            <label for="confirm_password">confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" onkeyup="checkPasswords()">
            <small id="password_message" class="form-text"></small>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
