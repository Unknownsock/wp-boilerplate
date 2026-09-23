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

        if (footerRect.top <= windowHeight) {
            socialSticky.classList.add('active');
        } else {
            socialSticky.classList.remove('active');
        }
    }

    window.addEventListener('scroll', checkFooterVisibility);
    checkFooterVisibility();
}
socialStickyFooterVisibility();
