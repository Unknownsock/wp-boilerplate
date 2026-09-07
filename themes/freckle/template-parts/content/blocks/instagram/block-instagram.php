<?php
/**
 * Content block: Instagram feed (Elfsight embed).
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$eyebrow     = $content['eyebrow'] ?? null;
	$text        = $content['text'] ?? null;
	$follow_link = $content['follow_link'] ?? null;

	$follow_url    = $follow_link['url'] ?? '';
	$follow_handle = $follow_link['title'] ?? '';
	$follow_target = $follow_link['target'] ?? '_self';

if ( ! $follow_handle && $follow_url ) {
	$follow_handle = '@' . trim( wp_parse_url( $follow_url, PHP_URL_PATH ), '/' );
}

?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $eyebrow ) { ?>
		<p class="c-eyebrow c-eyebrow--centre"><?php echo esc_html( $eyebrow ); ?></p>
	<?php } ?>
	<?php if ( $text ) { ?>
		<div class="<?php echo $block; ?>__text"><?php echo $text; ?></div>
	<?php } ?>
	<?php if ( $follow_url ) { ?>
		<div class="<?php echo $block; ?>__follow">
			<svg class="<?php echo $block; ?>__follow-icon" viewBox="0 3 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path d="M0.588135 8.29426C0.588135 5.69426 2.69402 3.58838 5.29402 3.58838H14.7058C17.3058 3.58838 19.4117 5.69426 19.4117 8.29426V17.706C19.4117 20.306 17.3058 22.4119 14.7058 22.4119H5.29402C2.69402 22.4119 0.588135 20.306 0.588135 17.706V8.29426Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M6.4707 13.0001C6.4707 14.9531 8.04717 16.5295 10.0001 16.5295C11.9531 16.5295 13.5295 14.9531 13.5295 13.0001C13.5295 11.0472 11.9531 9.4707 10.0001 9.4707C8.04717 9.4707 6.4707 11.0472 6.4707 13.0001Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M15.2942 7.69434V7.71787" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<p>Follow us <a href="<?php echo $follow_url; ?>" target="<?php echo $follow_target; ?>"><?php echo esc_html( $follow_handle ); ?></a></p>
		</div>
	<?php } ?>
	<script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
	<div class="elfsight-app-7fe864a7-d872-4691-b198-decf4a9726df"></div>
</section>
