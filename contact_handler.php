<?php
// Enable error reporting for debugging
ini_set('display_errors', 0); // Hide errors from users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
error_reporting(E_ALL);

// Log request details
error_log('Contact form submission received: ' . $_SERVER['REQUEST_URI']);

// Start session for success/error messages
session_start();

// Check if this is an AJAX request
$isAjax = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

// Database connection
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    error_log('Database connection successful');
} catch (Exception $e) {
    http_response_code(500);
    error_log('Database connection failed: ' . $e->getMessage());
    $errorMessage = 'Erreur serveur : Impossible de se connecter à la base de données';
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    } else {
        $_SESSION['error_message'] = $errorMessage;
        header('Location: /');
        exit;
    }
}

// Validate and sanitize form inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING) ?? '';
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING) ?? '';
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING) ?? '';
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING) ?? '';
$appointment_date = filter_input(INPUT_POST, 'appointment_date', FILTER_SANITIZE_STRING) ?? '';
$slot_id = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT) ?? null;

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    error_log('Form validation failed: Missing required fields');
    $errorMessage = 'Veuillez remplir tous les champs obligatoires';
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    } else {
        $_SESSION['error_message'] = $errorMessage;
        header('Location: /');
        exit;
    }
}

// Insert contact into database
try {
    $stmt = $conn->prepare("
        INSERT INTO contacts (name, email, phone, subject, message, created_at, status)
        VALUES (?, ?, ?, ?, ?, datetime('now'), 'new')
    ");
    $stmt->execute([$name, $email, $phone, $subject, $message]);
    $contact_id = $conn->lastInsertId();
    error_log('Contact inserted successfully: ID ' . $contact_id);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Failed to insert contact: ' . $e->getMessage());
    $errorMessage = 'Erreur serveur : Impossible d\'enregistrer le message';
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    } else {
        $_SESSION['error_message'] = $errorMessage;
        header('Location: /');
        exit;
    }
}

// Handle file uploads
$upload_dir = CONTACT_UPLOAD_PATH; // Defined in config.php
$allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$max_file_size = 5 * 1024 * 1024; // 5MB

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
    error_log('Created upload directory: ' . $upload_dir);
}

if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
    foreach ($_FILES['documents']['name'] as $key => $original_name) {
        if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['documents']['type'][$key];
            $file_size = $_FILES['documents']['size'][$key];
            $tmp_name = $_FILES['documents']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $file_name = 'contact_' . $contact_id . '_' . uniqid() . '.' . $file_ext;
$file_path = '/uploads/contact_files/' . $file_name;

            // Validate file type and size
            if (!in_array($file_type, $allowed_types)) {
                error_log('Invalid file type: ' . $file_type . ' for file: ' . $original_name);
                $_SESSION['error_message'] = 'Type de fichier non autorisé : ' . $original_name;
                continue;
            }
            if ($file_size > $max_file_size) {
                error_log('File too large: ' . $file_size . ' bytes for file: ' . $original_name);
                $_SESSION['error_message'] = 'Fichier trop volumineux : ' . $original_name;
                continue;
            }

            // Move file to upload directory
            if (move_uploaded_file($tmp_name, $upload_dir . '/' . $file_name)) {
                try {
                    $stmt = $conn->prepare("
                        INSERT INTO contact_files (contact_id, original_name, file_name, file_path, file_size, file_type, uploaded_at)
                        VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
                    ");
                    $stmt->execute([$contact_id, $original_name, $file_name, $file_path, $file_size, $file_type]);
                    error_log('File uploaded and saved: ' . $file_path);
                } catch (Exception $e) {
                    error_log('Failed to save file to database: ' . $e->getMessage());
                    $_SESSION['error_message'] = 'Erreur lors de l\'enregistrement du fichier : ' . $original_name;
                }
            } else {
                error_log('Failed to move uploaded file: ' . $original_name);
                $_SESSION['error_message'] = 'Erreur lors de l\'upload du fichier : ' . $original_name;
            }
        } else {
            error_log('Upload error for file: ' . $original_name . ', Error code: ' . $_FILES['documents']['error'][$key]);
            $_SESSION['error_message'] = 'Erreur lors de l\'upload du fichier : ' . $original_name;
        }
    }
}

// Handle appointment if provided
if ($appointment_date && $slot_id) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO appointments (contact_id, appointment_date, slot_id, created_at)
            VALUES (?, ?, ?, datetime('now'))
        ");
        $stmt->execute([$contact_id, $appointment_date, $slot_id]);
        error_log('Appointment saved for contact ID ' . $contact_id);
    } catch (Exception $e) {
        error_log('Failed to save appointment: ' . $e->getMessage());
        $_SESSION['error_message'] = 'Erreur lors de l\'enregistrement du rendez-vous';
    }
}

// Handle response based on request type
if ($isAjax) {
    // Return JSON response for AJAX requests
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Message envoyé avec succès'
    ]);
} else {
    // Set success message and redirect for regular form submissions
    $_SESSION['success_message'] = 'Message envoyé avec succès';
    header('Location: /');
}
exit;
?>