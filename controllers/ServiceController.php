<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

class ServiceController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function show($id) {
        // Récupérer le service par ID
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$service) {
            header('Location: /#services');
            exit;
        }
        
        // Récupérer tous les services pour la navigation
        $services = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/service-detail.php';
    }
}
?>