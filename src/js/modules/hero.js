import throttle from 'lodash.throttle';

//
// Home hero - slight parallax on the intro content as the page scrolls.
// Shape rotation is handled by the shared [data-rotate-scroll] handler
// in modules/layout.js via data attributes on the markup.
//
const heroParallaxMq = window.matchMedia('(max-width: 768px)');

function heroContentParallax() {
	const content = document.querySelector('.o-hero--home .o-hero__intro');
	if (!content) {
		return;
	}

	// Parallax is disabled on mobile - reset any transform and bail.
	if (heroParallaxMq.matches) {
		content.style.transform = '';
		return;
	}

	const offset = window.pageYOffset * 0.12;
	content.style.transform = `translateY(${offset}px)`;
}
window.addEventListener('load', heroContentParallax);
window.addEventListener('scroll', throttle(heroContentParallax, 10));
