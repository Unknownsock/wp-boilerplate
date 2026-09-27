// Scoped JS for the listing block.
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_listing.scss';

import Swiper from 'swiper';

const swiper_related = () => {
	new Swiper('.js-swiper-related', {
		loop: true,
		speed: 500,
		spaceBetween: 30,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 2,
			},
			768: {
				slidesPerView: 3,
			},
			1024: {
				slidesPerView: 4,
			},
		},
		disableOnInteraction: true,
	});
};
window.addEventListener('DOMContentLoaded', swiper_related);
