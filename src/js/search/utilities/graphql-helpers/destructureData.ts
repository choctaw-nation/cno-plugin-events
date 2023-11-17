import { EventData, choctawEventTaxonomy, wpgraphqlResponse } from './types';

export function destructureData(
	data: wpgraphqlResponse[ 'data' ][ 'choctawEvents' ][ 'edges' ]
): EventData[] {
	return data.map( ( { node } ) => {
		const {
			choctawEventCategories: { nodes: category },
			choctawEventsVenues: { nodes: venue },
			choctawEventsArchiveContent: { archiveContent },
			title,
			slug,
			choctawEventsDetails: {
				eventDetails: { eventDescription, eventWebsite, timeAndDate },
			},
		} = node;
		return {
			category: category[ 0 ]?.name,
			venue: venue[ 0 ]?.name,
			archiveContent,
			title,
			slug,
			featuredImage: node.featuredImage?.node,
			eventDescription,
			eventWebsite,
			timeAndDate,
		};
	} );
}

export function destructureTaxonomy( data: choctawEventTaxonomy ): string[] {
	return data.nodes.map( ( node ) => node.name );
}
