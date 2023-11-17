// 3rd Party
import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';

// Components
import SearchBar from './Components/SearchBar';
import EventPreview from './Components/EventPreview';

// hooks
import { useGetPosts } from './hooks/useGetPosts';

// Helpers & Utilities
import { initialQuery } from './utilities/graphql-helpers/initialQuery';

import useSearch from './hooks/useSearch';
import useFilters from './hooks/useFilters';

const root = document.getElementById( 'app' );
if ( root ) {
	createRoot( root ).render( <App /> );
}

function App() {
	const [ query ] = useState( initialQuery );
	const { isLoading, firstLoad, posts, setPosts, taxonomies, setTaxonomies } =
		useGetPosts( query );
	const [ searchTerm, setSearchTerm ] = useSearch( setPosts, firstLoad );
	const { resetFilters, selectedTaxonomies } = useFilters( {
		setPosts,
		setTaxonomies,
		firstLoad,
		taxonomies,
	} );
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
