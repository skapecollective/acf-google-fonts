<?php

/**
 * Plugin Name: Advanced Custom Fields: Google Fonts
 * Description: A field for Advanced Custom Fields (ACF) allowing users to select fonts from the Google Fonts suite.
 * Version: 1.0.1
 * Plugin URI: https://github.com/skapecollective/acf-google-fonts/
 * Author: Skape Collective
 * Author URI: https://skape.co/
 * Text Domain: skape
 * Network: false
 * Requires at least: 5.0.0
 * Requires PHP: 7.2
 */

require_once plugin_dir_path( __FILE__ ) . 'source/Autoload.php';
$autoload = new AcfGoogleFonts\Autoload( plugin_dir_path( __FILE__ ) );

$autoload->loadArray( [
	'AcfGoogleFonts\\' => 'source'
], 'psr-4' );

// Register global constants
AcfGoogleFonts\Wrappers\Constants::set( 'FILE', __FILE__ );
AcfGoogleFonts\Wrappers\Constants::set( 'DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
AcfGoogleFonts\Wrappers\Constants::set( 'VERSION', '1.0.1' );
AcfGoogleFonts\Wrappers\Constants::set( 'PATH', plugin_dir_path( __FILE__ ) );
AcfGoogleFonts\Wrappers\Constants::set( 'URL', plugin_dir_url( __FILE__ ) );
AcfGoogleFonts\Wrappers\Constants::set( 'BASENAME', plugin_basename( __FILE__ ) );

// Init admin notice controller.
AcfGoogleFonts\Admin\Notices::init();

// Init the admin controller.
AcfGoogleFonts\Controllers\Admin::init();

// Init ACF field > v5
add_action( 'acf/include_field_types', function( $version ) {
	new AcfGoogleFonts\Field;
} );
