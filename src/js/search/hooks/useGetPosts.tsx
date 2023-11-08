import { useEffect, useState } from 'react';
import {
	destructureData,
	destructureTaxonomy,
} from '../utilities/graphql-helpers/destructureData';
import { getTimeSortedEvents } from '../utilities/date-helpers';
import {
	EventData,
	wpgraphqlResponse,
} from '../utilities/graphql-helpers/types';
import { taxonomy } from '../utilities/types';

export function useGetPosts( query ) {
	const [ firstLoad, setFirstLoad ] = useState< EventData[] >( [] );
	const [ posts, setPosts ] = useState< EventData[] >( [] );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ hasNextPage, setHasNextPage ] = useState( false );
	const [ taxonomies, setTaxonomies ] = useState< taxonomy[] >( [] );

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
							choctawEventCategories: categories,
							choctawEventsVenues: venues,
						},
					} = data;
					setTaxonomies( () => {
						const cat = destructureTaxonomy( categories );
						const ven = destructureTaxonomy( venues );
						const category = {
							name: 'Category',
							values: cat,
							selected: '',
						} as taxonomy;
						const venue = {
							name: 'Venue',
							values: ven,
							selected: '',
						} as taxonomy;
						return [ category, venue ];
					} );
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
	}, [ hasNextPage, query ] );

	return { firstLoad, posts, isLoading, setPosts, taxonomies, setTaxonomies };
}
