<?php
/**
 * Content block: our work feed.
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
	$view_all_link = $content['view_all_link'] ?? null;
	$disable_shape = $content['disable_shape'] ?? null;

	$clients = get_sub_field( 'clients' );
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>" data-rotate>

	<?php if ( 'true' === $disable_shape ) { ?>
		<div class="<?php echo $block; ?>__shape" data-rotate-scroll data-rotate-direction="counterclockwise" data-rotate-opacity="0.1" data-rotate-speed="25">
			<svg class="our-work-hex" viewBox="0 0 1407 1614" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M685.755 5.52306C696.584 -0.729084 709.925 -0.729086 720.755 5.52306L1388.65 391.133C1399.48 397.385 1406.15 408.94 1406.15 421.444V1192.66C1406.15 1205.17 1399.48 1216.72 1388.65 1222.98L720.755 1608.59C709.926 1614.84 696.584 1614.84 685.755 1608.59L17.858 1222.98C7.029 1216.72 0.358032 1205.17 0.358032 1192.66L0.358032 421.444C0.358032 408.94 7.02899 397.385 17.858 391.133L685.755 5.52306Z" /> </svg>
		</div>
	<?php } ?>
	<?php
		get_template_part(
			'template-parts/components/component',
			'block-title',
			array(
				'title_options' => $title_options,
			)
		);
		?>
	<div class="<?php echo $block; ?>__grid">
		<?php
			$rows = $clients;
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$client_logo = get_field( 'client_logo', $row );
				$client      = get_the_title( $row );
				$client_link = get_permalink( $row );
				?>
					<a href="<?php echo esc_url( $client_link ); ?>" aria-label="<?php echo esc_attr( $client . ' - View case study' ); ?>">
						<img src="<?php echo esc_url( $client_logo['url'] ); ?>" width="<?php echo esc_attr( $client_logo['width'] ?? '' ); ?>" height="<?php echo esc_attr( $client_logo['height'] ?? '' ); ?>" alt="<?php echo esc_attr( $client_logo['alt'] ?? '' ); ?>" loading="lazy">
						<div class="<?php echo $block; ?>__overlay">
							<p><?php echo esc_html( $client ); ?></p>
						</div>
					</a>
					<?php
			}
		}
		?>
	</div>
	<?php if ( $view_all_link ) { ?>
		<div class="block-footer">
			<a href="<?php echo esc_url( $view_all_link['url'] ); ?>">View all...</a>
		</div>
	<?php } ?>
</section>
