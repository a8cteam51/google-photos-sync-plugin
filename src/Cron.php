<?php
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

declare( strict_types = 1 );

namespace WPCOMSpecialProjects\GooglePhotosSync;

use Google\ApiCore\ValidationException;
use Google\Photos\Types\MediaItem;
use WPCOMSpecialProjects\GooglePhotosSync\Models\Client;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality related to the syncing of Google Photos.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Cron {

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
		\add_action( 'init', array( $this, 'schedule_album_sync' ) );
		\add_action( 'google_photos_sync_album', array( $this, 'sync_album' ) );
	}

	/**
	 * Schedules the album sync cron job.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function schedule_album_sync(): void {
		if ( ! \wp_next_scheduled( 'google_photos_sync_album' ) ) {
			\wp_schedule_event( \time(), 'daily', 'google_photos_sync_album' );
		}
	}

	/**
	 * Syncs the Google Photos album with the WordPress media library.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function sync_album(): void {
		$album_id = \get_option( 'google_photos_sync_album_created', null );

		if ( is_null( $album_id ) ) {
			error_log( 'No album ID set, skipping sync.' );
			return;
		}

		try {
			$client = new Client();
		} catch ( ValidationException $e ) {
			error_log( 'Error fetching album data. ' . $e->getMessage() );
			return;
		}

		$album_images     = $client->get_album_media( $album_id );
		$album_images_ids = array_map( fn ( MediaItem $image ) => $image->getId(), $album_images );

		$media_library_images = \get_posts(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_query'  => array(
					array(
						'key'     => 'google_photos_sync_id',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		$media_library_images_ids = array_map( fn ( \WP_Post $image ) => \get_post_meta( $image->ID, 'google_photos_sync_id', true ), $media_library_images );

		// Upload new album images to media library
		$new_images = array_diff( $album_images_ids, $media_library_images_ids );

		foreach ( $album_images as $image ) {
			if ( in_array( $image->getId(), $new_images, true ) ) {
				$this->upload_image_to_media_library( $image );
			}
		}

		// Delete images from media library that are not in the album anymore
		$deleted_images = array_diff( $media_library_images_ids, $album_images_ids );

		foreach ( $deleted_images as $image ) {
			$this->remove_image_from_media_library( $image );
		}
	}

	/**
	 * Deletes an attachment from the media library.
	 *
	 * @param integer $post_id The ID of the attachment to delete.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 *
	 * @global  \wpdb $wpdb
	 */
	public function delete_attachment( int $post_id ): void {
		global $wpdb;

		$google_photos_sync_id = \get_post_meta( $post_id, 'google_photos_sync_id', true );

		if ( ! $google_photos_sync_id ) {
			return;
		}

		try {
			$client = new Client();
		} catch ( ValidationException $e ) {
			error_log( 'Error fetching album data. ' . $e->getMessage() );
			return;
		}

		$client->client->batchRemoveMediaItemsFromAlbum( array( $google_photos_sync_id ), get_option( 'google_photos_sync_album_created' ) );
	}


	/**
	 * Uploads an image to the media library.
	 *
	 * @param MediaItem $image The image to upload.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	private function upload_image_to_media_library( MediaItem $image ): void {
		$image_id    = $image->getId();
		$image_url   = $image->getBaseUrl() . '=d';
		$image_title = $image->getFilename() . '.' . wp_get_default_extension_for_mime_type( $image->getMimeType() );

		$tmp = download_url( $image_url );
		if ( is_wp_error( $tmp ) ) {
			error_log( 'Error downloading image. $image_url: ' . $image_url . ' - $tmp: ' . $tmp );
			return;
		}

		// Emulate a $_FILES entry.
		$file_array = array(
			'name'     => $image_title,
			'tmp_name' => $tmp,
			'type'     => $image->getMimeType(),
		);

		$id = media_handle_sideload( $file_array );

		if ( is_wp_error( $id ) ) {
			wp_delete_file( $file_array['tmp_name'] );
			error_log( 'Error handling sideload. $id: ' . print_r( $id ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return;
		}

		// Set the google_photos_sync_id meta key
		\update_post_meta( $id, 'google_photos_sync_id', $image_id );
		wp_delete_file( $file_array['tmp_name'] );
	}

	/**
	 * Removes an image from the media library.
	 *
	 * @param string $id The ID of the image to remove.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	private function remove_image_from_media_library( string $id ): void {
		$media_library_images = \get_posts(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_query'  => array(
					array(
						'key'     => 'google_photos_sync_id',
						'value'   => $id,
						'compare' => '=',
					),
				),
			)
		);

		foreach ( $media_library_images as $media_library_image ) {
			\wp_delete_attachment( $media_library_image->ID, true );
		}
	}

	// endregion
}
