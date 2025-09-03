<?php
// Enable error reporting for debugging
ini_set('display_errors', 0); // Hide errors from users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log'); // Log errors to a dedicated log file
error_reporting(E_ALL);

// Log request details
error_log('Request received: ' . $_SERVER['REQUEST_URI']);

// Start session for authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    error_log('Access denied: User not authenticated');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(['success' => false, 'message' => 'Accès refusé : Veuillez vous connecter']));
}

// Database connection
require_once 'includes/config.php';
require_once 'includes/Database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    error_log('Database connection successful');
} catch (Exception $e) {
    http_response_code(500);
    error_log('Database connection failed: ' . $e->getMessage());
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(['success' => false, 'message' => 'Erreur serveur : Impossible de se connecter à la base de données']));
}

// Get file ID from query parameter
$file_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
error_log('File ID requested: ' . $file_id);

if ($file_id <= 0) {
    http_response_code(400);
    error_log('Invalid file ID: ' . $file_id);
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(['success' => false, 'message' => 'ID de fichier invalide']));
}

// Fetch file details from the database
$stmt = $conn->prepare("SELECT file_path, file_type, original_name FROM contact_files WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    error_log('File not found in database for ID: ' . $file_id);
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(['success' => false, 'message' => 'Fichier non trouvé dans la base de données']));
}
error_log('File found in database: ' . json_encode($file));

// Construct full file path
// The file_path from DB is like: /public/uploads/contact_files/contact_....pdf
// ROOT_PATH is defined in config.php and ends with a slash.
$full_path = realpath(ROOT_PATH . ltrim($file['file_path'], '/'));
error_log('Attempting to access file: ' . $full_path);

// Security check: ensure the file exists and is within the allowed upload directory
$allowed_dir = realpath(CONTACT_UPLOAD_PATH);
if (!$full_path || !$allowed_dir || strpos($full_path, $allowed_dir) !== 0 || !file_exists($full_path)) {
    http_response_code(404);
    error_log('File not found or outside allowed directory. Full path: ' . $full_path . ' Allowed dir: ' . $allowed_dir);
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(['success' => false, 'message' => 'Fichier non trouvé sur le serveur ou accès non autorisé.']));
}

// Use the MIME type stored in the database directly. It's more reliable.
$mime_type = $file['file_type'] ?: 'application/octet-stream';
error_log('Serving file with MIME type: ' . $mime_type);

// Determine if the file should be displayed inline or downloaded
$disposition = (in_array($mime_type, ['application/pdf', 'image/jpeg', 'image/png', 'image/gif']) && isset($_GET['view'])) ? 'inline' : 'attachment';
error_log('Content-Disposition: ' . $disposition);

// Set headers
header('Content-Type: ' . $mime_type);
header('Content-Disposition: ' . $disposition . '; filename="' . basename($file['original_name']) . '"');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'none\'; img-src \'self\'; object-src \'self\';');
header('Accept-Ranges: bytes');

// Output the file
readfile($full_path);
error_log('File served successfully: ' . $full_path);
exit;
?>