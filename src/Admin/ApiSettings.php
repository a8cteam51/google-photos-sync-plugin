<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of Admin pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class ApiSettings {
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
		\add_action( 'admin_init', array( $this, 'register_api_settings' ) );
	}

	// endregion

	// region HOOKS

	/**
	 * Registers the plugin's settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function register_api_settings(): void {
		\register_setting(
			'google_photos_sync_api',
			'google_photos_sync_api',
			array( 'sanitize_callback' => array( $this, 'validate_api_options' ) )
		);

		\add_settings_section(
			'google_photos_sync_credentials',
			__( 'Client ID and Client Secret', 'google-photos-sync-plugin' ),
			array( $this, 'render_settings_section' ),
			'google_photos_sync_api'
		);

		\add_settings_field(
			'google_photos_sync_api_client_id',
			__( 'Client ID', 'google-photos-sync-plugin' ),
			array( $this, 'render_client_id_field' ),
			'google_photos_sync_api',
			'google_photos_sync_credentials',
			array(
				'label_for' => 'google_photos_sync_api_client_id',
			)
		);

		\add_settings_field(
			'google_photos_sync_api_client_secret',
			__( 'Client Secret', 'google-photos-sync-plugin' ),
			array( $this, 'render_client_secret_field' ),
			'google_photos_sync_api',
			'google_photos_sync_credentials',
			array(
				'label_for' => 'google_photos_sync_api_client_secret',
			)
		);
	}

	/**
	 * Renders the settings section.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_settings_section(): void {
		echo '<p>' . \esc_html__( 'Please enter your Google Photos API credentials.', 'google-photos-sync-plugin' ) . '</p>';
	}

	/**
	 * Renders the client ID field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_client_id_field(): void {
		$options   = \get_option( 'google_photos_sync_api' );
		$client_id = $options['client_id'] ?? '';

		echo '<input id="google_photos_sync_api_client_id" type="text" class="regular-text" name="google_photos_sync_api[client_id]" value="' . \esc_attr( $client_id ) . '" />';
	}

	/**
	 * Render the client secret field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function render_client_secret_field(): void {
		$options       = \get_option( 'google_photos_sync_api' );
		$client_secret = $options['client_secret'] ?? '';

		echo '<input id="google_photos_sync_api_client_secret" type="text" class="regular-text" name="google_photos_sync_api[client_secret]" value="' . \esc_attr( $client_secret ) . '" />';
	}

	/**
	 * Validates the API options.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array $input The input options.
	 *
	 * @return  array The validated options.
	 */
	public function validate_api_options( array $input ): array {
		return \array_map( 'sanitize_text_field', $input );
	}

	// endregion
}
