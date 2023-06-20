import React from '@wordpress/element';
import '../styles/loading-spinner.scss';
export default function LoadingSpinner() {
	const divs = [ 1, 2, 3, 4 ];
	return (
		<div className="lds-ring">
			{ divs.map( ( div ) => (
				<div />
			) ) }
		</div>
	);
}
