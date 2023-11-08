import React from 'react';
import { getTheDateTimes } from './utilities/date-helpers';
import { EventData } from './utilities/graphql-helpers/types';

export default function EventPreview( { event }: { event: EventData } ) {
	if ( ! event ) return;
	const { theDates } = getTheDateTimes( event.timeAndDate );
	const { featuredImage } = event;

	return (
		<li className="post-preview__container row my-5">
			{ featuredImage && (
				<div className="col-lg-4">
					<a href={ `/${ event.slug }` } className="ratio ratio-16x9">
						<img
							src={ featuredImage?.sourceUrl }
							alt={ featuredImage.altText }
							srcset={ featuredImage.srcSet }
							className="object-fit-cover"
						/>
					</a>
				</div>
			) }
			<div
				className={
					featuredImage
						? `col-lg-8 post-preview my-3`
						: `col post-preview my-3`
				}
			>
				<div
					className="post-preview__dates"
					dangerouslySetInnerHTML={ { __html: theDates } }
				/>

				<h2 className="post-preview__title">
					<a href={ `/${ event.slug }` }>{ event.title }</a>
				</h2>
				{ /* if venue, the venue*/ }
				{ event.archiveContent && (
					<div className="post-preview__excerpt">
						{ event.archiveContent }
					</div>
				) }
			</div>
		</li>
	);
}
