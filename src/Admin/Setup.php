<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Admin;

use WPCOMSpecialProjects\GooglePhotosSync\Admin\ApiSettings;
use WPCOMSpecialProjects\GooglePhotosSync\Admin\Menu;
use WPCOMSpecialProjects\GooglePhotosSync\Admin\Authenticate;
use WPCOMSpecialProjects\GooglePhotosSync\Admin\Album;
use WPCOMSpecialProjects\GooglePhotosSync\Admin\CreateAlbum;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of Admin pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Setup {
	// region METHODS

	/**
	 * Initializes the admin pages.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		( new Menu() )->initialize();
		( new ApiSettings() )->initialize();
		( new Authenticate() )->initialize();
		( new Album() )->initialize();
		( new CreateAlbum() )->initialize();
	}

	/**
	 * Returns true if the app is authorized.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  boolean
	 */
	public static function is_authorized(): bool {
		return false !== get_option( 'google_photos_sync_credentials', false );
	}

	// endregion
}
