<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mairie_wp_db' );

/** Database username */
define( 'DB_USER', 'mairie_user' );

/** Database password */
define( 'DB_PASSWORD', 'Baye1994@' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Ruj2#r#]DN!*F xjdmFTuQ_=d04e([r`%270Y}/hc.Vv5rgd#P|/l>OP(GA$SP[g' );
define( 'SECURE_AUTH_KEY',  'QK`X>!OMm3~5z}8f(iMroRX3yI5nQGeAz<4`iQhHZe0gVo9[pJuVb-,q ,[T5G~@' );
define( 'LOGGED_IN_KEY',    'qm(|dtM.@U8_s*D-%U{t%4e1ky_5j0Yf4Xl&j@|=4s4Vah~dsLQY0Wpp.@KRRowE' );
define( 'NONCE_KEY',        'Mh WDF3npdf&sZ:bjm:Hc(|{1aeK1-hqg#Ipq5)]=][8N8u?skVxQaUx93d){UC+' );
define( 'AUTH_SALT',        'js<_2QhH%r85?Uq9jAcn+p%QX:P+-_4Iz@0_M(x5jw5P^t9oj@=aXvRgn-MxD-5-' );
define( 'SECURE_AUTH_SALT', 'EKnhtW35^$+:p|CQ-oD`l#Bu9W`#)>+mm2Iq]qYRd$=ci8RP7=O-y,6WjS!twa8/' );
define( 'LOGGED_IN_SALT',   'pG=WL/}[%lsJ3$jV{Z%{u-eVu)]L{$]k4[ep5ui_YerBduT9T`=[d1W-iHcAhfN9' );
define( 'NONCE_SALT',       '1@XJtR[Jn]^Kj*fGXNh~Q;{9AC{IR@=c=T,h0FT^ rV$_mILn+PJl[LIu8b`%1J<' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * URL du site — Configuration corrigée pour éviter les boucles de redirection
 * RELOCATE permet à WordPress de détecter et utiliser l'URL actuelle
 */
define( 'WP_RELOCATE', true );

// Sur localhost/XAMPP HTTP, ne pas forcer HTTPS
if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

// Déterminer le schéma correctement
$scheme = ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== false ) ? 'https' : 'http';

// Utiliser l'HTTP_HOST fourni par le serveur
if ( isset( $_SERVER['HTTP_HOST'] ) ) {
	// Nettoyer HTTP_HOST (supprimer port si présent pour compatibilité)
	$http_host = explode( ':', $_SERVER['HTTP_HOST'] )[0];
	$port = isset( $_SERVER['SERVER_PORT'] ) && (
		( $scheme === 'https' && $_SERVER['SERVER_PORT'] !== '443' ) ||
		( $scheme === 'http' && $_SERVER['SERVER_PORT'] !== '80' )
	) ? ':' . $_SERVER['SERVER_PORT'] : '';
	
	define( 'WP_HOME', $scheme . '://' . $http_host . $port . '/mairie_wp' );
	define( 'WP_SITEURL', $scheme . '://' . $http_host . $port . '/mairie_wp' );
}

// Désactiver les redirections automatiques qui causent des boucles
define( 'AUTOMATIC_UPDATER_DISABLED', true );

/** Sécurité : forcer les mots de passe forts pour les nouveaux comptes */
define( 'WP_PASS_CHANGE_USERS', true );

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

$mairie_runtime_host   = isset( $_SERVER['HTTP_HOST'] ) ? explode( ':', $_SERVER['HTTP_HOST'] )[0] : 'localhost';
$mairie_runtime_scheme = $scheme ?? 'http';
$mairie_runtime_root   = $mairie_runtime_scheme . '://' . $mairie_runtime_host . '/mairie_wp';

/* Add any custom values between this line and the "stop editing" line. */

// URL de base de l'API Laravel exposee sous la meme base d'URL que WordPress.
define( 'MAIRIE_LARAVEL_API_BASE_URL', $mairie_runtime_root );

// Endpoint WordPress -> Laravel sur l'URL unifiee.
define( 'MAIRIE_LARAVEL_API_URL', $mairie_runtime_root . '/api/v1/demandes/wordpress' );

// Token partagé WP ↔ Laravel (X-Mairie-Token) — NE PAS CHANGER sans mettre à jour .env Laravel
define( 'MAIRIE_LARAVEL_API_TOKEN', '95lItfzPf4fQ8BlETGZxAIWZYy0js7UKi8TcaauiDrU=' );

// URL du portail Laravel (pour redirections)
define( 'MAIRIE_LARAVEL_APP_URL', $mairie_runtime_root );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
