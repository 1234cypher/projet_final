<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message de <?php echo htmlspecialchars($contact['name']); ?> - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-info h1 {
            font-size: 1.8rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #3b82f6;
            text-decoration: none;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .message-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .message-header {
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .sender-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .sender-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .sender-details h3 {
            color: #1f2937;
            font-size: 1.3rem;
            margin-bottom: 0.25rem;
        }

        .sender-details p {
            color: #6b7280;
            margin: 0.25rem 0;
        }

        .message-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 10px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .meta-icon {
            color: #3b82f6;
            width: 20px;
        }

        .meta-label {
            font-weight: 600;
            color: #374151;
        }

        .meta-value {
            color: #6b7280;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-new {
            background: #fef3c7;
            color: #92400e;
        }

        .status-read {
            background: #d1fae5;
            color: #065f46;
        }

        .message-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .content-title {
            font-size: 1.2rem;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message-text {
            font-size: 1rem;
            line-height: 1.7;
            color: #374151;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
        }

        .files-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .files-grid {
            display: grid;
            gap: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .file-item:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }

        .file-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .file-icon.pdf {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        .file-icon.doc {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .file-icon.image {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .file-info {
            flex: 1;
        }

        .file-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
            word-break: break-word;
        }

        .file-details {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .file-actions {
            display: flex;
            gap: 0.5rem;
        }

        .file-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-download {
            background: #10b981;
            color: white;
        }

        .file-btn:hover {
            transform: scale(1.1);
        }

        .pdf-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            padding: 1rem;
        }

        .pdf-modal-content {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .pdf-modal-header {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pdf-modal-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.2rem;
        }

        .pdf-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .pdf-modal-close:hover {
            background: #ef4444;
            color: white;
        }

        .pdf-modal-body {
            flex: 1;
            overflow: auto;
        }

        .pdf-embed {
            width: 100%;
            height: 100%;
            border: none;
        }

        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            padding: 1rem;
            flex-direction: column;
        }

        .image-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .image-modal-header h3 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
        }

        .image-modal-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-modal-body img {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 10px;
        }

        .actions-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .action-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .action-card h3 {
            font-size: 1.1rem;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .contact-info {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 2px solid #bfdbfe;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e0f2fe;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            color: #2563eb;
            width: 20px;
        }

        .info-text {
            color: #1e40af;
            font-weight: 500;
        }

        .empty-files {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        .empty-files i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .message-meta {
                grid-template-columns: 1fr;
            }

            .sender-info {
                flex-direction: column;
                text-align: center;
            }

            .header-actions {
                flex-wrap: wrap;
            }

            .pdf-modal, .image-modal {
                padding: 0.5rem;
            }

            .pdf-modal-content, .image-modal {
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div class="header-info">
                <h1>Message de <?php echo htmlspecialchars($contact['name']); ?></h1>
                <div class="breadcrumb">
                    <a href="/admin/contacts">Messages</a> / Détail du message
                </div>
            </div>
            <div class="header-actions">
                <a href="/admin/contacts" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <button class="btn btn-danger" onclick="deleteMessage(<?php echo $contact['id']; ?>)">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </div>
        </div>

        <div class="content-grid">
            <div class="main-content">
                <div class="message-card">
                    <div class="message-header">
                        <div class="sender-info">
                            <div class="sender-avatar">
                                <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                            </div>
                            <div class="sender-details">
                                <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contact['email']); ?></p>
                                <?php if (!empty($contact['phone'])): ?>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contact['phone']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="message-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar meta-icon"></i>
                                <span class="meta-label">Date:</span>
                                <span class="meta-value"><?php echo date('d/m/Y à H:i', strtotime($contact['created_at'])); ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-tag meta-icon"></i>
                                <span class="meta-label">Sujet:</span>
                                <span class="meta-value"><?php echo htmlspecialchars($contact['subject'] ?: 'Aucun sujet'); ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-info-circle meta-icon"></i>
                                <span class="meta-label">Statut:</span>
                                <span class="status-badge <?php echo $contact['status'] === 'new' ? 'status-new' : 'status-read'; ?>">
                                    <?php echo $contact['status'] === 'new' ? 'Nouveau' : 'Lu'; ?>
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-paperclip meta-icon"></i>
                                <span class="meta-label">Fichiers:</span>
                                <span class="meta-value"><?php echo count($files); ?> fichier(s)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="message-content">
                    <h2 class="content-title">
                        <i class="fas fa-comment-alt"></i>
                        Message
                    </h2>
                    <div class="message-text">
                        <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
                    </div>
                </div>

                <?php if (!empty($files)): ?>
                    <div class="files-section">
                        <h2 class="content-title">
                            <i class="fas fa-paperclip"></i>
                            Fichiers joints (<?php echo count($files); ?>)
                        </h2>
                        <div class="files-grid">
                            <?php foreach ($files as $file): ?>
                                <div class="file-item">
                                    <div class="file-icon <?php echo getFileIconClass($file['original_name']); ?>">
                                        <i class="<?php echo getFileIcon($file['original_name']); ?>"></i>
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name"><?php echo htmlspecialchars($file['original_name']); ?></div>
                                        <div class="file-details">
                                            <?php echo formatFileSize($file['file_size']); ?> • 
                                            <?php echo strtoupper(pathinfo($file['original_name'], PATHINFO_EXTENSION)); ?> • 
                                            <?php echo date('d/m/Y H:i', strtotime($file['uploaded_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="file-actions">
                                        <?php if (strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                                            <button class="file-btn btn-view" onclick="viewPDF('<?php echo htmlspecialchars($file['file_path']); ?>', '<?php echo htmlspecialchars($file['original_name']); ?>')" title="Aperçu">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php elseif (in_array(strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                            <button class="file-btn btn-view" onclick="viewImage('<?php echo htmlspecialchars($file['file_path']); ?>', '<?php echo htmlspecialchars($file['original_name']); ?>')" title="Aperçu">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download="<?php echo htmlspecialchars($file['original_name']); ?>" class="file-btn btn-download" title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="files-section">
                        <h2 class="content-title">
                            <i class="fas fa-paperclip"></i>
                            Fichiers joints
                        </h2>
                        <div class="empty-files">
                            <i class="fas fa-file"></i>
                            <h3>Aucun fichier joint</h3>
                            <p>Ce message ne contient aucun fichier.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="actions-sidebar">
                <div class="action-card contact-info">
                    <h3>
                        <i class="fas fa-user"></i>
                        Informations de contact
                    </h3>
                    <div class="info-item">
                        <i class="fas fa-user info-icon"></i>
                        <span class="info-text"><?php echo htmlspecialchars($contact['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope info-icon"></i>
                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="info-text"><?php echo htmlspecialchars($contact['email']); ?></a>
                    </div>
                    <?php if (!empty($contact['phone'])): ?>
                        <div class="info-item">
                            <i class="fas fa-phone info-icon"></i>
                            <a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>" class="info-text"><?php echo htmlspecialchars($contact['phone']); ?></a>
                        </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <i class="fas fa-clock info-icon"></i>
                        <span class="info-text"><?php echo date('d/m/Y à H:i', strtotime($contact['created_at'])); ?></span>
                    </div>
                </div>

                <div class="action-card">
                    <h3>
                        <i class="fas fa-cog"></i>
                        Actions
                    </h3>
                    <div class="action-list">
                        <?php if ($contact['status'] === 'new'): ?>
                            <button class="btn btn-primary" onclick="markAsRead(<?php echo $contact['id']; ?>)">
                                <i class="fas fa-check"></i>
                                Marquer comme lu
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary" onclick="markAsNew(<?php echo $contact['id']; ?>)">
                                <i class="fas fa-undo"></i>
                                Marquer comme nouveau
                            </button>
                        <?php endif; ?>
                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>?subject=Re: <?php echo htmlspecialchars($contact['subject'] ?: 'Votre message'); ?>" class="btn btn-primary">
                            <i class="fas fa-reply"></i>
                            Répondre par email
                        </a>
                        <button class="btn btn-danger" onclick="deleteMessage(<?php echo $contact['id']; ?>)">
                            <i class="fas fa-trash"></i>
                            Supprimer le message
                        </button>
                    </div>
                </div>

                <div class="action-card">
                    <h3>
                        <i class="fas fa-chart-bar"></i>
                        Statistiques
                    </h3>
                    <div class="info-item">
                        <i class="fas fa-envelope info-icon"></i>
                        <span class="info-text">ID: <?php echo $contact['id']; ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-paperclip info-icon"></i>
                        <span class="info-text"><?php echo count($files); ?> fichier(s) joint(s)</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-text-width info-icon"></i>
                        <span class="info-text"><?php echo str_word_count($contact['message']); ?> mots</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar info-icon"></i>
                        <span class="info-text">Il y a <?php echo timeAgo($contact['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pdfModal" class="pdf-modal">
        <div class="pdf-modal-content">
            <div class="pdf-modal-header">
                <h3 id="pdfTitle"></h3>
                <button class="pdf-modal-close" onclick="closePDFModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="pdf-modal-body">
                <embed id="pdfEmbed" class="pdf-embed" src="" type="application/pdf">
            </div>
        </div>
    </div>

    <div id="imageModal" class="image-modal">
        <div class="image-modal-header">
            <h3 id="imageTitle"></h3>
            <button class="btn btn-secondary" onclick="closeImageModal()">
                <i class="fas fa-times"></i>
                Fermer
            </button>
        </div>
        <div class="image-modal-body">
            <img id="modalImage" src="" alt="">
        </div>
    </div>

    <script>
        function viewPDF(filePath, fileName) {
            document.getElementById('pdfTitle').textContent = fileName;
            document.getElementById('pdfEmbed').src = filePath + '#toolbar=1';
            document.getElementById('pdfModal').style.display = 'block';
        }

        function closePDFModal() {
            document.getElementById('pdfModal').style.display = 'none';
            document.getElementById('pdfEmbed').src = '';
        }

        function viewImage(filePath, fileName) {
            document.getElementById('imageTitle').textContent = fileName;
            document.getElementById('modalImage').src = filePath;
            document.getElementById('imageModal').style.display = 'flex';
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
            document.getElementById('modalImage').src = '';
        }

        function markAsRead(id) {
            updateMessageStatus(id, 'read');
        }

        function markAsNew(id) {
            updateMessageStatus(id, 'new');
        }

        function updateMessageStatus(id, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/contacts';
            form.innerHTML = `
                <input type="hidden" name="action" value="mark_${status}">
                <input type="hidden" name="id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function deleteMessage(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/contacts';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePDFModal();
            }
        });

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePDFModal();
                closeImageModal();
            }
        });
    </script>

    <?php
    function getFileIcon($fileName) {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'pdf':
                return 'fas fa-file-pdf';
            case 'doc':
            case 'docx':
                return 'fas fa-file-word';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'fas fa-file-image';
            default:
                return 'fas fa-file';
        }
    }

    function getFileIconClass($fileName) {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'pdf':
                return 'pdf';
            case 'doc':
            case 'docx':
                return 'doc';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'image';
            default:
                return 'default';
        }
    }

    function formatFileSize($bytes) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        if ($time < 60) return 'moins d\'1 minute';
        if ($time < 3600) return floor($time/60) . ' minutes';
        if ($time < 86400) return floor($time/3600) . ' heures';
        if ($time < 2592000) return floor($time/86400) . ' jours';
        if ($time < 31104000) return floor($time/2592000) . ' mois';
        return floor($time/31104000) . ' ans';
    }
    ?>
</body>
</html>