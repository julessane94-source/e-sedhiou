<?php
/**
 * Template WordPress - Hero Section MAIRIE DE SEDHIOU
 * Version personnalisée avec couleurs AMIE-FPT
 * À ajouter dans votre thème WordPress (functions.php)
 */

// Enregistrer les styles personnalisés pour Sedhiou
add_action('wp_enqueue_scripts', function() {
    wp_register_style('hero-sedhiou', false);
    wp_enqueue_style('hero-sedhiou');
    
    $custom_css = "
    /* ========== HERO SECTION ========== */
    .hero-section-sedhiou {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section-sedhiou::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(100px, -100px);
    }

    .hero-section-sedhiou h1 {
        font-size: 3rem;
        margin-bottom: 15px;
        font-weight: 700;
        line-height: 1.2;
        position: relative;
        z-index: 1;
    }

    .hero-section-sedhiou .subtitle {
        font-size: 1.3rem;
        margin-bottom: 40px;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }

    /* ========== BUTTONS DESIGN ========== */
    .buttons-group-sedhiou {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .btn-sedhiou {
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

    .btn-primary-sedhiou {
        background-color: #2ecc71;
        color: white;
        box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
    }

    .btn-primary-sedhiou:hover {
        background-color: #27ae60;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
    }

    .btn-secondary-sedhiou {
        background-color: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-secondary-sedhiou:hover {
        background-color: white;
        color: #667eea;
        transform: translateY(-3px);
    }

    /* ========== STATISTICS SECTION ========== */
    .stats-section-sedhiou {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        padding: 60px 20px;
        background-color: #f8f9fa;
        text-align: center;
    }

    .stat-card-sedhiou {
        padding: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card-sedhiou:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
    }

    .stat-number-sedhiou {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 10px;
    }

    .stat-label-sedhiou {
        font-size: 1rem;
        color: #666;
        font-weight: 500;
    }

    /* ========== FEATURED IMAGE ========== */
    .hero-image-sedhiou {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin-top: 30px;
    }

    .placeholder-image {
        width: 100%;
        max-width: 500px;
        height: 300px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 1.2rem;
        margin-top: 30px;
    }

    /* ========== FEATURES GRID ========== */
    .features-section-sedhiou {
        padding: 60px 20px;
        background: white;
    }

    .features-section-sedhiou h2 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #333;
    }

    .features-description-sedhiou {
        text-align: center;
        color: #666;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .features-grid-sedhiou {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card-sedhiou {
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        text-align: center;
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
    }

    .feature-card-sedhiou:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
        border-left-color: #764ba2;
    }

    .feature-icon-sedhiou {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 15px;
    }

    .feature-card-sedhiou h3 {
        margin-bottom: 10px;
        color: #333;
        font-size: 1.3rem;
    }

    .feature-card-sedhiou p {
        color: #666;
        font-size: 0.95rem;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 768px) {
        .hero-section-sedhiou h1 {
            font-size: 2rem;
        }

        .hero-section-sedhiou .subtitle {
            font-size: 1.1rem;
        }

        .buttons-group-sedhiou {
            flex-direction: column;
        }

        .btn-sedhiou {
            width: 100%;
            justify-content: center;
        }

        .features-grid-sedhiou {
            grid-template-columns: 1fr;
        }

        .stats-section-sedhiou {
            grid-template-columns: 1fr;
        }
    }

    .btn-sedhiou i {
        font-size: 1.2rem;
    }
    ";
    
    wp_add_inline_style('hero-sedhiou', $custom_css);
});

// ======================================
// SHORTCODE : Hero Section Sedhiou
// ======================================
add_shortcode('hero_sedhiou', function() {
    ob_start();
    ?>
    <section class="hero-section-sedhiou">
        <h1>Bienvenue à la Mairie de Sedhiou</h1>
        <p class="subtitle">Portail des Services Municipaux</p>
        
        <div class="buttons-group-sedhiou">
            <a href="#services" class="btn-sedhiou btn-primary-sedhiou">
                <i class="fas fa-file-alt"></i>
                Demander un service
            </a>
            <a href="#features" class="btn-sedhiou btn-secondary-sedhiou">
                <i class="fas fa-arrow-down"></i>
                Découvrir nos services
            </a>
        </div>

        <!-- Placeholder pour image future -->
        <div class="placeholder-image">
            <p>📸 Image de la mairie (À insérer)</p>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

// ======================================
// SHORTCODE : Statistiques Sedhiou
// ======================================
add_shortcode('stats_sedhiou', function() {
    ob_start();
    ?>
    <section class="stats-section-sedhiou">
        <div class="stat-card-sedhiou">
            <div class="stat-number-sedhiou">+500</div>
            <div class="stat-label-sedhiou">Dossiers Traités</div>
        </div>
        <div class="stat-card-sedhiou">
            <div class="stat-number-sedhiou">+50</div>
            <div class="stat-label-sedhiou">Services Municipaux</div>
        </div>
        <div class="stat-card-sedhiou">
            <div class="stat-number-sedhiou">24/7</div>
            <div class="stat-label-sedhiou">Support Disponible</div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

// ======================================
// SHORTCODE : Services/Features Sedhiou
// ======================================
add_shortcode('features_sedhiou', function() {
    ob_start();
    ?>
    <section class="features-section-sedhiou" id="features">
        <h2>Nos Services</h2>
        <p class="features-description-sedhiou">
            Découvrez l'ensemble complet des services municipaux modernes et intégrés
        </p>
        
        <div class="features-grid-sedhiou">
            <div class="feature-card-sedhiou">
                <div class="feature-icon-sedhiou">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>État Civil</h3>
                <p>Gestion des actes d'état civil et des registres administratifs</p>
            </div>
            
            <div class="feature-card-sedhiou">
                <div class="feature-icon-sedhiou">
                    <i class="fas fa-building"></i>
                </div>
                <h3>Permis et Autorisations</h3>
                <p>Demande et suivi des permis de construire et autorisations</p>
            </div>
            
            <div class="feature-card-sedhiou">
                <div class="feature-icon-sedhiou">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <h3>Cadastre</h3>
                <p>Consultation du cadastre et des propriétés municipales</p>
            </div>
            
            <div class="feature-card-sedhiou">
                <div class="feature-icon-sedhiou">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h3>Impôts et Taxes</h3>
                <p>Calcul, paiement et suivi des impôts locaux</p>
            </div>
            
            <div class="feature-card-sedhiou">
                <div class="feature-icon-sedhiou">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Éducation</h3>
                <p>Inscription scolaire et gestion des établissements scolaires</p>
            </div>
            
            <div class="feature-card-sedhiou">
                <div class="feature-icon-sedhiou">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Support Client</h3>
                <p>Assistance pour tous vos besoins et questions administratives</p>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});
