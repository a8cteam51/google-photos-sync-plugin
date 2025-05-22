<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of Admin pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Menu {
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
		\add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		\add_action( 'admin_menu', array( $this, 'add_api_settings_submenu_page' ) );
		\add_action( 'admin_menu', array( $this, 'add_authenticate_submenu_page' ) );
		\add_action( 'admin_menu', array( $this, 'add_create_album_submenu_page' ) );
	}

	// endregion

	// region HOOKS
	/**
	 * Adds the plugin's menu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function add_menu_page(): void {
		\add_menu_page(
			__( 'Google Photos Sync', 'google-photos-sync-plugin' ),
			__( 'Google Photos Sync', 'google-photos-sync-plugin' ),
			'manage_options',
			'google-photos-sync',
			array( $this, 'render_menu_page' ),
			'dashicons-format-gallery',
			99
		);
	}

	/**
	 * Adds the plugin's submenu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function add_api_settings_submenu_page(): void {
		\add_submenu_page(
			'google-photos-sync',
			__( 'API settings', 'google-photos-sync-plugin' ),
			__( 'API settings', 'google-photos-sync-plugin' ),
			'manage_options',
			'google-photos-sync-settings',
			array( $this, 'render_api_settings_submenu_page' )
		);
	}

	/**
	 * Adds the plugin's submenu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function add_authenticate_submenu_page(): void {
		\add_submenu_page(
			'google-photos-sync',
			__( 'Authorize app', 'google-photos-sync-plugin' ),
			__( 'Authorize app', 'google-photos-sync-plugin' ),
			'manage_options',
			'google-photos-sync-authenticate',
			array( $this, 'render_authenticate_submenu_page' )
		);
	}

	/**
	 * Adds the plugin's submenu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function add_create_album_submenu_page(): void {
		\add_submenu_page(
			'google-photos-sync',
			__( 'Create album', 'google-photos-sync-plugin' ),
			__( 'Create album', 'google-photos-sync-plugin' ),
			'manage_options',
			'google-photos-sync-create-album',
			array( $this, 'render_create_album_submenu_page' )
		);
	}

	/**
	 * Renders the menu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_menu_page(): void {
		require_once GOOGLE_PHOTOS_SYNC_PATH . 'templates/admin/album.php';
	}

	/**
	 * Renders the submenu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_api_settings_submenu_page(): void {
		require_once GOOGLE_PHOTOS_SYNC_PATH . 'templates/admin/api-settings.php';
	}

	/**
	 * Renders the submenu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_authenticate_submenu_page(): void {
		require_once GOOGLE_PHOTOS_SYNC_PATH . 'templates/admin/authenticate.php';
	}

	/**
	 * Renders the submenu page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_create_album_submenu_page(): void {
		require_once GOOGLE_PHOTOS_SYNC_PATH . 'templates/admin/create-album.php';
	}

	// endregion
}
