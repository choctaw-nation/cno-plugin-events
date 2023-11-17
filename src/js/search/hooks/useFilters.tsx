import { useState, useEffect } from 'react';

// Types
import { selectedTaxonomy, taxonomy } from '../utilities/types';
import { EventData } from '../utilities/graphql-helpers/types';

/** Handles the Fitlers logic */
export default function useFilters( {
	setPosts,
	setTaxonomies,
	firstLoad,
	taxonomies,
}: {
	setPosts: React.Dispatch< React.SetStateAction< EventData[] > >;
	setTaxonomies: React.Dispatch< React.SetStateAction< taxonomy[] > >;
	taxonomies: taxonomy[];
	firstLoad: EventData[];
} ) {
	const [ selectedTaxonomies, setSelectedTaxonomies ] = useState<
		selectedTaxonomy[]
	>( [] );

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

	// Update Selected Taxonomies
	useEffect( () => {
		taxonomies.forEach( ( taxonomy ) => {
			if ( taxonomy.selected !== '' ) {
				setSelectedTaxonomies( ( prev ) => {
					const selected = {
						name: taxonomy.name,
						selected: taxonomy.selected,
					};
					const replace = prev.findIndex(
						( tax ) => tax.name === selected.name
					);
					if ( replace !== -1 ) {
						prev[ replace ] = selected;
						return [ ...prev ];
					} else {
						return [ ...prev, selected ];
					}
				} );
			}
		} );
	}, [ taxonomies ] );

	useEffect( () => {
		if ( selectedTaxonomies.length > 0 ) {
			// Group selected taxonomies by their names
			const selectedTaxonomiesByName = selectedTaxonomies.reduce(
				( grouped, tax ) => {
					grouped[ tax.name ] = tax.selected;
					return grouped;
				},
				{}
			);
			// If only one taxonomy is selected, filter posts by that taxonomy
			if ( Object.keys( selectedTaxonomiesByName ).length === 1 ) {
				const filteredPosts = firstLoad.filter(
					( post ) =>
						post.category ===
							selectedTaxonomiesByName[ 'Category' ] ||
						post.venue === selectedTaxonomiesByName[ 'Venue' ]
				);
				setPosts( filteredPosts );
			} else if ( Object.keys( selectedTaxonomiesByName ).length >= 2 ) {
				const filteredPosts = firstLoad.filter(
					( post ) =>
						post.category ===
							selectedTaxonomiesByName[ 'Category' ] &&
						post.venue === selectedTaxonomiesByName[ 'Venue' ]
				);
				setPosts( filteredPosts );
			}
		}
	}, [ selectedTaxonomies, firstLoad, setPosts ] );
	return { resetFilters, selectedTaxonomies, setSelectedTaxonomies };
}
