<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

class ContactController {
    private $db;
    private $uploadDir = CONTACT_UPLOAD_PATH;

    public function __construct() {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
        } catch (Exception $e) {
            error_log("ContactController Error: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']));
        }
    }

    private function handleFileUpload($files) {
        $absolute_dir = $_SERVER['DOCUMENT_ROOT'] . $this->uploadDir;
        $allowed_types = ALLOWED_FILE_TYPES;
        $max_size = 5 * 1024 * 1024; // 5MB
        $uploaded_files_details = [];

        if (!is_dir($absolute_dir)) {
            if (!mkdir($absolute_dir, 0755, true)) {
                error_log("Failed to create upload directory: $absolute_dir");
                return ['success' => false, 'message' => 'Erreur : Impossible de créer le répertoire de téléchargement.'];
            }
        }

        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                error_log("File upload error for $name: " . $files['error'][$key]);
                return ['success' => false, 'message' => "Erreur lors de l'upload du fichier $name."];
            }

            if (!in_array($files['type'][$key], $allowed_types)) {
                error_log("Invalid file type for $name: " . $files['type'][$key]);
                return ['success' => false, 'message' => "Type de fichier non autorisé pour $name. Seuls JPG, PNG et PDF sont acceptés."];
            }

            if ($files['size'][$key] > $max_size) {
                error_log("File size too large for $name: " . $files['size'][$key]);
                return ['success' => false, 'message' => "Le fichier $name est trop volumineux. Taille maximale : 5MB."];
            }

            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $filename = uniqid('contact_') . '.' . $extension;
            $destination = $absolute_dir . $filename;
            $relative_path = $this->uploadDir . $filename;

