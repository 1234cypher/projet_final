<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h(SITE_NAME); ?> - Avocats Spécialisés</title>
    <meta name="description" content="<?php echo h(isset($content['meta']['description']) ? $content['meta']['description'] : 'Cabinet d\'avocats spécialisé en droit des affaires, droit de la famille, droit immobilier et droit du travail. Expertise juridique depuis plus de 20 ans.'); ?>">
    <meta name="keywords" content="avocat, cabinet juridique, droit des affaires, droit de la famille, droit immobilier, droit du travail, conseil juridique, expertise">
    <meta name="author" content="<?php echo h(SITE_NAME); ?>">
    <meta name="robots" content="index, follow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/public/css/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="/public/images/favicon.ico" type="image/x-icon">
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo h(SITE_NAME); ?>",
        "url": "<?php echo h(SITE_URL); ?>",
        "logo": "/public/images/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo h($content['contact']['phone'] ?? '+33 1 23 45 67 89'); ?>",
            "contactType": "customer service",
            "email": "<?php echo h($content['contact']['email'] ?? 'contact@cabinet-excellence.fr'); ?>",
            "areaServed": "FR",
            "availableLanguage": ["French"]
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo h($content['contact']['address'] ?? '123 Avenue de la Justice, 75001 Paris'); ?>",
            "addressLocality": "Paris",
            "postalCode": "75001",
            "addressCountry": "FR"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Accueil",
                "item": "<?php echo h(SITE_URL); ?>"
            }
        ]
    }
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a href="/" class="navbar-brand" aria-label="<?php echo h(SITE_NAME); ?> Home">
                <div class="brand-container">
                    <div class="brand-icon">
                        <i class="fas fa-balance-scale" aria-hidden="true"></i>
                    </div>
                    <div class="brand-text">
                        <h1><?php echo h(SITE_NAME); ?></h1>
                        <p>Avocats Spécialisés</p>
                    </div>
                </div>
            </a>
            <ul class="navbar-nav" role="menubar">
                <li role="none"><a href="#home" class="nav-link active" role="menuitem"><i class="fas fa-home" aria-hidden="true"></i> Accueil</a></li>
                <li role="none"><a href="#about" class="nav-link" role="menuitem"><i class="fas fa-info-circle" aria-hidden="true"></i> À propos</a></li>
                <li role="none"><a href="#services" class="nav-link" role="menuitem"><i class="fas fa-gavel" aria-hidden="true"></i> Nos services</a></li>
                <li role="none"><a href="#team" class="nav-link" role="menuitem"><i class="fas fa-users" aria-hidden="true"></i> Équipe</a></li>
                <li role="none"><a href="#news" class="nav-link" role="menuitem"><i class="fas fa-newspaper" aria-hidden="true"></i> Actualités</a></li>
                <li role="none"><a href="#events" class="nav-link" role="menuitem"><i class="fas fa-calendar" aria-hidden="true"></i> Événements</a></li>
                <li role="none"><a href="#contact" class="btn btn-primary" role="menuitem"><i class="fas fa-envelope" aria-hidden="true"></i> Contact</a></li>
            </ul>
            <button class="mobile-menu-toggle" aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="mobile-menu" id="mobileMenu" role="menu">
                <div class="mobile-nav">
                    <a href="#home" class="nav-link" role="menuitem">Accueil</a>
                    <a href="#about" class="nav-link" role="menuitem">À propos</a>
                    <a href="#services" class="nav-link" role="menuitem">Nos services</a>
                    <a href="#team" class="nav-link" role="menuitem">Équipe</a>
                    <a href="#news" class="nav-link" role="menuitem">Actualités</a>
                    <a href="#events" class="nav-link" role="menuitem">Événements</a>
                    <a href="#contact" class="nav-link" role="menuitem">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero" aria-labelledby="hero-title">
        <div class="container">
            <div class="hero-content">
                <h1 id="hero-title"><?php echo h($content['hero']['title'] ?? 'Excellence Juridique à Votre Service'); ?></h1>
                <p class="lead"><?php echo h($content['hero']['subtitle'] ?? 'Depuis plus de 20 ans, nous accompagnons nos clients avec expertise, intégrité et dévouement dans tous leurs défis juridiques.'); ?></p>
                <div class="hero-buttons">
                    <a href="#contact" class="btn btn-primary btn-lg" aria-label="Prendre un rendez-vous"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Prendre rendez-vous</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section section-about" aria-labelledby="about-title">
        <div class="container">
            <div class="section-title">
                <span class="badge">À propos de nous</span>
                <h2 id="about-title"><?php echo h($content['about']['title'] ?? 'Votre Réussite, Notre Mission'); ?></h2>
                <p class="lead"><?php echo h($content['about']['subtitle'] ?? 'Fort d\'une expérience reconnue et d\'une approche personnalisée, notre cabinet vous offre un accompagnement juridique d\'excellence adapté à vos besoins spécifiques.'); ?></p>
            </div>
            <div class="about-grid">
                <div class="image-area">
                    <img src="/public/images/about-image.jpg" alt="Cabinet juridique en action" loading="lazy">
                    <div class="decorative-element blue"></div>
                    <div class="decorative-element yellow"></div>
                </div>
                <div>
                    <div class="feature-cards">
                        <div class="feature-card">
                            <div class="feature-icon blue"><i class="fas fa-shield-alt" aria-hidden="true"></i></div>
                            <div>
                                <h4 class="feature-title">Confidentialité</h4>
                                <p class="feature-subtitle">Garantie absolue</p>
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon yellow"><i class="fas fa-handshake" aria-hidden="true"></i></div>
                            <div>
                                <h4 class="feature-title">Accompagnement</h4>
                                <p class="feature-subtitle">Personnalisé</p>
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon green"><i class="fas fa-balance-scale" aria-hidden="true"></i></div>
                            <div>
                                <h4 class="feature-title">Expertise</h4>
                                <p class="feature-subtitle">Multidisciplinaire</p>
                            </div>
                        </div>
                    </div>
                    <p class="mb-6"><?php echo h('Nous accompagnons particuliers et entreprises avec une expertise pointue et une approche centrée sur vos besoins.'); ?></p>
                    <a href="#contact" class="btn btn-primary" aria-label="Nous contacter"><i class="fas fa-envelope" aria-hidden="true"></i> Nous contacter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section id="values" class="section values-section" aria-labelledby="values-title">
        <div class="container">
            <div class="section-title">
                <span class="badge">Nos valeurs</span>
                <h2 id="values-title"><?php echo h($content['values']['title'] ?? 'Nos Principes Fondateurs'); ?></h2>
                <p class="lead"><?php echo h($content['values']['subtitle'] ?? 'Intégrité, excellence et engagement guident chacune de nos actions pour nos clients.'); ?></p>
            </div>
            <div class="values-grid">
                <div class="value-card fade-in-up" style="animation-delay: 0.1s;">
                    <div class="value-icon blue"><i class="fas fa-shield-alt" aria-hidden="true"></i></div>
                    <h4>Intégrité</h4>
                    <p>Nous agissons avec transparence et éthique dans chaque dossier pour garantir votre confiance.</p>
                </div>
                <div class="value-card fade-in-up" style="animation-delay: 0.2s;">
                    <div class="value-icon gold"><i class="fas fa-trophy" aria-hidden="true"></i></div>
                    <h4>Excellence</h4>
                    <p>Nous poursuivons l'excellence à travers une expertise constamment actualisée.</p>
                </div>
                <div class="value-card fade-in-up" style="animation-delay: 0.3s;">
                    <div class="value-icon green"><i class="fas fa-handshake" aria-hidden="true"></i></div>
                    <h4>Engagement</h4>
                    <p>Votre succès est notre priorité, avec un dévouement total à chaque étape.</p>
                </div>
            </div>
            <div class="values-commitment fade-in-up" style="animation-delay: 0.6s;">
                <div class="commitment-content">
                    <div class="commitment-stats">
                        <div class="stat-item">
                            <span class="stat-number">20+</span>
                            <span class="stat-label">Années d'expérience</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Clients satisfaits</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">1000+</span>
                            <span class="stat-label">Dossiers traités</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">24h</span>
                            <span class="stat-label">Délai de réponse</span>
                        </div>
                    </div>
                    <div class="commitment-text">Nous vous offrons un accompagnement juridique de haute qualité, adapté à vos besoins uniques.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section" aria-labelledby="services-title">
        <div class="container">
            <div class="section-title">
                <span class="badge">Nos spécialisations</span>
                <h2 id="services-title"><?php echo h($content['services']['title'] ?? 'Domaines d\'Expertise'); ?></h2>
                <p class="lead"><?php echo h($content['services']['subtitle'] ?? 'Des solutions juridiques adaptées dans divers domaines pour répondre à vos besoins.'); ?></p>
            </div>
            <div class="services-grid" role="region" aria-live="polite">
                <?php if (isset($services) && is_array($services) && !empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                    <div class="service-card fade-in-up" style="animation-delay: <?php echo 0.1 * $index; ?>s;">
                        <div class="service-icon" style="background: <?php echo h($service['color'] ?? '#3b82f6'); ?>">
                            <i class="<?php echo h($service['icon'] ?? 'fas fa-gavel'); ?>" aria-hidden="true"></i>
                        </div>
                        <h4><?php echo h($service['title'] ?? 'Service'); ?></h4>
                        <p><?php echo h($service['description'] ?? 'Description du service'); ?></p>
                        <a href="/service/<?php echo h($service['id'] ?? ''); ?>" class="btn btn-outline" aria-label="En savoir plus sur <?php echo h($service['title'] ?? 'ce service'); ?>">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i> En savoir plus
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="service-card">
                        <div class="service-icon" style="background: #3b82f6;">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                        </div>
                        <h4>Services en cours de chargement</h4>
                        <p>Veuillez patienter pendant que nous chargeons nos domaines d'expertise.</p>
                        <a href="#contact" class="btn btn-outline" aria-label="Nous contacter"><i class="fas fa-envelope" aria-hidden="true"></i> Nous contacter</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="section" style="background: var(--gradient-bg);" aria-labelledby="team-title">
        <div class="container">
            <div class="section-title">
                <span class="badge">Notre équipe</span>
                <h2 id="team-title"><?php echo h($content['team']['title'] ?? 'Des Experts à Vos Côtés'); ?></h2>
                <p class="lead"><?php echo h($content['team']['subtitle'] ?? 'Des avocats expérimentés et passionnés, reconnus pour leur expertise et leur engagement.'); ?></p>
            </div>
            <div class="team-container">
                <button class="team-nav-btn team-nav-prev" aria-label="Précédent membre de l'équipe" disabled><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
                <div class="team-grid" role="region" aria-live="polite">
                    <?php if (isset($team) && is_array($team) && !empty($team)): ?>
                        <?php foreach ($team as $index => $member): ?>
                        <div class="team-card fade-in-up" style="animation-delay: <?php echo 0.1 * $index; ?>s;">
                            <img src="<?php echo h($member['image_path'] ?? DEFAULT_TEAM_IMAGE); ?>" 
                                 alt="<?php echo h($member['name'] ?? 'Membre de l\'équipe'); ?>" 
                                 class="team-image" loading="lazy">
                            <h4><?php echo h($member['name'] ?? 'Nom du membre'); ?></h4>
                            <p class="team-position"><?php echo h($member['position'] ?? 'Position'); ?></p>
                            <p class="team-description"><?php echo h($member['description'] ?? 'Description du membre de l\'équipe'); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="team-card">
                            <img src="<?php echo h(DEFAULT_TEAM_IMAGE); ?>" alt="Membre de l'équipe" class="team-image" loading="lazy">
                            <h4>Équipe en cours de chargement</h4>
                            <p class="team-position">Information en cours de mise à jour</p>
                            <p class="team-description">Les informations sur notre équipe seront bientôt disponibles.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="team-nav-btn team-nav-next" aria-label="Suivant membre de l'équipe"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="section news-section" aria-labelledby="news-title">
        <div class="container">
            <div class="section-title">
                <span class="badge">Actualités</span>
                <h2 id="news-title"><?php echo h($content['news']['title'] ?? 'Nos Dernières Actualités'); ?></h2>
                <p class="lead"><?php echo h($content['news']['subtitle'] ?? 'Restez informé des nouveautés du cabinet et des évolutions juridiques.'); ?></p>
            </div>
            <div class="news-grid" role="region" aria-live="polite">
                <?php if (isset($news) && is_array($news) && !empty($news)): ?>
                    <?php foreach ($news as $index => $item): ?>
                    <div class="news-card fade-in-up" style="animation-delay: <?php echo 0.1 * $index; ?>s;">
                        <img src="<?php echo h($item['image_path'] ?? DEFAULT_NEWS_IMAGE); ?>" 
                             alt="<?php echo h($item['title'] ?? 'Actualité'); ?>" 
                             class="news-image" loading="lazy">
                        <div class="news-content">
                            <p class="news-date"><i class="fas fa-calendar-alt" aria-hidden="true"></i> <?php echo date('d M Y', strtotime($item['publish_date'] ?? 'now')); ?></p>
                            <h4 class="news-title"><?php echo h($item['title'] ?? 'Titre de l\'actualité'); ?></h4>
                            <div class="news-excerpt"><?php echo nl2br(h(substr(strip_tags($item['content'] ?? 'Contenu de l\'actualité'), 0, 150))); ?>...</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="news-card">
                        <img src="<?php echo h(DEFAULT_NEWS_IMAGE); ?>" alt="Actualité" class="news-image" loading="lazy">
                        <div class="news-content">
                            <p class="news-date"><i class="fas fa-calendar-alt" aria-hidden="true"></i> <?php echo date('d M Y'); ?></p>
                            <h4 class="news-title">Actualités en cours de chargement</h4>
                            <div class="news-excerpt">Restez à l'écoute pour découvrir nos prochaines actualités.</div>
                            <a href="#contact" class="btn btn-outline" aria-label="Nous contacter"><i class="fas fa-envelope" aria-hidden="true"></i> Nous contacter</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="section events-section" aria-labelledby="events-title">
        <div class="container">
            <div class="section-title">
                <span class="badge">Événements</span>
                <h2 id="events-title"><?php echo h($content['events']['title'] ?? 'Nos Prochains Événements'); ?></h2>
                <p class="lead"><?php echo h($content['events']['subtitle'] ?? 'Participez à nos conférences et ateliers pour rester informé des évolutions juridiques.'); ?></p>
            </div>
            <div class="events-grid" role="region" aria-live="polite">
                <?php if (isset($events) && is_array($events) && !empty($events)): ?>
                    <?php foreach ($events as $index => $item): ?>
                    <div class="event-card fade-in-up" style="animation-delay: <?php echo 0.1 * $index; ?>s;">
                        <img src="<?php echo h($item['image_path'] ?? DEFAULT_EVENT_IMAGE); ?>" 
                             alt="<?php echo h($item['title'] ?? 'Événement'); ?>" 
                             class="event-image" loading="lazy">
                        <div class="event-content">
                            <p class="event-date"><i class="fas fa-calendar-alt" aria-hidden="true"></i> <?php echo date('d M Y', strtotime($item['event_date'] ?? 'now')); ?></p>
                            <h4 class="event-title"><?php echo h($item['title'] ?? 'Titre de l\'événement'); ?></h4>
                            <div class="event-excerpt"><?php echo nl2br(h(substr(strip_tags($item['content'] ?? 'Contenu de l\'événement'), 0, 150))); ?>...</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="event-card">
                        <img src="<?php echo h(DEFAULT_EVENT_IMAGE); ?>" alt="Événement" class="event-image" loading="lazy">
                        <div class="event-content">
                            <p class="event-date"><i class="fas fa-calendar-alt" aria-hidden="true"></i> <?php echo date('d M Y'); ?></p>
                            <h4 class="event-title">Événements en cours de chargement</h4>
                            <div class="event-excerpt">Restez à l'écoute pour découvrir nos prochains événements.</div>
                            <a href="#contact" class="btn btn-outline" aria-label="Nous contacter"><i class="fas fa-envelope" aria-hidden="true"></i> Nous contacter</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section" aria-labelledby="contact-title">
        <div class="container">
            <div class="max-w-4xl mx-auto">
                <div class="section-title">
                    <span class="badge">Nous contacter</span>
                    <h2 id="contact-title"><?php echo h($content['contact']['title'] ?? 'Parlons de Votre Situation'); ?></h2>
                    <p class="lead"><?php echo h($content['contact']['subtitle'] ?? 'Bénéficiez d\'un premier échange gratuit pour évaluer vos besoins juridiques.'); ?></p>
                </div>
                <div class="contact-form">
                    <div id="contactMessage" role="alert">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success"><?php echo h($success_message); ?></div>
                        <?php elseif (isset($error_message)): ?>
                            <div class="alert alert-error"><?php echo h($error_message); ?></div>
                        <?php endif; ?>
                    </div>
                    <form id="contactForm" method="POST" action="/contact" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo h(generateCSRFToken()); ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" required aria-required="true" placeholder="Votre nom complet">
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" required aria-required="true" placeholder="votre@email.com">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="+33 1 23 45 67 89">
                            </div>
                            <div class="form-group">
                                <label for="subject" class="form-label">Domaine juridique</label>
                                <select class="form-control form-control-lg" id="subject" name="subject">
                                    <option value="">Sélectionnez un domaine</option>
                                    <option value="droit-des-affaires">Droit des Affaires</option>
                                    <option value="droit-de-la-famille">Droit de la Famille</option>
                                    <option value="droit-immobilier">Droit Immobilier</option>
                                    <option value="droit-du-travail">Droit du Travail</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message" class="form-label">Décrivez votre situation *</label>
                            <textarea class="form-control form-control-lg" id="message" name="message" rows="5" required aria-required="true" placeholder="Expliquez-nous brièvement votre situation juridique..."></textarea>
                        </div>
                        <div class="file-upload-section">
                            <div class="file-upload-header">
                                <div class="file-upload-icon"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></div>
                                <h3>Joindre des documents</h3>
                                <p>Glissez-déposez vos fichiers ici ou cliquez pour sélectionner (max: 5MB, 3 fichiers max)</p>
                        <input type="file" id="fileInput" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="file-input">
                                <button type="button" class="file-upload-button" onclick="document.getElementById('fileInput').click()">Sélectionner des fichiers</button>
                            </div>
                            <div class="upload-info"><i class="fas fa-info-circle" aria-hidden="true"></i> Formats acceptés : PDF, JPG, PNG</div>
                            <div class="file-preview" id="filePreview"></div>
                        </div>
                        <div class="appointment-section">
                            <div class="appointment-header">
                                <h3>Prendre un rendez-vous (Obligatoire)</h3>
                                <p>Toute demande de contact nécessite la prise d'un rendez-vous</p>
                            </div>
                            <input type="hidden" name="appointment_requested" id="appointmentRequested" value="1">
                            <div class="appointment-details" id="appointmentDetails" style="display: none;">
                                <div class="appointment-grid">
                                    <div class="form-group">
                                        <label for="appointment_date" class="form-label">Date du rendez-vous *</label>
                                        <input type="date" class="form-control form-control-lg" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required aria-required="true">
                                    </div>
                                    <div class="form-group">
                                        <label for="slot_id" class="form-label">Heure du rendez-vous *</label>
                                        <select class="form-control form-control-lg" id="slot_id" name="slot_id" disabled aria-disabled="true" required aria-required="true">
                                            <option value="">Choisissez une date pour voir les créneaux...</option>
                                        </select>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-header">
                                            <div class="info-icon price"><i class="fas fa-euro-sign" aria-hidden="true"></i></div>
                                            <h4 class="info-title">Tarif Consultation</h4>
                                        </div>
                                        <div class="info-content">
                                            <span class="price-highlight">150€</span>
                                            <p>Consultation initiale incluant l'analyse de votre dossier.</p>
                                        </div>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-header">
                                            <div class="info-icon duration"><i class="fas fa-clock" aria-hidden="true"></i></div>
                                            <h4 class="info-title">Durée & Format</h4>
                                        </div>
                                        <div class="info-content">
                                            <p><strong>30 minutes</strong> de consultation</p>
                                            <p>En présentiel</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-options">
                                    <h4 class="payment-title">Mode de paiement</h4>
                                    <div class="payment-methods">
                                        <label class="payment-option">
                                            <input type="radio" id="paymentOnsite" name="payment_method" value="onsite">
                                            <div class="payment-icon onsite"><i class="fas fa-building" aria-hidden="true"></i></div>
                                            <div class="payment-label">Paiement sur place</div>
                                            <div class="payment-description">Payez lors de votre rendez-vous au cabinet.</div>
                                        </label>
                                        <!-- Paiement en ligne désactivé
                                        <label class="payment-option">
                                            <input type="radio" id="paymentOnline" name="payment_method" value="online">
                                            <div class="payment-icon online"><i class="fas fa-credit-card" aria-hidden="true"></i></div>
                                            <div class="payment-label">Paiement en ligne</div>
                                            <div class="payment-description">Payez immédiatement via notre plateforme sécurisée.</div>
                                        </label>
                                        -->
                                    </div>
                                    <div class="payment-note">
                                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                                        <p><strong>Important :</strong> Le paiement s'effectue directement au cabinet lors de votre rendez-vous.</p>
                                    </div>
                                </div>
                                <!-- Section Stripe désactivée -->
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" aria-label="Envoyer la demande" disabled>
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i> <span id="submitText">Demander un rendez-vous</span>
                            </button>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 1rem;">
                                <i class="fas fa-shield-alt" aria-hidden="true"></i> Vos données sont protégées et confidentielles
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <div class="brand-container mb-4">
                        <div class="brand-icon"><i class="fas fa-balance-scale" aria-hidden="true"></i></div>
                        <div class="brand-text">
                            <h6 style="color: white;"><?php echo h(SITE_NAME); ?></h6>
                            <p style="color: #9ca3af; font-size: 0.9rem;">Avocats Spécialisés</p>
                        </div>
                    </div>
                    <p style="color: #d1d5db; line-height: 1.6;">Votre partenaire juridique pour des solutions adaptées et un accompagnement de qualité depuis plus de 20 ans.</p>
                </div>
                <div class="footer-section">
                    <h6>Navigation</h6>
                    <a href="#home" aria-label="Accueil">Accueil</a>
                    <a href="#about" aria-label="À propos">À propos</a>
                    <a href="#services" aria-label="Nos services">Nos services</a>
                    <a href="#team" aria-label="Équipe">Équipe</a>
                    <a href="#news" aria-label="Actualités">Actualités</a>
                    <a href="#events" aria-label="Événements">Événements</a>
                    <a href="#contact" aria-label="Contact">Contact</a>
                </div>
                <div class="footer-section">
                    <h6>Contact</h6>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span><?php echo nl2br(h($content['contact']['address'] ?? '123 Avenue de la Justice<br>75001 Paris, France')); ?></span>
                    </p>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <a href="tel:<?php echo h($content['contact']['phone'] ?? '+33 1 23 45 67 89'); ?>"><?php echo h($content['contact']['phone'] ?? '+33 1 23 45 67 89'); ?></a>
                    </p>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <a href="mailto:<?php echo h($content['contact']['email'] ?? 'contact@cabinet-excellence.fr'); ?>"><?php echo h($content['contact']['email'] ?? 'contact@cabinet-excellence.fr'); ?></a>
                    </p>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        <span>Lun-Ven: 9h-18h<br>Sam: 10h-13h</span>
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p><?php echo h($content['footer']['copyright'] ?? '© ' . date('Y') . ' ' . h(SITE_NAME) . '. Tous droits réservés.'); ?></p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop" aria-label="Retour en haut de la page">
        <i class="fas fa-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Scripts -->
    <script src="/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>