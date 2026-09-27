<?php
/**
 * Content block: team grid, sourced from the `team_member` CPT via a
 * relationship field so editors can pick/order who appears without a
 * flat repeater.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$text    = $content['text'] ?? null;
	$members = $content['team_members'] ?? null;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $text ) { ?>
		<div class="<?php echo $block; ?>__intro">
			<div class="<?php echo $block; ?>__text u-wysiwyg"><?php echo $text; ?></div>
		</div>
	<?php } ?>
	<?php if ( $members ) { ?>
		<div class="<?php echo $block; ?>__grid">
			<?php
			foreach ( $members as $member_index => $member ) {
				$member_id = $member->ID;
				$name      = get_the_title( $member_id );
				$role      = get_field( 'role', $member_id );
				$mode      = get_field( 'read_bio_mode', $member_id ) ?? 'modal';
				// Prefers the ACF "Bio" wysiwyg field over the post's normal
				// editor content, same reasoning as "Photo" below - falls
				// back to post_content if the field's left empty.
				$acf_bio = get_field( 'bio', $member_id );
				$bio     = $acf_bio ? $acf_bio : apply_filters( 'the_content', get_post_field( 'post_content', $member_id ) );
				// Prefers the ACF "Photo" field (a plain image field on the
				// post edit screen itself) over the Featured Image sidebar
				// widget, since editors kept missing the latter - falls
				// back to it if set, then to the boilerplate_image() placeholder.
				$acf_photo = get_field( 'photo', $member_id );
				$photo     = null;
				if ( ! empty( $acf_photo['url'] ) ) {
					$photo = '<img class="' . esc_attr( $block ) . '__photo" src="' . esc_url( $acf_photo['url'] ) . '" alt="' . esc_attr( $acf_photo['alt'] ?: $name ) . '" loading="lazy">';
				} elseif ( has_post_thumbnail( $member_id ) ) {
					$photo = get_the_post_thumbnail(
						$member_id,
						'medium',
						array(
							'class'   => $block . '__photo',
							'loading' => 'lazy',
						)
					);
				}
				$modal_id = 'team-modal-' . $member_id;
				?>
				<div class="<?php echo $block; ?>__item" data-scroll-animation="fadeIn" data-scroll-delay="<?php echo esc_attr( $member_index * 100 ); ?>">
					<div class="<?php echo $block; ?>__image">
						<?php if ( $photo ) { ?>
							<?php echo $photo; ?>
						<?php } else { ?>
							<?php $placeholder = boilerplate_image( null, 400, 400, wp_strip_all_tags( $name ?: 'Team member' ) ); ?>
							<img class="<?php echo $block; ?>__photo" src="<?php echo esc_url( $placeholder['url'] ); ?>" alt="<?php echo esc_attr( $placeholder['alt'] ); ?>" loading="lazy">
						<?php } ?>
					</div>
					<p class="<?php echo $block; ?>__name"><?php echo esc_html( $name ); ?></p>
					<?php if ( $role ) { ?>
						<p class="<?php echo $block; ?>__role"><?php echo esc_html( $role ); ?></p>
					<?php } ?>
					<?php if ( 'modal' === $mode && $bio ) { ?>
						<button type="button" class="c-button c-button--outline c-button--orange <?php echo $block; ?>__cta js-team-modal-trigger" data-modal-target="<?php echo esc_attr( $modal_id ); ?>">
							<span class="c-button__inner">Read Bio</span>
						</button>
					<?php } elseif ( 'page' === $mode || 'modal' === $mode ) { ?>
						<?php // "Modal" mode with no bio content falls back to linking the member's own page instead of silently hiding the button. ?>
						<a class="c-button c-button--outline c-button--orange <?php echo $block; ?>__cta" href="<?php echo esc_url( get_permalink( $member_id ) ); ?>">
							<span class="c-button__inner">Read Bio</span>
						</a>
					<?php } ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		// Modals rendered outside .o-team__grid (position: fixed, so they
		// don't need to be grid children - keeping them out avoids them
		// ever being counted as grid tracks).
		foreach ( $members as $member ) {
			$member_id = $member->ID;
			$mode      = get_field( 'read_bio_mode', $member_id ) ?? 'modal';
			if ( 'modal' !== $mode ) {
				continue;
			}
			$name    = get_the_title( $member_id );
			$role    = get_field( 'role', $member_id );
			$acf_bio = get_field( 'bio', $member_id );
			$bio     = $acf_bio ? $acf_bio : apply_filters( 'the_content', get_post_field( 'post_content', $member_id ) );
			if ( ! $bio ) {
				continue;
			}
			// Same "Photo" field / Featured Image / placeholder fallback as
			// the grid item above.
			$acf_photo = get_field( 'photo', $member_id );
			$photo     = null;
			if ( ! empty( $acf_photo['url'] ) ) {
				$photo = '<img class="' . esc_attr( $block ) . '__modal-photo" src="' . esc_url( $acf_photo['url'] ) . '" alt="' . esc_attr( $acf_photo['alt'] ?: $name ) . '" loading="lazy">';
			} elseif ( has_post_thumbnail( $member_id ) ) {
				$photo = get_the_post_thumbnail(
					$member_id,
					'medium',
					array(
						'class'   => $block . '__modal-photo',
						'loading' => 'lazy',
					)
				);
			} else {
				$placeholder = boilerplate_image( null, 400, 400, wp_strip_all_tags( $name ?: 'Team member' ) );
				$photo       = '<img class="' . esc_attr( $block ) . '__modal-photo" src="' . esc_url( $placeholder['url'] ) . '" alt="' . esc_attr( $placeholder['alt'] ) . '" loading="lazy">';
			}
			$modal_id = 'team-modal-' . $member_id;
			?>
			<div id="<?php echo esc_attr( $modal_id ); ?>" class="o-modal o-modal--team js-team-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $modal_id ); ?>-title" style="display: none;">
				<div class="o-modal__overlay js-team-modal-close"></div>
				<div class="o-modal__panel">
					<button type="button" class="o-modal__close js-team-modal-close" aria-label="Close">
						<svg width="53" height="52" viewBox="0 0 53 52" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2 2L50.5 50.5" stroke="black" stroke-width="3" stroke-linecap="round"/><path d="M51 2L2.5 50.5" stroke="black" stroke-width="3" stroke-linecap="round"/></svg>
					</button>
					<div class="<?php echo $block; ?>__modal-image">
						<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() above. ?>
					</div>
					<div class="<?php echo $block; ?>__modal-content">
						<div class="o-modal__intro">
							<h3 id="<?php echo esc_attr( $modal_id ); ?>-title"><?php echo esc_html( $name ); ?></h3>
							<?php if ( $role ) { ?>
								<p><?php echo esc_html( $role ); ?></p>
							<?php } ?>
						</div>
						<div class="<?php echo $block; ?>__bio u-wysiwyg"><?php echo $bio; ?></div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	<?php } ?>
</section>
