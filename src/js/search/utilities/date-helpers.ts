import { format, parse } from 'date-fns';
import { RawEventData } from '../App';

export function getTheDateTimes(
	acf: RawEventData['acf']['event_details']['time_and_date']
) {
	const start = formatDate(acf.start_date);
	const theDates = getTheDates(acf);
	return { start, theDates };
}

function getTheDates(
	acf: RawEventData['acf']['event_details']['time_and_date']
): string {
	const { start_date, start_time, end_date, end_time } = acf;
	const startDate = formatDate(start_date);
	const startTime = formatTime(start_time) ?? undefined;
	const endDate = formatDate(end_date) ?? undefined;
	const endTime = formatTime(end_time) ?? undefined;
	let dateAndTime = '';
	if (startDate) {
		dateAndTime += startDate;
		if (startTime) {
			dateAndTime += ` @ ${startTime}`;
		}
	}
	if (endDate) {
		dateAndTime += ` &ndash; ${endDate}`;
		if (endTime) {
			dateAndTime += ` @ ${endTime}`;
		}
	}
	return dateAndTime;
}

function formatDate(inputDate: string) {
	if (!inputDate) return undefined;
	const parsedDate = parse(inputDate, 'yyyyMMdd', new Date());
	return format(parsedDate, 'MMM d');
}

function formatTime(inputTime: string) {
	if (!inputTime || '' === inputTime) return undefined;
	const parsedTime = parse(inputTime, 'HH:mm:ss', new Date());
	return format(parsedTime, 'h:mm a');
}
