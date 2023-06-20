import React, { useState, useEffect, createRoot } from '@wordpress/element';
import LoadingSpinner from '../spinner';
import Fuse from 'fuse.js';
import { fuzzySearchKeys } from './fuse-config';
import { EventPost, EventFilters } from './types';
import { getPosts, destructureData } from './DataHandler';
import SearchBar from './Presentational/SearchBar';
import ResultsContainer from './Presentational/ResultsContainer';

function App() {
	const [ isLoading, setIsLoading ] = useState( true );
	const [ posts, setPosts ] = useState< Array< EventPost > >( [] );
	const [ filters, setFilters ] = useState< Array< EventFilters > >( [] );
	const [ search, setSearch ] = useState( '' );
	useEffect( () => {
		if ( search === '' ) {
			getPosts().then( ( { eventLocations, eventTypes, events } ) => {
				setPosts(
					events.nodes.map( ( node ) => {
						return destructureData( node );
					} )
				);
				setIsLoading( false );
			} );
		}
	}, [ search ] );

	function handleSearchInput( { target } ) {
		setSearch( target.value );
	}
	useEffect( () => {
		if ( '' === search ) return;
		setIsLoading( true );
		const timeout = setTimeout( () => {
			const fuse = new Fuse( posts, {
				...fuzzySearchKeys,
			} );
			const results = fuse.search( search );
			setPosts( results.map( ( result ) => result.item ) );
			setIsLoading( false );
		}, 500 );
		return () => clearTimeout( timeout );
	}, [ search ] );
	const [ checkedFilters, setCheckedFilters ] = useState< string[] >( [] );
	return (
		<div className="cno-search">
			<SearchBar
				filters={ filters }
				search={ search }
				checkedFilters={ checkedFilters }
				setCheckedFilters={ setCheckedFilters }
				handleSearchInput={ handleSearchInput }
			/>
			<div className="container">
				{ ! isLoading ? (
					<ResultsContainer
						posts={ posts }
						checkedFilters={ checkedFilters }
					/>
				) : (
					<LoadingSpinner />
				) }
			</div>
		</div>
	);
}
const root = document.getElementById( 'app' );
if ( root ) createRoot( root ).render( <App /> );
