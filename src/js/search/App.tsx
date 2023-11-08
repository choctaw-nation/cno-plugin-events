// 3rd Party
import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Fuse from 'fuse.js';

// Components
import SearchBar from './Search';
import EventPreview from './EventPreview';

// Helpers & Utilities
import {
	EventData,
	wpgraphqlResponse,
} from './utilities/graphql-helpers/types';
import destructureData from './utilities/graphql-helpers/destructureData';
import { query } from './utilities/graphql-helpers/initialQuery';
import { getTimeSortedEvents } from './utilities/date-helpers';

const root = document.getElementById( 'app' );
if ( root ) {
	createRoot( root ).render(
		<React.StrictMode>
			<App />
		</React.StrictMode>
	);
}

function App() {
	const [ searchTerm, setSearchTerm ] = useState( '' );
	const [ firstLoad, setFirstLoad ] = useState< EventData[] >( [] );
	const [ posts, setPosts ] = useState< EventData[] >( [] );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ hasNextPage, setHasNextPage ] = useState( false );

	// First load
	useEffect( () => {
		setIsLoading( true );
		const controller = new AbortController();
		do {
			( async function () {
				try {
					const response = await fetch(
						`${ cnoEventSearchData.rootUrl }/graphql?query=${ query }`,
						{ signal: controller.signal }
					);
					if ( ! response.ok ) {
						throw new Error(
							`Couldn't get a response from graphql!`
						);
					}
					const data: wpgraphqlResponse = await response.json();
					const {
						data: {
							choctawEvents: { edges, pageInfo },
						},
					} = data;
					setHasNextPage( pageInfo.hasNextPage );
					const events = destructureData( edges );
					const sortedEvents = getTimeSortedEvents( events );
					setFirstLoad( sortedEvents );
					setPosts( events );
				} catch ( err ) {
					console.error( err );
				} finally {
					setIsLoading( false );
				}
			} )();
		} while ( hasNextPage );
		return () => controller.abort();
	}, [ hasNextPage ] );

	// Handle Search
	useEffect( () => {
		setIsLoading( true );
		if ( '' === searchTerm ) {
			setIsLoading( false );
			return;
		}
		const timeout = setTimeout( () => {
			const fuse = new Fuse( firstLoad, {
				isCaseSensitive: false,
				minMatchCharLength: searchTerm.length,
				// includeScore: true,
				// includeMatches: true,
				keys: [
					{ name: 'title', weight: 3 },
					'archiveContent',
					'eventDescription',
				],
			} );
			setPosts( fuse.search( searchTerm ) );
			setIsLoading( false );
		}, 350 );
		return () => clearTimeout( timeout );
	}, [ searchTerm, firstLoad ] );

	return (
		<>
			<SearchBar
				searchTerm={ searchTerm }
				setSearchTerm={ setSearchTerm }
			/>
			{ isLoading ? (
				<p>Loading...</p>
			) : (
				<section className="events-list__container">
					{ '' !== searchTerm && (
						<h2>You searched for "{ searchTerm }"</h2>
					) }
					<ol className="list-unstyled">
						{ '' === searchTerm
							? firstLoad.map( ( post ) => (
									<EventPreview event={ post } />
							  ) )
							: posts.map( ( post ) => (
									<EventPreview event={ post.item } />
							  ) ) }
					</ol>
				</section>
			) }
		</>
	);
}
