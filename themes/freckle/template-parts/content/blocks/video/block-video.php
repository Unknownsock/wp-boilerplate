<?php
/**
 * Content block: video.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$video_type      = $content['video_type'] ?? null;
	$video_ratio     = $content['video_ratio'] ?? null;
	$video_placement = $content['video_placement'] ?? null;
	$wysiwyg         = $content['wysiwyg'] ?? null;
	$button_group    = $content['button_group'] ?? null;
	$video_url       = $content['video_url'] ?? null;
	$video_file      = $content['video_file'] ?? null;
	$video_poster    = $content['video_poster'] ?? null;

	$autoplay = in_array( $content['autoplay'] ?? null, array( 1, '1', true, 'true' ), true );
	$loop     = in_array( $content['loop'] ?? null, array( 1, '1', true, 'true' ), true );
	$muted    = in_array( $content['muted'] ?? null, array( 1, '1', true, 'true' ), true );
	$controls = ! array_key_exists( 'controls', $content ) || in_array( $content['controls'], array( 1, '1', true, 'true' ), true );

	$is_portrait = 'portrait' === $video_ratio;
	$is_centered = 'video-center' === $video_placement;

	$classes = implode(
		' ',
		array_filter(
			array(
				$block,
				'white',
				$block . '--' . ( $is_portrait ? 'portrait' : 'landscape' ),
				$is_portrait && $is_centered ? $block . '--centered' : null,
			)
		)
	);

	$embed = freckle_render_video_embed(
		array(
			'video_type'   => $video_type,
			'video_url'    => $video_url,
			'video_file'   => $video_file,
			'video_poster' => $video_poster,
			'autoplay'     => $autoplay,
			'loop'         => $loop,
			'muted'        => $muted,
			'controls'     => $controls,
			'portrait'     => $is_portrait,
		)
	);
	?>
<section class="<?php echo esc_attr( $classes ); ?>">
	<?php if ( $embed ) : ?>
		<div class="<?php echo esc_attr( $block ); ?>__media">
			<?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built entirely from freckle_render_video_embed(), which escapes its own attributes. ?>
		</div>
	<?php endif; ?>

	<?php if ( $is_portrait && ! $is_centered && $wysiwyg ) : ?>
		<div class="<?php echo esc_attr( $block ); ?>__body">
			<?php echo wp_kses_post( $wysiwyg ); ?>
			<?php if ( $button_group ) : ?>
				<?php
				get_template_part(
					'template-parts/components/component',
					'button-group',
					array(
						'button_group' => $button_group,
					)
				);
				?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
