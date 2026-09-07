<?php
/**
 * Content block: latest blogs (auto-queries the 3 most recent posts).
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Two ways in: as an ACF flexible-content block (get_row() returns
	// the current row) or as a plain template part with its own posts
	// already chosen - e.g. single.php's "related articles" ACF field,
	// which supplies $args['posts']/$args['title'] instead of relying
	// on the auto-query below. Everything past this point reads from
	// the same locals either way.
	$content = get_row( true );

if ( $content ) {
	$block        = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );
	$modifier     = null;
	$eyebrow      = $content['eyebrow'] ?? null;
	$title        = $content['title'] ?? null;
	$button_group = $content['button_group'] ?? null;
	$given_posts  = null;
} else {
	$block = 'b-latest-blogs';
	// Used as a plain template part (single.php/index.php/single-
	// clients.php's related-articles field) rather than the ACF
	// Blog-listing block - titles here are usually much shorter, so
	// the card title's normal 3-line clamp/min-height (_card.scss)
	// leaves an oversized gap; this modifier scopes it down to 2.
	// No colour modifier needed here anymore - .b-latest-blogs itself
	// is light grey now, which this variant always was anyway.
	$modifier     = 'b-latest-blogs--compact';
	$eyebrow      = null;
	$title        = $args['title'] ?? null;
	$button_group = null;
	// Matches the auto-query branch's posts_per_page (3) - the
	// ACF relationship field driving this isn't capped itself,
	// so cap it here rather than relying on editors to.
	$given_posts = array_slice( $args['posts'] ?? array(), 0, 3 );
}

	$classes = implode( ' ', array_filter( array( $block, 'grey-light', $modifier ) ) );

	// wysiwyg wraps its output in <p> tags - strip them so they don't
	// nest invalidly inside the <h2> below.
	$title_html = $title ? str_replace( array( '<p>', '</p>' ), '', $title ) : '';

	// Only auto-query the latest posts when none were handed to us.
	$posts_query = $given_posts ? null : new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>
<section class="<?php echo $classes; ?>">
	<?php if ( $eyebrow || $title_html || $button_group ) { ?>
		<div class="<?php echo $block; ?>__header">
			<div class="<?php echo $block; ?>__intro">
				<?php if ( $eyebrow ) { ?>
					<p class="c-eyebrow c-eyebrow--default"><?php echo esc_html( $eyebrow ); ?></p>
				<?php } ?>
				<?php if ( $title_html ) { ?>
					<h2 class="<?php echo $block; ?>__title"><?php echo $title_html; ?></h2>
				<?php } ?>
			</div>
			<?php if ( $button_group ) { ?>
				<div class="<?php echo $block; ?>__actions">
					<?php
						get_template_part(
							'template-parts/components/component',
							'button-group',
							array(
								'button_group' => $button_group,
							)
						);
					?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	
	<?php if ( $given_posts || $posts_query->have_posts() ) { ?>
		<div class="<?php echo $block; ?>__grid">
			<?php
			// Cards render via the shared "listing" partial
			// (template-parts/articles/article-listing.php,
			// .c-card--small) rather than a local copy, so CPT/tag/
			// title/excerpt behaviour always matches the Listing block
			// and everywhere else this card appears.
			if ( $given_posts ) {
				global $post;
				foreach ( $given_posts as $given_post ) {
					$post = get_post( $given_post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
					setup_postdata( $post );
					get_template_part(
						'template-parts/articles/article',
						'listing'
					);
				}
			} else {
				while ( $posts_query->have_posts() ) {
					$posts_query->the_post();
					get_template_part(
						'template-parts/articles/article',
						'listing'
					);
				}
			}
			wp_reset_postdata();
			?>
		</div>
	<?php } ?>
</section>
