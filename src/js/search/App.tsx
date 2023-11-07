import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

import {
	EventData,
	wpgraphqlResponse,
} from './utilities/graphql-helpers/types';
import destructureData from './utilities/graphql-helpers/destructureData';

import SearchBar from './Search';
import EventPreview from './EventPreview';

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
	const [ posts, setPosts ] = useState< EventData[] >( [] );
	const [ isLoading, setIsLoading ] = useState( true );

	// First load
	useEffect( () => {
		setIsLoading( true );
		const controller = new AbortController();
		( async function () {
			try {
				const query = encodeURIComponent( `query events {
					choctawEvents {
					  edges {
						cursor
						node {
						  choctawEventCategories {
							nodes {
							  name
							}
						  }
						  choctawEventsArchiveContent {
							archiveContent
						  }
						  title(format: RENDERED)
						  slug
						  featuredImage {
							node {
							  srcSet(size: CHOCTAW_EVENTS_PREVIEW)
							  sourceUrl(size: CHOCTAW_EVENTS_PREVIEW)
							  altText
							}
						  }
						  choctawEventsVenues {
							nodes {
							  name
							}
						  }
						  choctawEventsDetails {
							eventDetails {
							  eventDescription
							  eventWebsite
							  timeAndDate {
								endDate
								endTime
								isAllDay
								startDate
								startTime
							  }
							}
						  }
						}
					  }
					  pageInfo {
						hasNextPage
						endCursor
					  }
					}
				  }` );
				const response = await fetch(
					`${ cnoEventSearchData.rootUrl }/graphql?query=${ query }`,
					{ signal: controller.signal }
				);
				if ( ! response.ok ) {
					throw new Error( `Couldn't get a response from graphql!` );
				}
				const data: wpgraphqlResponse = await response.json();
				const {
					data: {
						choctawEvents: { edges, pageInfo },
					},
				} = data;
				const events = destructureData( edges );
				setPosts( events );
			} catch ( err ) {
				console.error( err );
			} finally {
				setIsLoading( false );
			}
		} )();
		return () => controller.abort();
	}, [] );

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
					<ol className="list-unstyled">
						{ posts.map( ( post ) => (
							<EventPreview event={ post } />
						) ) }
					</ol>
				</section>
			) }
		</>
	);
}
