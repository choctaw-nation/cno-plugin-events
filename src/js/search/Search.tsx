import React, { useEffect, useState } from 'react';
import BasicSelect from './Components/Select';
import Button from '@mui/material/Button';
import { BootstrapButton } from './utilities/BootstrapButton';

const filterStyles = {
	flexBasis: '25%',
};

export default function SearchBar( {
	searchTerm,
	setSearchTerm,
	taxonomies,
	setTaxonomies,
	resetFilters,
} ) {
	const [ searchQuery, setSearchQuery ] = useState( searchTerm );
	const [ isSelected, setIsSelected ] = useState( false );

	function handleSubmit( ev ) {
		ev.preventDefault();
		setSearchTerm( searchQuery );
	}

	useEffect( () => {
		setIsSelected( taxonomies.some( ( obj ) => obj.selected !== '' ) );
	}, [ taxonomies ] );

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
									setSearchTerm( ev.target.value );
									setSearchQuery( ev.target.value );
								} }
								className="w-100"
								id="search-input"
								placeholder="Search for events"
							/>
						</div>
						<div className="col-2">
							<BootstrapButton>Find Events</BootstrapButton>
						</div>
					</div>
				</form>
			</div>
			<div className="row mt-3">
				{ taxonomies.map( ( taxonomy ) => {
					return (
						<BasicSelect
							sx={ filterStyles }
							taxonomy={ taxonomy }
							setTaxonomies={ setTaxonomies }
						/>
					);
				} ) }
				{ isSelected && (
					<Button
						variant="outlined"
						sx={ {
							...filterStyles,
							borderColor: 'var(--color-primary)!important',
							color: 'var(--color-primary)',
						} }
						onClick={ resetFilters }
					>
						Reset Filters
					</Button>
				) }
			</div>
		</section>
	);
}
