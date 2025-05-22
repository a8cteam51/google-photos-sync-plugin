import { createHooks } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';

window.google_photos_sync = window.google_photos_sync || {};
window.google_photos_sync.hooks = createHooks();

domReady( () => {
	window.google_photos_sync.hooks.doAction( 'editor.ready' );
} );
