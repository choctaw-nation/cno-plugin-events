import React from '@wordpress/element';
import EventPreview from './EventPreview';
export default function SearchResults({ posts, setIsLoading }) {
	return (
		<ol className="list-unstyled">
			{posts.map((post) => (
				<EventPreview
					key={post.id}
					event={post}
					setIsLoading={setIsLoading}
				/>
			))}
		</ol>
	);
}
