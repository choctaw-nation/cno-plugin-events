import React, { useState, useEffect, createRoot } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { WP_REST_API_Post } from 'wp-types';

import SearchBar from './Search';
import SearchResults from './SearchResults';

const root = document.getElementById('search-results-app');
if (root) {
	createRoot(root).render(<App />);
}

export interface RawEventData extends WP_REST_API_Post {
	acf: {
		event_details: {
			event_description: string;
			event_website: string;
			time_and_date: {
				start_date: string;
				start_time: string | null;
				end_date: string | null;
				end_time: string | null;
				is_all_day: boolean;
			};
		};
	};
}

function App() {
	console.log(`App Loaded`);
	const [searchQuery, setSearchQuery] = useState('');
	const [searchTerm, setSearchTerm] = useState('');
	const [posts, setPosts] = useState<RawEventData[]>([]);
	const [isLoading, setIsLoading] = useState(false);

	useEffect(() => {
		const currentUrl = new URL(window.location.href);
		const urlSearchParams = new URLSearchParams(currentUrl.search);
		const searchQueryParam = urlSearchParams.get('s');
		if (searchQueryParam) setSearchQuery(searchQueryParam);
	}, []);

	useEffect(() => {
		setIsLoading(true);
		const controller = new AbortController();
		apiFetch({
			path: `wp/v2/choctaw-events?s=${
				searchTerm === '' ? searchQuery : searchTerm
			}`,
			signal: controller.signal,
		})
			.then((res) => {
				setPosts(res);
				setIsLoading(false);
			})
			.catch((err) => console.error(err));
		return () => controller.abort();
	}, [searchTerm, searchQuery]);

	return (
		<>
			<SearchBar
				searchTerm={searchTerm}
				setSearchTerm={setSearchTerm}
				setSearchQuery={setSearchQuery}
			/>
			{isLoading ? (
				<p>Loading...</p>
			) : (
				<SearchResults posts={posts} setIsLoading={setIsLoading} />
			)}
		</>
	);
}
