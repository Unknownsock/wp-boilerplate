//
// Social Sticky Footer Visibility
//
function socialStickyFooterVisibility() {
    const footer = document.querySelector('.js-site-footer');
    const socialSticky = document.querySelector('.c-social--sticky');

    if (!footer || !socialSticky) {
        return;
    }

    function checkFooterVisibility() {
        const footerRect = footer.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        // Check if footer is in view (top of footer is visible)
        if (footerRect.top <= windowHeight) {
            socialSticky.classList.add('active');
        } else {
            socialSticky.classList.remove('active');
        }
    }

    // Check on scroll
    window.addEventListener('scroll', checkFooterVisibility);

    // Check on initial load
    checkFooterVisibility();
}
socialStickyFooterVisibility();
