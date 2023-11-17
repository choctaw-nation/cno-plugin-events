export const initialQuery = encodeURIComponent( `query events {
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
	choctawEventCategories {
	  nodes {
		name
	  }
	}
	choctawEventsVenues {
	  nodes {
		name
	  }
	}
  }` );
