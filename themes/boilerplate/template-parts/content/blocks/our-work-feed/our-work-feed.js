// Scoped JS for the our-work-feed block.
// The shape's scroll-rotate effect is driven by the shared
// [data-rotate-scroll] handler in src/js/modules/layout.js
// (scrollRotate()), which works off a data attribute, not a class -
// no change needed there. Add block-specific behaviour here if needed.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_our-work-feed.scss';
