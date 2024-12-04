<?php

require_once 'core/dbConfig.php';

function fetchActivityLogs($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM activity_logs ORDER BY action_time	DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <style>
        /* Add basic styles for the logs table */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Activity Logs</h1>

        <table border="1" cellspacing="0" cellpadding="8">
            <tr>
                <th>User ID</th>
                <th>Action Type</th>
                <th>Action Description</th>
                <th>Timestamp</th>
            </tr>
            <?php
            $activityLogs = fetchActivityLogs($pdo);
            ?>
            <?php if (!empty($activityLogs)): ?>
                <?php foreach ($activityLogs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_type']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_description']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_time']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No activity logs available.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
