import { throttle } from 'lodash';

// var $ = require("jquery");
// require("jquery/package.json");

import { isMobile } from './functions.js';

function menu_toggle() {
	// Get the hamburger button
	const menu_toggle = document.querySelector(".js-menu-toggle");
	const html = document.querySelectorAll("html");
	const body = document.querySelectorAll("body");

	// Get the controlled navigation element using aria-controls
	const controlsId = menu_toggle.getAttribute('aria-controls');
	const mobileMenu = controlsId ? document.getElementById(controlsId) : document.getElementById('primary-menu');

	// If the mobileMenu doesn't exist, default to primary-menu ID
	const navigationMenu = mobileMenu || document.getElementById('primary-menu');

	// All focusable elements outside the menu
	const allElementsExceptMenu = document.querySelectorAll('a:not(.js-site-header a), button:not(.js-menu-toggle), input, select, textarea, [tabindex]:not([tabindex="-1"])');

	// Store the element that had focus before opening menu
	let previouslyFocusedElement = null;

	// Add keyboard event for Escape key handling
	function handleKeyDown(e) {
		if (e.key === 'Escape' && menu_toggle.getAttribute('aria-expanded') === 'true') {
			closeMenu();
		}
	}

	// Function to close the menu
	function closeMenu() {
		menu_toggle.setAttribute('aria-expanded', 'false');
		body[0].classList.remove("is-toggled");
		menu_toggle.classList.remove("is-toggled");
		html[0].classList.remove("is-scroll-locked");

		// Restore tabindex on elements outside the menu
		allElementsExceptMenu.forEach(element => {
			element.tabIndex = '0';
		});

		// Make the menu tabbable again
		navigationMenu.setAttribute('tabindex', '1');

		// Remove keyboard event listener when menu is closed
		document.removeEventListener('keydown', handleKeyDown);

		// Return focus to the menu toggle button
		menu_toggle.focus();
	}

	// Function to open the menu
	function openMenu() {
		// Save the currently focused element
		previouslyFocusedElement = document.activeElement;

		menu_toggle.setAttribute('aria-expanded', 'true');
		body[0].classList.add("is-toggled");
		menu_toggle.classList.add("is-toggled");
		html[0].classList.add("is-scroll-locked");

		// Make elements outside menu non-tabbable
		allElementsExceptMenu.forEach(element => {
			element.tabIndex = '-1';
		});

		// Make the menu tabbable
		navigationMenu.setAttribute('tabindex', '0');

		// Add keyboard event listener when menu is open
		document.addEventListener('keydown', handleKeyDown);

		// Focus the first focusable element in the menu after a short delay
		setTimeout(() => {
			const firstFocusableElement = navigationMenu.querySelector('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
			if (firstFocusableElement) {
				firstFocusableElement.focus();
			}
		}, 100);
	}

	menu_toggle.addEventListener('click', (e) => {
		e.preventDefault();

		var value = menu_toggle.getAttribute('aria-expanded');

		// Toggle the menu state
		if (value === 'true') {
			closeMenu();
		} else {
			openMenu();
		}

	});
}
// window.addEventListener('DOMContentLoaded', menu_toggle);
menu_toggle();

function menuDropdown() {

	// Mobile-only: matches the max-width breakpoint that .js-nav-dropdown
	// relies on in _navigation.scss ($grid-nav / 992px). Above this width
	// dropdowns open on hover/focus via CSS alone - the click-driven
	// max-height toggle below must not run there.
	const NAV_BREAKPOINT = '(max-width: 991px)';

	// const menuItems = document.querySelectorAll('.menu-item-has-children');
	const menuItems = document.querySelectorAll('#primary-menu > .menu-item');

	menuItems.forEach(el => {
		const dropdownMenu = el.querySelector('.js-nav-dropdown');
		const arrow = el.querySelector('.js-nav-arrow');

		if(dropdownMenu) {
			// Enter/Space on the arrow behaves like a real <button> click -
			// role="button" alone doesn't get that for free the way a native
			// <button> element would.
			arrow.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					arrow.click();
				}
			});

			// open dropdown menu on click + transforms
			arrow.addEventListener('click', (e) => {
				if (!window.matchMedia(NAV_BREAKPOINT).matches) {
					// Desktop: let CSS hover/focus handle the dropdown, don't
					// touch inline max-height or preventDefault the click.
					return;
				}

				e.preventDefault();

				// close any other open dropdown first, so only one is ever
				// animating open at a time
				menuItems.forEach(item => {
					const otherDropdownMenu = item.querySelector('ul');
					if(otherDropdownMenu) {
						if (otherDropdownMenu !== dropdownMenu) {
							otherDropdownMenu.style.maxHeight = '0px';
							otherDropdownMenu.setAttribute('aria-sub-expanded', 'false');
							item.classList.remove('is-active');
							const otherArrow = item.querySelector('.js-nav-arrow');
							if (otherArrow) {
								otherArrow.setAttribute('aria-expanded', 'false');
							}
						}
					}
				});

				var value = dropdownMenu.getAttribute('aria-sub-expanded');
				var aria = value === 'true' ? 'false' : 'true';

				dropdownMenu.setAttribute('aria-sub-expanded', aria);
				arrow.setAttribute('aria-expanded', aria);

				if (value === 'false') {
					// Opening: measure the natural height first (against
					// the current viewport/content), so max-height has a
					// real px target to animate to instead of 'none'
					// (which can't be interpolated by the transition).
					dropdownMenu.style.maxHeight = 'none';
					var height = dropdownMenu.getBoundingClientRect().height;
					dropdownMenu.style.maxHeight = '0';

					// Force a reflow so the browser commits '0' as the
					// actual starting value before it's changed again
					// below - otherwise 'none' -> '0' -> height all land
					// in the same synchronous batch and the
					// getBoundingClientRect() call above already flushed
					// 'none' as the last settled style, so the transition
					// jumps from 'none' straight to the final height
					// (not animatable) instead of animating from 0.
					void dropdownMenu.offsetHeight;

					dropdownMenu.style.maxHeight = height + 'px';
					el.classList.add('is-active');
					// Focus on the first link when the dropdown is opened
					const firstLink = el.querySelector('ul a');
					if (firstLink) {
						firstLink.focus();
					}
				} else {
					// Closing: animate straight from whatever px height is
					// currently set down to 0 - no 'none' remeasure step,
					// which would otherwise kill this transition the same
					// way it did the opening one.
					el.classList.remove('is-active');
					dropdownMenu.style.maxHeight = '0px';
				}
			});
		}
	});

	// // Event listener for mouseenter and mouseleave on menu items
	// menuItems.forEach(item => {
	// 	const menuItemLink = item.querySelector('a');

	// 	menuItemLink.addEventListener('click', event => {
	// 		event.stopPropagation(); // Prevent the click on the link from triggering the click outside handler
	// 	});
	// });

	// // Resize handler to recalculate height only for the active dropdown
	// window.addEventListener('resize', () => {
	// 	if (activeDropdown && activeDropdown.getAttribute('aria-sub-expanded') === 'true') {
	// 		activeDropdown.style.maxHeight = 'none';
	// 		const newHeight = activeDropdown.getBoundingClientRect().height;
	// 		activeDropdown.style.maxHeight = newHeight + 'px';
	// 	}
	// });
}
document.addEventListener('DOMContentLoaded', menuDropdown);

