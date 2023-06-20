import React from '@wordpress/element';
import { EventFilter } from '../types';

export default function SearchFilters( {
	filters,
	checkedFilters,
	setCheckedFilters,
} ) {
	function handleClick( x, { target } ) {
		if ( ! target.id ) return;
		if ( checkedFilters.includes( target.id ) ) {
			setCheckedFilters(
				checkedFilters.filter( ( f ) => {
					return f !== target.id;
				} )
			);
		} else setCheckedFilters( ( prev ) => [ ...prev, target.id ] );
	}
	return (
		<div className="cno-event-search-filters">
			{ filters.map( ( { type: { name, filters } } ) => {
				return (
					<div
						className="cno-event-search-filters__container"
						onClick={ ( ev ) => {
							handleClick( name, ev );
						} }
					>
						<h4 className="cno-event-search-filters__title">
							{ name }
						</h4>
						{ filters.map( ( filter: EventFilter ) => {
							return (
								<div className="cno-event-search-filters__filter">
									<input
										type="checkbox"
										name={ filter.link }
										id={ filter.name }
										checked={ checkedFilters.includes(
											filter.name
										) }
										onChange={ () => {} }
									/>
									<label htmlFor={ filter.name }>
										{ filter.name }
									</label>
								</div>
							);
						} ) }
					</div>
				);
			} ) }
		</div>
	);
}
