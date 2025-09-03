<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tableau de bord d'administration pour Cabinet Excellence">
    <title>Tableau de bord - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            color: white;
            padding: 2rem 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #374151;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-size: 0.95rem;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(59, 130, 246, 0.15);
            border-left-color: #3b82f6;
        }

        .sidebar-nav i {
            margin-right: 1rem;
            width: 24px;
            text-align: center;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: auto;
        }

        .status-new {
            background: #fef3c7;
            color: #92400e;
        }

        .status-pending {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Main Content */
        .main-content {
            padding: 2.5rem;
            max-height: 100vh;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 2.5rem;
        }

        .page-header h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: #6b7280;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .stat-card-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-card-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card-icon.yellow { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-card-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-card-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Sections */
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .contacts-list, .appointments-list {
            list-style: none;
        }

        .contact-item, .appointment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .contact-item:hover, .appointment-item:hover {
            background: #f8fafc;
        }

        .contact-item:last-child, .appointment-item:last-child {
            border-bottom: none;
        }

        .contact-info, .appointment-info {
            flex: 1;
        }

        .contact-info h4, .appointment-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .contact-info p, .appointment-info p {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .contact-meta, .appointment-meta {
            text-align: right;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-read { background: #d1fae5; color: #065f46; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.5s ease;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .logout-btn {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 48px;
            height: 48px;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            background: none;
            border: none;
            color: #1f2937;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1001;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                width: 280px;
                left: -280px;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                padding: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .sidebar-toggle {
                display: block;
            }

            .logout-btn {
                bottom: 1rem;
                left: 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .stat-value {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><?php echo defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : 'Cabinet Excellence'; ?></h2>
                <p>Administration</p>
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin/dashboard" class="active" aria-current="page">
                    <i class="fas fa-chart-line"></i>
                    Tableau de bord
                </a></li>
                <li><a href="/admin/content" aria-current="<?php echo $_SERVER['REQUEST_URI'] === '/admin/content' ? 'page' : 'false'; ?>">
                    <i class="fas fa-edit"></i>
                    Contenu du site
                </a></li>
                <li><a href="/admin/contacts" aria-current="<?php echo $_SERVER['REQUEST_URI'] === '/admin/contacts' ? 'page' : 'false'; ?>">
                    <i class="fas fa-envelope"></i>
                    Messages
                    <?php if ($stats['new_contacts'] > 0): ?>
                        <span class="status-badge status-new"><?php echo htmlspecialchars($stats['new_contacts']); ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="/admin/schedule" aria-current="<?php echo $_SERVER['REQUEST_URI'] === '/admin/schedule' ? 'page' : 'false'; ?>">
                    <i class="fas fa-calendar-alt"></i>
                    Planning
                    <?php if ($new_appointments_count > 0): ?>
                        <span class="status-badge status-pending"><?php echo htmlspecialchars($new_appointments_count); ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="/admin/settings" aria-current="<?php echo $_SERVER['REQUEST_URI'] === '/admin/settings' ? 'page' : 'false'; ?>">
                    <i class="fas fa-cog"></i>
                    Paramètres
                </a></li>
                <li><a href="/" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-external-link-alt"></i>
                    Voir le site
                </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Basculer le menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="page-header">
                <h1>Tableau de bord</h1>
                <div class="breadcrumb">Administration / Tableau de bord</div>
            </div>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert <?php echo $_SESSION['flash_message']['success'] ? 'alert-success' : 'alert-error'; ?>">
                    <i class="fas fa-<?php echo $_SESSION['flash_message']['success'] ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"><?php echo htmlspecialchars($stats['contacts']); ?></div>
                            <div class="stat-label">Messages totaux</div>
                        </div>
                        <div class="stat-card-icon blue">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"><?php echo htmlspecialchars($stats['new_contacts']); ?></div>
                            <div class="stat-label">Nouveaux messages</div>
                        </div>
                        <div class="stat-card-icon yellow">
                            <i class="fas fa-exclamation"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"><?php echo htmlspecialchars($stats['appointments']); ?></div>
                            <div class="stat-label">Rendez-vous actifs</div>
                        </div>
                        <div class="stat-card-icon red">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"><?php echo htmlspecialchars($stats['services']); ?></div>
                            <div class="stat-label">Services actifs</div>
                        </div>
                        <div class="stat-card-icon green">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value"><?php echo htmlspecialchars($stats['team_members']); ?></div>
                            <div class="stat-label">Membres de l'équipe</div>
                        </div>
                        <div class="stat-card-icon purple">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Contacts -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-envelope"></i>
                    Messages récents
                </h2>
                <?php if (empty($recent_contacts)): ?>
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">Aucun message pour le moment.</p>
                <?php else: ?>
                    <ul class="contacts-list">
                        <?php foreach ($recent_contacts as $contact): ?>
                            <li class="contact-item">
                                <div class="contact-info">
                                    <h4><a href="/admin/message/<?php echo htmlspecialchars($contact['id']); ?>" style="color: #1f2937; text-decoration: none;"><?php echo htmlspecialchars($contact['name']); ?></a></h4>
                                    <p><?php echo htmlspecialchars($contact['email']); ?> • <?php echo htmlspecialchars($contact['subject'] ?: 'Aucun sujet'); ?></p>
                                    <p><?php echo htmlspecialchars(substr($contact['message'], 0, 100)) . (strlen($contact['message']) > 100 ? '...' : ''); ?></p>
                                    <?php if ($contact['appointment_time']): ?>
                                        <p><strong>Rendez-vous :</strong> <?php echo date('d/m/Y H:i', strtotime($contact['appointment_time'])); ?> 
                                           (<span class="status-badge status-<?php echo $contact['appointment_status']; ?>">
                                               <?php echo $contact['appointment_status'] === 'pending' ? 'En attente' : ($contact['appointment_status'] === 'confirmed' ? 'Confirmé' : 'Annulé'); ?>
                                           </span>)</p>
                                    <?php endif; ?>
                                </div>
                                <div class="contact-meta">
                                    <div class="status-badge status-<?php echo $contact['status']; ?>">
                                        <?php echo $contact['status'] === 'new' ? 'Nouveau' : 'Lu'; ?>
                                    </div>
                                    <div style="margin-top: 0.5rem;">
                                        <?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="/admin/contacts" class="btn btn-primary" aria-label="Voir tous les messages">
                            <i class="fas fa-eye"></i>
                            Voir tous les messages
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Upcoming Appointments -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Rendez-vous à venir
                </h2>
                <?php if (empty($upcoming_appointments)): ?>
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">Aucun rendez-vous à venir pour le moment.</p>
                <?php else: ?>
                    <ul class="appointments-list">
                        <?php foreach ($upcoming_appointments as $appointment): ?>
                            <li class="appointment-item">
                                <div class="appointment-info">
                                    <h4><?php echo htmlspecialchars($appointment['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($appointment['email']); ?></p>
                                    <p><strong>Date :</strong> <?php echo date('d/m/Y H:i', strtotime($appointment['appointment_time'])); ?></p>
                                </div>
                                <div class="appointment-meta">
                                    <div class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php echo $appointment['status'] === 'pending' ? 'En attente' : ($appointment['status'] === 'confirmed' ? 'Confirmé' : 'Annulé'); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="/admin/schedule" class="btn btn-primary" aria-label="Voir tous les rendez-vous">
                            <i class="fas fa-calendar-alt"></i>
                            Voir tous les rendez-vous
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="logout()" title="Se déconnecter" aria-label="Se déconnecter">
        <i class="fas fa-sign-out-alt"></i>
    </button>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                window.location.href = '/admin/logout';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.querySelector('.sidebar-toggle');
            const flash = document.querySelector('.alert');
            const mediaQuery = window.matchMedia('(max-width: 768px)');

            function handleMediaQuery(e) {
                toggleBtn.style.display = e.matches ? 'block' : 'none';
            }
            handleMediaQuery(mediaQuery);
            mediaQuery.addEventListener('change', handleMediaQuery);

            if (flash) {
                setTimeout(() => {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>