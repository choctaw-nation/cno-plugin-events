<?php
/**
 * Single Event Template
 */

get_header();

?>
<div class="cno-events-container">
	<article class="cno-event py-5">
		<div class="cno-event__image"><?php the_post_thumbnail(); ?></div>
		<h1 class="cno-event__title headline"><?php the_title(); ?></h1>
		<aside class="cno-event-meta">
			<?php extract( get_field( 'info' ) ); ?>
			<div class="cno-event-meta__day">
				<strong>When: </strong><?php echo $day . ', ' . cno_get_the_date( $day ); ?>
			</div>
			<div class="cno-event-meta__start-time">
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
		<section class="cno-event__about">
			<?php $event_description = get_field( 'description' ); ?>
			<?php echo $event_description; ?>
		</section>
	</article>
</div>
<?php
get_footer();
