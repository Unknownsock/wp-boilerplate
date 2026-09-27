<?php
/**
 * The template for displaying 404 (not found) pages.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	get_header();

	$enable_footer = get_field( 'enable_footer' );

	get_template_part(
		'template-parts/header/site',
		'header'
	);

	?>

<div class="o-site-content">
	<main id="main" class="o-site-main" tabindex="-1">

		<section class="fourzerofour white">
			<h1>404 ERROR.</h1>
			<h2>The page you are looking for is not found</h2>
			<p>The page you are looking for does not exist. It may have been moved, or removed altogether. Perhaps you can return back to the site’s homepage and see if you can find what you are looking for.</p>
			<?php
				$href = get_home_url();
				echo add_button( $href, 'Back to Hompage', 'solid orange ' );
			?>
		</section>

	</main>
</div>

<?php
	get_footer();
?>
