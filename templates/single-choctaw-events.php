<?php
/**
 * The Single Display for the Events
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

use ChoctawNation\Events\Choctaw_Event;

get_header();
wp_enqueue_script( 'choctaw-events-add-to-calendar' );
$event = new Choctaw_Event( get_field( 'event_details' ), get_the_ID() );
?>
<div class="container my-5 py-5">
	<nav arial-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/events">All Events</a></li>
			<li class="breadcrumb-item active" aria-current="page"><?php echo $event->get_the_name(); ?></li>
		</ol>
	</nav>
	<article <?php post_class(); ?> id="<?php echo 'post-' . get_the_ID(); ?>">
		<header class='event-header'>
			<h1 class='event-header__title' id='event-name'>
				<?php echo $event->get_the_name(); ?>
			</h1>
			<p class="event-header__date-time"><?php $event->the_start_date_time(); ?></p>
			<div class="ratio ratio-16x9 event-header__featured-image">
				<?php the_post_thumbnail( 'choctaw-events-single', array( 'class' => 'object-fit-cover' ) ); ?>
			</div>
		</header>
		<section class="event-body row mt-3">
			<div id="event-description">
				<?php $event->the_description(); ?>
			</div>
			<?php $event->the_add_to_calendar_button(); ?>
		</section>
		<hr>
		<aside class="event-info row">
			<div class="event-details d-flex flex-column col-sm-6 p-3">
				<h3>Details</h3>
				<div class="event-details__date mt-3">
					<h4>Date:</h4>
					<p><?php $event->the_dates( 'M d' ); ?></p>
				</div>
				<?php if ( ! $event->is_all_day ) : ?>
				<div class="event-details__time mt-3">
					<h4>Time:</h4>
					<p><?php $event->the_times(); ?></p>
				</div>
				<?php endif; ?>
				<?php if ( $event->has_category ) : ?>
				<div class="event-details__category mt-3">
					<?php
					$category_count = count( $event->categories );
					echo ( $category_count > 1 ) ? '<h4>Event Categories:</h4>' : '<h4>Event Category:</h4>';
					$event->the_category();
					?>
				</div>
				<?php endif; ?>
				<?php if ( $event->get_the_website() ) : ?>
				<div class="event-details__website mt-3">
					<h4>Website:</h4>
					<?php $event->the_website(); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php if ( $event->venue ) : ?>
			<div class="event-venue d-flex-flex-column col-sm-6 p-3">
				<h3>Venue</h3>
				<div class="event-venue__address">
					<p id='venue-name'>
						<?php $event->venue->the_name(); ?>
					</p>
					<p id="venue-address"><?php $event->venue->the_address(); ?></p>
				</div>
				<?php if ( $event->venue->get_the_phone() ) : ?>
				<div class="event-venue__phone mt-3">
					<h4>Phone:</h4>
					<?php $event->venue->the_phone(); ?>
				</div>
				<?php endif; ?>
				<?php if ( $event->venue->get_the_website() ) : ?>
				<div class="event-venue__website mt-3">
					<h4>Venue Website</h4>
					<?php $event->venue->the_website(); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</aside>
	</article>
	<hr>
	<nav aria-label="Post Navigation" class='my-5'>
		<ul class="pagination d-flex justify-content-between pagination-lg">
			<li class="page-item"><?php echo get_previous_post_link( '%link' ); ?></li>
			<li class="page-item"><?php echo get_next_post_link( '%link' ); ?></li>
		</ul>
	</nav>
</div>

<?php
wp_footer();