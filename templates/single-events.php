<?php
/**
 * Single Event Template
 */

extract( get_field( 'info' ) );
get_header();
?>
<article class="cno-event py-5 container">
	<div class="row align-items-stretch">
		<div class="col-lg-8 col-md-6">
			<div class="row">
				<figure class="cno-event__image">
					<?php the_post_thumbnail( attr:array( 'class' => 'cno-event__image--image' ) ); ?>
				</figure>
			</div>
			<div class="row cno-event__content">
				<?php the_title( '<h1 class="cno-event__title">', '</h1>' ); ?>
				<div class="cno-event__description">
					<?php echo acf_esc_html( $event_description ); ?>
				</div>
			</div>
			<?php if ( $has_learn_more_button ) : ?>
			<div class="row">
				<?php echo cno_create_external_link( $button_link, $button_text ); ?>
			</div>
			<?php endif; ?>
		</div>

		<aside class="col p-0">
			<div class="cno-event-meta">
				<div class="cno-event-meta__start-time">
					<strong>Start Time:</strong> <?php echo $start_date_and_time; ?>
				</div>
				<div class="event-meta__start-time">
					<strong>End Time:</strong> <?php echo $end_date_and_time; ?>
				</div>
			</div>
		</aside>
	</div>
</article>
<?php
get_footer();