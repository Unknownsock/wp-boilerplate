// Scoped JS for the feature-grid block.
// No behaviour needed yet - add it here when the block requires it.
//
// This file exists purely so Vite treats it as its own entry (see
// vite.config.mjs findBlockEntries()) and can emit _feature-grid.scss as
// its own CSS chunk, loaded only on pages that use this block - see
// enqueue_block_assets() in includes/asset-management.php.
import './_feature-grid.scss';
