// Scoped JS for the testimonial-contact block.
// The .swiper-testimonial carousel still initialises via
// src/js/modules/swiper.js (swiper_testimonial()), and the .mail CTA
// opens the shared contact modal via src/js/modules/layout.js (modal()) -
// both are shared, sitewide behaviour, not moved here.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_testimonial-contact.scss';
