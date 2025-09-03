<?php
require_once 'includes/Database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->query("SELECT id, file_path FROM contact_files");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $basePath = 'C:\\Users\\HP PC\\Desktop\\project1\\project-bolt-github-ydgix1ar\\project\\';
    $stmt = $conn->prepare("UPDATE contact_files SET file_path = ? WHERE id = ?");
    
    foreach ($files as $file) {
        $newPath = str_replace($basePath, '', $file['file_path']);
        $newPath = str_replace('\\', '/', $newPath); // Convertir en barres obliques
        $stmt->execute([$newPath, $file['id']]);
    }
    echo "Paths updated successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>