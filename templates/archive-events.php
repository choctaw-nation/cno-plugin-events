<?php
/**
 * The Events Archive
 */

get_header();
?>
<section class="cno-event-archive__hero">
	<div class="container">
		<h1>The Events</h1>
	</div>
</section>
<div class="cno-events-wrapper" id="">
	<?php if ( have_posts() ) : ?>
	<section class="cno-event-search">
		<div class="container">
			<form action="search">
				<h2 class="cno-event-search__title">Search Events</h2>
				<input type="search" name="search" id="search" placeholder="Find an Event" class="cno-event-search__search-bar" />
			</form>
			<div class="cno-event-search__filters cno-event-search-filters">
				<h3 class="cno-event-search-filters__title">Filters</h3>
				<div class="cno-event-search-filters__container">
					<div class="cno-event-search-filters__filter">
						<input type="checkbox" name="filter" id="filter" />
						<label for="filter">A Filter</label>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="cno-events">
		<div class="container">
			<div class="row">
				<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="cno-event col-lg-4 col-md-6">
					<figure class="cno-event__image">
						<?php
						the_post_thumbnail(
							attr:array(
								'class' => 'cno-event__image--image',
							)
						);
						?>
					</figure>
					<?php the_title( '<h2 class="cno-event__title">', '</h2>' ); ?>
					<div class="cno-event__meta">
						<?php extract( get_field( 'info' ) ); ?>
						<div class="cno-event__meta--start">
							<strong>Start: </strong> <?php echo $start_date_and_time; ?>
						</div>
						<div class="cno-event__meta--end">
							<strong>End: </strong> <?php echo $end_date_and_time; ?>
						</div>
					</div>
					<div class="about">
						<?php echo acf_esc_html( $event_description ); ?>
					</div>
				</a>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php else : ?>
	<section class="cno-events">No events found.</section>
	<?php endif; ?>
</div>

<?php
get_footer();