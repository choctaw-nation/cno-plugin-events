import React from 'react';
import Box from '@mui/material/Box';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';

export default function BasicSelect( { taxonomy, setTaxonomies, sx } ) {
	function handleChange( event: SelectChangeEvent ) {
		setTaxonomies( ( prev ) => {
			const selected = {
				name: taxonomy.name,
				values: taxonomy.values,
				selected: event.target.value,
			};
			const updatedTaxonomies = prev.map( ( tax ) => {
				if ( tax.name === selected.name ) {
					return selected;
				} else {
					return tax;
				}
			} );
			return updatedTaxonomies;
		} );
	}

	return (
		<Box sx={ sx }>
			<FormControl fullWidth>
				<InputLabel id={ `${ taxonomy.name }-label` }>
					{ taxonomy.name }
				</InputLabel>
				<Select
					labelId={ `${ taxonomy.name }-label` }
					id="category"
					value={ taxonomy.selected }
					label={ taxonomy.name }
					onChange={ handleChange }
				>
					{ taxonomy.values.map( ( value ) => (
						<MenuItem value={ value }>{ value }</MenuItem>
					) ) }
				</Select>
			</FormControl>
		</Box>
	);
}
