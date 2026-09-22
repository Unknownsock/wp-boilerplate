<?php
/**
 * Article partial: gallery card layout.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	global $post;

	// Groups.
	$card_options = get_field( 'card_options', $post );

	// Individuals.
	$featured_image = $card_options['featured_image'] ?? null;
	$excerpt        = $content_options['excerpt'] ?? null;
	$excerpt        = mb_strimwidth( $excerpt, 0, 150, '...' );

	$title = get_the_title();
	$link  = get_the_permalink();

?>
<a href="<?php echo esc_url( $link ); ?>">
	<div class="overlay">
		<h3>
			<?php echo esc_html( $title ); ?>
		</h3>
		<?php
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
	<picture>
		<source
			media="(max-width:768px)"
			width="1920"
			height="1080"
			srcset="<?php echo esc_url( $featured_image['sizes']['article-small'] ); ?>">
		<source
			media="(min-width:768px)"
			width="1920"
			height="1080"
			srcset="<?php echo esc_url( $featured_image['sizes']['article-large'] ?? $featured_image['sizes']['article-small'] ); ?>">
		<img
			src="<?php echo esc_url( $featured_image['sizes']['article-large'] ?? $featured_image['url'] ); ?>"
			width="<?php echo esc_attr( $featured_image['sizes']['article-large-width'] ); ?>"
			height="<?php echo esc_attr( $featured_image['sizes']['article-large-height'] ); ?>"
			alt="<?php echo esc_attr( $featured_image['alt'] ?? '' ); ?>"
			loading="lazy">
	</picture>
</a>
