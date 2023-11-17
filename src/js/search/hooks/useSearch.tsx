import { useEffect, useState } from 'react';
import Fuse from 'fuse.js';

// Types
import { EventData } from '../utilities/graphql-helpers/types';

/** Handles the search logic with Fuse.js */
export default function useSearch(
	setPosts: React.Dispatch< React.SetStateAction< EventData[] > >,
	firstLoad: EventData[]
) {
	const [ searchTerm, setSearchTerm ] = useState( '' );
	useEffect( () => {
		if ( '' === searchTerm ) {
			setPosts( firstLoad );
			return;
		}
		const timeout = setTimeout( () => {
			const fuse = new Fuse( firstLoad, {
				isCaseSensitive: false,
				minMatchCharLength: searchTerm.length,
				keys: [
					{ name: 'title', weight: 3 },
					'archiveContent',
					'eventDescription',
				],
			} );
			setPosts( fuse.search( searchTerm ) );
		}, 350 );
		return () => clearTimeout( timeout );
	}, [ searchTerm, firstLoad, setPosts ] );
	return [ searchTerm, setSearchTerm ];
}
