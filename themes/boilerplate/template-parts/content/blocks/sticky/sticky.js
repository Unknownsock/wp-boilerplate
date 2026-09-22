// Scoped JS for the sticky block.
// The scroll-scale effect for this block still lives in
// src/js/modules/layout.js (scrollScale()) since it's shared global
// scroll-listener plumbing - move it here if it should load only on
// pages that use this block.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_sticky.scss';
