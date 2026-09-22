// Scoped JS for the logos block.
// No behaviour needed - the marquee is a pure CSS animation (see
// _logos.scss). Swiper was dropped since autoplay-only was all this
// block ever used it for, and the prev/next nav it also supported
// wasn't in active use.
//
// Still pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_logos.scss';
