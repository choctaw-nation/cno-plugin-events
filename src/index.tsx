import App from './js/admin/App';
import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

domReady( () => {
	const root = document.getElementById( 'cno-plugin-events-admin-root' );
	if ( root ) {
		createRoot( root ).render( <App /> );
	}
} );