            if (move_uploaded_file($files['tmp_name'][$key], $destination)) {
                $uploaded_files_details[] = [
                    'original_name' => $name,
                    'file_name' => $filename,
                    'file_path' => $relative_path,
                    'file_size' => $files['size'][$key],
                    'file_type' => $files['type'][$key]
                ];
                error_log("File uploaded successfully: $relative_path");
            } else {
                error_log("Failed to move uploaded file: $destination");
                return ['success' => false, 'message' => "Échec de l'upload du fichier $name."];
            }
        }

        return ['success' => true, 'files' => $uploaded_files_details];
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        // Démarrer la transaction au début pour englober tout le processus
        $this->db->beginTransaction();

        try {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                http_response_code(403);
                throw new Exception('Token CSRF invalide');
            }

            $name = htmlspecialchars(trim($_POST['name'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
            $message = htmlspecialchars(trim($_POST['message'] ?? ''));
            $slot_id = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT);

            if (!$name || !$email || !$message || !$slot_id) {
                $errors = [];
                if (!$name) $errors[] = 'Nom requis';
                if (!$email) $errors[] = 'Email invalide';
                if (!$message) $errors[] = 'Message requis';
                if (!$slot_id) $errors[] = 'Sélection d\'un créneau obligatoire';
                error_log("Contact form validation failed: " . implode(', ', $errors));
                http_response_code(400);
                throw new Exception('Erreur : ' . implode(', ', $errors));
            }

            $file_result = ['success' => true, 'files' => []];
            if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
                $file_result = $this->handleFileUpload($_FILES['files']);
                if (!$file_result['success']) {
                    http_response_code(400);
                    throw new Exception($file_result['message']);
                }
            }

            $appointment_id = null;

            // 1. Vérifier la disponibilité du créneau
            $stmt = $this->db->prepare("
                SELECT id FROM appointment_slots 
                WHERE id = ? AND is_booked = 0 AND start_time > datetime('now')
            ");
            $stmt->execute([$slot_id]);
            $slot = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$slot) {
                throw new Exception('Créneau invalide ou non disponible.');
            }

            // 2. Insérer le contact en premier, sans l'ID du rendez-vous pour le moment.
            $stmt = $this->db->prepare("
                INSERT INTO contacts (name, email, phone, message, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'new', datetime('now'), datetime('now'))
            ");
            if (!$stmt->execute([$name, $email, $phone, $message])) {
                throw new Exception('Échec de l\'enregistrement du contact.');
            }
            $contact_id = $this->db->lastInsertId();

            // 3. Créer le rendez-vous (obligatoire maintenant)
            $stmt = $this->db->prepare("
                INSERT INTO appointments (slot_id, contact_id, status, created_at, updated_at)
                VALUES (?, ?, 'pending', datetime('now'), datetime('now'))
            ");
            if (!$stmt->execute([$slot_id, $contact_id])) {
                throw new Exception('Échec de la création du rendez-vous.');
            }
            $appointment_id = $this->db->lastInsertId();

            // Mettre à jour le contact avec l'ID du nouveau rendez-vous.
            $stmt = $this->db->prepare("UPDATE contacts SET appointment_id = ? WHERE id = ?");
            if (!$stmt->execute([$appointment_id, $contact_id])) {
                throw new Exception('Échec de la liaison du rendez-vous au contact.');
            }

            // Marquer le créneau comme réservé.
            $stmt = $this->db->prepare("UPDATE appointment_slots SET is_booked = 1, updated_at = datetime('now') WHERE id = ?");
            if (!$stmt->execute([$slot_id])) {
                throw new Exception('Échec de la réservation du créneau.');
            }

            // 4. Enregistrer les fichiers joints liés au contact.
            if (!empty($file_result['files'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO contact_files (contact_id, original_name, file_name, file_path, file_size, file_type, uploaded_at)
                    VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
                ");
                foreach ($file_result['files'] as $file_detail) {
                    if (!$stmt->execute([
                        $contact_id,
                        $file_detail['original_name'],
                        $file_detail['file_name'],
                        $file_detail['file_path'],
                        $file_detail['file_size'],
                        $file_detail['file_type']
                    ])) {
                        throw new Exception('Échec de l\'enregistrement des fichiers.');
                    }
                }
            }

            // Si tout s'est bien passé, on valide la transaction.
            $this->db->commit();

            $_SESSION['new_appointment'] = true;

            error_log("Contact submitted successfully: contact_id=$contact_id, appointment_id=" . ($appointment_id ?: 'none'));
            http_response_code(200);
            echo json_encode([
                'success' => true, 
                'message' => 'Votre demande de rendez-vous a été envoyée avec succès. Nous vous contacterons pour confirmer.',
                'data' => ['new_token' => generateCSRFToken()]
            ]);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Contact submission error: " . $e->getMessage());
            // Assurer que le code de statut HTTP est une erreur si non déjà défini
            if (http_response_code() < 400) {
                http_response_code(500);
            }
            echo json_encode(['success' => false, 'message' => 'Erreur lors du traitement de votre demande : ' . $e->getMessage()]);
        }
    }

    public function getAvailableSlots() {
        try {
            $date = trim(filter_input(INPUT_GET, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ?? '';
            
            if (!$date || !DateTime::createFromFormat('Y-m-d', $date)) {
                error_log("Invalid date provided: $date");
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Date invalide']);
                return;
            }

            $startDate = $date . ' 00:00:00';
            $endDate = $date . ' 23:59:59';

            $stmt = $this->db->prepare("
                SELECT id, start_time, end_time 
                FROM appointment_slots 
                WHERE is_booked = 0 
                AND start_time >= :start_date 
                AND start_time <= :end_date
                AND start_time > datetime('now')
                ORDER BY start_time ASC
            ");
            
            $stmt->execute([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            
            $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $formattedSlots = [];

            foreach ($slots as $slot) {
                $startTime = new DateTime($slot['start_time']);
                $endTime = new DateTime($slot['end_time']);
                
                $formattedSlots[] = [
                    'id' => $slot['id'],
                    'time_display' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time']
                ];
            }

            header('Content-Type: application/json');
            error_log("Fetched " . count($formattedSlots) . " available slots for date=$date");
            echo json_encode([
                'success' => true,
                'data' => ['slots' => $formattedSlots],
                'message' => count($formattedSlots) . ' créneaux disponibles pour le ' . (new DateTime($date))->format('d/m/Y')
            ]);

        } catch (Exception $e) {
            error_log("Error fetching appointment slots: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement des créneaux']);
        }
    }
}
?>