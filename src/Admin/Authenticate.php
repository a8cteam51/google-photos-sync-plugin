<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Admin;

use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Auth\OAuth2;
use WPCOMSpecialProjects\GooglePhotosSync\Models\Client;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of Admin pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Authenticate {
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
		\add_action( 'admin_init', array( $this, 'google_authenticate' ) );
	}

	// endregion

	// region HOOKS

	/**
	 * Authenticate with Google Photos API.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function google_authenticate(): void {
		\register_setting(
			'google_photos_sync_authorization',
			'google_photos_sync_authorization',
			array( 'sanitize_callback' => array( $this, 'validate_code' ) )
		);

		\add_settings_section(
			'google_photos_sync_google_code',
			__( 'Authenticate with Google', 'google-photos-sync-plugin' ),
			array( $this, 'render_authenticate_with_google' ),
			'google_photos_sync_authorization'
		);
	}

	/**
	 * Renders the authentication button.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_authenticate_with_google(): void {
		$options = \get_option( 'google_photos_sync_api' );

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

		$oauth2 = new OAuth2(
			array(
				'clientId'           => $options['client_id'],
				'clientSecret'       => $options['client_secret'],
				'authorizationUri'   => 'https://accounts.google.com/o/oauth2/v2/auth',
				'redirectUri'        => admin_url( 'admin.php?page=google-photos-sync-authenticate' ),
				'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
				'scope'              => Client::SCOPES,
			)
		);

		$auth_url = $oauth2->buildFullAuthorizationUri(
			array(
				'access_type' => 'offline',
				'prompt'      => 'consent',
			)
		);

		if ( isset( $_GET['code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_option( 'google_photos_sync_auth_code', sanitize_text_field( $_GET['code'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

			$oauth2->setCode( $_GET['code'] ); // phpcs:ignore WordPress.Security.NonceVerification
			$auth_token    = $oauth2->fetchAuthToken();
			$refresh_token = $oauth2->getRefreshToken();

			update_option( 'google_photos_sync_auth_token', $auth_token );

			$credentials = new UserRefreshCredentials(
				Client::SCOPES,
				array(
					'client_id'     => $options['client_id'],
					'client_secret' => $options['client_secret'],
					'refresh_token' => $refresh_token,
				)
			);

			update_option( 'google_photos_sync_credentials', $credentials );

			echo '<p>' . \esc_html__( 'Successfully authenticated with Google Photos API.', 'google-photos-sync-plugin' ) . '</p>';
		} else {
			echo '<p>' . \esc_html__( 'Click the button below to authenticate with Google Photos API.', 'google-photos-sync-plugin' ) . '</p>';
			echo '<a class="login button-primary" href="' . \esc_url( $auth_url ) . '" target="_blank">' . \esc_html__( 'Connect My Google Account', 'google-photos-sync-plugin' ) . '</a>';
		}
	}

	// endregion
}
