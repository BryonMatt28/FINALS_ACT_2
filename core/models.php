<?php

require_once 'core/dbConfig.php';

function createApplicant($pdo, $data, $userId) {
    try {
        $sql = "INSERT INTO applicants (first_name, last_name, email, phone_number, specialization, years_experience) 
                VALUES (:first_name, :last_name, :email, :phone_number, :specialization, :years_experience)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':phone_number' => $data['phone_number'],
            ':specialization' => $data['specialization'],
            ':years_experience' => $data['years_experience'],
        ]);

        // Log the activity
        logActivity($pdo, $userId, 'INSERT', 'Added a new applicant');

        return [
            'message' => 'Applicant added successfully!',
            'statusCode' => 200,
            'success' => true
        ];
    } catch (Exception $e) {
        return [
            'message' => 'Failed to add applicant: ' . $e->getMessage(),
            'statusCode' => 400,
            'success' => false
        ];
    }
}

// Ensure readApplicants function includes the applicant_id field in the query
function readApplicants($pdo) {
    $sql = "SELECT applicant_id, first_name, last_name, email, phone_number, specialization, years_experience FROM applicants";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($applicants) {
        return [
            'statusCode' => 200,
            'querySet' => $applicants,
        ];
    } else {
        return [
            'statusCode' => 400,
            'message' => 'No applicants found.',
        ];
    }
}



/**
 * Search applicants by various fields.
 */
function searchApplicants($pdo, $searchQuery) {
    try {
        $sql = "SELECT * FROM applicants 
                WHERE first_name LIKE :search 
                OR last_name LIKE :search 
                OR email LIKE :search 
                OR phone_number LIKE :search 
                OR specialization LIKE :search 
                OR years_experience LIKE :search";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':search', '%' . $searchQuery . '%', PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'message' => 'Search completed successfully.',
            'statusCode' => 200,
            'querySet' => $result
        ];
    } catch (Exception $e) {
        return [
            'message' => 'Failed to search applicants: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

/**
 * Update an applicant's details.
 */
function updateApplicant($pdo, $userId, $applicantId, $data) {
    try {
        // Ensure the column names in the query match your database schema
        $sql = "UPDATE applicants SET first_name = :first_name, last_name = :last_name, 
                email = :email, phone_number = :phone_number, specialization = :specialization, 
                years_experience = :years_experience WHERE applicant_id = :applicant_id";

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone_number', $data['phone_number']);
        $stmt->bindParam(':specialization', $data['specialization']);
        $stmt->bindParam(':years_experience', $data['years_experience']);
        $stmt->bindParam(':applicant_id', $applicantId);


        logActivity($pdo, $userId, 'UPDATE', 'Updated an applicant');
        // Execute the query
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Applicant updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update applicant: ' . $e->getMessage()];
    }
}

/**
 * Delete an applicant by ID.
 */
function deleteApplicant($pdo,$userID, $applicant_id) {
    // Make sure to use the correct column name 'applicant_id' in the WHERE clause
    $sql = "DELETE FROM applicants WHERE applicant_id = :applicant_id";
    $stmt = $pdo->prepare($sql);


    logActivity($pdo, $userID, 'DELETE', 'Deleted an applicant');

    // Bind the applicant_id to the prepared statement
    $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Applicant deleted successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to delete applicant.'];
    }
}

function registerUser($pdo, $data) {
    try {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $data['username'],
            ':password' => $hashedPassword,
            ':email' => $data['email']
        ]);
        return [
            'message' => 'User registered successfully!',
            'statusCode' => 200
        ];
    } catch (Exception $e) {
        return [
            'message' => 'Failed to register user: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

/**
 * Login a user.
 */
function loginUser($pdo, $username, $password) {
    try {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return [
                'message' => 'Login successful!',
                'statusCode' => 200,
                'user' => $user
            ];
        } else {
            return [
                'message' => 'Invalid username or password.',
                'statusCode' => 401
            ];
        }
    } catch (Exception $e) {
        return [
            'message' => 'Login failed: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

/**
 * Log user activity.
 */
// Function to log activity
function logActivity($pdo, $user_id, $actionType, $actionDescription) {
    // Check if the user is logged in and if action data is valid

        // Prepare the SQL query to insert the activity log
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action_type, action_description, action_time)
                               VALUES (?, ?, ?, NOW())");

        // Execute the query with parameters
        $stmt->execute([$user_id, $actionType, $actionDescription]);
}



function getApplicantById($pdo, $applicantId) {
    $sql = "SELECT * FROM applicants WHERE applicant_id = :applicant_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicant_id', $applicantId, PDO::PARAM_INT);
    $stmt->execute();
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($applicant) {
        return [
            'statusCode' => 200,
            'querySet' => $applicant
        ];
    } else {
        return [
            'statusCode' => 404,
            'message' => 'Applicant not found.'
        ];
    }
}



