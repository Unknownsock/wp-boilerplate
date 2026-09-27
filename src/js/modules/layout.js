function modal() {
    const overlay = document.querySelectorAll('.js-modal .js-modal-overlay');
    const buttons = document.querySelectorAll('.js-modal-trigger');
    const close = document.querySelectorAll('.js-modal-close');

    let previouslyFocusedElement = null;

    const focusableElements =
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

    function openModal(modal) {
        previouslyFocusedElement = document.activeElement;

        const html = document.querySelectorAll('html');

        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-active');
        modal.style.display = 'flex';
        html[0].classList.add('is-scroll-locked');

        const focusableContent = Array.from(
            modal.querySelectorAll(focusableElements)
        );

        focusableContent.forEach((element) => {
            element.removeAttribute('tabindex');
        });

        if (focusableContent.length > 0) {
            setTimeout(() => {
                focusableContent[0].focus();
            }, 50);
        }

        document.addEventListener('keydown', handleKeyDown);

        // Hides the rest of the page from screen readers while the modal is open
        document
            .querySelectorAll('body > *:not(.js-modal):not(script):not(link)')
            .forEach((element) => {
                if (element.getAttribute('aria-hidden') !== 'true') {
                    element.setAttribute(
                        'data-original-aria-hidden',
                        element.getAttribute('aria-hidden') || 'false'
                    );
                    element.setAttribute('aria-hidden', 'true');
                }
            });
    }

    function closeModal(modal) {
        const html = document.querySelectorAll('html');

        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('is-active');
        modal.style.display = 'none';
        html[0].classList.remove('is-scroll-locked');

        const focusableContent = Array.from(
            modal.querySelectorAll(focusableElements)
        );
        focusableContent.forEach((element) => {
            element.setAttribute('tabindex', '-1');
        });

        document.removeEventListener('keydown', handleKeyDown);

        document
            .querySelectorAll('[data-original-aria-hidden]')
            .forEach((element) => {
                const originalValue = element.getAttribute(
                    'data-original-aria-hidden'
                );
                if (originalValue === 'false') {
                    element.removeAttribute('aria-hidden');
                } else {
                    element.setAttribute('aria-hidden', originalValue);
                }
                element.removeAttribute('data-original-aria-hidden');
            });

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

        if (e.key === 'Escape') {
            closeModal(modal);
            return;
        }

        if (e.key !== 'Tab') return;

        const focusableContent = Array.from(
            modal.querySelectorAll(focusableElements)
        );
        if (focusableContent.length === 0) return;

        const firstFocusableElement = focusableContent[0];
        const lastFocusableElement =
            focusableContent[focusableContent.length - 1];

        // Wraps focus around instead of letting Tab escape the modal
        if (e.shiftKey && document.activeElement === firstFocusableElement) {
            e.preventDefault();
            lastFocusableElement.focus();
        } else if (
            !e.shiftKey &&
            document.activeElement === lastFocusableElement
        ) {
            e.preventDefault();
            firstFocusableElement.focus();
        }
    }

    function toggleModal(element) {
        element.forEach((el) => {
            el.addEventListener('click', (e) => {
                modalComponents(e);
            });
        });
    }

    toggleModal(overlay);
    toggleModal(buttons);
    toggleModal(close);

    const modal = document.querySelector('.js-modal');
    if (modal && modal.getAttribute('aria-hidden') === 'true') {
        const initialFocusableContent =
            modal.querySelectorAll(focusableElements);
        Array.from(initialFocusableContent).forEach((element) => {
            element.setAttribute('tabindex', '-1');
        });
    }
}
window.addEventListener('DOMContentLoaded', modal);
