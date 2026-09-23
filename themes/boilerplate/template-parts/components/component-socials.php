<?php
/**
 * Component: social media links.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Repeater - one row per platform, editor-chosen icon + link rather
	// than a fixed list of platform fields.
	$social_media = get_field( 'social_media', 'option' );

?>

<div class="c-social">
	<?php if ( $social_media ) : ?>
		<?php foreach ( $social_media as $row ) : ?>
			<?php
			$icon = $row['icon'] ?? null;
			$link = $row['link'] ?? null;
			$url  = $link['url'] ?? '';
			if ( ! $icon || ! $url ) {
				continue;
			}
			?>
			<a class="c-social__link" href="<?php echo esc_url( $url ); ?>" target="<?php echo esc_attr( $link['target'] ?: '_blank' ); ?>" aria-label="<?php echo esc_attr( $link['title'] ?: 'Social link' ); ?>">
				<img class="c-social__icon" src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ?? '' ); ?>" width="14" height="15" loading="lazy">
			</a>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
