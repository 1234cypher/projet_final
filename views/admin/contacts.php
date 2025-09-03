<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Administration</title>
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
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(135deg, #1f2937, #111827);
            color: white;
            padding: 2rem 0;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 1rem 2rem;
            border-bottom: 1px solid #374151;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        .sidebar-nav i {
            margin-right: 0.75rem;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Filters */
        .filters-bar {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .filter-select, .filter-input {
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
        }

        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Messages Table */
        .messages-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .messages-table {
            width: 100%;
            border-collapse: collapse;
        }

        .messages-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 0.9rem;
        }

        .messages-table td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .messages-table tr:hover {
            background: #f9fafb;
        }

        .message-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-row:hover {
            background: #f0f9ff;
        }

        .message-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .message-email {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .message-subject {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .message-preview {
            font-size: 0.85rem;
            color: #6b7280;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .message-date {
            font-size: 0.85rem;
            color: #6b7280;
            text-align: center;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
            display: inline-block;
            min-width: 60px;
        }

        .status-new {
            background: #fef3c7;
            color: #92400e;
            animation: pulse 2s infinite;
        }

        .status-read {
            background: #d1fae5;
            color: #065f46;
        }

        .files-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .files-count {
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .no-files {
            color: #d1d5db;
        }

        .actions-cell {
            text-align: center;
        }

        .action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination button {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button:hover:not(:disabled) {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination .current {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .messages-table {
                font-size: 0.8rem;
            }
            
            .message-preview {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence'; ?></h2>
                <p>Administration</p>
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin/dashboard">
                    <i class="fas fa-chart-line"></i>
                    Tableau de bord
                </a></li>
                <li><a href="/admin/content">
                    <i class="fas fa-edit"></i>
                    Contenu du site
                </a></li>
                <li><a href="/admin/contacts" class="active">
                    <i class="fas fa-envelope"></i>
                    Messages
                </a></li>
                <li><a href="/admin/settings">
                    <i class="fas fa-cog"></i>
                    Paramètres
                </a></li>
                <li><a href="/" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Voir le site
                </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Messages</h1>
                <div class="breadcrumb">Administration / Messages</div>
            </div>

            <!-- Filters Bar -->
            <div class="filters-bar">
                <form method="GET" id="filtersForm">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Statut</label>
                            <select name="status" class="filter-select" onchange="applyFilters()">
                                <option value="">Tous les messages</option>
                                <option value="new" <?php echo (isset($_GET['status']) && $_GET['status'] === 'new') ? 'selected' : ''; ?>>Nouveaux</option>
                                <option value="read" <?php echo (isset($_GET['status']) && $_GET['status'] === 'read') ? 'selected' : ''; ?>>Lus</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Date</label>
                            <input type="date" name="date" class="filter-input" value="<?php echo $_GET['date'] ?? ''; ?>" onchange="applyFilters()">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Tri</label>
                            <select name="sort" class="filter-select" onchange="applyFilters()">
                                <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : ''; ?>>Plus récent</option>
                                <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : ''; ?>>Plus ancien</option>
                                <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'name') ? 'selected' : ''; ?>>Nom A-Z</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Recherche</label>
                            <input type="text" name="search" class="filter-input" placeholder="Nom, email, sujet..." value="<?php echo $_GET['search'] ?? ''; ?>" oninput="applyFilters()">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">&nbsp;</label>
                            <div class="filter-buttons">
                                <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Messages Section -->
            <div class="messages-section">
                <h2 class="section-title">
                    <i class="fas fa-envelope"></i>
                    Liste des messages
                    <span style="margin-left: auto; font-size: 0.9rem; color: #6b7280;" id="message-count">
                        <?php echo count($contacts); ?> message(s)
                    </span>
                </h2>
                
                <?php if (!empty($success)): ?>
                    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($contacts)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucun message</h3>
                        <p>Il n'y a pas de messages correspondant à vos critères.</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="messages-table" id="messages-table">
                            <thead>
                                <tr>
                                    <th>Expéditeur</th>
                                    <th>Sujet / Message</th>
                                    <th>Date</th>
                                    <th>Fichiers</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="messages-tbody">
                                <?php foreach ($contacts as $contact): ?>
                                    <tr class="message-row" 
                                        data-name="<?php echo htmlspecialchars(strtolower($contact['name'])); ?>" 
                                        data-email="<?php echo htmlspecialchars(strtolower($contact['email'])); ?>" 
                                        data-subject="<?php echo htmlspecialchars(strtolower($contact['subject'] ?? '')); ?>" 
                                        data-message="<?php echo htmlspecialchars(strtolower($contact['message'])); ?>" 
                                        data-date="<?php echo date('Y-m-d', strtotime($contact['created_at'])); ?>" 
                                        data-status="<?php echo htmlspecialchars($contact['status']); ?>" 
                                        data-timestamp="<?php echo strtotime($contact['created_at']); ?>">
                                        <td>
                                            <div class="message-name"><?php echo htmlspecialchars($contact['name']); ?></div>
                                            <div class="message-email"><?php echo htmlspecialchars($contact['email']); ?></div>
                                            <?php if (!empty($contact['phone'])): ?>
                                                <div class="message-email"><?php echo htmlspecialchars($contact['phone']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="message-subject"><?php echo htmlspecialchars($contact['subject'] ?: 'Aucun sujet'); ?></div>
                                            <div class="message-preview"><?php echo htmlspecialchars(substr($contact['message'], 0, 100)) . (strlen($contact['message']) > 100 ? '...' : ''); ?></div>
                                        </td>
                                        <td class="message-date">
                                            <?php echo date('d/m/Y', strtotime($contact['created_at'])); ?><br>
                                            <small><?php echo date('H:i', strtotime($contact['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="files-indicator">
                                                <?php $fileCount = $contact['file_count'] ?? 0; ?>
                                                <?php if ($fileCount > 0): ?>
                                                    <i class="fas fa-paperclip"></i>
                                                    <span class="files-count"><?php echo $fileCount; ?></span>
                                                <?php else: ?>
                                                    <i class="fas fa-minus no-files"></i>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $contact['status'] === 'new' ? 'status-new' : 'status-read'; ?>">
                                                <?php echo $contact['status'] === 'new' ? 'Nouveau' : 'Lu'; ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="action-btn btn-view" onclick="event.stopPropagation(); viewMessage(<?php echo $contact['id']; ?>)" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn btn-delete" onclick="event.stopPropagation(); deleteMessage(<?php echo $contact['id']; ?>)" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Store original table rows for filtering
        const originalRows = Array.from(document.querySelectorAll('#messages-tbody tr'));
        
        function applyFilters() {
            const status = document.querySelector('select[name="status"]').value;
            const date = document.querySelector('input[name="date"]').value;
            const sort = document.querySelector('select[name="sort"]').value;
            const search = document.querySelector('input[name="search"]').value.toLowerCase();

            // Filter rows
            let filteredRows = originalRows.filter(row => {
                const rowStatus = row.dataset.status;
                const rowDate = row.dataset.date;
                const rowName = row.dataset.name;
                const rowEmail = row.dataset.email;
                const rowSubject = row.dataset.subject;
                const rowMessage = row.dataset.message;

                // Status filter
                if (status && rowStatus !== status) {
                    return false;
                }

                // Date filter
                if (date && rowDate !== date) {
                    return false;
                }

                // Search filter
                if (search && !(
                    rowName.includes(search) ||
                    rowEmail.includes(search) ||
                    rowSubject.includes(search) ||
                    rowMessage.includes(search)
                )) {
                    return false;
                }

                return true;
            });

            // Sort rows
            filteredRows.sort((a, b) => {
                if (sort === 'newest') {
                    return b.dataset.timestamp - a.dataset.timestamp;
                } else if (sort === 'oldest') {
                    return a.dataset.timestamp - b.dataset.timestamp;
                } else if (sort === 'name') {
                    return a.dataset.name.localeCompare(b.dataset.name);
                }
                return 0;
            });

            // Update table
            const tbody = document.getElementById('messages-tbody');
            tbody.innerHTML = '';
            filteredRows.forEach(row => tbody.appendChild(row));

            // Update message count
            document.getElementById('message-count').textContent = `${filteredRows.length} message(s)`;

            // Show/hide empty state
            const emptyState = document.querySelector('.empty-state');
            if (filteredRows.length === 0 && !emptyState) {
                const messagesSection = document.querySelector('.messages-section');
                messagesSection.innerHTML += `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucun message</h3>
                        <p>Il n'y a pas de messages correspondant à vos critères.</p>
                    </div>
                `;
            } else if (filteredRows.length > 0 && emptyState) {
                emptyState.remove();
            }
        }

        function resetFilters() {
            const form = document.getElementById('filtersForm');
            form.querySelector('select[name="status"]').value = '';
            form.querySelector('input[name="date"]').value = '';
            form.querySelector('select[name="sort"]').value = 'newest';
            form.querySelector('input[name="search"]').value = '';
            applyFilters();
        }

        function viewMessage(id) {
            window.open('/admin/message/' + id, '_blank');
        }

        function deleteMessage(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
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

        // Auto-refresh for new messages
        setInterval(function() {
            const newBadges = document.querySelectorAll('.status-new');
            newBadges.forEach(badge => {
                badge.style.animation = 'pulse 1s ease-in-out';
            });
        }, 30000); // Every 30 seconds

        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyFilters();
        });
    </script>
</body>
</html>