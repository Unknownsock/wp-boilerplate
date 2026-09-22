// Scoped modal handling for the team block's "Read Bio" buttons.
//
// Independent from the sitewide .js-modal singleton in
// src/js/modules/layout.js (that one assumes exactly one modal on the
// page via querySelector) - this block can render several modals at
// once, one per team member, addressed by id via data-modal-target.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_team.scss';

function initTeamModals() {
	const triggers = document.querySelectorAll( '.js-team-modal-trigger' );
	if ( ! triggers.length ) {
		return;
	}

	let previouslyFocused = null;
	const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

	function openModal( modal ) {
		previouslyFocused = document.activeElement;
		modal.setAttribute( 'aria-hidden', 'false' );
		modal.classList.add( 'is-active' );
		modal.style.display = 'flex';
		document.documentElement.classList.add( 'is-scroll-locked' );

		const focusable = modal.querySelectorAll( focusableSelector );
		if ( focusable.length ) {
			setTimeout( () => focusable[ 0 ].focus(), 50 );
		}
		document.addEventListener( 'keydown', handleKeyDown );
	}

	function closeModal( modal ) {
		modal.setAttribute( 'aria-hidden', 'true' );
		modal.classList.remove( 'is-active' );
		modal.style.display = 'none';
		document.documentElement.classList.remove( 'is-scroll-locked' );
		document.removeEventListener( 'keydown', handleKeyDown );
		if ( previouslyFocused ) {
			previouslyFocused.focus();
		}
	}

	function activeModal() {
		return document.querySelector( '.js-team-modal.is-active' );
	}

	function handleKeyDown( e ) {
		const modal = activeModal();
		if ( ! modal ) {
			return;
		}
		if ( e.key === 'Escape' ) {
			closeModal( modal );
			return;
		}
		if ( e.key !== 'Tab' ) {
			return;
		}
		const focusable = Array.from( modal.querySelectorAll( focusableSelector ) );
		if ( ! focusable.length ) {
			return;
		}
		const first = focusable[ 0 ];
		const last = focusable[ focusable.length - 1 ];
		if ( e.shiftKey && document.activeElement === first ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && document.activeElement === last ) {
			e.preventDefault();
			first.focus();
		}
	}

	triggers.forEach( ( trigger ) => {
		trigger.addEventListener( 'click', () => {
			const targetId = trigger.getAttribute( 'data-modal-target' );
			const modal = targetId ? document.getElementById( targetId ) : null;
			if ( modal ) {
				openModal( modal );
			}
		} );
	} );

	document.querySelectorAll( '.js-team-modal-close' ).forEach( ( closeEl ) => {
		closeEl.addEventListener( 'click', () => {
			const modal = closeEl.closest( '.js-team-modal' );
			if ( modal ) {
				closeModal( modal );
			}
		} );
	} );
}

window.addEventListener( 'DOMContentLoaded', initTeamModals );
