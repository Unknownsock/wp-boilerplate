<?php
/**
 * The template for displaying the site header.
 *
 * @package Boilerplate
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<!-- Add a webfont provider (Adobe Fonts/Google Fonts/self-hosted) here per project. -->

	<svg style="display: none;">
		<symbol id="arrow-icon" viewBox="0 0 17 50" width="17" height="50">
			<path d="M16.9725 0C14.8325 0 12.8525 1.14 11.7725 3L0.8025 22C-0.2675 23.86 -0.2675 26.14 0.8025 28L11.7725 47C12.8425 48.86 14.8225 50 16.9725 50V0Z" />
		</symbol>
		<symbol id="arrow-icon-small" viewBox="0 0 18 50" width="9" height="25" stroke-miterlimit="10" vector-effect="non-scaling-stroke" stroke-alignment="inner">
			<path d="M18,50c-3,0-6-1-8-3L2,29c-2-3-2-8,0-11L10,3c2-2,5-3,8-3" />
		</symbol>
	</svg>

	<?php
		// X-Frame-Options used to be sent from here, after output had
		// already started - PHP silently can't send headers at that point.
		// It now goes out via the send_headers hook (theme-settings.php).
		wp_head();
	?>

	<!-- Analytics/ad tags are owned by a cookie-consent plugin per project, not this theme. -->
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link sr-only sr-only-focusable" href="#main"><?php esc_html_e( 'Skip to content', 'boilerplate-default' ); ?></a>
