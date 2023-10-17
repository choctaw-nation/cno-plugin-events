import { EventData, EventElements } from './types';

/**
 * Gets the HTML Elements and provides sub-classes with concise EventData type and the button to attach the "click" listener handle to
 *
 * @property {EventData} event the data
 * @property {HTMLButtonElement} button the "Add to Calendar" button
 */
export default class EventConstructor {
	/** Default Duration: 60 minutes (min * sec * millisecond)
	 *
	 * @var number #EVENT_DURATION
	 */
	private EVENT_DURATION = 60 * 60 * 1000;

	/** The Event Data
	 * @var EventData
	 */
	protected event: EventData = {
		name: '',
		start: new Date(),
		end: new Date(),
		description: '',
		website: '',
		isAllDay: false,
		venue: null,
	};

	/**
	 * The "Add to Calendar" button
	 * @var {HTMLButtonElement}
	 */
	protected button: HTMLButtonElement;

	constructor() {
		try {
			const elements = this.getTheElements();
			this.setTheProperties( elements );
		} catch ( err ) {
			console.error( err );
		}
	}

	private getTheElements(): EventElements {
		const name = this.elSelector( 'event-name', 'Name' );
		const dateButton = this.elSelector(
			'add-to-calendar',
			'Add to calendar button'
		) as HTMLButtonElement;
		const eventDescription = document
			.getElementById( 'event-description' )
			?.querySelectorAll( 'p' );
		if ( ! eventDescription ) {
			throw new Error( 'Event Description not found!' );
		}
		const eventWebsite = document.getElementById(
			'event-website'
		) as HTMLAnchorElement | null;
		const venueName = this.elSelector( 'venue-name', 'Venue name' );
		const address = this.elSelector( 'venue-address', 'Venue address' );
		const venueWebsite = document.getElementById(
			'venue-website'
		) as HTMLAnchorElement | null;

		const data: EventElements = {
			name,
			dateButton,
			eventDescription,
			website: eventWebsite,
		};
		const venueData = [ venueName, address, venueWebsite ];
		if ( venueData.every( ( element ) => null !== element ) ) {
			data.venue = {
				name: venueName!,
				address: address!,
				website: venueWebsite,
			};
		}
		return data;
	}

	/**
	 * A quick TS function to handle errors or select elements by ID
	 * @param id The HTML id
	 * @param errorName The pretty element name to print if error
	 * @returns HTMLElement
	 */
	private elSelector( id: string, errorName: string ): HTMLElement | null {
		const el = document.getElementById( id );
		const isVenueEl = errorName.includes( 'Venue' );
		if ( ! el && ! isVenueEl ) {
			throw new Error( `${ errorName } not found!` );
		}
		return el;
	}

	private setTheProperties( elements: EventElements ) {
		this.event.name = elements.name.innerText;
		this.event.description = this.setTheDescription(
			elements.eventDescription
		);
		this.event.website = elements.website?.innerText;
		if ( ! elements.venue ) {
			this.event.venue = null;
		} else {
			this.event.venue.name = elements.venue.name.innerText;
			this.event.venue.address = elements.venue.address.innerText;
			this.event.venue.website = elements.venue.website?.innerText;
		}
		this.button = elements.dateButton;
		this.setEventDateTimes( this.button );
	}

	/**
	 * Iterates through a node list and returns a string
	 *
	 * @param eventDescription the Node List of elements in the event's description
	 * @returns string
	 */
	private setTheDescription(
		eventDescription: NodeListOf< HTMLElement >
	): string {
		let description = '';
		eventDescription.forEach( ( p ) => ( description += p.textContent ) );
		return description;
	}

	/** Assigns the Event Details */
	private setEventDateTimes( button: HTMLButtonElement ) {
		const start = button.dataset.eventStart;
		if ( ! start ) {
			console.warn( `Start Date not found!` );
			return;
		}
		const end = button.dataset.eventEnd;
		this.event.isAllDay = 'true' === button.dataset.isAllDay;
		this.event.start = new Date( start );
		this.event.end = end
			? new Date( end )
			: new Date( this.event.start.getTime() + this.EVENT_DURATION );
	}
}
