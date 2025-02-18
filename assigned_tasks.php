<?php
session_start();
include '../includes/db.php'; // Database connection

// Check if employee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['user_id'];

// Fetch assigned tasks for the logged-in employee using prepared statements

 $queryTasks = "
    SELECT ta.assignment_id, 
       ec.name AS name, 
       sc.sub_name AS sub_name, 
       c.Business_Partner AS Business_Partner, 
       c.Phone AS Phone, 
       ta.status, 
       ta.score,
       ta.Plan
FROM task_assignments ta
INNER JOIN evaluation_criteria ec ON ta.criteria_id = ec.criteria_id
LEFT JOIN sub_criteria sc ON ta.sub_criteria_id = sc.id
LEFT JOIN customer c ON ta.partner_id = c.id -- Use LEFT JOIN to handle NULL partner_id
WHERE ta.employee_id = $employee_id
ORDER BY ec.name, sc.sub_name;

   
     
";
$tasksResult = mysqli_query($conn, $queryTasks);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Tasks</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
<!-- Include the sidebar -->
<?php include 'sidebar.php'; ?>
<div class="container">
    <h2 class="text-center">Assigned Tasks</h2>
    <?php
    if (!$tasksResult) {
        die("Query error: " . $conn->error);
    }

    if ($tasksResult->num_rows == 0) {
        echo "No tasks found for employee ID " . htmlspecialchars($employee_id);
    }
    ?>
    <table id="tasksTable" class="table table-striped table-bordered">
        <thead>
            <tr>
			<th>S No.</th>
                <th>office name</th>
                <th>criteria</th>
               
               
                <th>plan</th>
                <th>scores</th>
                <th>status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
		<?php $serialNumber = 1; ?>
           <?php while ($task = mysqli_fetch_assoc($tasksResult)): ?>
                <tr>
				<td><?php echo $serialNumber++; ?></td>
                    <td><?php echo htmlspecialchars($task['name']); ?></td>
                    <td><?php echo htmlspecialchars($task['sub_name']); ?></td>
                    
                     
                    <td><?php echo htmlspecialchars($task['Plan']); ?></td>
                   <td><?php echo htmlspecialchars($task['score']); ?></td>
                    <td><?php echo htmlspecialchars($task['status']); ?></td>
                    <td>

                        <a href="update_task_status.php?assignment_id=<?php echo $task['assignment_id']; ?>" class="btn btn-primary">Update Status</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Include jQuery, DataTables, and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tasksTable').DataTable();
    });
</script>

</body>
</html>
