<?php
session_start();
include '../includes/db.php'; // Database connection

// Check if the user is logged in as an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['user_id'];

// Fetch tasks assigned to the logged-in employee with status "Completed"
$queryCompletedTasks = "
    SELECT 
    ta.assignment_id, 
    ec.name AS name, 
    COALESCE(sc.sub_name, 'N/A') AS sub_name, 
    c.Business_Partner AS Business_Partner, 
    COALESCE(c.Phone, 'N/A') AS Phone, 
    ta.status, 
    ta.Plan, 
    ta.score, 
    ta.completed_at,
    ta.file
FROM 
    task_assignments ta
INNER JOIN 
    evaluation_criteria ec ON ta.criteria_id = ec.criteria_id
LEFT JOIN 
    sub_criteria sc ON ta.sub_criteria_id = sc.id
LEFT JOIN 
    customer c ON ta.partner_id = c.id
WHERE 
    ta.employee_id = $employee_id AND ta.status = 'Completed';
";
$completedTasksResult = mysqli_query($conn, $queryCompletedTasks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Tasks Report</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .container { margin-top: 20px; }
        .print-btn { margin-bottom: 20px; }
        @media print {
            body * { visibility: hidden; }
            #completedTasksTable_wrapper, #completedTasksTable_wrapper * { visibility: visible; }
            #completedTasksTable_wrapper { position: absolute; top: 0; left: 0; width: 100%; }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <h2 class="text-center">Completed Task Report</h2>

    <div class="form-group row">
        <label for="completedDate" class="col-sm-2 col-form-label">Filter by Completed Date:</label>
        <div class="col-sm-4">
            <input type="date" id="completedDate" class="form-control" placeholder="Select date">
        </div>
        <div class="col-sm-2">
            <button class="btn btn-primary print-btn" onclick="printTable()">Print</button>
        </div>
    </div>

    <table id="completedTasksTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Office Name</th>
                <th>Criteria</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Score</th>
                <th>Completed Date</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php $serialNumber = 1; ?>
            <?php while ($task = mysqli_fetch_assoc($completedTasksResult)): ?>
                <tr>
                    <td><?php echo $serialNumber++; ?></td>
                    <td><?php echo htmlspecialchars($task['name']); ?></td>
                    <td><?php echo htmlspecialchars($task['sub_name']); ?></td>
                    <td><?php echo htmlspecialchars($task['Plan']); ?></td>
                    <td><?php echo htmlspecialchars($task['status']); ?></td>
                    <td><?php echo htmlspecialchars($task['score']); ?></td>
                    <td><?php echo htmlspecialchars($task['completed_at']); ?></td>
                    <td>
                        <?php if (!empty($task['file'])): ?>
                            <a href="<?php echo htmlspecialchars($task['file']); ?>" target="_blank">View PDF</a>
                        <?php else: ?>
                            No file
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#completedTasksTable').DataTable();

        $('#completedDate').on('change', function() {
            var selectedDate = this.value;
            table.column(6).search(selectedDate).draw();
        });
    });

    function printTable() {
        var printContents = document.getElementById('completedTasksTable_wrapper').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
</body>
</html>