// 3rd Party
import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Fuse from 'fuse.js';

// Components
import SearchBar from './Search';
import EventPreview from './EventPreview';

// hooks
import { useGetPosts } from './hooks/useGetPosts';

// Helpers & Utilities
import { initialQuery } from './utilities/graphql-helpers/initialQuery';
import { taxonomy } from './utilities/types';
import { EventData } from './utilities/graphql-helpers/types';

const root = document.getElementById( 'app' );
if ( root ) {
	createRoot( root ).render(
		<React.StrictMode>
			<App />
		</React.StrictMode>
	);
}

function App() {
	const [ query, setQuery ] = useState( initialQuery );
	const [ searchTerm, setSearchTerm ] = useState( '' );
	const { isLoading, firstLoad, posts, setPosts, taxonomies, setTaxonomies } =
		useGetPosts( query );
	const [ selectedTaxonomies, setSelectedTaxonomies ] = useState< string[] >(
		[]
	);

	function resetFilters() {
		setSelectedTaxonomies( [] );
		setPosts( firstLoad );
		setTaxonomies( ( prev ) => {
			return prev.map( ( tax ) => {
				const reset = { ...tax, selected: '' };
				return reset;
			} );
		} );
	}

	useEffect( () => {
		if ( '' === searchTerm ) {
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
		}, 350 );
		return () => clearTimeout( timeout );
	}, [ searchTerm, firstLoad, setPosts ] );

	useEffect( () => {
		taxonomies.forEach( ( taxonomy ) => {
			if ( taxonomy.selected !== '' ) {
				setSelectedTaxonomies( ( prev ) => {
					const selection = [ ...prev, taxonomy.selected ];
					return selection;
				} );
			}
		} );
	}, [ taxonomies ] );

	useEffect( () => {
		if ( selectedTaxonomies.length > 0 ) {
			const filteredPosts = posts.filter( ( post ) =>
				selectedTaxonomies.some(
					( tax ) => tax === post.category || tax === post.venue
				)
			);
			console.log( posts );
			console.log( selectedTaxonomies );
			console.log( filteredPosts );
			setPosts( filteredPosts );
		}
	}, [ selectedTaxonomies ] );

	return (
		<>
			<SearchBar
				searchTerm={ searchTerm }
				setSearchTerm={ setSearchTerm }
				taxonomies={ taxonomies }
				setTaxonomies={ setTaxonomies }
				resetFilters={ resetFilters }
			/>
			{ isLoading ? (
				<p>Loading...</p>
			) : (
				<section className="events-list__container">
					{ '' !== searchTerm && (
						<h2>You searched for "{ searchTerm }"</h2>
					) }
					<ol className="list-unstyled">
						{ '' === searchTerm &&
							selectedTaxonomies.length === 0 &&
							firstLoad.map( ( post ) => (
								<EventPreview event={ post } />
							) ) }
						{ selectedTaxonomies.length > 0 &&
							posts.map( ( post ) => (
								<EventPreview event={ post } />
							) ) }
						{ '' !== searchTerm &&
							posts.map( ( post ) => (
								<EventPreview event={ post.item } />
							) ) }
					</ol>
				</section>
			) }
		</>
	);
}
