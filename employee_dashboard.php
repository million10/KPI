<?php
// Start session and verify user is logged in as an employee
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: index.php"); // Redirect to login if not an employee
    exit();
}

include '../includes/db.php'; // Database connection

$employee_id = $_SESSION['user_id'];

// Fetch task data for the logged-in employee with sub-criteria
$queryTasks = "
    SELECT ec.sub_name AS sub_criteria_name, SUM(ta.Plan) AS total_plan, SUM(ta.score) AS total_score 
    FROM task_assignments ta
    INNER JOIN sub_criteria ec ON ta.sub_criteria_id = ec.id
    WHERE ta.employee_id = $employee_id
    GROUP BY ec.sub_name
";
$result = mysqli_query($conn, $queryTasks);

$labels = [];
$planData = [];
$scoreData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['sub_criteria_name'];
    $planData[] = $row['total_plan'];
    $scoreData[] = $row['total_score'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 20px; }
        .chart-container {
            width: 100%;
            max-width: 900px;
            height: 450px; /* Ensures the chart has enough space */
            margin: auto;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content" style="margin-left: 250px; padding: 20px;">
    <h2>Welcome, <?php echo $_SESSION['name']; ?></h2>
    <p>Access your tasks, submit evaluations, and manage your profile here.</p>

    <!-- Task Progress Chart -->
    <div class="mt-5 chart-container">
        <h3><i class="fas fa-chart-bar"></i> Task Progress by KPI Criteria</h3>
        <canvas id="taskChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('taskChart').getContext('2d');

            // Check if data is available
            var labels = <?php echo json_encode($labels); ?>;
            var planData = <?php echo json_encode($planData); ?>;
            var scoreData = <?php echo json_encode($scoreData); ?>;

            if (labels.length === 0) {
                console.error("No data available for the chart.");
                document.getElementById('taskChart').parentElement.innerHTML = "<p class='text-center text-danger'>No task data available.</p>";
                return;
            }

            var taskChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Planned Tasks',
                            data: planData,
                            backgroundColor: 'rgba(0, 123, 255, 0.7)',
                            borderColor: '#0056b3',
                            borderWidth: 1,
                            hoverBackgroundColor: 'rgba(0, 123, 255, 1)'
                        },
                        {
                            label: 'Completed Tasks',
                            data: scoreData,
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: '#1c7430',
                            borderWidth: 1,
                            hoverBackgroundColor: 'rgba(40, 167, 69, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuad'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                },
                                usePointStyle: true,
                                boxWidth: 10
                            },
                            onClick: function(e, legendItem) {
                                var index = legendItem.datasetIndex;
                                var meta = taskChart.getDatasetMeta(index);
                                meta.hidden = meta.hidden === null ? !taskChart.data.datasets[index].hidden : null;
                                taskChart.update();
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Task Count'
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
