<?php
/**
 * The Search Results for the Events
 *
 * @since 1.0
 * @package ChoctawNation
 */

get_header();
?>
<div class="container my-5 py-5">
	<section class="search row">
		<form class="flex-grow-1" method="GET" action=<?php echo esc_url( site_url( '/events' ) ); ?>>
			<div class="form-group row">
				<div class="col p-0">
					<input type="search" class="form-control" name="s" id="search-input" placeholder="Search for events" />
				</div>
				<div class="col-2">
					<input type="submit" value="Find Events" class='btn btn-primary' id='search-button' aria-label="Find Events">
				</div>
			</div>
		</form>
	</section>
	<h2>Showing Results for "<?php echo esc_html( get_search_query( false ) ); ?>"</h2>
	<?php if ( have_posts() ) : ?>
	<section class="results">
		<ol class="list-unstyled">
			<?php
			while ( have_posts() ) {
				the_post();
				require dirname( __DIR__ ) . '/template-parts/archive/content-event-preview.php';
			}
			?>
		</ol>
		<?php else : ?>
		<p>No events found.</p>
		<?php endif; ?>
	</section>
</div>
<?php
wp_footer();