// Scoped JS for the before-after block: drag/touch/keyboard slider that
// reveals more or less of the "before" image over the "after" image.
//
// Also pulls in this block's own stylesheet - see enqueue_block_assets()
// in includes/asset-management.php.
import './_before-after.scss';

function initBeforeAfterSlider(root) {
    const slider = root.querySelector('.b-before-after__content');
    const before = root.querySelector('.b-before-after__before');
    const resizer = root.querySelector('.b-before-after__resizer');

    if (!slider || !before || !resizer) {
        console.warn('before-after: required elements not found, aborting init.');
        return;
    }

    const beforeImage = before.querySelector('img');
    if (!beforeImage) {
        console.warn('before-after: image inside .b-before-after__before not found, aborting init.');
        return;
    }

    let active = false;

    function setInitialWidth() {
        beforeImage.style.width = slider.offsetWidth + 'px';
    }
    setInitialWidth();
    window.addEventListener('resize', setInitialWidth);

    function slideIt(x) {
        const width = slider.offsetWidth;
        const clamped = Math.max(0, Math.min(x, width));
        before.style.width = clamped + 'px';
        resizer.style.left = clamped + 'px';
        resizer.setAttribute('aria-valuenow', String(Math.round((clamped / width) * 100)));
    }

    function pauseEvent(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function handlePointerDown() {
        active = true;
        resizer.classList.add('resize');
    }
    resizer.addEventListener('mousedown', handlePointerDown);
    resizer.addEventListener('touchstart', handlePointerDown);

    function handlePointerUp() {
        active = false;
        resizer.classList.remove('resize');
    }
    document.body.addEventListener('mouseup', handlePointerUp);
    document.body.addEventListener('mouseleave', handlePointerUp);
    document.body.addEventListener('touchend', handlePointerUp);
    document.body.addEventListener('touchcancel', handlePointerUp);

    document.body.addEventListener('mousemove', (e) => {
        if (!active) return;
        slideIt(e.pageX - slider.getBoundingClientRect().left);
        pauseEvent(e);
    });

    document.body.addEventListener(
        'touchmove',
        (e) => {
            if (!active) return;
            const touch = e.changedTouches[e.changedTouches.length - 1];
            slideIt(touch.pageX - slider.getBoundingClientRect().left);
            pauseEvent(e);
        },
        { passive: false }
    );

    // Keyboard equivalent for the drag-only handle - left/right (or
    // up/down) nudge the split point by 5% of the slider width per press.
    resizer.addEventListener('keydown', (e) => {
        const step = slider.offsetWidth * 0.05;
        const current = before.offsetWidth;

        if (['ArrowLeft', 'ArrowDown'].includes(e.key)) {
            slideIt(current - step);
            e.preventDefault();
        } else if (['ArrowRight', 'ArrowUp'].includes(e.key)) {
            slideIt(current + step);
            e.preventDefault();
        }
    });
}

document.querySelectorAll('.b-before-after').forEach(initBeforeAfterSlider);
