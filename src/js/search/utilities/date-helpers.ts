import { format, parse } from 'date-fns';
import { EventData } from './graphql-helpers/types';

/**
 * Creates the Date/Time strings
 *
 * @param {EventData['timeAndDate']} acf the ACF Fields
 * @returns {obj} the start date and any further info
 */
export function getTheDateTimes( acf: EventData[ 'timeAndDate' ] ): {
	start: string;
	theDates: string;
} {
	const start = formatDate( acf.startDate )!;
	const theDates = getTheDates( acf );
	return { start, theDates };
}

/**
 * Builds the Full date/time string from passed acf fields
 *
 * @param {EventData['timeAndDate']} acf the ACF fields
 * @return string the parsed Date (+ Time) string
 */
function getTheDates( acf: EventData[ 'timeAndDate' ] ): string {
	const { startDate, startTime, endDate, endTime } = acf;

	const start_date = formatDate( startDate );
	const start_time = startTime ? formatTime( startTime ) : undefined;
	const end_date = endDate ? formatDate( endDate ) : undefined;
	const end_time = endTime ? formatTime( endTime ) : undefined;

	let dateAndTime = '';
	if ( start_date ) {
		dateAndTime += start_date;
		if ( start_time ) {
			dateAndTime += ` @ ${ start_time }`;
		}
	}
	if ( end_date ) {
		dateAndTime += ` &ndash; ${ end_date }`;
		if ( end_time ) {
			dateAndTime += ` @ ${ end_time }`;
		}
	}
	return dateAndTime;
}

/** Takes a date string and returns it as the correct date format
 *
 * @param {string|null} inputDate the event date
 * @return {undefined|string} the formatted date string
 */
function formatDate( inputDate: string ): undefined | string {
	if ( ! inputDate ) return undefined;
	const parsedDate = parse( inputDate, 'MM/dd/yyyy', new Date() );
	return format( parsedDate, 'MMM d' );
}

/** Takes a string and returns it as the correct Time format
 *
 * @param {string|null} inputTime the event time
 * @return {undefined|string} the formatted time string
 */
function formatTime( inputTime: string | null ): undefined | string {
	if ( ! inputTime || '' === inputTime ) return undefined;
	const parsedTime = parse( inputTime, 'hh:mm a', new Date() );
	return format( parsedTime, 'h:mm a' );
}

/** Orders the events of the day by time from morning to night.
 * @param {EventData} sortedEvents the data to sort.
 * @returns {EventData} the sorted events.
 */
export function getTimeSortedEvents( events: EventData[] ): EventData[] {
	/** The sorting event
	 * @param {LaborDayEvent} a the first event
	 * @param {LaborDayEvent} b the event to compare it against.
	 */
	function eventSorter( a: EventData, b: EventData ): number {
		const date1 = new Date( a.timeAndDate.startDate );
		const date2 = new Date( b.timeAndDate.startDate );
		return date1 - date2;
	}
	events.sort( eventSorter );
	return events;
}
