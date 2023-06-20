import { EventPost, PrettyEventData } from './types';

const graphQL = cnoSiteData.rootUrl;
async function makeRequest( request ) {
	try {
		const response = await fetch( `${ graphQL }`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( request ),
		} );
		const { data } = await response.json();
		return data;
	} catch ( error ) {
		console.error( 'makeRequest error:', error );
		throw error;
	}
}
export async function getPosts() {
	const variables = {
		first: Number( POSTS_PER_PAGE ) ?? 4,
		after: '',
		include: [ 'LARGE' ],
		size: 'LARGE',
	};
	const query = `query Events($first: Int = 4, $after: String = "", $include: [MediaItemSizeEnum] = [LARGE], $size: MediaItemSizeEnum = LARGE) {
  events(after: $after, first: $first) {
    pageInfo {
      hasNextPage
      endCursor
    }
    nodes {
      eventLocations {
        nodes {
          name
          link
          event_locationId
        }
      }
      event_info {
        description
        info {
          day
          endTime
          startTime
        }
      }
      featuredImage {
        node {
          altText
          mediaDetails {
            sizes(include: $include) {
              height
              name
              width
              sourceUrl
            }
          }
          srcSet(size: $size)
		  sizes(size: $size)
        }
      }
      title(format: RENDERED)
      link
	  eventId
      eventTypes {
        nodes {
          event_typeId
          name
          link
        }
      }
    }
  }
  eventTypes {
    nodes {
      event_typeId
      name
	  link
    }
  }
  eventLocations {
    nodes {
      name
      link
      event_locationId
    }
  }
}`;

	const request = {
		query: query,
		variables: variables,
	};
	try {
		const data = await makeRequest( request );
		return data;
	} catch ( err ) {
		console.error( err );
	}
}
export function destructureData( data: EventPost ): PrettyEventData {
	const {
		eventLocations: { nodes: locations },
	} = data;
	const {
		eventTypes: { nodes: type },
	} = data;
	const { eventId, link, title } = data;
	const { event_info } = data;
	const {
		featuredImage: {
			node: { altText, srcSet, mediaDetails, sizes },
		},
	} = data;
	const size = mediaDetails.sizes[ 0 ];
	const destructuredData = {
		locations,
		type,
		sizes,
		eventId,
		link,
		title,
		event_info,
		altText,
		srcSet,
		size,
	};
	return destructuredData;
}
