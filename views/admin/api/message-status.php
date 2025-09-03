<?php
   // Enable error reporting
   ini_set('display_errors', 0);
   ini_set('log_errors', 1);
   ini_set('error_log', __DIR__ . '/../../error.log');
   error_reporting(E_ALL);

   // Start session
   session_start();
   if (!isset($_SESSION['admin_logged_in'])) {
       http_response_code(403);
       error_log('Access denied: User not authenticated for /admin/api/message-status');
       header('Content-Type: application/json; charset=UTF-8');
       die(json_encode(['error' => 'Accès refusé']));
   }

   // Database connection
   require_once '../../includes/Database.php';
   try {
       $db = new Database();
       $conn = $db->getConnection();
   } catch (Exception $e) {
       http_response_code(500);
       error_log('Database connection failed: ' . $e->getMessage());
       header('Content-Type: application/json; charset=UTF-8');
       die(json_encode(['error' => 'Erreur serveur']));
   }

   // Get message ID from URL
   $message_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
   if ($message_id <= 0) {
       http_response_code(400);
       error_log('Invalid message ID: ' . $message_id);
       header('Content-Type: application/json; charset=UTF-8');
       die(json_encode(['error' => 'ID de message invalide']));
   }

   // Fetch message status
   $stmt = $conn->prepare("SELECT status FROM contacts WHERE id = ?");
   $stmt->execute([$message_id]);
   $message = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($message) {
       header('Content-Type: application/json; charset=UTF-8');
       echo json_encode(['status' => $message['status']]);
   } else {
       http_response_code(404);
       error_log('Message not found for ID: ' . $message_id);
       header('Content-Type: application/json; charset=UTF-8');
       die(json_encode(['error' => 'Message non trouvé']));
   }
   ?>