<?php
// Configuration de base
define('ENV', 'development'); // 'development' ou 'production'

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs
if (ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Configuration de la session
if (session_status() === PHP_SESSION_NONE) {
    $sessionDir = __DIR__ . '/../tmp/sessions';
    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0755, true);
    }
    session_save_path($sessionDir);
    $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443;
    session_start([
        'name' => 'cabinet_session',
        'cookie_httponly' => true,
        'cookie_secure' => $isSecure,
        'cookie_samesite' => 'Strict',
        'cookie_lifetime' => 86400 // 24 heures
    ]);
}

// Configuration de la base de données SQLite
define('DB_NAME', __DIR__ . '/../database/cabinet_excellence.db');
define('DB_TYPE', 'sqlite');

// Configuration du site
define('SITE_NAME', 'Cabinet Juridique Excellence');
define('SITE_URL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/');
define('ADMIN_EMAIL', 'admin@cabinet-excellence.fr');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Mot de passe en clair pour insertion initiale, hashé dans Database.php

// Chemins absolus
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', __DIR__ . '/');
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('UPLOAD_PATH', PUBLIC_PATH . 'uploads/');
define('CONTACT_UPLOAD_PATH', UPLOAD_PATH . 'contact_files/');
define('TEAM_UPLOAD_PATH', UPLOAD_PATH . 'team/');
define('NEWS_UPLOAD_PATH', UPLOAD_PATH . 'news/');
define('EVENTS_UPLOAD_PATH', UPLOAD_PATH . 'events/');
define('DEFAULT_TEAM_IMAGE', '/public/uploads/team/default_team_member.jpeg');
define('DEFAULT_NEWS_IMAGE', '/public/uploads/news/default_news.jpg');
define('DEFAULT_EVENT_IMAGE', '/public/uploads/events/default_event.jpg');

// Configuration des uploads
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png'
]);
define('ALLOWED_FILE_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Configuration de sécurité
define('SESSION_NAME', 'cabinet_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Configuration SMTP (placeholder, à configurer pour production)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');
define('SMTP_FROM_NAME', SITE_NAME);
define('SMTP_FROM_EMAIL', ADMIN_EMAIL);

// Création des répertoires nécessaires
$directories = [
    dirname(DB_NAME),
    __DIR__ . '/../logs',
    __DIR__ . '/../tmp/sessions',
    UPLOAD_PATH,
    CONTACT_UPLOAD_PATH,
    TEAM_UPLOAD_PATH,
    NEWS_UPLOAD_PATH,
    EVENTS_UPLOAD_PATH,
    VIEWS_PATH
];

foreach ($directories as $dir) {
    try {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true) || !is_dir($dir)) {
                error_log("Échec de la création du répertoire : $dir");
                throw new Exception("Impossible de créer le répertoire : $dir");
            }
            chmod($dir, 0755);
        }
        if (!is_writable($dir)) {
            error_log("Répertoire non accessible en écriture : $dir");
            throw new Exception("Répertoire non accessible en écriture : $dir");
        }
    } catch (Exception $e) {
        error_log("Erreur lors de la configuration du répertoire : " . $e->getMessage());
    }
}

// Génération du jeton CSRF
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        try {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            error_log("Erreur lors de la génération du jeton CSRF : " . $e->getMessage());
            return null;
        }
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Vérification du jeton CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || empty($_SESSION[CSRF_TOKEN_NAME])) {
        error_log("Jeton CSRF manquant dans la session");
        return false;
    }
    $isValid = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    if (!$isValid) {
        error_log("Échec de la vérification du jeton CSRF. Reçu : $token, Attendu : " . $_SESSION[CSRF_TOKEN_NAME]);
    }
    // Régénérer le jeton après vérification pour éviter la réutilisation
    unset($_SESSION[CSRF_TOKEN_NAME]);
    generateCSRFToken();
    return $isValid;
}

// Obtenir l'URL de base
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    
    // Si le script est à la racine, dirname retourne '/', ce qui peut causer des doubles slashs.
    // Nous le remplaçons par une chaîne vide pour éviter ce problème.
    if ($path === '/' || $path === '\\') {
        $path = '';
    }
    
    return $protocol . $host . $path;
}

// Sanitisation de la sortie
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

// Destruction de la session
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443;
        setcookie(SESSION_NAME, '', time() - 3600, '/', '', $isSecure, true);
    }
}

// Vérification de la connexion
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirection
function redirect($url) {
    $baseUrl = getBaseUrl();
    // On s'assure que l'URL de destination commence par un slash, et un seul.
    $location = '/' . ltrim($url, '/');
    $finalUrl = $baseUrl . $location;
    header("Location: " . $finalUrl);
    exit;
}

// Envoi d'email (placeholder, utiliser PHPMailer pour production)
function sendEmail($to, $subject, $message, $headers = '') {
    try {
        $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
        $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        if (mail($to, $subject, $message, $headers)) {
            error_log("Email envoyé à : $to, sujet : $subject");
            return true;
        } else {
            error_log("Échec de l'envoi de l'email à : $to, sujet : $subject");
            return false;
        }
    } catch (Exception $e) {
        error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        return false;
    }
}

// Vérification des permissions des fichiers uploadés
function verifyUploadPermissions($filePath) {
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return is_writable($dir);
}
?>