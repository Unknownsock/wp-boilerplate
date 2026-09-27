<?php
/**
 * Header template part: home page hero.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();

// singles.
$hero_options = get_field( 'hero_options' ) ?? array();
$eyebrow      = $hero_options['eyebrow'] ?? null;
$text         = $hero_options['text'] ?? null;
$button_group = $hero_options['button_group'] ?? null;
$media_type   = $hero_options['media_type'] ?? 'image';
$image        = 'video' === $media_type ? null : ( $hero_options['image'] ?? null );
$video        = 'video' === $media_type ? ( $hero_options['video'] ?? array() ) : array();

get_header();
?>

<section class="o-hero o-hero--home">
	<div class="o-hero__panel">
		<div class="o-hero__intro">
			<?php if ( $eyebrow ) { ?>
				<p class="c-eyebrow c-eyebrow--default"><?php echo esc_html( $eyebrow ); ?></p>
			<?php } ?>
			<?php if ( $text ) { ?>
				<div class="o-hero__text"><?php echo $text; ?></div>
			<?php } ?>
			<?php if ( $button_group ) { ?>
				<div class="o-hero__actions">
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
	</div>
	<div class="o-hero__media">
		<div class="o-hero__image">
			<?php
			// Resolve the video's actual playable source up front so the
			// video markup below only renders once there's actually
			// something playable - "Video" selected with the relevant
			// field (upload/YouTube URL/external URL) left empty renders
			// nothing rather than a broken player.
			$video_source = $video['video_source'] ?? 'upload';
			$youtube_id   = 'youtube' === $video_source ? get_youtube_id( $video['youtube_url'] ?? '' ) : null;
			$video_src    = 'external' === $video_source
				? ( $video['external_video_url'] ?? '' )
				: ( $video['video_file']['url'] ?? '' );
			$has_video    = $youtube_id || ( in_array( $video_source, array( 'upload', 'external' ), true ) && $video_src );
			?>
			<?php if ( 'video' === $media_type && $has_video ) : ?>
				<?php
				$poster   = $video['poster'] ?? null;
				$autoplay = ! empty( $video['autoplay'] );
				$muted    = ! empty( $video['muted'] );
				$loop     = ! empty( $video['loop'] );
				$controls = ! empty( $video['controls'] );
				?>
				<?php if ( $youtube_id ) : ?>
					<?php
					// autoplay only works muted, and looping a single video
					// via the API requires playlist=<same id>.
					// cc_load_policy=0 stops YouTube force-showing captions on
					// this decorative embed (default is "off unless the
					// viewer turned them on last time" - explicit 0 pins it).
					$embed_params = array(
						'autoplay'       => $autoplay ? 1 : 0,
						'mute'           => $muted ? 1 : 0,
						'loop'           => $loop ? 1 : 0,
						'controls'       => $controls ? 1 : 0,
						'playsinline'    => 1,
						'rel'            => 0,
						'cc_load_policy' => 0,
					);

					if ( $loop ) {
						$embed_params['playlist'] = $youtube_id;
					}
					$embed_url = 'https://www.youtube-nocookie.com/embed/' . $youtube_id . '?' . http_build_query( $embed_params );
					?>
					<iframe class="o-hero__video-embed" src="<?php echo esc_url( $embed_url ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
				<?php else : ?>
					<video class="o-hero__video-el"
						<?php echo $poster ? 'poster="' . esc_url( $poster['url'] ) . '"' : ''; ?>
						<?php echo $autoplay ? 'autoplay' : ''; ?>
						<?php echo $muted ? 'muted' : ''; ?>
						<?php echo $loop ? 'loop' : ''; ?>
						<?php echo $controls ? 'controls' : ''; ?>
						playsinline
						preload="metadata">
						<source src="<?php echo esc_url( $video_src ); ?>">
					</video>
				<?php endif; ?>
			<?php elseif ( $image ) : ?>
				<picture>
					<source media="(max-width: 767px)"
						srcset="<?php echo esc_url( $image['sizes']['gallery-mobile'] ); ?>"
						width="<?php echo esc_attr( $image['sizes']['gallery-mobile-width'] ); ?>"
						height="<?php echo esc_attr( $image['sizes']['gallery-mobile-height'] ); ?>"
						fetchpriority="high">
					<source media="(min-width: 768px)"
						srcset="<?php echo esc_url( $image['sizes']['gallery'] ); ?>"
						width="<?php echo esc_attr( $image['sizes']['gallery-width'] ); ?>"
						height="<?php echo esc_attr( $image['sizes']['gallery-height'] ); ?>"
						fetchpriority="high">
					<img src="<?php echo esc_url( $image['sizes']['gallery'] ); ?>"
						width="<?php echo esc_attr( $image['sizes']['gallery-width'] ); ?>"
						height="<?php echo esc_attr( $image['sizes']['gallery-height'] ); ?>"
						alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
						fetchpriority="high">
				</picture>
			<?php endif; ?>
		</div>
	</div>
</section>