// Mega-menu CTA card - same fixed-height/growing-content hover lift as
// .c-card--large (see themes/imp/template-parts/content/blocks/cards/
// cards.js, which this mirrors): the card's own height is locked to its
// rest-state rendered height via --cta-height, and -reveal's hidden text
// grows open by exactly --cta-lift (its own natural, un-collapsed
// height) while -body translates up by that same amount (_navigation.scss)
// - so hovering never changes the CTA card's own footprint, only its
// contents move. Both vary per card (title/text length, viewport width),
// so they're measured here rather than hardcoded, and re-measured on
// resize since text reflows.
function ctaHoverLift() {
	const ctas = document.querySelectorAll('.o-primary-nav__dropdown-cta');

	if (!ctas.length) {
		return;
	}

	function measure() {
		ctas.forEach(cta => {
			const reveal = cta.querySelector('.o-primary-nav__dropdown-cta-reveal');
			if (!reveal) {
				return;
			}

			// Clear the pinned height first so this re-measures the
			// card's actual rest-state height after a resize/reflow,
			// rather than just reading back the value it pinned last
			// time - same reason cards.js momentarily un-fixes
			// .c-card__image's flex-basis before re-measuring it.
			cta.style.removeProperty('--cta-height');
			const restHeight = cta.getBoundingClientRect().height;
			cta.style.setProperty('--cta-height', restHeight + 'px');

			// scrollHeight, not getBoundingClientRect() - it reports the
			// element's full content height regardless of the max-height/
			// overflow:hidden currently restricting what's visible, so
			// there's no need to temporarily toggle max-height:none just
			// to measure it. This is -reveal's own natural height and
			// nothing else - not capped against the media or any other
			// element, so -body's hover translate (_navigation.scss)
			// always matches exactly what -reveal actually grows by.
			cta.style.setProperty('--cta-lift', reveal.scrollHeight + 'px');
		});
	}

	measure();
	window.addEventListener('load', measure);
	window.addEventListener('resize', throttle(measure, 100));
}
document.addEventListener('DOMContentLoaded', ctaHoverLift);

