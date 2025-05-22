<?php declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync\Models;

use Google\ApiCore\ValidationException;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\ApiCore\ApiException;
use Google\Photos\Types\Album;
use Google\Photos\Types\MediaItem;
use GuzzleHttp\Exception\ClientException;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality related to the Google Photos API.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Client {

	const SCOPES = 'https://www.googleapis.com/auth/photoslibrary.appendonly https://www.googleapis.com/auth/photoslibrary.readonly.appcreateddata';

	/**
	 * The client component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     PhotosLibraryClient|null
	 */
	public ?PhotosLibraryClient $client = null;

	/**
	 * Returns the Google Photos Library client using the stored credentials.
	 *
	 * @throws ValidationException If the credentials are invalid.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function __construct() {
		/**
		 * Credentials stored after authentication.
		 *
		 * @var \Google\Auth\Credentials\UserRefreshCredentials $credentials
		 */
		$credentials  = \get_option( 'google_photos_sync_credentials' );
		$this->client = new PhotosLibraryClient( array( 'credentials' => $credentials ) );
	}

	/**
	 * Retrieves an album from the Google Photos Library.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param string $id The album ID.
	 *
	 * @return Album|null
	 */
	public function get_album( string $id ): ?Album {
		$album = null;

		try {
			$album = $this->client->getAlbum( $id );
		} catch ( ApiException | ClientException $e ) {
			error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		} finally {
			$this->client->close();
		}

		return $album;
	}


	/**
	 * Retrieves the albums from the Google Photos Library.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return array|null
	 */
	public function get_albums(): ?array {
		$albums = array();

		try {
			$paged_response = $this->client->listAlbums();
			foreach ( $paged_response->iterateAllElements() as $element ) {
				$albums[] = $element;
			}
		} catch ( ApiException | ClientException | ValidationException $e ) {
			$albums = null;
			error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		} finally {
			$this->client->close();
		}

		return $albums;
	}


	/**
	 * Retrieves the albums from the Google Photos Library.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param string $id The album ID.
	 *
	 * @return MediaItem[]|null
	 */
	public function get_album_media( string $id ): ?array {
		$items = array();

		try {
			$paged_response = $this->client->searchMediaItems( array( 'albumId' => $id ) );

			foreach ( $paged_response->iterateAllElements() as $element ) {
				$items[] = $element;
			}
		} catch ( ApiException | ClientException | ValidationException $e ) {
			$items = null;
			error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		} finally {
			$this->client->close();
		}

		return $items;
	}

	/**
	 * Creates an album in the Google Photos Library.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param Album $album The album to create.
	 *
	 * @return string|null The album ID.
	 */
	public function create_album( Album $album ): ?string {
		try {
			$created_album = $this->client->createAlbum( $album );
			$album_id      = $created_album->getId();
		} catch ( ApiException | ClientException $e ) {
			$album_id = null;
			error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		} finally {
			$this->client->close();
		}

		return $album_id;
	}

	/**
	 * Checks if the user provided the necessary credentials.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return boolean
	 */
	public static function is_configured(): bool {
		$options = \array_filter( \get_option( 'google_photos_sync_api', array() ) );

		return isset( $options['client_id'], $options['client_secret'] );
	}

	/**
	 * Checks if the user is authenticated with Google Photos.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return boolean
	 */
	public static function is_authenticated(): bool {
		$credentials = \get_option( 'google_photos_sync_credentials' );

		return self::is_configured() && ! empty( $credentials );
	}

	// endregion
}
