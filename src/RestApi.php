<?php // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync;

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use WP_REST_Server;
use Google\Photos\Library\V1\PhotosLibraryResourceFactory;
use Google\Rpc\Code;
use GuzzleHttp\Exception\GuzzleException;
use WP_REST_Response;
use WPCOMSpecialProjects\GooglePhotosSync\Models\Client;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality related to the Google Photos API.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class RestApi {

	const NAMESPACE = 'google-photos-sync/v1';

	/**
	 * Initializes the REST API.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function initialize(): void {
		\add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the REST API routes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		\register_rest_route(
			self::NAMESPACE,
			'/upload',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'upload' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

	/**
	 * Uploads a file to Google Photos.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function upload( \WP_REST_Request $request ): \WP_REST_Response {
		$files         = $request->get_file_params();
		$upload_tokens = array();

		try {
			$client = new Client();

			$number_files = count( $files['file']['name'] );

			for ( $i = 0; $i < $number_files; $i++ ) {
				$upload_tokens[] = $client->client->upload(
					file_get_contents( $files['file']['tmp_name'][ $i ] ), // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					null,
					$files['file']['type'][ $i ]
				);
			}
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
			return rest_ensure_response( array( 'error' => $e->getMessage() ) );
		}

		if ( empty( $upload_tokens ) ) {
			error_log( __( 'No upload tokens found.', 'google-photos-sync-plugin' ) );
			return rest_ensure_response( array( 'error' => __( 'No upload tokens found.', 'google-photos-sync-plugin' ) ) );
		}

		try {
			$media_items = array();

			foreach ( $upload_tokens as $index => $token ) {
				$media_items[] = PhotosLibraryResourceFactory::newMediaItemWithFileName(
					$token,
					$files['file']['name'][ $index ]
				);
			}

			$response = $client->client->batchCreateMediaItems(
				$media_items,
				array( 'albumId' => \get_option( 'google_photos_sync_album_created' ) )
			);

			foreach ( $response->getNewMediaItemResults() as $item ) {
				$status = $item->getStatus();

				if ( $status->getCode() !== Code::OK ) {
					error_log( 'Error creating media item: ' . $status->getMessage() );
					return rest_ensure_response( array( 'error' => $status->getMessage() ) );
				}
			}
		} catch ( ApiException $e ) {
			error_log( 'Error uploading media items - Exception message: ' . $e->getMessage() . ' Media items: ' . print_r( $media_items, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return rest_ensure_response( array( 'error' => $e->getMessage() ) );
		}

		return rest_ensure_response( array( 'success' => true ) );
	}

	/**
	 * Checks if the current user has the necessary permissions to upload files.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return boolean
	 */
	public function permissions_check(): bool {
		return current_user_can( 'edit_posts' );
	}
}
