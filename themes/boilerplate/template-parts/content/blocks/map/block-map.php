<?php
/**
 * Content block: map. Renders an OpenStreetMap (Leaflet) embed pinned to a
 * free-text address, geocoded server-side - see boilerplate_geocode_address() in
 * includes/functions.php. No API key needed.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$address = $content['address'] ?? null;

	$coords = $address ? boilerplate_geocode_address( $address ) : null;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $coords ) : ?>
		<div
			class="<?php echo esc_attr( $block ); ?>__embed"
			data-lat="<?php echo esc_attr( (string) $coords['lat'] ); ?>"
			data-lng="<?php echo esc_attr( (string) $coords['lng'] ); ?>"
			data-address="<?php echo esc_attr( $address ); ?>"
			role="img"
			aria-label="<?php echo esc_attr( 'Map showing ' . $address ); ?>"
		>
			<noscript>
				<a href="https://www.openstreetmap.org/search?query=<?php echo rawurlencode( $address ); ?>" target="_blank" rel="noopener">
					<?php echo esc_html( $address ); ?> - view on OpenStreetMap
				</a>
			</noscript>
		</div>
	<?php elseif ( $address ) : ?>
		<p class="<?php echo esc_attr( $block ); ?>__error">
			Couldn't locate "<?php echo esc_html( $address ); ?>" on the map.
		</p>
	<?php endif; ?>
</section>
