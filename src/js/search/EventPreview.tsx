import React, { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { RawEventData } from './App';
import { WP_REST_API_Attachment } from 'wp-types';
import { getTheDateTimes } from './utilities/date-helpers';

export default function EventPreview({
	event,
	setIsLoading,
}: {
	event: RawEventData;
	setIsLoading: CallableFunction;
}) {
	const [thumbnail, setThumbnail] = useState('');
	const { start, theDates } = getTheDateTimes(
		event.acf.event_details.time_and_date
	);

	useEffect(() => {
		setIsLoading(true);
		apiFetch({
			path: `wp/v2/media/${event.featured_media}`,
		})
			.then((res: WP_REST_API_Attachment) => {
				setThumbnail(res.description.rendered);
				setIsLoading(false);
			})
			.catch((err) => console.error(err));
	}, []);

	return (
		<li className="post-preview__container d-block row d-flex my-5 my-lg-3">
			{start && (
				<div className="col-1 text-center font-weight-bold h5">
					{start}
				</div>
			)}
			<div className="col">
				<div className={`row ${thumbnail && `flex-row-reverse`}`}>
					{thumbnail && (
						<div
							className="col-lg-4"
							dangerouslySetInnerHTML={{ __html: thumbnail }}
						/>
					)}
					<div className="col-lg-8 post-preview my-3 my-lg-0">
						<div className="post-preview__dates">{theDates}</div>
						<h2 className="post-preview__title">
							<a href={`/${event.slug}`}>
								{event.title.rendered}
							</a>
						</h2>
						{/* if venue, the venue*/}
						<div
							className="post-preview__excerpt"
							dangerouslySetInnerHTML={{
								__html:
									event.excerpt.rendered ??
									event.acf.event_details.event_description,
							}}
						/>
					</div>
				</div>
			</div>
		</li>
	);
}
