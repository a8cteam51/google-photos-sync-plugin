<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync;

use WPCOMSpecialProjects\GooglePhotosSync\Admin\Setup;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class Plugin {
	// region FIELDS AND CONSTANTS

	/**
	 * The blocks component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     Blocks|null
	 */
	public ?Blocks $blocks = null;

	/**
	 * The admin component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     Setup|null
	 */
	public ?Setup $admin = null;

	/**
	 * The Cron component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     Cron|null
	 */
	public ?Cron $cron = null;

	/**
	 * The CLI component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     CLI|null
	 */
	public ?CLI $cli = null;

	/**
	 * The REST API component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     RestApi|null
	 */
	public ?RestApi $rest_api = null;

	// endregion

	// region MAGIC METHODS

	/**
	 * Plugin constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function __construct() {
		/* Empty on purpose. */
	}

	/**
	 * Prevent cloning.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	private function __clone() {
		/* Empty on purpose. */
	}

	/**
	 * Prevent unserializing.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function __wakeup() {
		/* Empty on purpose. */
	}

	// endregion

	// region METHODS

	/**
	 * Returns the singleton instance of the plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  Plugin
	 */
	public static function get_instance(): self {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}


	/**
	 * Initializes the plugin components.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		$this->blocks = new Blocks();
		$this->blocks->initialize();

		$this->admin = new Setup();
		$this->admin->initialize();

		$this->cron = new Cron();
		$this->cron->initialize();

		if ( \defined( 'WP_CLI' ) && WP_CLI ) {
			$this->cli = new CLI();
			$this->cli->initialize();
		}

		$this->rest_api = new RestApi();
		$this->rest_api->initialize();
	}

	// endregion

	// region HOOKS

	// endregion
}
