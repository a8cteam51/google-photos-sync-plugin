<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync;

use WP_CLI_Command;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality related to CLI commands.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class CLI extends WP_CLI_Command {

	// region METHODS

	/**
	 * Initializes the cron jobs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		\WP_CLI::add_command( 'google-photos-sync', array( $this, 'sync' ) );
	}

	/**
	 * Syncs the Google Photos albums with the WordPress media library.
	 *
	 * ## EXAMPLES
	 *
	 *     wp google-photos-sync
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function sync(): void {
		( new Cron() )->sync_album();
	}

	// endregion
}
