<div class="wrap">
	<?php settings_errors(); ?>
	<h1><?php esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
			settings_fields( 'google_photos_sync_create_album' );

			do_settings_sections( 'google_photos_sync_create_album' );
		?>
	</form>
</div>
