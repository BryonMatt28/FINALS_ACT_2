<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';


// Handle actions
$response = [
    'message' => 'No action performed.',
    'statusCode' => 400,
    'querySet' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine the action
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        // Create an applicant
        $response = $model->createApplicant([
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone_number' => $_POST['phone_number'],
            'specialization' => $_POST['specialization'],
            'years_experience' => $_POST['years_experience']
        ]);
    } elseif ($action === 'update') {
        // Update an applicant
        $response = $model->updateApplicant($_POST['applicant_id'], [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone_number' => $_POST['phone_number'],
            'specialization' => $_POST['specialization'],
            'years_experience' => $_POST['years_experience']
        ]);
    } elseif ($action === 'delete') {
        // Delete an applicant
        $response = $model->deleteApplicant($_POST['applicant_id']);
    }
}

// Return the response
header('Content-Type: application/json');
echo json_encode($response);
?>
