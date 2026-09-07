// Scoped JS for the listing block.
// The carousel variant's Swiper instance (.swiper-related) still
// initialises via src/js/modules/swiper.js (swiper_related()), since
// Swiper is a shared dependency across several blocks. Move it here
// if this block should own its own conditionally-loaded copy.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_listing.scss';
