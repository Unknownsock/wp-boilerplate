<?php
/**
 * Content block: post listing.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Groups.
	$title_options = $content['title_options'] ?? null;

	// Singles.
	$query_type        = $content['query_type'] ?? null;
	$relationship      = $content['relationship'] ?? null;
	$number_of_queries = $content['number_of_queries'] ?? null;
	$view_all_link     = $content['view_all_link'] ?? null;

	$enable_carousel = $content['enable_carousel'] ?? null;
?>

<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php
		get_template_part(
			'template-parts/components/component',
			'block-title',
			array(
				'title_options' => $title_options,
			)
		);
		?>
	<div class="facetwp-template">

			<?php if ( 'enable' === $enable_carousel ) { ?>
				<div class="swiper swiper-slider swiper-related js-swiper-related">
					<div class="swiper-wrapper">
			<?php } elseif ( 'disable' === $enable_carousel ) { ?>
				<div class="<?php echo $block; ?>__grid">
			<?php } ?>
				<?php
				if ( 'relationship' === $query_type ) {
					$posts = $relationship;
				} else {
					$args  = array(
						'post_type'      => 'post',
						'orderby'        => array(
							'date' => 'DESC', // Primary sort: by post date.
							'ID'   => 'DESC',  // Secondary, fallback sort: by post ID.
						),
						'order'          => 'DESC',
						'posts_per_page' => $number_of_queries,
						'post_status'    => 'publish',
						'facetwp'        => true,
					);
					$posts = new WP_Query( $args );
					$posts = $posts->get_posts();
				}
				if ( $posts ) {
					foreach ( $posts as $post ) {
						get_template_part(
							'template-parts/articles/article',
							'listing'
						);
					}
				} else {
					?>
						<div class="content no-results">
							<h3>No search results...</h3>
							<p>Please try another search term or pick a category from the list in the sidebar</p>
						</div>
					<?php
				}
					wp_reset_postdata();
				?>
			</div>
			<div class="filters--bottom"></div>

		<?php if ( 'enable' === $enable_carousel ) { ?>
			</div>
		<?php } ?>

	</div>
	<?php if ( $view_all_link ) { ?>
		<div class="block-footer">
			<a href="<?php echo $view_all_link['url']; ?>">View all...</a>
		</div>
	<?php } ?>
</section>
