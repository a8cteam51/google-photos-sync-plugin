<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Admin;

use Error;
use Google\ApiCore\ValidationException;
use GuzzleHttp\Exception\ClientException;
use WPCOMSpecialProjects\GooglePhotosSync\Models\Client;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of Admin pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Album {
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
		\add_action( 'admin_init', array( $this, 'register_album_section' ) );
	}

	// endregion

	// region HOOKS

	/**
	 * Register the album section.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function register_album_section(): void {
		\add_settings_section(
			'google_photos_sync_album_info',
			__( 'Google Photos Album', 'google-photos-sync-plugin' ),
			array( $this, 'render_album_info' ),
			'google_photos_sync_album'
		);
	}

	/**
	 * Renders the album information section.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_album_info(): void {
		if ( ! Client::is_configured() ) {
			echo '<p>';
			printf(
				/* translators: %s: URL to the settings page */
				esc_html__( 'Please configure the %s settings first.', 'google-photos-sync-plugin' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=google-photos-sync-settings' ) ) . '">' . esc_html__( 'Google Photos Sync', 'google-photos-sync-plugin' ) . '</a>'
			);
			echo '</p>';
			return;
		}

		if ( ! Client::is_authenticated() ) {
			echo '<p>';
			printf(
				/* translators: %s: URL to the authentication page */
				esc_html__( 'Please %s with Google first.', 'google-photos-sync-plugin' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=google-photos-sync-authenticate' ) ) . '">' . esc_html__( 'authenticate', 'google-photos-sync-plugin' ) . '</a>'
			);
			echo '</p>';
			return;
		}

		$album_id = get_option( 'google_photos_sync_album_created' );

		try {
			$client = new Client();
			$album  = null;

			if ( $album_id ) {
				$album = $client->get_album( $album_id );
			}

			if ( ! $album ) {
				// Album doesn't exist anymore or was never created
				delete_option( 'google_photos_sync_album_created' );

				echo '<p>' . esc_html__( 'No album found. Please create a new one. ', 'google-photos-sync-plugin' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=google-photos-sync-create-album' ) ) . '">' . esc_html__( 'Create Album', 'google-photos-sync-plugin' ) . '</a>.</p>';
				return;
			}

			// Album exists, show its details
			echo '<table class="form-table">';
			echo '<tr>';
			echo '<th>' . esc_html__( 'Album Name', 'google-photos-sync-plugin' ) . '</th>';
			echo '<td>' . esc_html( $album->getTitle() ) . '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>' . esc_html__( 'Google Photos URL', 'google-photos-sync-plugin' ) . '</th>';
			echo '<td><a href="' . esc_url( $album->getProductUrl() ) . '" target="_blank">' . esc_html__( 'View in Google Photos', 'google-photos-sync-plugin' ) . '</a></td>';
			echo '</tr>';
			echo '</table>';
		} catch ( ValidationException $e ) {
			echo '<p>' . esc_html__( 'Error retrieving album information.', 'google-photos-sync-plugin' ) . '</p>';
			error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	// endregion
}
