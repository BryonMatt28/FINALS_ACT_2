<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';


// Handle logout if the logout button is clicked
if (isset($_POST['logout'])) {
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Existing logic for adding and displaying applicants...
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['logout']) && !isset($_POST['edit_applicant'])) {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'specialization' => $_POST['specialization'],
        'years_experience' => $_POST['years_experience']
    ];

    $response = createApplicant($pdo, $data, $_SESSION['user_id']);


}

// Check if editing an applicant
$applicantToEdit = null;
if (isset($_GET['edit'])) {
    $applicantId = (int)$_GET['edit'];
    $applicantResponse = getApplicantById($pdo, $applicantId);
    if (isset($applicantResponse['statusCode']) && $applicantResponse['statusCode'] === 200) {
        $applicantToEdit = $applicantResponse['querySet'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_applicant'])) {
    $applicantId = (int)$_POST['applicant_id'];
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'specialization' => $_POST['specialization'],
        'years_experience' => $_POST['years_experience']
    ];

    $updateResponse = updateApplicant($pdo, $_SESSION['user_id'], $applicantId, $data);

    if ($updateResponse['success']) {
        // Log the activity after updating an applicant
        logActivity($pdo, $_SESSION['user_id'], 'UPDATE', 'Updated applicant: ' . $_POST['first_name'] . ' ' . $_POST['last_name']);

        $message = "<p class='message success'>" . htmlspecialchars($updateResponse['message']) . "</p>";
        $applicantToEdit = null; // Clear the edit form after updating
    } else {
        $message = "<p class='message error'>" . htmlspecialchars($updateResponse['message']) . "</p>";
    }
}

$applicants = readApplicants($pdo);

if (isset($applicants['statusCode']) && $applicants['statusCode'] === 200) {
    $applicantList = $applicants['querySet'];
} else {
    $applicantList = [];
    $message = "<p class='message error'>Error fetching applicants.</p>";
}

$searchResults = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $searchResultsResponse = searchApplicants($pdo, $searchQuery);

    if (isset($searchResultsResponse['statusCode']) && $searchResultsResponse['statusCode'] === 200) {
        $searchResults = $searchResultsResponse['querySet'];
    } else {
        $message = "<p class='message error'>Error searching applicants.</p>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
    <style>
        /* Your existing CSS styles */
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
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .logout-button {
            background-color: #f44336;
            color: white;
            margin-bottom: 20px;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #e02f2f;
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
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Job Application System</h1>

        <!-- Logout button -->
        <form method="POST">
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>

        <!-- Activity Logs Button -->
        <a href="activity_logs.php"><button type="button">View Activity Logs</button></a>

        <!-- Display message -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Add New Applicant Form -->
        <h2>Add New Applicant</h2>
        <form method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <input type="text" name="specialization" placeholder="Specialization" required>
            <input type="number" name="years_experience" placeholder="Years of Experience" required>
            <button type="submit">Add Applicant</button>
        </form>

        <!-- Search -->
        <form method="GET">
            <input type="text" name="search" placeholder="Search applicants..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($searchResults)): ?>
            <h2>Search Results</h2>
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                </tr>
                <?php foreach ($searchResults as $applicant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($applicant['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['years_experience']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <h2>All Applicants Table</h2>

        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Specialization</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($applicantList)): ?>
                <?php foreach ($applicantList as $applicant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($applicant['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['specialization']); ?></td>
                        <td>
                            <a href="edit.php?edit=<?php echo urlencode($applicant['applicant_id']); ?>" style="text-decoration: none;">
                                <button type="button">Edit</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No applicants found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
