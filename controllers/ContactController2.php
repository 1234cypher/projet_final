<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

class ContactController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Configuration des dossiers
        define('UPLOAD_DIR', __DIR__ . '/../public/upload/contacts/');
        define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
        define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
        define('ADMIN_EMAIL', 'votre-email@example.com');
        
        // Créer les dossiers nécessaires
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
    }
    
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            try {
                // Validation et nettoyage des données
                $data = $this->sanitizeAndValidate($_POST);
                
                // Traiter les fichiers uploadés
                $uploadedFiles = $this->processFileUploads($_FILES, $data['name']);
                
                // Sauvegarder en base de données
                $contactId = $this->saveToDatabase($data, $uploadedFiles);
                
                // Envoyer l'email
                $this->sendEmail($data, $uploadedFiles);
                
                // Logger la soumission
                $this->logSubmission($data, $uploadedFiles);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Votre demande a été envoyée avec succès. Nous vous contacterons dans les plus brefs délais.'
                ]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    private function sanitizeAndValidate($post) {
        $data = [
            'name' => $this->sanitizeString($post['name'] ?? ''),
            'email' => $this->sanitizeEmail($post['email'] ?? ''),
            'phone' => $this->sanitizeString($post['phone'] ?? ''),
            'subject' => $this->sanitizeString($post['subject'] ?? ''),
            'message' => $this->sanitizeString($post['message'] ?? '')
        ];
        
        // Validation
        if (empty($data['name'])) {
            throw new Exception('Le nom est requis');
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email valide requis');
        }
        
        if (empty($data['message'])) {
            throw new Exception('Le message est requis');
        }
        
        return $data;
    }
    
    private function processFileUploads($files, $senderName) {
        $uploadedFiles = [];
        
        if (!isset($files['documents']) || !is_array($files['documents']['name'])) {
            return $uploadedFiles;
        }
        
        $fileCount = count($files['documents']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['documents']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            if ($files['documents']['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur lors de l'upload du fichier");
            }
            
            $fileInfo = [
                'name' => $files['documents']['name'][$i],
                'type' => $files['documents']['type'][$i],
                'tmp_name' => $files['documents']['tmp_name'][$i],
                'size' => $files['documents']['size'][$i]
            ];
            
            $uploadedFile = $this->processUploadedFile($fileInfo, $senderName);
            if ($uploadedFile) {
                $uploadedFiles[] = $uploadedFile;
            }
        }
        
        return $uploadedFiles;
    }
    
    private function processUploadedFile($fileInfo, $senderName) {
        // Vérifier la taille
        if ($fileInfo['size'] > MAX_FILE_SIZE) {
            throw new Exception("Fichier trop volumineux (max: 10MB)");
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            throw new Exception("Type de fichier non autorisé");
        }
        
        // Générer un nom de fichier sécurisé
        $safeSenderName = preg_replace('/[^a-zA-Z0-9_\-]/', '', str_replace(' ', '_', $senderName));
        $timestamp = date('Y-m-d_H-i-s');
        $uniqueId = uniqid();
        
        $newFilename = "{$safeSenderName}_{$timestamp}_{$uniqueId}.{$extension}";
        $destinationPath = UPLOAD_DIR . $newFilename;
        
        if (!move_uploaded_file($fileInfo['tmp_name'], $destinationPath)) {
            throw new Exception("Impossible de sauvegarder le fichier");
        }
        
        return [
            'original_name' => $fileInfo['name'],
            'saved_name' => $newFilename,
            'path' => str_replace(__DIR__ . '/../public/', '', $destinationPath)
        ];
    }
    
    private function saveToDatabase($data, $uploadedFiles) {
        $stmt = $this->db->prepare("
            INSERT INTO contacts (name, email, phone, subject, message, files, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $filesJson = !empty($uploadedFiles) ? json_encode($uploadedFiles) : null;
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['subject'],
            $data['message'],
            $filesJson,
            date('Y-m-d H:i:s')
        ]);
        
        return $this->db->lastInsertId();
    }
    
    private function sendEmail($data, $uploadedFiles) {
        $to = ADMIN_EMAIL;
        $subject = "[Cabinet Excellence] Nouvelle demande - " . $data['name'];
        
        $htmlBody = $this->generateEmailBody($data, $uploadedFiles);
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $data['email'],
            'Reply-To: ' . $data['email']
        ];
        
        mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    }
    
    private function generateEmailBody($data, $uploadedFiles) {
        $html = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e3a8a; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 30px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #1e3a8a; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nouvelle demande de consultation</h1>
                </div>
                
                <div class='content'>
                    <div class='field'><div class='label'>Nom:</div> {$data['name']}</div>
                    <div class='field'><div class='label'>Email:</div> {$data['email']}</div>
                    <div class='field'><div class='label'>Téléphone:</div> {$data['phone']}</div>
                    <div class='field'><div class='label'>Sujet:</div> {$data['subject']}</div>
                    <div class='field'><div class='label'>Message:</div> {$data['message']}</div>";
        
        if (!empty($uploadedFiles)) {
            $html .= "<div class='field'><div class='label'>Fichiers joints:</div>";
            foreach ($uploadedFiles as $file) {
                $html .= "<div>{$file['original_name']} (sauvegardé: {$file['saved_name']})</div>";
            }
            $html .= "</div>";
        }
        
        $html .= "</div></body></html>";
        return $html;
    }
    
    private function logSubmission($data, $uploadedFiles) {
        $logDir = __DIR__ . '/../logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . 'contact_submissions.log';
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'name' => $data['name'],
            'email' => $data['email'],
            'files' => array_column($uploadedFiles, 'original_name')
        ];
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    private function sanitizeString($string) {
        return trim(strip_tags($string));
    }
    
    private function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
}
?>
