import React, { useEffect, useState } from 'react';
import BasicSelect from './Select';
import Button from '@mui/material/Button';
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import { BootstrapButton } from './BootstrapButton';

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
			<Box
				component="form"
				sx={ {
					'& > :not(style)': { m: 1, width: '25ch' },
				} }
				noValidate
				autoComplete="off"
				className="row row-cols-auto"
				onSubmit={ handleSubmit }
			>
				<TextField
					sx={ {
						'& .MuiInputLabel-root.Mui-focused': {
							color: `var(--color-primary)`,
						},
						'& .MuiInputBase-root.Mui-focused .MuiOutlinedInput-notchedOutline':
							{
								borderColor: `var(--color-primary)`,
							},
					} }
					value={ searchQuery }
					onChange={ ( ev ) => {
						setSearchTerm( ev.target.value );
						setSearchQuery( ev.target.value );
					} }
					id="search-input"
					placeholder="Search for events"
					label="Search"
					variant="outlined"
					className="col text-primary flex-grow-1"
				/>
				<BootstrapButton>Find Events</BootstrapButton>
			</Box>
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
