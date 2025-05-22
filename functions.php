<?php declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

use WPCOMSpecialProjects\GooglePhotosSync\Plugin;

// region

/**
 * Returns the plugin's main class instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  Plugin
 */
function google_photos_sync_get_plugin_instance(): Plugin {
	return Plugin::get_instance();
}

/**
 * Returns the plugin's slug.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function google_photos_sync_get_plugin_slug(): string {
	return sanitize_key( GOOGLE_PHOTOS_SYNC_METADATA['TextDomain'] );
}

// endregion

//region OTHERS

require GOOGLE_PHOTOS_SYNC_PATH . 'includes/assets.php';
require GOOGLE_PHOTOS_SYNC_PATH . 'includes/settings.php';

// endregion
