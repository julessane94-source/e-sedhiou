<?php
/**
 * Laravel Bridge for WordPress Integration
 * This file integrates Laravel routes within WordPress
 */

// Define the Laravel path
define( 'LARAVEL_ROOT_PATH', __DIR__ . '/backend-laravel' );

// Check if Laravel exists
if ( ! file_exists( LARAVEL_ROOT_PATH ) ) {
	wp_die( 'Laravel backend directory not found.' );
}

// Load Laravel's autoloader
if ( ! file_exists( LARAVEL_ROOT_PATH . '/vendor/autoload.php' ) ) {
	wp_die( 'Laravel composer dependencies not installed. Run: php composer.phar install in backend-laravel/' );
}

require LARAVEL_ROOT_PATH . '/vendor/autoload.php';

// Create Laravel app instance
try {
	$app = require_once LARAVEL_ROOT_PATH . '/bootstrap/app.php';

	// Get the Kernel
	$kernel = $app->make( Illuminate\Contracts\Http\Kernel::class );

	// Create request from WordPress globals
	$request = Illuminate\Http\Request::capture();

	// Handle the request through Laravel
	$response = $kernel->handle( $request );

	// Send the response
	$response->send();

	// Terminate
	$kernel->terminate( $request, $response );

} catch ( Exception $e ) {
	wp_die( 'Laravel Error: ' . $e->getMessage() );
}
