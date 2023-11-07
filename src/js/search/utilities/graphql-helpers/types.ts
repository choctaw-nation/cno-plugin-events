export type wpgraphqlResponse = {
	data: {
		choctawEvents: {
			edges: [
				{
					cursor: string;
					node: RawEventData;
				},
			];
			pageInfo: {
				hasNextPage: boolean;
				endCursor: string;
			};
		};
	};
};

export type RawEventData = {
	choctawEventCategories: {
		nodes: [
			{
				name: 'Cultural' | 'Entertainment';
			},
		];
	};
	choctawEventsVenues: {
		nodes: [
			{
				name: string;
			},
		];
	};
	choctawEventsArchiveContent: {
		archiveContent: string | null;
	};
	title: string;
	slug: string;
	featuredImage: null | {
		node: {
			srcSet: string;
			sourceUrl: string;
			altText: string;
		};
	};
	choctawEventsDetails: {
		eventDetails: {
			eventDescription: string;
			eventWebsite: string;
			timeAndDate: {
				startDate: string;
				startTime: string | null;
				endDate: string | null;
				endTime: string | null;
				isAllDay: boolean | null;
			};
		};
	};
};

export type EventData = {
	category: 'Cultural' | 'Entertainment';
	venue: string;
	archiveContent: string | null;
	title: string;
	slug: string;
	featuredImage?: {
		srcSet: string;
		sourceUrl: string;
		altText: string;
	};
	eventDescription: string;
	eventWebsite: string;
	timeAndDate: {
		startDate: string;
		startTime: string | null;
		endDate: string | null;
		endTime: string | null;
		isAllDay: boolean | null;
	};
};
