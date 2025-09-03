<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Administration</title>
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

        .section-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .info-icon.blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .info-icon.green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .info-icon.yellow {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .info-icon.purple {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .info-content h4 {
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .info-content p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list i {
            color: #10b981;
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
            width: 50px;
            height: 50px;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo SITE_NAME; ?></h2>
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
                <li><a href="/admin/contacts">
                    <i class="fas fa-envelope"></i>
                    Messages
                </a></li>
                <li><a href="/admin/settings" class="active">
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
                <h1>Paramètres du système</h1>
                <div class="breadcrumb">Administration / Paramètres</div>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Site Information -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Informations du site
                </h2>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon blue">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="info-content">
                            <h4>Nom du site</h4>
                            <p><?php echo SITE_NAME; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon green">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="info-content">
                            <h4>URL du site</h4>
                            <p><?php echo SITE_URL; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon yellow">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h4>Email administrateur</h4>
                            <p><?php echo ADMIN_EMAIL; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon purple">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="info-content">
                            <h4>Base de données</h4>
                            <p>SQLite - <?php echo DB_NAME; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Features -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-cogs"></i>
                    Fonctionnalités du système
                </h2>

                <ul class="feature-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Gestion dynamique du contenu</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Modification en temps réel des textes et sections du site</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Gestion des services juridiques</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Ajout, modification et personnalisation des domaines d'expertise</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Gestion de l'équipe</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Présentation dynamique des membres du cabinet</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Système de messages intégré</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Réception et gestion des demandes clients</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Interface d'administration sécurisée</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Accès protégé avec authentification</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Design responsive</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Optimisé pour tous les appareils (mobile, tablette, desktop)</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Base de données SQLite</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Stockage léger et efficace sans serveur</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Animations et micro-interactions</strong>
                            <p style="color: #6b7280; font-size: 0.85rem;">Interface moderne avec effets visuels fluides</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Technical Information -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-code"></i>
                    Informations techniques
                </h2>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon blue">
                            <i class="fab fa-php"></i>
                        </div>
                        <div class="info-content">
                            <h4>Version PHP</h4>
                            <p><?php echo PHP_VERSION; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon green">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="info-content">
                            <h4>Serveur Web</h4>
                            <p><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon yellow">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h4>Fuseau horaire</h4>
                            <p><?php echo date_default_timezone_get(); ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon purple">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="info-content">
                            <h4>Date actuelle</h4>
                            <p><?php echo date('d/m/Y H:i:s'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-question-circle"></i>
                    Aide et support
                </h2>

                <div style="background: #f9fafb; padding: 2rem; border-radius: 15px;">
                    <h4 style="color: #1f2937; margin-bottom: 1rem;">Guide d'utilisation</h4>
                    <ul style="color: #6b7280; line-height: 1.8; list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-arrow-right" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                            <strong>Tableau de bord :</strong> Vue d'ensemble des statistiques et messages récents
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-arrow-right" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                            <strong>Contenu du site :</strong> Modification des textes, services et équipe
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-arrow-right" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                            <strong>Messages :</strong> Gestion des demandes clients et consultation
                        </li>
                        <li>
                            <i class="fas fa-arrow-right" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                            <strong>Paramètres :</strong> Informations système et aide
                        </li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="logout()" title="Se déconnecter">
        <i class="fas fa-sign-out-alt"></i>
    </button>

    <script>
        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                window.location.href = '/admin/logout';
            }
        }
    </script>
</body>
</html>