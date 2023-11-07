import React, { useState } from 'react';

export default function SearchBar( { searchTerm, setSearchTerm } ) {
	const [ searchQuery, setSearchQuery ] = useState( searchTerm );

	function handleSubmit( ev ) {
		ev.preventDefault();
		setSearchTerm( searchQuery );
	}
	return (
		<section className="search py-5">
			<div className="row">
				<form className="flex-grow-1" onSubmit={ handleSubmit }>
					<div className="form-group row">
						<div className="col">
							<input
								type="search"
								name="s"
								value={ searchQuery }
								onChange={ ( ev ) => {
									setSearchQuery( ev.target.value );
									setTimeout( () => {
										setSearchTerm( ev.target.value );
									}, 300 );
								} }
								id="search-input"
								placeholder="Search for events"
							/>
						</div>
						<div className="col-2">
							<input
								type="submit"
								value="Find Events"
								className="btn btn-primary"
								id="search-button"
							/>
						</div>
					</div>
				</form>
			</div>
		</section>
	);
}
