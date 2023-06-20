export default function SearchInput( { search, handleSearchInput } ) {
	return (
		<input
			type="text"
			name="search"
			id="search"
			placeholder="Find an Event"
			className="cno-event-search__search-bar"
			value={ search }
			onChange={ handleSearchInput }
		/>
	);
}
