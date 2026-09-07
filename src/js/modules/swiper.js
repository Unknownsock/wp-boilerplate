import Swiper from 'swiper';

import { Autoplay, Pagination, Navigation, Thumbs, FreeMode, EffectFade } from 'swiper/modules';

// modules: [Pagination, Autoplay],

// import 'swiper/css';

const swiper_gallery = () => {
	// Find all thumbs sliders first and store their instances
	let thumbsSliders = document.querySelectorAll('.swiper-gallery-thumbs');
	let thumbsInstances = [];

	thumbsSliders.forEach((thumbsElement) => {
		const thumbsSwiper = new Swiper(thumbsElement, {
			loop: true,
			modules: [Autoplay, FreeMode],
			autoplay: {
				delay: 2500,
			},
			speed: 500,
			spaceBetween: 10,
			freeMode: true,
			watchSlidesProgress: true,
			disableOnInteraction: true,
			breakpoints: {
				0: {
					slidesPerView: 2,
				},
				480: {
					slidesPerView: 3,
				},
				768: {
					slidesPerView: 4,
				}
			},
		});
		thumbsInstances.push(thumbsSwiper);
	});

	// Now initialize main galleries
	let galleries = document.querySelectorAll('.swiper-gallery');
	galleries.forEach((gallery, index) => {
		const mainSwiper = new Swiper(gallery, {
			loop: true,
			spaceBetween: 10,
			modules: [Autoplay, Thumbs],
			autoplay: {
				delay: 2500,
			},
			speed: 500,
			thumbs: {
				swiper: thumbsInstances[index],
			},
			disableOnInteraction: true,
		});
	});
};
window.addEventListener('load', swiper_gallery);

const swiper_gallery_legacy = () => {
	var swiper2 = new Swiper(".swiper-gallery-legacy", {
		loop: true,
		modules: [Autoplay, Pagination],
		autoplay: {
			delay: 2500,
		},
		speed: 500,
		spaceBetween: 10,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
			}
		},
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
		disableOnInteraction: true,
	});
};
window.addEventListener('load', swiper_gallery_legacy);

const swiper_related = () => {
	var swiper = new Swiper(".js-swiper-related", {
		loop: true,
		// modules: [Autoplay],
		// autoplay: {
		// 	delay: 2500,
		// },
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
			// 2100: {
			// 	slidesPerView: 5,
			// }
		},
		disableOnInteraction: true,
	});
};
window.addEventListener('DOMContentLoaded', swiper_related);

const swiper_testimonial = () => {
	let swiper_slider = document.querySelectorAll('.js-swiper-testimonial');
	swiper_slider.forEach((slider, i) => {
		const swiper = new Swiper(slider, {
			loop: true,
			spaceBetween: 150,
			speed: 300,
			autoplay: {
				delay: 5000,
			},
			effect: "fade",
			fadeEffect: {
				crossFade: true,
			},
			modules: [Autoplay, Pagination, EffectFade],
			pagination: {
				el: ".swiper-pagination",
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

// The logos carousel now initialises itself - see
// template-parts/content/blocks/logos/logos.js.
