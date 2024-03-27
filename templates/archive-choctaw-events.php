<?php
/**
 * The Archive Display for the Events
 *
 * @since 1.0
 * @package ChoctawNation
 */

get_header();
?>
<div class="container my-5 py-5">
	<?php if ( class_exists( 'WPGraphQL' ) ) : ?>
		<?php wp_enqueue_script( 'choctaw-events-search' ); ?>
	<div class="row">
		<div class="col">
			<h1 class="my-5">Events</h1>
		</div>
	</div>
	<div id="app">
		This page requires Javascript to run.
	</div>

	<?php else : ?>

	<div class="container my-5 py-5">
		<?php if ( have_posts() ) : ?>
		<section class="events-list__container">
			<h1 class="mb-5">Upcoming Events</h1>
			<ol class="list-unstyled events-list">
				<?php
				while ( have_posts() ) {
					the_post();
					$template_override = get_template_part( 'template-parts/events/content', 'event-preview' );
					if ( false === $template_override ) {
						require dirname( __DIR__ ) . '/template-parts/archive/content-event-preview.php';
					}
				}
				?>
			</ol>
			<?php else : ?>
			<p>No events found.</p>
			<?php endif; ?>
		</section>
	</div>
	<?php endif; ?>
</div>
<?php
get_footer();
