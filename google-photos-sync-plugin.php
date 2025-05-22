<?php
/**
 * The google-photos-sync-plugin bootstrap file.
 *
 * @since       1.0.0
 * @version     2.0.0
 * @author      WordPress.com Special Projects
 * @license     GPL-3.0-or-later
 *
 * @noinspection    ALL
 *
 * @wordpress-plugin
 * Plugin Name:             Google Photos Sync
 * Plugin URI:              https://wpspecialprojects.wordpress.com
 * Description:             Sync your Google Photos with your WordPress site
 * Version:                 2.0.0
 * Requires at least:       6.5
 * Tested up to:            6.5
 * Requires PHP:            8.1
 * Author:                  WordPress.com Special Projects
 * Author URI:              https://wpspecialprojects.wordpress.com
 * License:                 GPL v3 or later
 * License URI:             https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:             google-photos-sync-plugin
 * Domain Path:             /languages
 **/

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
function_exists( 'get_plugin_data' ) || require_once ABSPATH . 'wp-admin/includes/plugin.php';
define( 'GOOGLE_PHOTOS_SYNC_METADATA', get_plugin_data( __FILE__, false, false ) );

define( 'GOOGLE_PHOTOS_SYNC_BASENAME', plugin_basename( __FILE__ ) );
define( 'GOOGLE_PHOTOS_SYNC_PATH', plugin_dir_path( __FILE__ ) );
define( 'GOOGLE_PHOTOS_SYNC_URL', plugin_dir_url( __FILE__ ) );

// Load plugin translations so they are available even for the error admin notices.
add_action(
	'init',
	static function () {
		load_plugin_textdomain(
			GOOGLE_PHOTOS_SYNC_METADATA['TextDomain'],
			false,
			dirname( GOOGLE_PHOTOS_SYNC_BASENAME ) . GOOGLE_PHOTOS_SYNC_METADATA['DomainPath']
		);
	}
);

// Load the autoloader.
if ( ! is_file( GOOGLE_PHOTOS_SYNC_PATH . '/vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		static function () {
			$message      = __( 'It seems like <strong>google-photos-sync</strong> is corrupted. Please reinstall!', 'google-photos-sync-plugin' );
			$html_message = wp_sprintf( '<div class="error notice google-photos-sync-error">%s</div>', wpautop( $message ) );
			echo wp_kses_post( $html_message );
		}
	);
	return;
}
require_once GOOGLE_PHOTOS_SYNC_PATH . '/vendor/autoload.php';

// Initialize the plugin if system requirements check out.
$google_photos_sync_requirements = validate_plugin_requirements( GOOGLE_PHOTOS_SYNC_BASENAME );
define( 'GOOGLE_PHOTOS_SYNC_REQUIREMENTS', $google_photos_sync_requirements );

if ( $google_photos_sync_requirements instanceof WP_Error ) {
	add_action(
		'admin_notices',
		static function () use ( $google_photos_sync_requirements ) {
			$html_message = wp_sprintf( '<div class="error notice google-photos-sync-error">%s</div>', $google_photos_sync_requirements->get_error_message() );
			echo wp_kses_post( $html_message );
		}
	);
} else {
	require_once GOOGLE_PHOTOS_SYNC_PATH . 'functions.php';
	add_action( 'plugins_loaded', array( google_photos_sync_get_plugin_instance(), 'initialize' ) );
}
