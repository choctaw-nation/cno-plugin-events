<?php
/**
 * The Event Preview for Archive-Choctaw Events
 *
 * @package ChoctawNation
 * @subpackage Events
 */

use ChoctawNation\Events\Choctaw_Event;

$event_details = get_field( 'event_details' );
if ( $event_details ) {
	$event = new Choctaw_Event( get_field( 'event_details' ), get_the_ID() );
} else {
	return;
}
?>
<li class="post-preview__container row my-5">
	<div class="col">
		<div class="row row-gap-3 row-gap-lg-0">
			<?php if ( has_post_thumbnail() ) : ?>
			<div class="col-lg-4">
				<a href="<?php the_permalink(); ?>" class="ratio ratio-16x9">
					<?php the_post_thumbnail( 'choctaw-events-preview', array( 'class' => 'object-fit-cover' ) ); ?>
				</a>
			</div>
			<?php endif; ?>
			<div class="post-preview col-lg-8 flex-grow-1">
				<div class="post-preview__dates">
					<?php $event->the_dates(); ?>
				</div>
				<h2 class="post-preview__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<?php
				if ( $event->has_venue ) {
					echo '<p class="post-preview__location my-2 fw-bold fs-5">';
					$event->the_venue_name();
					echo '</p>';
				}
				if ( $event->has_excerpt ) {
					echo '<div class="post-preview__excerpt">';
					$event->the_excerpt();
					echo '</div>';
				}
				?>
			</div>
		</div>
	</div>
</li>
