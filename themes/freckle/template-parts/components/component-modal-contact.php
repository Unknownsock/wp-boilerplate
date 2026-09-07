<?php
/**
 * Component: contact modal.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="contact-modal" class="o-modal js-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title" style="display: none;">
	<div class="o-modal__overlay js-modal-overlay"></div>
	<div class="o-modal__panel">
		<button class="o-modal__close js-modal-close" aria-label="Close modal" tabindex="-1">
			<svg width="53" height="52" viewBox="0 0 53 52" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L50.5 50.5" stroke="black" stroke-width="3" stroke-linecap="round"/><path d="M51 2L2.5 50.5" stroke="black" stroke-width="3" stroke-linecap="round"/></svg>
		</button>
		<div class="o-modal__intro">
			<h3>Get in Touch</h3>
		</div>
		<div class="o-modal__form">
			<?php echo do_shortcode( '[contact-form-7 id="8a086d2" title="Contact form"]' ); ?>
		</div>
	</div>
</div>
