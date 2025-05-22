<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Admin;

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Photos\Types\Album;
use Google\Photos\Types\SharedAlbumOptions;
use WPCOMSpecialProjects\GooglePhotosSync\Models\Client;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of Admin pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class CreateAlbum {
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
		\add_action( 'admin_init', array( $this, 'create_album' ) );
	}

	// endregion

	// region HOOKS

		/**
	 * Select the album to sync.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function create_album(): void {
		\register_setting(
			'google_photos_sync_create_album',
			'google_photos_sync_create_album',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);

		if ( Client::is_configured() && Client::is_authenticated() ) {
			\add_settings_field(
				'google_photos_sync_album_created',
				__( 'Create album', 'google-photos-sync-plugin' ),
				array( $this, 'render_album_create_field' ),
				'google_photos_sync_create_album',
				'google_photos_sync_create_new_album',
				array(
					'label_for' => 'google_photos_sync_album_created',
				)
			);
		}

		\add_settings_section(
			'google_photos_sync_create_new_album',
			__( 'Select Album to Create', 'google-photos-sync-plugin' ),
			array( $this, 'render_create_album' ),
			'google_photos_sync_create_album'
		);
	}

	/**
	 * Renders the album ID field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_album_create_field(): void {
		if ( isset( $_GET['settings-updated'] ) && is_null( get_option( 'google_photos_sync_album_created', null ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			try {
				$client = new Client();
			} catch ( ValidationException $e ) {
				echo '<p>' . esc_html__( 'Error creating album. Please try again.', 'google-photos-sync-plugin' ) . '</p>';
				error_log( 'Error fetching album data. ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				return;
			}

			$album = new Album();
			$album->setTitle( 'Google Photos Sync' );

			$album_id = $client->create_album( $album );

			if ( ! empty( $album_id ) ) {
				\update_option( 'google_photos_sync_album_created', $album_id );
			}

			$options = new SharedAlbumOptions();
			$options->setIsCollaborative( true );
			$options->setIsCommentable( true );

			try {
				$client->client->shareAlbum( $album_id, array( 'sharedAlbumOptions' => $options ) );
			} catch ( ApiException $e ) {
				error_log( 'Error sharing album: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}

		if ( ! is_null( get_option( 'google_photos_sync_album_created', null ) ) ) {
			echo '<p>' . esc_html__( 'Album created successfully.', 'google-photos-sync-plugin' ) . '</p>';
			return;
		}

		echo '<input type="submit" name="submit" id="create-album" class="button" value="' . esc_html__( 'Create', 'google-photos-sync-plugin' ) . '">';
	}

	/**
	 * Renders the select album section.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_create_album(): void {
		if ( ! Client::is_configured() ) {
			echo '<p>';
			printf(
				/* translators: %s: URL to the settings page */
				\esc_html__( 'Please configure the %s settings first.', 'google-photos-sync-plugin' ),
				'<a href="' . \esc_url( admin_url( 'admin.php?page=google-photos-sync-settings' ) ) . '">' . \esc_html__( 'Google Photos Sync', 'google-photos-sync-plugin' ) . '</a>'
			);
			echo '</p>';

			return;
		}

		if ( ! Client::is_authenticated() ) {
			echo '<p>';
			printf(
				/* translators: %s: URL to the authentication page */
				\esc_html__( 'Please %s with Google first.', 'google-photos-sync-plugin' ),
				'<a href="' . \esc_url( admin_url( 'admin.php?page=google-photos-sync-authenticate' ) ) . '">' . \esc_html__( 'authenticate', 'google-photos-sync-plugin' ) . '</a>'
			);
			echo '</p>';

			return;
		}

		echo '<p>' . \esc_html__( 'Please create an album to upload images to.', 'google-photos-sync-plugin' ) . '</p>';
	}

	// endregion
}
