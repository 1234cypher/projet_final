<?php
// Inclure les fichiers de configuration et dépendances
require_once __DIR__ . '/includes/config.php';
require_once INCLUDES_PATH . 'Database.php';
require_once INCLUDES_PATH . 'Router.php';

// Initialisation implicite de la session via config.php (déjà gérée par session_start())

// Récupérer le jeton CSRF généré par config.php
$csrfToken = generateCSRFToken();

try {
    // Initialiser la base de données
    $database = new Database();
    $db = $database->getConnection();

    // Initialiser le routeur
    $router = new Router();

    // Définir les routes
    $router->add('/', 'HomeController', 'index', 'GET');
    $router->add('/contact', 'ContactController', 'submit', 'POST');
    $router->add('/api/appointment-slots', 'ContactController', 'getAvailableSlots', 'GET');
    $router->add('/admin', 'AdminController', 'login', 'GET');
    $router->add('/admin/login', 'AdminController', 'login', 'POST');
    $router->add('/admin/dashboard', 'AdminController', 'dashboard', 'GET');
    $router->add('/admin/content', 'AdminController', 'content', 'GET');
    $router->add('/admin/content', 'AdminController', 'content', 'POST');
    $router->add('/admin/contacts', 'AdminController', 'contacts', 'GET');
    $router->add('/admin/schedule', 'AdminController', 'schedule', 'GET');
    $router->add('/admin/schedule', 'AdminController', 'schedule', 'POST');
    $router->add('/admin/settings', 'AdminController', 'settings', 'GET');
    $router->add('/admin/logout', 'AdminController', 'logout', 'GET');
    $router->add('/service/{id}', 'ServiceController', 'show', 'GET');
    $router->add('/admin/message/{id}', 'AdminController', 'messageDetail', 'GET');

    // Récupérer l'URL actuelle et la méthode HTTP
    $url = $_SERVER['REQUEST_URI'];
    $url = strtok($url, '?'); // Supprimer les paramètres de requête
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $queryParams = $_GET;

    // Router la requête
    $router->route($url, $httpMethod, $queryParams);

} catch (Exception $e) {
    error_log("Erreur d'application : " . $e->getMessage() . " à " . date('Y-m-d H:i:s'));
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Erreur serveur interne : ' . h($e->getMessage())], JSON_UNESCAPED_UNICODE);
    exit;
}
?>