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

<section class="o-hero o-hero--standard">
	<div class="o-hero__content">

		<?php
		// "Back to parent" label, not a call to action - styled as eyebrow
		// text (matching .c-eyebrow used everywhere else in the theme)
		// rather than as a full button.
		if ( $page_parent_id ) {
			$parent_title = get_the_title( $page_parent_id );
			printf(
				'<a href="%s" class="c-eyebrow c-eyebrow--default" aria-label="%s">%s</a>',
				esc_url( get_permalink( $page_parent_id ) ),
				esc_attr( sprintf( 'Back to %s', $parent_title ) ),
				esc_html( $parent_title )
			);
		} elseif ( 'portfolio' === $post_type ) {
			echo '<a href="/portfolio/" class="c-eyebrow c-eyebrow--default" aria-label="Back to Our Work">Case Study</a>';
		} elseif ( 'post' === $post_type ) {
			echo '<a href="/blog/" class="c-eyebrow c-eyebrow--default" aria-label="Back to all Blogs">Blog</a>';
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
