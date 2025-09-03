<?php
require_once 'includes/Database.php';
require_once 'includes/config.php';

class HomeController {
    private $db;
    private $uploadDir;
    private $maxFileSize = 10 * 1024 * 1024; // 10MB
    private $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];

    public function __construct() {
        try {
            $this->uploadDir = CONTACT_UPLOAD_PATH;
            $database = new Database();
            $this->db = $database->getConnection();
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("La connexion à la base de données est null ou invalide");
            }
            $this->ensureUploadDirectory();
        } catch (Exception $e) {
            error_log("HomeController Constructor Error: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Erreur d\'initialisation du serveur : ' . $e->getMessage()]));
        }
    }

    public function index() {
        try {
            $content = $this->getContent();
            $services = $this->getServices();
            $team = $this->getTeam();
            $news = $this->getNews();
            $events = $this->getEvents();
            $appointment_slots = $this->getAvailableAppointmentSlots();

            // Validate data structure
            if (!is_array($content) || !is_array($services) || !is_array($team) || !is_array($news) || !is_array($events) || !is_array($appointment_slots)) {
                error_log("Invalid data structure in HomeController::index");
                throw new Exception("Données invalides récupérées depuis la base de données");
            }

            include 'views/home.php';
        } catch (Exception $e) {
            error_log("HomeController::index Error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors du chargement de la page : ' . $e->getMessage()];
            $content = $this->getDefaultContent();
            $services = $this->getDefaultServices();
            $team = $this->getDefaultTeam();
            $news = $this->getDefaultNews();
            $events = $this->getDefaultEvents();
            $appointment_slots = [];
            include 'views/error.php';
        }
    }

    public function dashboard() {
        try {
            $stats = $this->getStats();
            $recent_contacts = $this->getRecentContacts();
            $upcoming_appointments = $this->getUpcomingAppointments();

            // Validate data structure
            if (!is_array($stats) || !is_array($recent_contacts) || !is_array($upcoming_appointments)) {
                error_log("Invalid data structure in HomeController::dashboard");
                throw new Exception("Données invalides pour le tableau de bord");
            }

            include 'views/admin/dashboard.php';
        } catch (Exception $e) {
            error_log("HomeController::dashboard Error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors du chargement du tableau de bord : ' . $e->getMessage()];
            $stats = $this->getDefaultStats();
            $recent_contacts = [];
            $upcoming_appointments = [];
            include 'views/admin/dashboard.php';
        }
    }

    public function schedule() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                    throw new Exception('Token CSRF invalide');
                }

                $action = $_POST['action'] ?? '';
                if ($action === 'add_daily_slots') {
                    $this->addDailySlots();
                } elseif ($action === 'delete_slot') {
                    $this->deleteSlot();
                } else {
                    throw new Exception('Action invalide');
                }
            }

            $stats = $this->getStats();
            $slots = $this->getAllAppointmentSlots();

            // Validate data structure
            if (!is_array($stats) || !is_array($slots)) {
                error_log("Invalid data structure in HomeController::schedule");
                throw new Exception("Données invalides pour le planning");
            }

            include 'views/admin/schedule.php';
        } catch (Exception $e) {
            error_log("HomeController::schedule Error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors du chargement du planning : ' . $e->getMessage()];
            $stats = $this->getDefaultStats();
            $slots = [];
            include 'views/admin/schedule.php';
        }
    }

    public function contacts() {
        try {
            $contacts = $this->getAllContacts();
            $stats = $this->getStats();

            // Validate data structure
            if (!is_array($contacts) || !is_array($stats)) {
                error_log("Invalid data structure in HomeController::contacts");
                throw new Exception("Données invalides pour les contacts");
            }

            include 'views/admin/contacts.php';
        } catch (Exception $e) {
            error_log("HomeController::contacts Error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors du chargement des contacts : ' . $e->getMessage()];
            $contacts = [];
            $stats = $this->getDefaultStats();
            include 'views/admin/contacts.php';
        }
    }

    public function handleContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, "Méthode non autorisée", [], 405);
        }

        try {
            // Vérifier la connexion à la base de données
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }

            error_log("handleContact: Starting transaction");
            $this->db->beginTransaction();
            $formData = $this->validateContactForm();
            $contactId = $this->saveContact($formData);
            $uploadedFiles = $this->handleFileUploads($contactId);

            $message = "Message envoyé avec succès";
            if (isset($formData['appointment_requested']) && $formData['appointment_requested'] === '1') {
                $appointmentId = $this->saveAppointment($contactId, $formData);
                if ($appointmentId) {
                    $stmt = $this->db->prepare("UPDATE contacts SET appointment_id = :appointment_id WHERE id = :contact_id");
                    $stmt->execute([':appointment_id' => $appointmentId, ':contact_id' => $contactId]);
                    $message = $formData['payment_method'] === 'online'
                        ? "Paiement effectué avec succès ! Votre rendez-vous est confirmé."
                        : "Demande de rendez-vous envoyée ! Nous vous contacterons pour confirmer.";
                } else {
                    $message = "Message envoyé, mais échec de la création du rendez-vous.";
                }
            }

            if (!empty($uploadedFiles)) {
                $message .= " (" . count($uploadedFiles) . " fichier(s) joint(s))";
            }

            if ($this->db->inTransaction()) {
                error_log("handleContact: Committing transaction");
                $this->db->commit();
            }
            return $this->sendJsonResponse(true, $message, ['uploaded_files' => count($uploadedFiles)]);
        } catch (Exception $e) {
            if ($this->db && $this->db->inTransaction()) {
                error_log("handleContact: Rolling back transaction due to error: " . $e->getMessage());
                $this->db->rollBack();
            }
            error_log("HomeController::handleContact Error: " . $e->getMessage());
            return $this->sendJsonResponse(false, "Erreur lors du traitement de votre demande : " . $e->getMessage(), [], 400);
        }
    }

    private function validateContactForm() {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token CSRF invalide');
        }

        $requiredFields = ['name', 'email', 'message'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Le champ $field est requis";
            }
        }

        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide";
        }

        $appointmentRequested = isset($_POST['appointment_requested']) && $_POST['appointment_requested'] === '1';
        if ($appointmentRequested) {
            if (empty($_POST['slot_id'])) {
                $errors[] = "Sélection d'un créneau de rendez-vous requis";
            } else {
                $slotId = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT);
                if ($slotId === false || $slotId <= 0) {
                    $errors[] = "Identifiant de créneau invalide";
                } else {
                    $stmt = $this->db->prepare("SELECT id FROM appointment_slots WHERE id = :id AND is_booked = 0 AND start_time > datetime('now')");
                    $stmt->execute([':id' => $slotId]);
                    if (!$stmt->fetch()) {
                        $errors[] = "Créneau de rendez-vous invalide ou déjà réservé";
                    }
                }
            }

            if (empty($_POST['payment_method']) || !in_array($_POST['payment_method'], ['online', 'onsite'])) {
                $errors[] = "Mode de paiement invalide";
            } elseif ($_POST['payment_method'] === 'online') {
                $paymentFields = ['cardNumber', 'cardExpiry', 'cardCvc', 'cardName'];
                foreach ($paymentFields as $field) {
                    if (empty($_POST[$field])) {
                        $errors[] = "Le champ $field est requis pour le paiement en ligne";
                    }
                }
                $cardNumber = str_replace(' ', '', $_POST['cardNumber'] ?? '');
                if (!preg_match('/^\d{16}$/', $cardNumber)) {
                    $errors[] = "Numéro de carte invalide";
                }
                if (!preg_match('/^\d{2}\/\d{2}$/', $_POST['cardExpiry'] ?? '')) {
                    $errors[] = "Date d'expiration invalide";
                }
                if (!preg_match('/^\d{3,4}$/', $_POST['cardCvc'] ?? '')) {
                    $errors[] = "Code CVC invalide";
                }
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        return [
            'name' => trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'phone' => trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'subject' => trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'message' => trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'appointment_requested' => $appointmentRequested ? '1' : '0',
            'payment_method' => trim(filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'slot_id' => $appointmentRequested ? filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT) : null
        ];
    }

    private function saveContact($formData) {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            error_log("saveContact: Saving contact for " . $formData['email']);
            $sql = "INSERT INTO contacts (name, email, phone, subject, message, appointment_requested, payment_method, status, created_at, updated_at) 
                    VALUES (:name, :email, :phone, :subject, :message, :appointment_requested, :payment_method, 'new', datetime('now'), datetime('now'))";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $formData['name'],
                ':email' => $formData['email'],
                ':phone' => $formData['phone'],
                ':subject' => $formData['subject'],
                ':message' => $formData['message'],
                ':appointment_requested' => $formData['appointment_requested'],
                ':payment_method' => $formData['payment_method'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Save contact error: " . $e->getMessage());
            throw new Exception("Erreur lors de l'enregistrement du contact : " . $e->getMessage());
        }
    }

    private function saveAppointment($contactId, $formData) {
        if (!$formData['appointment_requested'] || !$formData['slot_id']) {
            return null;
        }

        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            error_log("saveAppointment: Saving appointment for contact ID $contactId, slot ID " . $formData['slot_id']);
            if (!$this->db->inTransaction()) {
                $this->db->beginTransaction();
            }
            $status = $formData['payment_method'] === 'online' ? 'confirmed' : 'pending';
            $sql = "INSERT INTO appointments (contact_id, slot_id, status, created_at, updated_at) 
                    VALUES (:contact_id, :slot_id, :status, datetime('now'), datetime('now'))";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':contact_id' => $contactId,
                ':slot_id' => $formData['slot_id'],
                ':status' => $status
            ]);
            $appointmentId = $this->db->lastInsertId();

            $sql = "UPDATE appointment_slots SET is_booked = 1, updated_at = datetime('now') WHERE id = :slot_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':slot_id' => $formData['slot_id']]);

            if ($this->db->inTransaction()) {
                error_log("saveAppointment: Committing transaction");
                $this->db->commit();
            }
            return $appointmentId;
        } catch (PDOException $e) {
            if ($this->db && $this->db->inTransaction()) {
                error_log("saveAppointment: Rolling back transaction due to error: " . $e->getMessage());
                $this->db->rollBack();
            }
            error_log("Save appointment error: " . $e->getMessage());
            throw new Exception("Erreur lors de l'enregistrement du rendez-vous : " . $e->getMessage());
        }
    }

    private function addDailySlots() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            } 
            $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $allDay = isset($_POST['all_day']) && $_POST['all_day'] === 'on';
            $startTime = $allDay ? '09:00' : trim(filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $endTime = $allDay ? '18:00' : trim(filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $breakStart = trim(filter_input(INPUT_POST, 'break_start', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ?: null;
            $breakEnd = trim(filter_input(INPUT_POST, 'break_end', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ?: null;

            // Validate inputs
            if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                throw new Exception("Date invalide");
            }
            if (strtotime($date) < strtotime(date('Y-m-d'))) {
                throw new Exception("La date ne peut pas être dans le passé");
            }
            if (!$allDay) {
                if (!$startTime || !$endTime || !preg_match('/^\d{2}:\d{2}$/', $startTime) || !preg_match('/^\d{2}:\d{2}$/', $endTime)) {
                    throw new Exception("Heures de début ou de fin invalides");
                }
                if (strtotime($startTime) >= strtotime($endTime)) {
                    throw new Exception("L'heure de début doit être antérieure à l'heure de fin");
                }
            }
            if ($breakStart && $breakEnd) {
                if (!preg_match('/^\d{2}:\d{2}$/', $breakStart) || !preg_match('/^\d{2}:\d{2}$/', $breakEnd)) {
                    throw new Exception("Heures de pause invalides");
                }
                if (strtotime($breakStart) >= strtotime($breakEnd)) {
                    throw new Exception("Le début de la pause doit être antérieur à la fin de la pause");
                }
                if (strtotime($breakStart) < strtotime($startTime) || strtotime($breakEnd) > strtotime($endTime)) {
                    throw new Exception("La pause doit être comprise dans les heures de disponibilité");
                }
            }

            error_log("addDailySlots: Starting transaction for date $date");
            $this->db->beginTransaction();

            // Generate 30-minute slots
            $start = new DateTime("$date $startTime");
            $end = new DateTime("$date $endTime");
            $interval = new DateInterval('PT30M');
            $slots = [];
            $breakStartTime = $breakStart ? new DateTime("$date $breakStart") : null;
            $breakEndTime = $breakEnd ? new DateTime("$date $breakEnd") : null;

            while ($start < $end) {
                $slotEnd = clone $start;
                $slotEnd->add($interval);
                if ($slotEnd > $end) break;

                // Skip slots during break
                if ($breakStartTime && $breakEndTime && $start >= $breakStartTime && $start < $breakEndTime) {
                    $start->add($interval);
                    continue;
                }

                $slots[] = [
                    'start_time' => $start->format('Y-m-d H:i:s'),
                    'end_time' => $slotEnd->format('Y-m-d H:i:s')
                ];
                $start->add($interval);
            }

            // Insert slots into database
            $stmt = $this->db->prepare("INSERT INTO appointment_slots (start_time, end_time, is_booked, created_at, updated_at) 
                                        VALUES (:start_time, :end_time, 0, datetime('now'), datetime('now'))");
            foreach ($slots as $slot) {
                $stmt->execute([
                    ':start_time' => $slot['start_time'],
                    ':end_time' => $slot['end_time']
                ]);
            }

            if ($this->db->inTransaction()) {
                error_log("addDailySlots: Committing transaction");
                $this->db->commit();
            }
            $_SESSION['flash_message'] = ['success' => true, 'message' => count($slots) . ' créneaux ajoutés avec succès'];
        } catch (Exception $e) {
            if ($this->db && $this->db->inTransaction()) {
                error_log("addDailySlots: Rolling back transaction due to error: " . $e->getMessage());
                $this->db->rollBack();
            }
            error_log("Add daily slots error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors de l\'ajout des créneaux : ' . $e->getMessage()];
        }
    }

    private function deleteSlot() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $slotId = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT);
            if (!$slotId || $slotId <= 0) {
                throw new Exception("Identifiant de créneau invalide");
            }

            error_log("deleteSlot: Checking slot ID $slotId");
            // Verify slot is not booked
            $stmt = $this->db->prepare("SELECT is_booked FROM appointment_slots WHERE id = :id");
            $stmt->execute([':id' => $slotId]);
            $slot = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$slot) {
                throw new Exception("Créneau introuvable");
            }
            if ($slot['is_booked']) {
                throw new Exception("Impossible de supprimer un créneau réservé");
            }

            $stmt = $this->db->prepare("DELETE FROM appointment_slots WHERE id = :id");
            $stmt->execute([':id' => $slotId]);

            $_SESSION['flash_message'] = ['success' => true, 'message' => 'Créneau supprimé avec succès'];
        } catch (Exception $e) {
            error_log("Delete slot error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors de la suppression du créneau : ' . $e->getMessage()];
        }
    }

    private function getStats() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stats = [
                'contacts' => 0,
                'new_contacts' => 0,
                'appointments' => 0,
                'services' => 0,
                'team_members' => 0
            ];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM contacts");
            $stats['contacts'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'new'");
            $stats['new_contacts'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM appointments WHERE status IN ('pending', 'confirmed')");
            $stats['appointments'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1");
            $stats['services'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM team_members WHERE is_active = 1");
            $stats['team_members'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];

            return $stats;
        } catch (Exception $e) {
            error_log("Get stats error: " . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getRecentContacts() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("
                SELECT c.*, a.status as appointment_status, s.start_time as appointment_time
                FROM contacts c
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN appointment_slots s ON a.slot_id = s.id
                ORDER BY c.created_at DESC
                LIMIT 5
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Get recent contacts error: " . $e->getMessage());
            return [];
        }
    }

    private function getUpcomingAppointments() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("
                SELECT a.*, c.name, c.email, s.start_time as appointment_time
                FROM appointments a
                JOIN contacts c ON a.contact_id = c.id
                JOIN appointment_slots s ON a.slot_id = s.id
                WHERE s.start_time >= datetime('now')
                AND a.status IN ('pending', 'confirmed')
                ORDER BY s.start_time ASC
                LIMIT 5
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Get upcoming appointments error: " . $e->getMessage());
            return [];
        }
    }

    private function getAllContacts() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("
                SELECT c.*, a.status as appointment_status, s.start_time as appointment_time
                FROM contacts c
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN appointment_slots s ON a.slot_id = s.id
                ORDER BY c.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Get all contacts error: " . $e->getMessage());
            return [];
        }
    }

    private function getAllAppointmentSlots() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("
                SELECT 
                    s.*, 
                    c.name as contact_name, 
                    c.email as contact_email, 
                    a.status as appointment_status
                FROM appointment_slots s
                LEFT JOIN appointments a ON s.id = a.slot_id
                LEFT JOIN contacts c ON a.contact_id = c.id
                ORDER BY s.start_time DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Get all appointment slots error: " . $e->getMessage());
            return [];
        }
    }

    private function handleFileUploads($contactId) {
        if (empty($_FILES['documents']['name'][0])) {
            return [];
        }

        if (!extension_loaded('fileinfo')) {
            error_log("Fileinfo extension not loaded");
            throw new Exception("Erreur serveur : extension fileinfo requise");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $uploadedFiles = [];
        $errors = [];

        foreach ($_FILES['documents']['name'] as $key => $name) {
            if ($_FILES['documents']['error'][$key] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur de téléversement pour le fichier : $name (code: {$_FILES['documents']['error'][$key]})";
                continue;
            }

            $file = [
                'name' => $name,
                'type' => finfo_file($finfo, $_FILES['documents']['tmp_name'][$key]),
                'tmp_name' => $_FILES['documents']['tmp_name'][$key],
                'size' => $_FILES['documents']['size'][$key]
            ];

            try {
                if (!$this->validateFile($file)) {
                    $errors[] = "Fichier invalide : $name";
                    continue;
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('doc_') . '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                $destination = $this->uploadDir . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $sql = "INSERT INTO contact_files (contact_id, original_name, file_name, file_path, file_size, file_type, uploaded_at, updated_at) 
                            VALUES (:contact_id, :original_name, :file_name, :file_path, :file_size, :file_type, datetime('now'), datetime('now'))";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        ':contact_id' => $contactId,
                        ':original_name' => $file['name'],
                        ':file_name' => $newFileName,
                        ':file_path' => '/public/uploads/contact_files/' . $newFileName,
                        ':file_size' => $file['size'],
                        ':file_type' => $file['type']
                    ]);
                    $uploadedFiles[] = $newFileName;
                } else {
                    $errors[] = "Échec du déplacement du fichier : $name";
                }
            } catch (Exception $e) {
                $errors[] = "Erreur avec le fichier $name : " . $e->getMessage();
            }
        }

        finfo_close($finfo);

        if (!empty($errors)) {
            error_log("File upload errors: " . implode(', ', $errors));
        }

        return $uploadedFiles;
    }

    private function validateFile($file) {
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new Exception("Type de fichier non autorisé : {$file['name']}");
        }
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("Fichier trop volumineux : {$file['name']} (Max : 10MB)");
        }
        if ($file['size'] <= 0) {
            throw new Exception("Fichier vide : {$file['name']}");
        }
        return true;
    }

    private function ensureUploadDirectory() {
        try {
            if (!is_dir($this->uploadDir)) {
                if (!mkdir($this->uploadDir, 0755, true)) {
                    throw new Exception("Impossible de créer le répertoire d'upload : {$this->uploadDir}");
                }
            }
            if (!is_writable($this->uploadDir)) {
                throw new Exception("Le répertoire d'upload n'est pas accessible en écriture : {$this->uploadDir}");
            }
        } catch (Exception $e) {
            error_log("Ensure upload directory error: " . $e->getMessage());
            throw $e;
        }
    }

    private function getAvailableAppointmentSlots() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("SELECT id, start_time, end_time FROM appointment_slots WHERE is_booked = 0 AND start_time > datetime('now') ORDER BY start_time ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Get available appointment slots error: " . $e->getMessage());
            return [];
        }
    }

    private function getContent() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("SELECT section, key_name, value FROM site_content");
            $content = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $content[$row['section']][$row['key_name']] = $row['value'];
            }
            return !empty($content) ? $content : $this->getDefaultContent();
        } catch (Exception $e) {
            error_log("Get content error: " . $e->getMessage());
            return $this->getDefaultContent();
        }
    }

    private function getServices() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($services as &$service) {
                $service['icon'] = $service['icon'] ?: 'fas fa-gavel';
                $service['color'] = $service['color'] ?: '#3b82f6';
            }
            return !empty($services) ? $services : $this->getDefaultServices();
        } catch (Exception $e) {
            error_log("Get services error: " . $e->getMessage());
            return $this->getDefaultServices();
        }
    }

    private function getTeam() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_position");
            $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($team as &$member) {
                $imagePath = $member['image_path'] ?? '';
                $member['image_path'] = $imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)
                    ? $imagePath
                    : '/public/uploads/team/default_team_member.jpeg';
            }
            return !empty($team) ? $team : $this->getDefaultTeam();
        } catch (Exception $e) {
            error_log("Get team error: " . $e->getMessage());
            return $this->getDefaultTeam();
        }
    }

    private function getNews() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("SELECT * FROM news WHERE is_active = 1 ORDER BY publish_date DESC LIMIT 3");
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($news as &$item) {
                $imagePath = $item['image_path'] ?? '';
                $item['image_path'] = $imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)
                    ? $imagePath
                    : '/public/uploads/news/default_news.jpg';
            }
            return !empty($news) ? $news : $this->getDefaultNews();
        } catch (Exception $e) {
            error_log("Get news error: " . $e->getMessage());
            return $this->getDefaultNews();
        }
    }

    private function getEvents() {
        try {
            if ($this->db === null || !($this->db instanceof PDO)) {
                throw new Exception("Connexion à la base de données non initialisée");
            }
            $stmt = $this->db->query("SELECT * FROM events WHERE is_active = 1 ORDER BY event_date DESC LIMIT 3");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($events as &$item) {
                $imagePath = $item['image_path'] ?? '';
                $item['image_path'] = $imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)
                    ? $imagePath
                    : '/public/uploads/events/default_event.jpg';
            }
            return !empty($events) ? $events : $this->getDefaultEvents();
        } catch (Exception $e) {
            error_log("Get events error: " . $e->getMessage());
            return $this->getDefaultEvents();
        }
    }

    private function getDefaultStats() {
        return [
            'contacts' => 0,
            'new_contacts' => 0,
            'appointments' => 0,
            'services' => 0,
            'team_members' => 0
        ];
    }

    private function getDefaultContent() {
        return [
            'hero' => [
                'title' => defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence',
                'subtitle' => 'Votre partenaire de confiance pour tous vos besoins juridiques'
            ],
            'about' => [
                'title' => 'À propos de nous',
                'subtitle' => 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne avec professionnalisme.'
            ],
            'services' => [
                'title' => 'Nos services',
                'subtitle' => 'Des domaines d\'expertise variés pour répondre à tous vos besoins'
            ],
            'team' => [
                'title' => 'Notre équipe',
                'subtitle' => 'Des experts à votre service'
            ],
            'news' => [
                'title' => 'Nos dernières actualités',
                'subtitle' => 'Restez informé des dernières nouvelles juridiques.'
            ],
            'events' => [
                'title' => 'Nos prochains événements',
                'subtitle' => 'Participez à nos conférences et ateliers juridiques.'
            ],
            'contact' => [
                'title' => 'Contactez-nous',
                'address' => '123 Avenue de la Justice, 75001 Paris, France',
                'phone' => '+33 1 23 45 67 89',
                'email' => defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@cabinet-excellence.fr'
            ],
            'footer' => [
                'copyright' => '© ' . date('Y') . ' ' . (defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence') . '. Tous droits réservés.'
            ],
            'values' => [
                'title' => 'Nos valeurs',
                'subtitle' => 'Intégrité, professionnalisme et engagement.'
            ]
        ];
    }

    private function getDefaultServices() {
        return [
            [
                'id' => 1,
                'title' => 'Droit des Affaires',
                'description' => 'Accompagnement juridique complet pour les entreprises.',
                'icon' => 'fas fa-briefcase',
                'color' => '#3b82f6',
                'order_position' => 1,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'id' => 2,
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'id' => 3,
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières et contentieux.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'id' => 4,
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ]
        ];
    }

    private function getDefaultTeam() {
        return [
            [
                'id' => 1,
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'id' => 2,
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 2,
                'is_active' => 1
            ],
            [
                'id' => 3,
                'name' => 'Maître Paul Lefèvre',
                'position' => 'Avocat - Droit Immobilier',
                'description' => 'Expert en transactions immobilières et litiges fonciers.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 3,
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultNews() {
        return [
            [
                'id' => 1,
                'title' => 'Nouvelles Réglementations en Droit des Affaires',
                'content' => 'Découvrez les dernières évolutions législatives affectant les entreprises en 2025.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Réforme du Droit de la Famille',
                'content' => 'Une analyse approfondie des récentes modifications du droit matrimonial.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'is_active' => 1
            ],
            [
                'id' => 3,
                'title' => 'Nouveau Partenariat Stratégique',
                'content' => 'Notre cabinet s’associe à un leader en conseil fiscal pour offrir des services intégrés.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s', strtotime('-2 weeks')),
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultEvents() {
        return [
            [
                'id' => 1,
                'title' => 'Conférence sur le Droit Digital',
                'content' => 'Rejoignez-nous pour une conférence sur les défis juridiques du monde digital.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Atelier Droit des Affaires',
                'content' => 'Atelier pratique sur les contrats commerciaux.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+2 months')),
                'is_active' => 1
            ],
            [
                'id' => 3,
                'title' => 'Séminaire sur la Conformité RGPD',
                'content' => 'Apprenez à respecter les réglementations sur la protection des données.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+3 months')),
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultDetailedContent() {
        return '
            <h3>Notre approche</h3>
            <p>Nous offrons une expertise sur-mesure adaptée à vos besoins spécifiques, avec un suivi personnalisé.</p>
            <ul>
                <li>Analyse approfondie de votre dossier</li>
                <li>Stratégie juridique adaptée</li>
                <li>Accompagnement à chaque étape</li>
                <li>Suivi post-résolution</li>
            </ul>
            <h3>Pourquoi nous choisir ?</h3>
            <p>Notre expérience et notre engagement garantissent :</p>
            <ul>
                <li>Expertise reconnue</li>
                <li>Approche client-centrée</li>
                <li>Réactivité et disponibilité</li>
                <li>Transparence des honoraires</li>
            </ul>
        ';
    }

    private function sendJsonResponse($success, $message, $data = [], $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>