// Desktop dropdowns open on plain CSS :hover/:focus-within (see
// _navigation.scss), but the top-level <li> only hugs its own text height
// rather than spanning the header's full height, while its dropdown is a
// fixed-position panel anchored below the header. That leaves a visual gap
// between the two, so a straight-line cursor move from the link down into
// the dropdown crosses out of the <li>'s (and the dropdown's) hit area for
// a moment, which drops :hover and cuts the open transition short before
// it re-triggers. .is-nav-open bridges that: entering the <li> OR its
// dropdown adds the class immediately; leaving either schedules its
// removal after a short delay, cancelled if the cursor lands back on the
// <li> or dropdown before it fires - normal "hover intent" debouncing.
//
// The close is scheduled from .js-site-header itself (not each <li>), so
// crossing over other header chrome in between - the logo, the header
// actions - while a dropdown is open doesn't close it; only actually
// leaving the header (or its open dropdown, which is a fixed panel
// rendered outside the header's own box and so needs its own listeners)
// does. A plain nav item with no dropdown of its own is the one
// exception - hovering it closes an open dropdown immediately, same as
// switching to a different item that does have one.
function menuHoverDropdown() {
	const DESKTOP_QUERY = window.matchMedia(`(min-width: 992px)`);
	const header = document.querySelector('.js-site-header');
	const menuItems = document.querySelectorAll('#primary-menu > .menu-item');
	const CLOSE_DELAY = 200;
	let closeTimeout = null;

	if (!header) {
		return;
	}

	const cancelClose = () => {
		if (!DESKTOP_QUERY.matches) {
			return;
		}
		clearTimeout(closeTimeout);
	};

	const scheduleClose = () => {
		if (!DESKTOP_QUERY.matches) {
			return;
		}
		clearTimeout(closeTimeout);
		closeTimeout = setTimeout(() => {
			menuItems.forEach(item => item.classList.remove('is-nav-open'));
		}, CLOSE_DELAY);
	};

	const closeNow = () => {
		if (!DESKTOP_QUERY.matches) {
			return;
		}
		clearTimeout(closeTimeout);
		menuItems.forEach(item => item.classList.remove('is-nav-open'));
	};

	menuItems.forEach(item => {
		const dropdown = item.querySelector(':scope > .js-nav-dropdown');
		if (!dropdown) {
			// Plain item, no dropdown of its own - hovering it should close
			// whatever dropdown is currently open straight away, not wait
			// out the header's leave-debounce.
			item.addEventListener('mouseenter', closeNow);
			return;
		}

		item.addEventListener('mouseenter', () => {
			if (!DESKTOP_QUERY.matches) {
				return;
			}
			cancelClose();
			menuItems.forEach(other => {
				if (other !== item) {
					other.classList.remove('is-nav-open');
				}
			});
			item.classList.add('is-nav-open');
		});

		dropdown.addEventListener('mouseenter', cancelClose);
		dropdown.addEventListener('mouseleave', scheduleClose);
	});

	header.addEventListener('mouseenter', cancelClose);
	header.addEventListener('mouseleave', scheduleClose);
}
document.addEventListener('DOMContentLoaded', menuHoverDropdown);


