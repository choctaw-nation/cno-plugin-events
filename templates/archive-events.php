<?php
/**
 * The Events Archive
 */

get_header();
?>
<section class="hero">
	<div class="container">
		<h1>The Events</h1>
	</div>
</section>
<div class="cno-events-wrapper" id="app">
	<?php if ( have_posts() ) : ?>
	<section class="cno-event-search">
		<div class="cno-events-container">
			<h2 class="cno-event-search__title">Search Events</h2>
			<input type=" text" name="search" id="search" placeholder="Find an Event" class="cno-event-search__search-bar" />
			<div class="cno-event-search__filters cno-event-search-filters">
				<h3 class="cno-event-search-filters__title">Filters</h3>
				<div class="cno-event-search-filters__container">
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class=" cno-events-container">
		<section class="cno-events">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
			<article class="cno-event">
				<h2><?php the_title(); ?></h2>
				<aside class="event-meta">
					<?php extract( get_field( 'info' ) ); ?>
					<div class="event-meta__day">
						<strong>When: </strong>
					</div>
					<div class="event-meta__start-time">
						<strong>Start Time:</strong> <?php echo $start_time; ?>
					</div>
					<?php
					if ( isset( $end_time ) ) {
								echo '<div class="event-meta__start-time">';
								echo '<strong>End Time:</strong> ' . $end_time;
								echo '</div>';
					}
					?>
				</aside>
				<div class="about">
					<?php $event_description = get_field( 'description' ); ?>
					<?php echo $event_description; ?>
				</div>
			</article>
			<?php endwhile; ?>
		</section>
	</div>
	<?php else : ?>
	<section class="cno-events">No events found.</section>
	<?php endif; ?>
</div>

<?php
get_footer();