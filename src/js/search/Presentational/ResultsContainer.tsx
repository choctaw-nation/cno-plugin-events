import React from '@wordpress/element';
import SinglePost from './SinglePost';
import { EventPost } from '../types';

export default function ResultsContainer( {
	posts,
	checkedFilters,
}: {
	posts: EventPost[];
	checkedFilters: string[];
} ) {
	return (
		<section className="cno-events">
			{ posts.map( ( post ) => {
				if ( checkedFilters.length === 0 ) {
					return <SinglePost data={ post } />;
				} else if ( checkedFilters.length === 1 ) {
					if (
						checkedFilters.includes(
							post.eventLocations.nodes[ 0 ].name
						) ||
						checkedFilters.includes(
							post.eventTypes.nodes[ 0 ].name
						)
					) {
						return <SinglePost data={ post } />;
					}
				} else {
					if (
						checkedFilters.includes(
							post.eventLocations.nodes[ 0 ].name
						) &&
						checkedFilters.includes(
							post.eventTypes.nodes[ 0 ].name
						)
					) {
						return <SinglePost data={ post } />;
					}
				}
			} ) }
		</section>
	);
}
