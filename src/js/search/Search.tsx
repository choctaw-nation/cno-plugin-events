import React from '@wordpress/element';

export default function SearchBar({
	searchTerm,
	setSearchTerm,
	setSearchQuery,
}) {
	function handleSubmit(ev) {
		ev.preventDefault();
		setSearchQuery(searchTerm);
	}
	return (
		<section className="row search">
			<form className="flex-grow-1" onSubmit={handleSubmit}>
				<div className="form-group row">
					<div className="col p-0">
						<input
							type="search"
							name="s"
							value={searchTerm}
							onChange={(ev) => setSearchTerm(ev.target.value)}
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
		</section>
	);
}
