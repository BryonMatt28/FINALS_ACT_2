<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Check if the 'id' parameter is set in the URL
if (!isset($_GET['edit'])) {
    echo "Invalid applicant ID.";
    exit;
}

$applicantId = (int)$_GET['edit'];

// Fetch the applicant's details
$applicantResponse = getApplicantById($pdo, $applicantId);

// Check if the response has the 'statusCode' key and is 200
if (isset($applicantResponse['statusCode']) && $applicantResponse['statusCode'] !== 200) {
    echo $applicantResponse['message'];
    exit;
}

$applicant = $applicantResponse['querySet'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Handle the delete action
        $deleteResponse = deleteApplicant($pdo, $_GET['edit'],$applicantId);

        // Check if delete was successful
        if (isset($deleteResponse['statusCode']) && $deleteResponse['statusCode'] === 200) {
            header("Location: index.php?message=Applicant deleted successfully!");
            exit;
        } else {
            echo $deleteResponse['message'];
        }
    } elseif (isset($_POST['update'])) {
        // Handle the update action
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone_number' => $_POST['phone_number'],
            'specialization' => $_POST['specialization'],
            'years_experience' => $_POST['years_experience']
        ];

        $updateResponse = updateApplicant($pdo, $_GET['edit'], $applicantId, $data);

        // Check if the update was successful
        if (isset($updateResponse['success']) && $updateResponse['success']) {
            header("Location: index.php?message=Applicant updated successfully!");
            exit;
        } else {
            echo $updateResponse['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Applicant</h1>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input 
                        type="text" 
                        id="first_name"
                        name="first_name" 
                        value="<?php echo htmlspecialchars($applicant['first_name']); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input 
                        type="text" 
                        id="last_name"
                        name="last_name" 
                        value="<?php echo htmlspecialchars($applicant['last_name']); ?>" 
                        required
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        value="<?php echo htmlspecialchars($applicant['email']); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input 
                        type="text" 
                        id="phone_number"
                        name="phone_number" 
                        value="<?php echo htmlspecialchars($applicant['phone_number']); ?>" 
                        required
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input 
                        type="text" 
                        id="specialization"
                        name="specialization" 
                        value="<?php echo htmlspecialchars($applicant['specialization']); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="years_experience">Years of Experience</label>
                    <input 
                        type="number" 
                        id="years_experience"
                        name="years_experience" 
                        value="<?php echo htmlspecialchars($applicant['years_experience']); ?>" 
                        required
                    >
                </div>
            </div>

            <div class="button-group">
                <button type="submit" name="update" class="btn btn-primary">Update Applicant</button>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this applicant?');">Delete Applicant</button>
                <a href="index.php" class="btn btn-back">Back to List</a>
            </div>
        </form>
    </div>
</body>
</html>
