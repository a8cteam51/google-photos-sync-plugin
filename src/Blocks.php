<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of blocks.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Blocks {
	// region METHODS

	/**
	 * Initializes the blocks.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		\add_action( 'init', array( $this, 'register_blocks' ) );
		\add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	// endregion

	// region HOOKS

	/**
	 * Registers the blocks with Gutenberg.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function register_blocks(): void {
		\register_block_type( GOOGLE_PHOTOS_SYNC_PATH . 'blocks/build/photos-sync-album' );
	}

	/**
	 * Registers a plugin-level script for the block editor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function enqueue_block_editor_assets(): void {
		$plugin_slug = google_photos_sync_get_plugin_slug();

		$asset_meta = google_photos_sync_get_asset_meta( GOOGLE_PHOTOS_SYNC_PATH . 'assets/js/build/editor.js' );
		\wp_register_script(
			"$plugin_slug-editor",
			GOOGLE_PHOTOS_SYNC_URL . 'assets/js/build/editor.js',
			$asset_meta['dependencies'],
			$asset_meta['version'],
			false
		);
	}

	// endregion
}
