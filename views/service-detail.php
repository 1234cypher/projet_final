<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['title']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($service['description']); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --accent-gold: #f59e0b;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--gray-50);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        .header {
            background: white;
            border-bottom: 1px solid var(--gray-100);
            padding: 1rem 0;
            box-shadow: 0 2px 10px var(--shadow-light);
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon i {
            color: white;
            font-size: 1.25rem;
        }

        .brand-text h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .brand-text p {
            font-size: 0.7rem;
            color: var(--text-light);
            margin: -0.25rem 0 0 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px var(--shadow-medium);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: white;
            padding: 1rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .breadcrumb-nav a {
            color: var(--secondary-blue);
            text-decoration: none;
        }

        .breadcrumb-nav a:hover {
            text-decoration: underline;
        }

        /* Service Hero */
        .service-hero {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 4rem 0;
            text-align: center;
        }

        .service-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
        }

        .service-icon i {
            font-size: 3rem;
            color: white;
        }

        .service-hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .service-hero .lead {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Service Content */
        .service-content {
            padding: 4rem 0;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .main-content {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 30px var(--shadow-light);
        }

        .sidebar {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px var(--shadow-light);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .content-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-dark);
            margin-bottom: 2rem;
        }

        .content-text h3 {
            color: var(--primary-blue);
            font-size: 1.4rem;
            margin: 2rem 0 1rem;
            font-weight: 700;
        }

        .content-text ul {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        .content-text li {
            margin-bottom: 0.5rem;
        }

        /* Sidebar Services */
        .sidebar-services {
            margin-bottom: 2rem;
        }

        .sidebar h3 {
            font-size: 1.2rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .service-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            border: 2px solid transparent;
        }

        .service-link:hover {
            background: var(--light-blue);
            border-color: var(--secondary-blue);
        }

        .service-link.active {
            background: var(--light-blue);
            border-color: var(--secondary-blue);
            color: var(--secondary-blue);
            font-weight: 600;
        }

        .service-link-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .contact-cta {
            background: linear-gradient(135deg, var(--accent-gold), #d97706);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }

        .contact-cta h3 {
            color: white;
            margin-bottom: 1rem;
        }

        .contact-cta p {
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        .btn-white {
            background: white;
            color: var(--accent-gold);
            font-weight: 700;
        }

        .btn-white:hover {
            background: #f9f9f9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .service-hero h1 {
                font-size: 2rem;
            }

            .service-hero .lead {
                font-size: 1.1rem;
            }

            .main-content {
                padding: 2rem;
            }

            .sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <a href="/" class="brand-container" style="text-decoration: none;">
                <div class="brand-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="brand-text">
                    <h1>Cabinet Excellence</h1>
                    <p>Avocats Spécialisés</p>
                </div>
            </a>

            <div>
                <a href="/" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Retour à l'accueil
                </a>
                <a href="/#contact" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i>
                    Contact
                </a>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <section class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="/">Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="/#services">Services</a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($service['title']); ?></span>
            </nav>
        </div>
    </section>

    <!-- Service Hero -->
    <section class="service-hero">
        <div class="container">
            <div class="service-icon" style="background: <?php echo htmlspecialchars($service['color']); ?>33;">
                <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
            </div>
            <h1><?php echo htmlspecialchars($service['title']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($service['description']); ?></p>
        </div>
    </section>

    <!-- Service Content -->
    <section class="service-content">
        <div class="container">
            <div class="content-grid">
                <!-- Main Content -->
                <div class="main-content">
                    <h2 class="section-title">Détails du service</h2>
                    
                    <div class="content-text">
                        <?php if (!empty($service['detailed_content'])): ?>
                            <?php echo nl2br(htmlspecialchars($service['detailed_content'])); ?>
                        <?php else: ?>
                            <p>Ce service fait partie de notre expertise principale. Notre équipe d'avocats spécialisés vous accompagne avec professionnalisme et dévouement dans tous vos besoins juridiques.</p>
                            
                            <h3>Notre approche</h3>
                            <p>Nous privilégions une approche personnalisée et sur-mesure pour chaque client. Notre méthode comprend :</p>
                            <ul>
                                <li>Analyse approfondie de votre situation</li>
                                <li>Conseil juridique adapté à vos besoins</li>
                                <li>Accompagnement tout au long de la procédure</li>
                                <li>Suivi post-dossier et conseils préventifs</li>
                            </ul>

                            <h3>Pourquoi nous choisir ?</h3>
                            <p>Fort de plus de 20 ans d'expérience, notre cabinet vous garantit :</p>
                            <ul>
                                <li>Une expertise reconnue dans ce domaine</li>
                                <li>Un accompagnement personnalisé</li>
                                <li>Une disponibilité et une réactivité optimales</li>
                                <li>Des tarifs transparents et compétitifs</li>
                            </ul>

                            <h3>Première consultation</h3>
                            <p>Nous vous proposons une première consultation gratuite pour évaluer votre situation et vous présenter les différentes options qui s'offrent à vous. Cette rencontre nous permet de mieux comprendre vos besoins et de vous proposer la stratégie la plus adaptée.</p>
                        <?php endif; ?>
                    </div>

                    <div style="text-align: center; margin-top: 3rem;">
                        <a href="/#contact" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                            <i class="fas fa-calendar-alt"></i>
                            Prendre rendez-vous
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Other Services -->
                    <div class="sidebar-services">
                        <h3>Nos autres services</h3>
                        <?php foreach ($services as $otherService): ?>
                            <a href="/service/<?php echo $otherService['id']; ?>" 
                               class="service-link <?php echo $otherService['id'] == $service['id'] ? 'active' : ''; ?>">
                                <div class="service-link-icon" style="background: <?php echo htmlspecialchars($otherService['color']); ?>;">
                                    <i class="<?php echo htmlspecialchars($otherService['icon']); ?>"></i>
                                </div>
                                <span><?php echo htmlspecialchars($otherService['title']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Contact CTA -->
                    <div class="contact-cta">
                        <h3>Besoin d'aide ?</h3>
                        <p>Contactez-nous pour une consultation gratuite et sans engagement.</p>
                        <a href="/#contact" class="btn btn-white">
                            <i class="fas fa-phone"></i>
                            Nous contacter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Smooth scrolling pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>