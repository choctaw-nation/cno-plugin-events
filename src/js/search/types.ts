interface Filter {
	link: string;
	name: string;
}
type EventInfo = {
	description: string;
	info: {
		day: string;
		endTime: string;
		startTime: string;
	};
};
export interface EventType extends Filter {
	event_typeId: number;
}

export interface EventLocation extends Filter {
	event_locationId: number;
}

export type EventFilter = EventType | EventLocation;
export type EventFilters = {
	type: {
		name: string;
		filters: EventType[];
	};
};

/** The Structure of an Event Post Type */
export type EventPost = {
	eventLocations: {
		nodes: EventLocation[];
	};
	event_info: EventInfo;
	featuredImage: {
		node: {
			altText: string;
			mediaDetails: {
				sizes: [
					{
						height: string;
						name: string;
						width: string;
						sourceUrl: string;
					}
				];
			};
			srcSet: string;
			sizes: string;
		};
	};
	title: string;
	link: string;
	eventId: number;
	eventTypes: {
		nodes: EventType[];
	};
};
/** The Formatted Event Post Type Data */
export type PrettyEventData = {
	locations: EventLocation[];
	type: EventType[];
	sizes: string;
	eventId: number;
	link: string;
	title: string;
	event_info: EventInfo;
	altText: string;
	srcSet: string;
	size: {
		height: string;
		name: string;
		width: string;
		sourceUrl: string;
	};
};
