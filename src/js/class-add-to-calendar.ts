import EventConstructor from './class-event-constructor';

export default class AddToCalendar extends EventConstructor {
	/** On Construct, get the date and time from the DOM or return with console Warnings */
	constructor() {
		super();
		this.button.addEventListener( 'click', this.handleClick.bind( this ) );
	}

	/** Prevents Default Submission,  and Generates the ICS file */
	private handleClick( ev: MouseEvent ) {
		ev.preventDefault();
		try {
			this.downloadICSFile();
		} catch ( err ) {
			this.showErrorMessage();
			console.error( err );
		}
	}

	/** Generates the ICS file and downloads it
	 *
	 */
	private downloadICSFile() {
		const filename = `${ this.event.name }.ics`;
		const data = `BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\nDTSTART:${ this.getiCalDateStrings(
			this.event.start
		) }\nDTEND:${ this.getiCalDateStrings( this.event.end ) }\nSUMMARY:${
			this.event.name
		}\nLOCATION:${
			this.event.venue?.address ? this.event.venue.address : ''
		}\nDESCRIPTION:${ this.event.description }\nEND:VEVENT\nEND:VCALENDAR`;

		const blob = new Blob( [ data ], {
			type: 'text/calendar;charset=utf-8',
		} );
		const link = document.createElement( 'a' );
		link.href = URL.createObjectURL( blob );
		link.download = filename;
		document.body.appendChild( link );
		link.click();
		document.body.removeChild( link );
	}

	private showErrorMessage() {
		const message = `Could not generate a calendar event. Refresh the page and try again, or add it to your calendar manually.`;
		this.button.insertAdjacentHTML(
			'afterend',
			`<div class='alert alert-warning'>${ message }</div>`
		);
	}

	private getiCalDateStrings( date: Date ): string {
		const year = date.getUTCFullYear();
		const month = String( date.getUTCMonth() + 1 ).padStart( 2, '0' );
		const day = String( date.getUTCDate() ).padStart( 2, '0' );
		const hours = String( date.getUTCHours() ).padStart( 2, '0' );
		const minutes = String( date.getUTCMinutes() ).padStart( 2, '0' );
		const seconds = String( date.getUTCSeconds() ).padStart( 2, '0' );
		const iCalDateString = `${ year }${ month }${ day }T${ hours }${ minutes }${ seconds }Z`;
		return iCalDateString;
	}
}
