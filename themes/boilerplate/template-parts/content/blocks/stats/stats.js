// Scoped JS for the stats block.
// Counts each .js-stat-count up from 0 to its data-value once the
// hexagon scrolls into view. Prefix/suffix are static markup either
// side of .js-stat-value - only the number itself animates.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_stats.scss';

function formatNumber( value, useCommas ) {
	var rounded = Math.round( value );
	return useCommas ? rounded.toLocaleString( 'en-GB' ) : String( rounded );
}

function animateCount( el ) {
	var target = parseFloat( el.dataset.value ) || 0;
	var useCommas = el.dataset.commas === '1';
	var valueEl = el.querySelector( '.js-stat-value' );
	if ( ! valueEl ) {
		return;
	}

	var duration = 1500;
	var start = null;

	function step( timestamp ) {
		if ( start === null ) {
			start = timestamp;
		}
		var progress = Math.min( ( timestamp - start ) / duration, 1 );
		var eased = 1 - Math.pow( 1 - progress, 3 );
		valueEl.textContent = formatNumber( target * eased, useCommas );

		if ( progress < 1 ) {
			window.requestAnimationFrame( step );
		}
	}

	window.requestAnimationFrame( step );
}

function initStatsCounters() {
	var counters = document.querySelectorAll( '.js-stat-count' );
	if ( ! counters.length ) {
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					animateCount( entry.target );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.5 }
	);

	counters.forEach( function ( counter ) {
		observer.observe( counter );
	} );
}

document.addEventListener( 'DOMContentLoaded', initStatsCounters );
