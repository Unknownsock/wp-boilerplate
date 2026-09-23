function modal() {
	const overlay = document.querySelectorAll('.js-modal .js-modal-overlay');
	const buttons = document.querySelectorAll('.js-modal-trigger');
	const close = document.querySelectorAll('.js-modal-close');

	// Store the element that had focus before the modal was opened
    let previouslyFocusedElement = null;

	// All the elements inside the modal that can receive focus
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

	function openModal(modal) {
		// Save current focus position to return to later
        previouslyFocusedElement = document.activeElement;

		const html = document.querySelectorAll('html');

		// Show the modal
		modal.setAttribute('aria-hidden', 'false');
		modal.classList.add('is-active');
		modal.style.display = 'flex';
		html[0].classList.add("is-scroll-locked");

		// Get all focusable elements within the modal
        const focusableContent = Array.from(modal.querySelectorAll(focusableElements));

        // Enable all focusable elements
        focusableContent.forEach(element => {
            element.removeAttribute('tabindex');
        });

        // Set focus to first focusable element (typically close button)
        if (focusableContent.length > 0) {
            setTimeout(() => {
                focusableContent[0].focus();
            }, 50);
        }

		// Add keyboard listener
		document.addEventListener('keydown', handleKeyDown);

		// Mark the rest of the page as hidden from screen readers
        document.querySelectorAll('body > *:not(.js-modal):not(script):not(link)').forEach(element => {
            if (element.getAttribute('aria-hidden') !== 'true') {
                element.setAttribute('data-original-aria-hidden', element.getAttribute('aria-hidden') || 'false');
                element.setAttribute('aria-hidden', 'true');
            }
        });
	}

	function closeModal(modal) {
		const html = document.querySelectorAll('html');

		// Hide the modal
		modal.setAttribute('aria-hidden', 'true');
		modal.classList.remove('is-active');
		modal.style.display = 'none';
		html[0].classList.remove("is-scroll-locked");

		// Disable all focusable elements while modal is hidden
		const focusableContent = Array.from(modal.querySelectorAll(focusableElements));
        focusableContent.forEach(element => {
            element.setAttribute('tabindex', '-1');
        });

		// Remove keyboard listener
		document.removeEventListener('keydown', handleKeyDown);

		// Restore original aria-hidden values to the rest of the page
        document.querySelectorAll('[data-original-aria-hidden]').forEach(element => {
            const originalValue = element.getAttribute('data-original-aria-hidden');
            if (originalValue === 'false') {
                element.removeAttribute('aria-hidden');
            } else {
                element.setAttribute('aria-hidden', originalValue);
            }
            element.removeAttribute('data-original-aria-hidden');
        });

        // Return focus to the element that opened the modal
        if (previouslyFocusedElement) {
            previouslyFocusedElement.focus();
        }
	}

	function modalComponents(e) {
		e.preventDefault();
		const modal = document.querySelector('.js-modal');
		const value = modal.getAttribute('aria-hidden');

		if (value === 'true') {
			openModal(modal);
		} else {
			closeModal(modal);
		}
	}

	function handleKeyDown(e) {
        const modal = document.querySelector('.js-modal');
        if (!modal || modal.getAttribute('aria-hidden') === 'true') return;

        // Close on escape key
        if (e.key === 'Escape') {
            closeModal(modal);
            return;
        }

        // Focus trapping logic
        if (e.key !== 'Tab') return;

        const focusableContent = Array.from(modal.querySelectorAll(focusableElements));
        if (focusableContent.length === 0) return;

        const firstFocusableElement = focusableContent[0];
        const lastFocusableElement = focusableContent[focusableContent.length - 1];

        // Shift+Tab from first element should wrap to last
        if (e.shiftKey && document.activeElement === firstFocusableElement) {
            e.preventDefault();
            lastFocusableElement.focus();
        }
        // Tab from last element should wrap to first
        else if (!e.shiftKey && document.activeElement === lastFocusableElement) {
            e.preventDefault();
            firstFocusableElement.focus();
        }
    }

	function toggleModal(element) {
		element.forEach((el, i)=>{
			el.addEventListener('click', (e) => {
				modalComponents(e);
			});
		});
	}

	toggleModal(overlay);
	toggleModal(buttons);
	toggleModal(close);

	// Initialize - ensure all focusable elements are initially set with tabindex=-1
    const modal = document.querySelector('.js-modal');
    if (modal && modal.getAttribute('aria-hidden') === 'true') {
        const initialFocusableContent = modal.querySelectorAll(focusableElements);
        Array.from(initialFocusableContent).forEach(element => {
            element.setAttribute('tabindex', '-1');
        });
    }
}
window.addEventListener('DOMContentLoaded', modal);
