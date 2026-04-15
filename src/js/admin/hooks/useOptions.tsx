import { useEffect, useState } from '@wordpress/element';
import type { Settings } from '../types';

type Notice = {
	message: string;
	type: 'success' | 'error';
};
export default function useOptions() {
	const [ settings, setSettings ] = useState< Settings | null >( null );
	const [ saving, setSaving ] = useState( false );
	const [ notice, setNotice ] = useState< Notice | null >( null );

	// Load settings on mount.
	useEffect( () => {
		if (
			typeof cnoEventsAdmin !== 'undefined' &&
			cnoEventsAdmin.settings
		) {
			setSettings( cnoEventsAdmin.settings );
			return;
		}

		fetch( cnoEventsAdmin.apiUrl, {
			headers: {
				'X-WP-Nonce': cnoEventsAdmin.apiNonce,
			},
		} )
			.then( ( r ) => r.json() )
			.then( ( data ) => setSettings( data ) )
			.catch( () => setSettings( null ) );
	}, [] );

	/**
	 * Save settings to the server.
	 */
	async function save() {
		setSaving( true );
		setNotice( null );
		try {
			const res = await fetch( cnoEventsAdmin.apiUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': cnoEventsAdmin.apiNonce,
				},
				body: JSON.stringify( settings ),
			} );
			if ( res.ok ) {
				const data = await res.json();
				setSettings( data );
				setNotice( { message: 'Settings saved.', type: 'success' } );
			} else {
				setNotice( {
					message: 'Failed to save settings.',
					type: 'error',
				} );
			}
		} catch ( error ) {
			setNotice( {
				message: `Failed to save settings! ${ error }`,
				type: 'error',
			} );
		}
		setSaving( false );
		setTimeout( () => setNotice( null ), 3000 );
	}
	return { settings, setSettings, saving, notice, save };
}
