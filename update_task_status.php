<?php
session_start();
ob_start();
include '../includes/db.php'; // Database connection

// Check if employee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    // Fetch task details
    $queryTask = "
        SELECT ta.assignment_id, ec.name AS name, sc.sub_name AS sub_name, 
               c.Business_Partner AS Business_Partner, c.Phone AS Phone, 
               ta.status, ta.Plan, ta.score, ta.comment, ta.file
        FROM task_assignments ta
        INNER JOIN evaluation_criteria ec ON ta.criteria_id = ec.criteria_id
        LEFT JOIN sub_criteria sc ON ta.sub_criteria_id = sc.criterion_id
        LEFT JOIN customer c ON ta.partner_id = c.id
        WHERE ta.assignment_id = ?
        ORDER BY ec.name, sc.sub_name;
    ";
    $stmt = $conn->prepare($queryTask);
    $stmt->bind_param('i', $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        echo "Task not found.";
        exit();
    }
} else {
    echo "No task selected.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newScore = $_POST['score'];
    $status = $_POST['status'];
    $comment = $_POST['comment']; // Optional comment field
    $filePath = $task['file']; // Default to existing file if no new upload

    // Validate that the new score is numeric
    if (!is_numeric($newScore)) {
        echo "<script>alert('The score must be a number.');</script>";
        exit();
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $allowedTypes = ['application/pdf'];
        if (in_array($_FILES['file']['type'], $allowedTypes)) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['file']['name']);
            $filePath = $uploadDir . $fileName;
            move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
        } else {
            echo "<script>alert('Only PDF files are allowed.');</script>";
            exit();
        }
    }

    // Fetch the previous score and plan
    $previousScore = $task['score'] ?? 0;
    $plan = $task['Plan'];
    $updatedScore = $previousScore + $newScore;

    if ($updatedScore > $plan) {
        echo "<script>alert('Error: The score cannot exceed the plan ($plan).');</script>";
    } else {
        // Update task details
        $updateQuery = "
            UPDATE task_assignments 
            SET score = ?, status = ?, comment = ?, file = ?, completed_at = NOW() 
            WHERE assignment_id = ?
        ";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('isssi', $updatedScore, $status, $comment, $filePath, $assignment_id);

        if ($stmt->execute()) {
            header("Location: assigned_tasks.php");
            exit();
        } else {
            echo "Error updating task: " . $stmt->error;
        }
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Task Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <h2 class="text-center">Update Task Status</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="assignment_id" value="<?php echo $task['assignment_id']; ?>">
        <div class="form-group">
            <label for="score">Score (NB: add only the new score)</label>
            <input type="text" class="form-control" id="score" name="score" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="Pending" <?php echo ($task['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
        <div class="form-group">
            <label for="file">Attach File (PDF only)</label>
            <input type="file" name="file" id="file" class="form-control">
            <?php if (!empty($task['file'])): ?>
                <p>Current File: <a href="<?php echo $task['file']; ?>" target="_blank">View File</a></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="comment">Comment (Optional)</label>
            <textarea class="form-control" id="comment" name="comment" rows="3"><?php echo htmlspecialchars($task['comment']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
