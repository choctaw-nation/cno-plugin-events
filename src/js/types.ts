/**
 * The Collection of relevant HTML Elements where the data needs to be extracted from
 */
export type EventElements = {
	name: HTMLElement;
	dateButton: HTMLButtonElement;
	eventDescription: NodeListOf< HTMLParagraphElement >;
	website: HTMLAnchorElement | null;
	venue?: VenueElements;
};

type VenueElements = {
	name: HTMLElement;
	address: HTMLElement;
	website: HTMLAnchorElement | null;
};

/** The Data structure for the Event */
export type EventData = {
	name: string;
	start: Date;
	end: Date;
	isAllDay: boolean;
	description: string;
	website?: string;
	venue: VenueData | null;
};

type VenueData = {
	name: string;
	address: string;
	website?: string;
};
