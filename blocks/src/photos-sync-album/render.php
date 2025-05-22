<?php
/**
 * Renders the block `google-photos-sync/album` on the frontend.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

use WPCOMSpecialProjects\GooglePhotosSync\Models\Client;
use Google\ApiCore\ValidationException;

$google_photos_sync_album_id = \get_option( 'google_photos_sync_album_created', null );
$google_photos_sync_response = null;
$google_photos_sync_columns  = $attributes['columns'] ?? 3;

if ( ! is_null( $google_photos_sync_album_id ) ) {
	try {
		$google_photos_sync_client   = new Client();
		$google_photos_sync_response = $google_photos_sync_client->get_album_media( $google_photos_sync_album_id );
	} catch ( ValidationException $e ) {
		error_log( $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
?>

<?php if ( is_null( $google_photos_sync_album_id ) ) : ?>
	<p><?php \esc_html_e( 'Please create an album first.', 'google-photos-sync-plugin' ); ?></p>

	<?php if ( \is_user_logged_in() && \current_user_can( 'manage_options' ) ) : ?>
		<a href="<?php echo \esc_url( \admin_url( 'admin.php?page=google-photos-sync-create-album' ) ); ?>"><?php \esc_html_e( 'Create Album', 'google-photos-sync-plugin' ); ?>.</a>
	<?php endif; ?>
<?php elseif ( empty( $google_photos_sync_response ) ) : ?>
	<p><?php \esc_html_e( 'The selected album is empty.', 'google-photos-sync-plugin' ); ?></p>
<?php else : ?>
	<figure <?php echo \wp_kses_data( \get_block_wrapper_attributes( array( 'class' => "google-photos-sync-columns-{$google_photos_sync_columns}" ) ) ); ?>>
		<?php foreach ( $google_photos_sync_response as $google_photos_sync_item ) : ?>
			<figure class="google-photos-sync-gallery-img">
				<img src="<?php echo \esc_url( $google_photos_sync_item->getBaseUrl() ); ?>" alt="<?php echo \esc_attr( $google_photos_sync_item->getFilename() ); ?>" />
			</figure>
		<?php endforeach; ?>
	</figure>
<?php endif; ?>
