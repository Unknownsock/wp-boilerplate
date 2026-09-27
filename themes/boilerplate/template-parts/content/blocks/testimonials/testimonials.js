// Scoped JS for the testimonials block.
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_testimonials.scss';

import Swiper from 'swiper';
import { Autoplay, Pagination, EffectFade } from 'swiper/modules';

const swiper_testimonial = () => {
	document.querySelectorAll('.js-swiper-testimonial').forEach((slider) => {
		new Swiper(slider, {
			loop: true,
			spaceBetween: 150,
			speed: 300,
			autoplay: {
				delay: 5000,
			},
			effect: 'fade',
			fadeEffect: {
				crossFade: true,
			},
			modules: [Autoplay, Pagination, EffectFade],
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			},
			breakpoints: {
				0: {
					slidesPerView: 1,
				},
			},
		});
	});
};
window.addEventListener('DOMContentLoaded', swiper_testimonial);
