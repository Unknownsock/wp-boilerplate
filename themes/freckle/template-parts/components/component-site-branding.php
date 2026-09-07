<?php
/**
 * Component: site logo and branding.
 *
 * @package Boilerplate
 */

	$site_branding = get_field( 'site_branding', 'options' );
	$primary_logo  = $site_branding['primary_logo'] ?? null;

	$link = $primary_logo['url'] ?? null;
	$home = get_home_url();

	// Placeholder wordmark shown until a real logo is uploaded to the
	// `site_branding.primary_logo` ACF field - swap for the client's logo
	// there rather than hardcoding SVG here.
	$placeholder_logo_svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 40" role="img" aria-hidden="true"><text x="0" y="28" font-family="sans-serif" font-size="24" font-weight="700" fill="currentColor">Your Logo</text></svg>
SVG;

?>

<div class="site-branding">
	<?php if ( $primary_logo['url'] ?? null ) : ?>
		<?php echo add_icon( $primary_logo['url'], $home, '', '', '_self', 'aria-label="Go to the homepage"' ); ?>
	<?php else : ?>
		<a href="<?php echo esc_url( $home ); ?>" aria-label="Go to the homepage"><?php echo $placeholder_logo_svg; ?></a>
	<?php endif; ?>
</div>