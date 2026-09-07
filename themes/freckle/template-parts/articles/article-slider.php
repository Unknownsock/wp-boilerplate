<?php
/**
 * Article partial: slider card layout.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	$post_type = $args['post_type'] ?? null;

	// Groups.
	$card_options = get_field( 'card_options' );

	// Individuals.
	$featured_image = $card_options['featured_image'] ?? null;

	$title = get_the_title();
	$link  = get_the_permalink();

?>
<a href="<?php echo esc_url( $link ); ?>">
	<div class="overlay">
		<h3>
			<?php echo esc_html( $title ); ?>
		</h3>
		<?php
			global $post;
			$terms = wp_get_post_terms( $post->ID, 'category' );

		if ( $terms ) {
			echo '<ul class="tags">';
			foreach ( $terms as $term ) {
				echo '<span class="tag">' . esc_html( $term->name ) . '</span>';
			}
			echo '</ul>';
		}

			echo add_button( false, 'Find out more', 'standard right white' );
		?>
	</div>
	<img
		src="<?php echo esc_url( $featured_image['url'] ); ?>"
		alt="<?php echo esc_attr( $featured_image['alt'] ?? '' ); ?>"
		width="<?php echo esc_attr( $featured_image['sizes']['gallery-small-width'] ); ?>"
		height="<?php echo esc_attr( $featured_image['sizes']['gallery-small-height'] ); ?>"
		loading="lazy">
</a>
