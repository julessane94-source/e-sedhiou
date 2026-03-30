<?php
/**
 * Template WordPress - Hero Section moderne
 * À ajouter dans votre thème WordPress (functions.php)
 */

// Enregistrer les styles personnalisés
add_action('wp_enqueue_scripts', function() {
    wp_register_style('hero-custom', false);
    wp_enqueue_style('hero-custom');
    
    $custom_css = "
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 20px;
        text-align: center;
    }

    .hero-section h1 {
        font-size: 3rem;
        margin-bottom: 15px;
        font-weight: 700;
        line-height: 1.2;
    }

    .hero-section .subtitle {
        font-size: 1.3rem;
        margin-bottom: 30px;
        opacity: 0.95;
    }

    /* BUTTONS DESIGN */
    .buttons-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .btn {
        padding: 15px 35px;
        font-size: 1rem;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #2ecc71;
        color: white;
        box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
    }

    .btn-primary:hover {
        background-color: #27ae60;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
    }

    .btn-secondary {
        background-color: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-secondary:hover {
        background-color: white;
        color: #667eea;
        transform: translateY(-3px);
    }

    /* STATISTICS SECTION */
    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        padding: 50px 20px;
        background-color: #f8f9fa;
        text-align: center;
    }

    .stat-card {
        padding: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 10px;
    }

    .stat-label {
        font-size: 1rem;
        color: #666;
        font-weight: 500;
    }

    /* FEATURES GRID */
    .features-section {
        padding: 50px 20px;
        background: white;
    }

    .features-section h2 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #333;
    }

    .features-description {
        text-align: center;
        color: #666;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        text-align: center;
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
        border-left-color: #764ba2;
    }

    .feature-icon {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 15px;
    }

    .feature-card h3 {
        margin-bottom: 10px;
        color: #333;
        font-size: 1.3rem;
    }

    .feature-card p {
        color: #666;
        font-size: 0.95rem;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .hero-section h1 { font-size: 2rem; }
        .hero-section .subtitle { font-size: 1.1rem; }
        .buttons-group { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
        .features-grid { grid-template-columns: 1fr; }
        .stats-section { grid-template-columns: 1fr; }
    }
    ";
    
    wp_add_inline_style('hero-custom', $custom_css);
});

// Shortcode pour afficher la section Hero
add_shortcode('hero_section', function() {
    ob_start();
    ?>
    <section class="hero-section">
        <h1>Bienvenue à la Mairie</h1>
        <p class="subtitle">Portail des Services Municipaux Intégrés</p>
        
        <div class="buttons-group">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-file-alt"></i>
                Ouvrir un Compte
            </a>
            <a href="#features" class="btn btn-secondary">
                <i class="fas fa-arrow-down"></i>
                En savoir plus
            </a>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

// Shortcode pour les statistiques
add_shortcode('stats_section', function() {
    ob_start();
    ?>
    <section class="stats-section">
        <div class="stat-card">
            <div class="stat-number">+450</div>
            <div class="stat-label">Dossiers Traités</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">+85</div>
            <div class="stat-label">Services Municipaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support Disponible</div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

// Shortcode pour les services
add_shortcode('features_section', function() {
    ob_start();
    ?>
    <section class="features-section" id="features">
        <h2>Nos Services</h2>
        <p class="features-description">
            Découvrez l'ensemble complet des services municipaux modernes et intégrés
        </p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>Gestion Administrative</h3>
                <p>Enregistrement et suivi des dossiers administratifs de la mairie</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>Services en Ligne</h3>
                <p>Accédez aux services municipaux directement depuis votre domicile</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>Infrastructure</h3>
                <p>Suivi des projets d'infrastructure municipale et de développement</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <h3>Cartographie</h3>
                <p>Visualisation intéractive des zones et ressources municipales</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h3>Documents</h3>
                <p>Archivage sécurisé et consultation des documents municipaux</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Support Client</h3>
                <p>Assistance dédiée pour tous vos besoins et questions</p>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});
