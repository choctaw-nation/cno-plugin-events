import React from '@wordpress/element';
import { PrettyEventData } from '../types';

export default function SinglePost({ data }: { data: PrettyEventData }) {
	const { locations, title, event_info, altText, srcSet, size, sizes, type } =
		data;

	return (
		<article className="cno-event">
			<figure className="cno-event__image">
				<img
					width={size.width}
					height={size.height}
					src=""
					className="attachment-large size-large wp-post-image"
					alt={altText}
					decoding="async"
					srcSet={srcSet}
					sizes={sizes}
				/>
			</figure>
			<h2>{title}</h2>
			<aside className="event-meta">
				<div className="event-meta__day">
					<strong>When: </strong>
					{event_info.info.day}, September 1
				</div>
				{locations.length > 0 && (
					<div className="event-meta__location">
						<strong>Where:</strong>{' '}
						<a href={locations[0].link} rel="tag">
							{locations[0].name}
						</a>
					</div>
				)}
				<div className="event-meta__start-time">
					<strong>Start Time:</strong>
					{' ' + event_info.info.startTime}
				</div>
				{event_info.info.endTime && (
					<div className="event-meta__end-time">
						<strong>End Time:</strong> {event_info.info.endTime}
					</div>
				)}
				<div className="event-meta__type">
					<strong>Event Type:</strong>{' '}
					<a href="${type[0].link}" rel="tag">
						{type[0].name}
					</a>
				</div>
			</aside>
			<div className="about">{event_info.description}</div>
		</article>
	);
}
