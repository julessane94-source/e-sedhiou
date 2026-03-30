<?php
/**
 * Script pour générer un rapport de diagnostic sécurité
 * Utilise DOMPDF pour créer le PDF
 */

require_once __DIR__ . '/backend-laravel/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Initialiser DOMPDF
$options = new Options();
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Générer le contenu HTML du rapport
$html = <<<'HTML'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Diagnostic Sécurité - Mairie Civique</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #007A5E;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #007A5E;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .metadata {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-size: 12px;
        }
        
        .metadata strong {
            color: #007A5E;
        }
        
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #007A5E;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 3px;
        }
        
        .subsection-title {
            color: #007A5E;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #007A5E;
            padding-left: 10px;
        }
        
        .content {
            padding: 0 10px;
            line-height: 1.8;
        }
        
        .content p {
            margin-bottom: 10px;
            text-align: justify;
        }
        
        .strength-item {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .weakness-item {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .improvement-item {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .item-label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        
        .item-description {
            color: #555;
            font-size: 13px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        table th {
            background-color: #007A5E;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .pricing-table th,
        .pricing-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .pricing-table th {
            background-color: #007A5E;
            color: white;
        }
        
        .price {
            color: #007A5E;
            font-weight: bold;
        }
        
        .footer {
            border-top: 1px solid #ddd;
            margin-top: 40px;
            padding-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 3px;
            margin: 15px 0;
        }
        
        .recommendation-box {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 3px;
            margin: 15px 0;
        }
        
        .score-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
        }
        
        .score-high {
            background-color: #4caf50;
        }
        
        .score-medium {
            background-color: #ff9800;
        }
        
        .score-low {
            background-color: #f44336;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>📋 Rapport de Diagnostic Sécurité</h1>
            <p>Plateforme : Mairie Civique (WordPress + Laravel)</p>
        </div>
        
        <!-- Métadonnées -->
        <div class="metadata">
            <p><strong>Date du rapport :</strong> 27 mars 2026</p>
            <p><strong>Plateforme :</strong> WordPress 6.x + Laravel 12.x (Architecture Hybride)</p>
            <p><strong>Type d'évaluation :</strong> Diagnostic complet sécurité, hébergement et maintenance</p>
            <p><strong>Environnement :</strong> Développement local (XAMPP) / Production à planifier</p>
        </div>
        
        <div class="page-break"></div>
        
        <!-- Section 1: Vue d'ensemble -->
        <div class="section">
            <div class="section-title">1. Vue d'Ensemble de l'Architecture</div>
            <div class="content">
                <p><strong>Infrastructure actuelle :</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 15px;">
                    <li>Framework : WordPress (CMS principal) + Laravel 12 (API backend)</li>
                    <li>PHP : Version 8.2+ requis (Laravel 12)</li>
                    <li>Base de données : MySQL (mairie_wp_db + mairie_laravel_db)</li>
                    <li>Serveur : XAMPP (développement), Production non déployée</li>
                    <li>Thème actif : Sedhiou Civique Personnalisé</li>
                </ul>
                
                <p><strong>Composants clés :</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Frontend : WordPress avec thème personnalisé Bootstrap 5</li>
                    <li>API : Laravel API REST (JWT Auth)</li>
                    <li>Assets : Vite (compilation CSS/JS)</li>
                    <li>PDF : DOMPDF (génération de documents)</li>
                </ul>
            </div>
        </div>
        
        <!-- Section 2: Points Forts -->
        <div class="section">
            <div class="section-title">2. ✅ Points Forts en Sécurité</div>
            <div class="content">
                <div class="strength-item">
                    <span class="item-label">✓ Authentification JWT pour l'API Laravel</span>
                    <span class="item-description">Tymon/JWT-Auth (v2.3) offre une authentification sécurisée et stateless pour les appels API. Moins vulnérable aux attaques CSRF que les sessions.</span>
                </div>
                
                <div class="strength-item">
                    <span class="item-label">✓ Hash Bcrypt pour les mots de passe</span>
                    <span class="item-description">BCRYPT_ROUNDS=12 dans Laravel fournit un hachage sécurisé avec coût computational adéquat (résistance aux attaques par dictionnaire).</span>
                </div>
                
                <div class="strength-item">
                    <span class="item-label">✓ Sessions chiffrées dans Laravel</span>
                    <span class="item-description">SESSION_ENCRYPT=true active le chiffrement des sessionStorage dans la base de données, protégeant les données sensibles.</span>
                </div>
                
                <div class="strength-item">
                    <span class="item-label">✓ Séparation d'architecture WordPress/Laravel</span>
                    <span class="item-description">Isolation des préoccupations : WordPress gère le CMS, Laravel gère l'API métier. Réduit la surface d'attaque de chaque couche.</span>
                </div>
                
                <div class="strength-item">
                    <span class="item-label">✓ Protection des fichiers sensibles</span>
                    <span class="item-description">Fichiers .env, config et vendor en dehors du web root (XAMPP), empêchant l'accès direct.</span>
                </div>
                
                <div class="strength-item">
                    <span class="item-label">✓ Clés de sécurité WordPress configurées</span>
                    <span class="item-description">AUTH_KEY, SECURE_AUTH_KEY, LOGGED_IN_KEY, NONCE_SALT sont uniques et aléatoires, valides pour la production.</span>
                </div>
                
                <div class="strength-item">
                    <span class="item-label">✓ Dépendances à jour (Composer)</span>
                    <span class="item-description">Laravel 12.x et toutes dépendances utilisant des versions stables et maintenus (DOMPDF, JWT-Auth, etc.).</span>
                </div>
            </div>
        </div>
        
        <div class="page-break"></div>
        
        <!-- Section 3: Points Faibles -->
        <div class="section">
            <div class="section-title">3. ⚠️ Points Faibles et Vulnérabilités</div>
            <div class="content">
                <div class="weakness-item">
                    <span class="item-label">🔴 CRITIQUE : Mots de passe en texte clair dans wp-config.php</span>
                    <span class="item-description">
                        <strong>Problème :</strong> DB_PASSWORD et MAIRIE_LARAVEL_API_TOKEN sont visibles dans le code source.<br>
                        <strong>Risque :</strong> Si wp-config.php est expposé, tous les secrets sont compromis.SEVERE RISK
                        <strong>Impact :</strong> Accès direct à la base de données, usurpation d'authentification sur l'API.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🔴 CRITIQUE : APP_DEBUG=true en environnement (même local)</span>
                    <span class="item-description">
                        <strong>Problème :</strong> Le mode debug Laravel est activé (.env : APP_DEBUG=true).<br>
                        <strong>Risque :</strong> Les pages d'erreur (Whoops) révèlent la structure du code, chemins serveur, variables. Parfait pour la reconnaissance lors d'une attaque.
                        <strong>Action :</strong> APP_DEBUG=false obligatoire en production.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🔴 CRITIQUE : SESSION_SECURE_COOKIE=false</span>
                    <span class="item-description">
                        <strong>Problème :</strong> Les cookies de session ne sont pas marqués comme Secure.
                        <strong>Risque :</strong> Les cookies peuvent être transmis en HTTP clair, vulnérables aux attaques Man-in-the-Middle (MITM).
                        <strong>Action :</strong> Forcer SESSION_SECURE_COOKIE=true + HTTPS en production.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🔴 CRITIQUE : Pas d'HTTPS en développement (attendu, mais nécessaire en production)</span>
                    <span class="item-description">
                        <strong>Problème :</strong> L'application fonctionne en HTTP local. Pas de chiffrement des données en transit.
                        <strong>Risque :</strong> En PROD, sans HTTPS, toutes les données sont lisibles en transit (credentials, données civiques, etc.).
                        <strong>Action :</strong> Cert SSL/TLS obligatoire en production.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🟠 ÉLEVÉ : Deux bases de données MySQL non synchronisées</span>
                    <span class="item-description">
                        <strong>Problème :</strong> mairie_wp_db (WordPress) + mairie_laravel_db (Laravel) sont séparées.
                        <strong>Risque :</strong> Risque de désynchronisation, d'incohérence de données, de contraintes ACID compromises.
                        <strong>Action :</strong> Unifier les BDD ou mettre en place une synchronisation robuste.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🟠 ÉLEVÉ : Pas de Rate Limiting visible sur l'API Laravel</span>
                    <span class="item-description">
                        <strong>Problème :</strong> Aucun throttle/rate-limiting configuré pour les endpoints API.
                        <strong>Risque :</strong> Attaques par force brute, DDoS, épuisement des ressources serveur.
                        <strong>Action :</strong> Implémenter Laravel Throttle middleware.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🟠 ÉLEVÉ : Pas de validation CORS visible</span>
                    <span class="item-description">
                        <strong>Problème :</strong> Pas de configurations CORS observées pour l'API.
                        <strong>Risque :</strong> Requêtes cross-origin non contrôlées = XSS, vol de données.
                        <strong>Action :</strong> Configurer allowedOrigins strictement, refuser origins inconnues.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🟠 ÉLEVÉ : Permissions fichiers sur .env et secrets</span>
                    <span class="item-description">
                        <strong>Problème :</strong> .env doit avoir des permissions 600 (readable que par owner). Pas de vérification.
                        <strong>Risque :</strong> Autres processus/utilisateurs système peuvent lire les secrets.
                        <strong>Action :</strong> Vérifier permissions et documenter : chmod 600 .env
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🟡 MOYEN : Plugins WordPress minimaux mais non audités</span>
                    <span class="item-description">
                        <strong>Problème :</strong> Seul Akismet activé. Pas de plugins de sécurité (Wordfence, iThemes Security).
                        <strong>Risque :</strong> WordPress sans hardening supplémentaire, vulnérable aux attacks courantes (brute force login, SQL injection, etc.).
                        <strong>Action :</strong> Ajouter WAF plugin, monitoring de sécurité.
                    </span>
                </div>
                
                <div class="weakness-item">
                    <span class="item-label">🟡 MOYEN : Logs non centralisés</span>
                    <span class="item-description">
                        <strong>Problème :</strong> LOG_CHANNEL=stack (logs locaux uniquement). Pas d'agrégation centralisée (Cloudwatch, Datadog, etc.).
                        <strong>Risque :</strong> Logs éparpillés, difficiles à monitorer en cas de breach.
                        <strong>Action :</strong> Implémenter ELK Stack ou service tiers pour logging centralisé.
                    </span>
                </div>
            </div>
        </div>
        
        <div class="page-break"></div>
        
        <!-- Section 4: Recommandations d'amélioration -->
        <div class="section">
            <div class="section-title">4. 📈 Recommandations d'Amélioration</div>
            <div class="content">
                <div class="subsection-title">Phase 1 : CRITIQUE (À faire avant production) — 1-2 semaines</div>
                
                <div class="improvement-item">
                    <span class="item-label">1.1 Utiliser des variables d'environnement système au lieu de fichiers</span>
                    <span class="item-description">• Migrer DB_PASSWORD, API_TOKEN vers variables d'env système / secrets manager<br>
                    • Jamais committer .env en Git (vérifier .gitignore)<br>
                    • Utiliser AWS Secrets Manager / Azure Key Vault / HashiCorp Vault en production
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">1.2 Activer HTTPS avec certificat SSL/TLS</span>
                    <span class="item-description">• Acheter/générer cert SSL (Let's Encrypt gratuit)<br>
                    • Forcer redirection HTTP → HTTPS<br>
                    • Configurer HSTS header (Strict-Transport-Security)<br>
                    • SESSION_SECURE_COOKIE=true<br>
                    • Tester avec https://www.ssllabs.com/ssltest/
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">1.3 Désactiver debug mode et configurer erreurs sécurisées</span>
                    <span class="item-description">• APP_DEBUG=false<br>
                    • WP_DEBUG=false (déjà correct dans wp-config)<br>
                    • Rediriger erreurs vers fichiers logs, pas vers utilisateurs<br>
                    • Configurer error_reporting = E_ALL & ~E_WARNING en prod
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">1.4 Implémenter Rate Limiting sur l'API</span>
                    <span class="item-description">• Ajouter RateLimitServiceProvider dans Laravel<br>
                    • Exemple : 60 requêtes/minute/user, 1000/heure/IP<br>
                    • Middleware Throttle sur routes API critiques<br>
                    • Bloquer automatiquement IPs suspectes (>500 erreurs/jour)
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">1.5 Configurer CORS strictement</span>
                    <span class="item-description">• Blanche liste origins autorisées uniquement (ex: https://mairie-sedhiou.sn)<br>
                    • Refuser : 'Accept-Language', 'User-Agent' en wildcard<br>
                    • Middleware config/cors.php dans Laravel<br>
                    • Tester : curl -H "Origin: evil.com" ... (doit être rejeté)
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">1.6 Sécuriser la base de données</span>
                    <span class="item-description">• Changer user/password MySQL par défaut<br>
                    • Créer utilisateur dédié par schéma (WP vs Laravel)<br>
                    • Limiter privileges : SELECT, INSERT, UPDATE (pas DROP, GRANT)<br>
                    • Backup automatisé quotidien + test restore mensuel<br>
                    • Activer encryption at rest (InnoDB)
                    </span>
                </div>
                
                <div class="subsection-title">Phase 2 : ÉLEVÉ (1-3 mois) — Infrastructure et Monitoring</div>
                
                <div class="improvement-item">
                    <span class="item-label">2.1 Ajouter WAF et protection WordPress</span>
                    <span class="item-description">• Plugin : Wordfence Security (gratuit) ou iThemes Security<br>
                    • Cloudflare WAF (10$ minimum, ou plan gratuit limité)<br>
                    • Bloquer équipes admin scanners, malware bots<br>
                    • Monitoring infections temps réel
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">2.2 Implémenter logging centralisé et monitoring</span>
                    <span class="item-description">• Stack ELK (Elasticsearch, Logstash, Kibana) OU<br>
                    • Service : Datadog, New Relic, Sentry (error tracking)<br>
                    • Alertes : Login failed x3, SQL errors, API latency >5s<br>
                    • Rétention logs : 90 jours min
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">2.3 Configuration 2FA/MFA pour administrateurs</span>
                    <span class="item-description">• WordPress : Google Authenticator plugin (MFA obligatoire admins)<br>
                    • Laravel API : JWT + TOTP pour routes sensibles<br>
                    • Backup codes : imprimer + stocker sécurisé
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">2.4 Audit et hardening système fichiers</span>
                    <span class="item-description">• Permissions : 755 dossiers, 644 fichiers (stricts)<br>
                    • .env : 600 (owner only)<br>
                    • wp-config.php : 600<br>
                    • Désactiver FTP/SFTP par défaut, SSH only avec clés ED25519
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">2.5 Backups automatisés et testés</span>
                    <span class="item-description">• Backup complet (files + BDD) chaque jour à 2am UTC<br>
                    • Stocker offsite : AWS S3, Google Cloud, Azure Blob Storage<br>
                    • Restauration test mensuel<br>
                    • Durée rétention : 30 jours min, 1 an pour archives critiques
                    </span>
                </div>
                
                <div class="subsection-title">Phase 3 : MOYEN (3-6 mois) — Optimisation et Scaling</div>
                
                <div class="improvement-item">
                    <span class="item-label">3.1 Unifier les bases de données ou synchroniser</span>
                    <span class="item-description">• Décision : une seule BDD centralisée (préféré) OU<br>
                    • Synchronisation bi-directionnelle avec triggers/workers<br>
                    • Event sourcing pour audit trail complet<br>
                    • Tester intégrité ACID sur 100k+ transactions
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">3.2 Mise en cache et optimisation performance</span>
                    <span class="item-description">• Redis/Memcached pour cache sessions et requêtes API<br>
                    • CDN Cloudflare ou AWS CloudFront pour assets statics<br>
                    • Compression gzip + brotli<br>
                    • Lazy loading images, minification JS/CSS
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">3.3 Tests de sécurité automatisés (CI/CD)</span>
                    <span class="item-description">• SonarQube ou Snyk pour code analysis (dependency vulnerabilities)<br>
                    • OWASP ZAP scanning sur chaque release<br>
                    • Security testing dans pipelines GitHub Actions / GitLab CI<br>
                    • Pentest annuel obligatoire ($5-10k)
                    </span>
                </div>
                
                <div class="improvement-item">
                    <span class="item-label">3.4 Documentation sécurité et incident response</span>
                    <span class="item-description">• Playbook incident : breach, DDoS, data loss<br>
                    • Contact d'escalade 24h/24<br>
                    • Chiffrement fichiers sensibles (GDPR/RGPD compliance)<br>
                    • Audit de conformité légale (CNIL si FR, ou régulateur sénégalais)
                    </span>
                </div>
            </div>
        </div>
        
        <div class="page-break"></div>
        
        <!-- Section 5: Coûts Hébergement et Maintenance -->
        <div class="section">
            <div class="section-title">5. 💰 Coûts Estimés : Hébergement et Maintenance</div>
            <div class="content">
                <div class="subsection-title">5.1 Infrastructures d'Hébergement Possibles (mensuel)</div>
                
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Option</th>
                            <th>Configuration</th>
                            <th>Prix/mois USD</th>
                            <th>Prix/mois EUR</th>
                            <th>Recommandation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>1. Shared Hosting</strong><br>(Low-cost)</td>
                            <td>1 vCore, 2GB RAM, 50GB SSD, MySQL</td>
                            <td class="price">$5-10</td>
                            <td class="price">€5-9</td>
                            <td>❌ Non recommandé (faible perfs, limites)</td>
                        </tr>
                        <tr>
                            <td><strong>2. VPS Cloud</strong><br>(Recommandé)</td>
                            <td>2-4 vCore, 4-8GB RAM, 100GB SSD, MySQL</td>
                            <td class="price">$15-50</td>
                            <td class="price">€14-45</td>
                            <td>✅ Bon compromis scalabilité/coût</td>
                        </tr>
                        <tr>
                            <td><strong>3. Dedicated Server</strong></td>
                            <td>8 vCore, 32GB RAM, 500GB SSD, MySQL</td>
                            <td class="price">$50-150</td>
                            <td class="price">€45-135</td>
                            <td>⚠️ Overkill pour traffic faible-moyen</td>
                        </tr>
                        <tr>
                            <td><strong>4. PaaS (Heroku/Railway)</strong></td>
                            <td>Auto-scaling, CDN, BDD managed</td>
                            <td class="price">$30-100+</td>
                            <td class="price">€27-90+</td>
                            <td>✅ Zero-ops, sécurité built-in</td>
                        </tr>
                        <tr>
                            <td><strong>5. Docker/K8s (AWS/GCP)</strong></td>
                            <td>Container, auto-scaling, load balancing</td>
                            <td class="price">$50-200+</td>
                            <td class="price">€45-180+</td>
                            <td>✅ Enterprise-grade, coût variable</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="recommendation-box">
                    <strong>✅ Recommandation pour Mairie Civique :</strong> VPS Cloud (OVH, Digitalocean, Vultr, Linode) en configuration 2-4 vCore + 4-8GB RAM = <span class="price">€20-40/mois</span>. Offre un bon équilibre perf/sécurité/coût pour une mairie.
                </div>
                
                <div class="subsection-title">5.2 Services Additionnels (mensuel)</div>
                
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Description</th>
                            <th>Prix/mois USD</th>
                            <th>Obligatoire ?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Certificat SSL/TLS</strong></td>
                            <td>Let's Encrypt (gratuit) + auto-renewal</td>
                            <td class="price">$0</td>
                            <td>✅ Oui</td>
                        </tr>
                        <tr>
                            <td><strong>Domaine .sn</strong></td>
                            <td>mairie-sedhiou.sn (ex)</td>
                            <td class="price">$10-20/an</td>
                            <td>✅ Oui</td>
                        </tr>
                        <tr>
                            <td><strong>Cloudflare Pro</strong></td>
                            <td>CDN, WAF, DDoS protection</td>
                            <td class="price">$20</td>
                            <td>✅ Recommandé</td>
                        </tr>
                        <tr>
                            <td><strong>Backup Service</strong></td>
                            <td>BackWPup, UpdraftPlus (cloud storage)</td>
                            <td class="price">$5-15</td>
                            <td>✅ Oui</td>
                        </tr>
                        <tr>
                            <td><strong>Monitoring / Sentry</strong></td>
                            <td>Error tracking, uptime monitoring</td>
                            <td class="price">$0-29</td>
                            <td>⚠️ Recommandé</td>
                        </tr>
                        <tr>
                            <td><strong>Email Transactionnel</strong></td>
                            <td>SendGrid, Brevo (SMTP)</td>
                            <td class="price">$10-25</td>
                            <td>✅ Oui</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="subsection-title">5.3 Coûts Maintenance et Support (mensuel/annuel)</div>
                
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Type de Maintenance</th>
                            <th>Effort/Mois</th>
                            <th>Coût Estimé</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Maintenance Interne</strong> (équipe locale)</td>
                            <td>4-8h/mois</td>
                            <td>€500-1200/mois</td>
                            <td>Updates plugins, monitoring, sauvegarde</td>
                        </tr>
                        <tr>
                            <td><strong>Support Technique Externe</strong> (SaaS)</td>
                            <td>24h annual</td>
                            <td>€2000-5000/an</td>
                            <td>Support incident, troubleshooting</td>
                        </tr>
                        <tr>
                            <td><strong>Audit Sécurité Annuel</strong> (pentest)</td>
                            <td>40-80h annuel</td>
                            <td>€5000-15000/an</td>
                            <td>Recommandé pour gouvernance civique</td>
                        </tr>
                        <tr>
                            <td><strong>Développement Features</strong></td>
                            <td>Variable</td>
                            <td>€3000-10000+ /projet</td>
                            <td>Nouvelles demandes citoyens, intégrations</td>
                        </tr>
                        <tr>
                            <td><strong>Formation/Documentation</strong></td>
                            <td>Annuel</td>
                            <td>€1000-3000</td>
                            <td>Formation staff, doc sécurité</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="subsection-title">5.4 Synthèse Budget Annuel Estimé</div>
                
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Coût Annuel (EUR)</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background-color: #f0f0f0;">
                            <td><strong>HÉBERGEMENT</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>• Infrastructure VPS Cloud</td>
                            <td class="price">€240-480</td>
                            <td>2-4 vCore, 4-8GB RAM</td>
                        </tr>
                        <tr>
                            <td>• Domaine + DNS</td>
                            <td class="price">€12-20</td>
                            <td>.sn registration</td>
                        </tr>
                        <tr>
                            <td>• SSL/TLS (Let's Encrypt)</td>
                            <td class="price">€0</td>
                            <td>Gratuit + auto-renewal</td>
                        </tr>
                        <tr>
                            <td>• CDN/WAF (Cloudflare Pro)</td>
                            <td class="price">€240</td>
                            <td>$20/mois</td>
                        </tr>
                        <tr>
                            <td>• Backup Service</td>
                            <td class="price">€60-180</td>
                            <td>€5-15/mois</td>
                        </tr>
                        <tr>
                            <td>• Email Transactionnel</td>
                            <td class="price">€120-300</td>
                            <td>€10-25/mois</td>
                        </tr>
                        <tr style="background-color: #ffe8e8; font-weight: bold;">
                            <td><strong>Sous-total Hébergement</strong></td>
                            <td class="price">€672-1260</td>
                            <td></td>
                        </tr>
                        
                        <tr style="background-color: #f0f0f0;">
                            <td><strong>MAINTENANCE</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>• Maintenance interne (équipe)</td>
                            <td class="price">€6000-14400</td>
                            <td>€500-1200/mois</td>
                        </tr>
                        <tr>
                            <td>• Support technique externe (SaaS)</td>
                            <td class="price">€2000-5000</td>
                            <td>Ticket support 24h</td>
                        </tr>
                        <tr>
                            <td>• Audit sécurité annuel (pentest)</td>
                            <td class="price">€5000-15000</td>
                            <td>Important pour données civiques</td>
                        </tr>
                        <tr>
                            <td>• Formation staff</td>
                            <td class="price">€1000-3000</td>
                            <td>Formation, documentation</td>
                        </tr>
                        <tr style="background-color: #ffe8e8; font-weight: bold;">
                            <td><strong>Sous-total Maintenance</strong></td>
                            <td class="price">€14000-37400</td>
                            <td></td>
                        </tr>
                        
                        <tr style="background-color: #e8f5e9;">
                            <td colspan="2" style="font-weight: bold; text-align: right;">TOTAL ANNUEL ESTIMÉ (minimum)</td>
                            <td class="price" style="background-color: #c8e6c9;">€14672-38660</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="warning-box">
                    <strong>⚠️ Note :</strong> Les coûts de maintenance varient fortement selon :
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li><strong>Expertise interne :</strong> Équipe locale expérimentée = moins cher que consultant externe</li>
                        <li><strong>Complexité métier :</strong> Nouvelles demandes = développement additionnel = coût +</li>
                        <li><strong>Volume données/utilisateurs :</strong> Si > 100k users/an, scale vers infra plus puissante</li>
                        <li><strong>Conformité légale :</strong> RGPD/CNIL si données personnelles = audit annuel obligatoire</li>
                    </ul>
                </div>
                
                <div class="subsection-title">5.5 Recommandations Pricing</div>
                
                <div class="recommendation-box">
                    <strong>💡 Scénario 1 : Mairie avec équipe IT locale (recommandé)</strong><br><br>
                    • Infrastructure : VPS Cloud OVH 2-4 vCore = €25-40/mois<br>
                    • Services (WAF, backup, email) = €30-40/mois<br>
                    • Équipe IT interne : 1 admin system + 1 dev (embauche interne ou prestataire fixe)<br>
                    • Pentest annuel : €5-10k une fois/an<br><br>
                    <strong>Budget annuel : €15-20k</strong> (infrastructure + équipe)
                </div>
                
                <div class="recommendation-box">
                    <strong>💡 Scénario 2 : Mairie sans équipe IT (outsource complet)</strong><br><br>
                    • PaaS managé (Heroku/Railway) : €50-150/mois<br>
                    • Support SaaS 24/7 : €2-5k/an<br>
                    • Pentest + consulting : €10-15k/an<br><br>
                    <strong>Budget annuel : €20-30k</strong> (zéro gestion locale)
                </div>
                
                <div class="recommendation-box">
                    <strong>💡 Scénario 3 : Démarrage lean (à croissance)</strong><br><br>
                    • Shared Hosting (déconseillé mais économique) : €5-10/mois<br>
                    • Pas de support externe = autogestion<br>
                    • Pas de pentest = risque de sécurité ⚠️<br><br>
                    <strong>Budget annuel : €60-120</strong> (non recommandé pour gouvernance civique)
                </div>
            </div>
        </div>
        
        <div class="page-break"></div>
        
        <!-- Section 6: Conformité Légale et RGPD -->
        <div class="section">
            <div class="section-title">6. ⚖️ Conformité Légale (RGPD/Données Personnelles)</div>
            <div class="content">
                <div class="warning-box">
                    <strong>⚠️ Attention :</strong> Cette plateforme gère probablement des données civiles (citoyens, demandes administratives = données personnelles).
                </div>
                
                <div class="subsection-title">Enjeux RGPD (Sénégal/Afrique)</div>
                
                <p>Même si le RGPD est européen, le Sénégal dépend de la CNIL sénégalaise et peut avoir ses propres réglementations.</p>
                
                <ul style="margin-left: 20px; margin-bottom: 15px;">
                    <li><strong>Consentement explicite :</strong> Avant de traiter données persos, consentement utilisateur enregistré</li>
                    <li><strong>Droit à l'oubli :</strong> Utilisateur peut demander suppression de ses données</li>
                    <li><strong>Transparence :</strong> Politique de confidentialité claire, accessible</li>
                    <li><strong>Sécurité :</strong> Chiffrement données en transit (HTTPS) + at-rest</li>
                    <li><strong>Notification breach :</strong> Si fuite données, notifier users sous 72h</li>
                </ul>
                
                <div class="recommendation-box">
                    <strong>Action obligatoire :</strong><br>
                    ✅ Ajouter page /confidentialite avec politique sécurité/consentement<br>
                    ✅ Implémenter cookie consent banner (GDPR/ePrivacy)<br>
                    ✅ Audit RGPD annuel par conseil juridique<br>
                    ✅ Contrat DPA avec fournisseurs cloud si données hébergées chez tiers<br>
                    ✅ Breach response plan documenté
                </div>
            </div>
        </div>
        
        <!-- Section 7: Conclusion et Score Global -->
        <div class="section">
            <div class="section-title">7. 📊 Conclusion et Score Global de Sécurité</div>
            <div class="content">
                <div style="text-align: center; margin: 30px 0;">
                    <h3 style="color: #007A5E; margin-bottom: 20px;">Score de Sécurité Actuel</h3>
                    <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
                        <div style="text-align: center;">
                            <div style="font-size: 36px; font-weight: bold; color: #ff9800; margin-bottom: 10px;">4.5 / 10</div>
                            <div style="color: #666;">Score Global</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 28px; font-weight: bold; color: #4caf50; margin-bottom: 10px;">✅ 7 Points Forts</div>
                            <div style="color: #666;">Architecture, auth, encryption</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 28px; font-weight: bold; color: #f44336; margin-bottom: 10px;">🔴 9 Points Faibles</div>
                            <div style="color: #666;">Debug, HTTPS, rate-limit, logs</div>
                        </div>
                    </div>
                </div>
                
                <div class="subsection-title">Synthèse</div>
                
                <p><strong>Statut :</strong> <span class="score-badge score-medium">PRÊT POUR DÉVELOPPEMENT - PAS PRÊT POUR PRODUCTION</span></p>
                
                <p>La plateforme <strong>Mairie Civique</strong> a une good foundation avec Laravel et architecture hybride sensible. Cependant, <strong>plusieurs vulnérabilités critiques</strong> doivent être addressées avant déploiement en production :</p>
                
                <ul style="margin-left: 20px; margin-bottom: 15px;">
                    <li>🔴 <strong>CRITIQUE :</strong> Secrets (mots de passe, tokens) en texte clair dans wp-config.php</li>
                    <li>🔴 <strong>CRITIQUE :</strong> Debug mode activé (APP_DEBUG=true)</li>
                    <li>🔴 <strong>CRITIQUE :</strong> Pas d'HTTPS, cookies non sécurisés</li>
                    <li>🟠 <strong>ÉLEVÉ :</strong> Pas de Rate Limiting, CORS, ou WAF sur API</li>
                </ul>
                
                <p><strong>Coût d'amélioration :</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Phase 1 (CRITIQUE) : €2-5k (1-2 semaines dev)</li>
                    <li>Phase 2 (ÉLEVÉ) : €5-15k (1-3 mois)</li>
                    <li>Phase 3 (MOYEN) : €10-20k (3-6 mois)</li>
                </ul>
                
                <div class="recommendation-box" style="margin-top: 30px; margin-bottom: 30px;">
                    <strong>✅ Plan d'action recommandé :</strong><br>
                    1. Implémenter Phase 1 (critique) avant release beta<br>
                    2. Déployer sur VPS Cloud sécurisé avec HTTPS + WAF<br>
                    3. Pentest indépendant avant production (€5-10k)<br>
                    4. Formation équipe maintenance<br>
                    5. Monitoring 24/7 + incident response plan<br>
                    6. Audit sécurité annuel obligatoire<br><br>
                    <strong>Timeline estimée production : 2-3 mois après Phase 1</strong>
                </div>
                
                <div class="subsection-title">Contacts Recommandés</div>
                
                <p style="margin-bottom: 15px;"><strong>Pour infrastructure & sécurité :</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 15px;">
                    <li>OVH / Vultr / Linode : VPS Cloud et managé (€25-50/mois Europe)</li>
                    <li>Cloudflare : WAF et DDoS protection (€20/mois, excellente réputation)</li>
                    <li>Sentry.io : Error tracking et performance monitoring (gratuit - $500+)</li>
                </ul>
                
                <p style="margin-bottom: 15px;"><strong>Pour audit & consulting sécurité :</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Agences spécialisées sécurité PHP/Laravel en Afrique ou Europe</li>
                    <li>OWASP members pour pentest certifiés</li>
                    <li>Conseil juridique pour conformité RGPD/données sénégalaises</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Rapport généré automatiquement le 27 mars 2026</strong></p>
            <p>Document confidentiel - Exclusivement pour Mairie Civique (Sedhiou)</p>
            <p>Les recommandations sont basées sur bonnes pratiques OWASP et industrie cloud.</p>
        </div>
    </div>
</body>
</html>
HTML;

// Charger le HTML dans DOMPDF
$dompdf->loadHtml($html);

// Configurer le papier (A4)
$dompdf->setPaper('A4', 'portrait');

// Renderer le PDF
$dompdf->render();

// Sauvegarder le PDF
$filename = 'DIAGNOSTIC_SECURITE_MAIRIE_CIVIQUE_' . date('Y-m-d_H-i-s') . '.pdf';
$filepath = __DIR__ . '/' . $filename;

file_put_contents($filepath, $dompdf->output());

// Message de confirmation
echo "✅ PDF généré avec succès : " . $filename . "\n";
echo "📁 Chemin complet : " . $filepath . "\n";
echo "💾 Poids du fichier : " . round(filesize($filepath) / 1024, 2) . " KB\n";

?>
