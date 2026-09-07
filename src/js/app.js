// var $ = require("jquery");
// require("jquery/package.json");

// JS
import './modules/functions.js';

import './modules/hero.js';
import './modules/swiper.js';
import './modules/navigation.js';
import './modules/blocks.js';
import './modules/components.js';
import './modules/forms.js';
// import './modules/instagram.js';
// import './modules/accessibility.js';
import './modules/layout.js';
import './modules/heading-fullstop.js';
import './modules/strong-to-span.js';

// FAQ schema (seo.js) used to be generated client-side and appended to
// <head> after load - moved server-side, see freckle_add_faq_schema() in
// includes/asset-management.php, which is more reliable (works without JS
// executing at all) and is simpler than the round-trip this replaced.
//
// GA + Pinterest used to load here too (modules/cookies.js), gated behind
// a theme-built cookie banner - removed along with that banner now that a
// cookie-consent plugin handles this instead. Nothing currently loads
// Google Analytics or the Pinterest tag anywhere in the theme; that's on
// you to confirm is intentional (plugin owns it entirely) rather than a
// gap - see the chat for the two ways this usually goes.

// Note: wysiwyg.js is NOT imported here - it's its own Vite
// entry, enqueued conditionally by freckle_enqueue_block_script() only on
// pages that use that block. See includes/asset-management.php.

// Stylesheet
import '/src/sass/style.scss';

// import 'swiper/css/navigation';
// import 'swiper/css/pagination';
