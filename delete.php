<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicantId = (int)$_POST['id'];
    $deleteResponse = deleteApplicant($pdo, $applicantId);

    if ($deleteResponse['statusCode'] === 200) {
        header("Location: index.php?message=Applicant deleted successfully!");
    } else {
        header("Location: index.php?message=" . urlencode($deleteResponse['message']));
    }
    exit;
}
