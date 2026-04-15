import type { TimeInputValue } from './types';

export const dFlexColumnStyles: React.CSSProperties = {
	display: 'flex',
	flexDirection: 'column',
	gap: '1rem',
};

export class TimeConverter {
	/**
	 * Converts a time string in the format "HH:MM" to an object with hours and minutes.
	 * @param timeString The time string to convert.
	 * @return An object with hours and minutes, or undefined if the input is invalid.
	 */
	static stringToTimeValue( timeString: string ): TimeInputValue | undefined {
		if ( ! timeString ) {
			return undefined;
		}
		const [ hours, minutes ] = timeString.split( ':' ).map( Number );
		return {
			hours,
			minutes,
		};
	}

	/**
	 * Converts a TimeInputValue to a string in the format "HH:MM".
	 * @param value The TimeInputValue to convert.
	 * @return A string representation of the time, or an empty string if the input is null.
	 */
	static timeValueToString( value: TimeInputValue | null ): string {
		if ( ! value ) {
			return '';
		}

		const hours = String( value.hours ).padStart( 2, '0' );
		const minutes = String( value.minutes ).padStart( 2, '0' );

		return `${ hours }:${ minutes }`;
	}
}