// Transparent-over-hero header that swaps to a solid white background once
// the page has scrolled past a small threshold - .is-scrolled toggles the
// background/box-shadow in _header.scss. Threshold (not just scrollY > 0)
// avoids flicker from the trivial bounce/rubber-banding scroll some
// browsers report at the very top of the page.
function headerScrollState() {
	const header = document.querySelector('.js-site-header');
	const SCROLL_THRESHOLD = 50;

	if (!header) {
		return;
	}

	const setScrolledState = () => {
		header.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
	};

	setScrolledState();
	window.addEventListener('scroll', throttle(setScrolledState, 100));
}
document.addEventListener('DOMContentLoaded', headerScrollState);


// var lastScrollTop = 0;
// function header_check() {
// 	const header = document.querySelector(".site-header");
// 	const hero_height = document.querySelector(".hero").offsetHeight;
// 	const body = document.querySelectorAll("body");
// 	var scroll = window.pageYOffset,
// 		content = 1,
// 		st = scroll;

// 	// header.style.transition = '0ms';

// 	if (scroll >= hero_height/4) {
// 		body[0].classList.add("active-nav");
// 	}
// 	if (st > lastScrollTop) {
// 		// downscroll code
// 		if (scroll >= hero_height/4) {
// 			body[0].classList.add("active-nav");
// 		} else {
// 			// console.log('NAV');
// 			body[0].classList.remove("active-nav");
// 		}
// 	} else {
// 		// upscroll code
// 		if (scroll >= hero_height/4) {
// 			body[0].classList.add("active-nav");
// 		} else {
// 			body[0].classList.remove("active-nav");
// 		}
// 	}
// 	lastScrollTop = st;
// }
// window.addEventListener('load', header_check);
// document.addEventListener('scroll', header_check);


// Smooth scroll
// Handle anchor links
var anchorLinks = document.querySelectorAll('a[href*="#"]');
for (var i = 0; i < anchorLinks.length; i++) {
	var link = anchorLinks[i];
	if (link.getAttribute('href') !== '#' && link.getAttribute('href') !== '#0') {
		link.addEventListener('click', function(event) {
			// On-page links
			if (
				location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
				location.hostname == this.hostname
			) {
			// Figure out element to scroll to
			var target = document.querySelector(this.hash);
			target = target ? target : document.querySelector('[name=' + this.hash.slice(1) + ']');
			// Does a scroll target exist?
			if (target) {
				// Only prevent default if animation is actually gonna happen
				event.preventDefault();
				var scrollTop = target.getBoundingClientRect().top + window.pageYOffset - 150;
				var duration = 1000;
				var easing = function(t) {
				return t * (2 - t);
				};
				var start = window.pageYOffset;
				var time = 0;
				var animateScroll = function() {
				time += 16;
				var elapsed = time / duration;
				elapsed = elapsed > 1 ? 1 : elapsed;
				var eased = easing(elapsed);
				var newScrollTop = start + (scrollTop - start) * eased;
				window.scrollTo(0, newScrollTop);
				if (time < duration) {
					window.requestAnimationFrame(animateScroll);
				} else {
					// Callback after animation
					// Must change focus!
					var $target = $(target);
					$target.focus();
					if ($target.is(":focus")) { // Checking if the target was focused
					return false;
					} else {
					$target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
					$target.focus(); // Set focus again
					};
				}
				};
				animateScroll();
			}
			}
		});
	}
}
