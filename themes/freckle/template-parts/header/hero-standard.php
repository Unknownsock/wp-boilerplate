<?php
/**
 * Header template part: standard page hero.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// groups.
	$hero_options = get_field( 'hero_options' );

	// Singles.
	$title          = get_the_title();
	$text           = $hero_options['text'] ?? null;
	$post_type      = get_post_type();
	$page_parent_id = wp_get_post_parent_id( get_the_ID() );

	get_header();

?>

<section class="o-hero o-hero--standard js-parallax">
	<div class="o-hero__content">

		<?php
		// These are "back to parent" labels, not calls to action - styled
		// as eyebrow text (matching .c-eyebrow used everywhere else in
		// the theme) rather than as a full button.
		if ( 495 === $page_parent_id ) {
			// Child of Product Sampling Agency.
			echo '<a href="/product-sampling-agency/" class="c-eyebrow c-eyebrow--default" aria-label="Back to Product Sampling Agency">Product Sampling Agency</a>';
		} elseif ( 503 === $page_parent_id ) {
			// Child of Promo Vehicles.
			echo '<a href="/promo-vehicles/" class="c-eyebrow c-eyebrow--default" aria-label="Back to Promo Vehicles">Promo Vehicles</a>';
		} elseif ( 602 === $page_parent_id ) {
			// Child of Staffing.
			echo '<a href="/staffing/" class="c-eyebrow c-eyebrow--default" aria-label="Back to Staffing">Staffing</a>';
		} elseif ( 404 === $page_parent_id ) {
			// Child of Experiential.
			echo '<a href="/experiential-marketing-agency/" class="c-eyebrow c-eyebrow--default" aria-label="Back to Experiential Marketing Agency">Experiential Marketing Agency</a>';
		} elseif ( 5385 === $page_parent_id ) {
			echo '<a href="/places-we-operate/" class="c-eyebrow c-eyebrow--default" aria-label="Back to all Blogs">Places We Operate</a>';
		} elseif ( 'clients' === $post_type ) {
			// Client CPT variant.
			echo '<a href="/clients/" class="c-eyebrow c-eyebrow--default" aria-label="Contact us">iMP CASE STUDY</a>';
		} elseif ( 'post' === $post_type ) {
			// Regular post variant.
			echo '<a href="/blog/" class="c-eyebrow c-eyebrow--default" aria-label="Back to all Blogs">iMP BLOG</a>';
		}
		?>

		<h1>
			<?php
				echo $title . '.';
			?>
		</h1>
		<?php if ( $text ) { ?>
			<p>
				<?php echo $text; ?>
			</p>
		<?php } ?>
	</div>
</section>
