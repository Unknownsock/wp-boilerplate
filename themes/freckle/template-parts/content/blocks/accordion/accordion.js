// Scoped JS for the accordion block: toggles one panel open at a time,
// animating height via max-height (see _accordion.scss) and keeping
// `hidden`/aria-expanded in sync so closed panels are genuinely out of the
// tab order (not just visually collapsed) once the close transition ends.
//
// Also pulls in this block's own stylesheet - see the note at the top of
// src/sass/style.scss's (removed) block imports section.
import './_accordion.scss';

function closePanel(trigger, panel) {
    trigger.setAttribute('aria-expanded', 'false');

    // Set an explicit starting height (from "auto") so the transition to 0
    // has something to animate from, then collapse on the next frame.
    panel.style.maxHeight = panel.scrollHeight + 'px';
    requestAnimationFrame(() => {
        panel.style.maxHeight = '0px';
    });

    panel.addEventListener(
        'transitionend',
        () => {
            if (trigger.getAttribute('aria-expanded') === 'false') {
                panel.hidden = true;
            }
        },
        { once: true }
    );
}

function openPanel(trigger, panel) {
    trigger.setAttribute('aria-expanded', 'true');
    panel.hidden = false;

    // Read scrollHeight only after `hidden` is cleared - a hidden element
    // reports 0.
    const targetHeight = panel.scrollHeight;
    panel.style.maxHeight = '0px';
    requestAnimationFrame(() => {
        panel.style.maxHeight = targetHeight + 'px';
    });

    panel.addEventListener(
        'transitionend',
        () => {
            if (trigger.getAttribute('aria-expanded') === 'true') {
                // Let the panel grow/reflow freely once open (e.g. if a
                // button wraps to a new line on resize) instead of staying
                // pinned to the height captured at open time.
                panel.style.maxHeight = 'none';
            }
        },
        { once: true }
    );
}

function initAccordion(block) {
    const list = block.querySelector('.b-accordion__list');
    const image = block.querySelector('.js-accordion-image');
    if (!list) return;

    const items = Array.from(list.querySelectorAll(':scope > .b-accordion__item'));

    function setImage(trigger) {
        if (!image) return;
        const src = trigger.getAttribute('data-image');
        const alt = trigger.getAttribute('data-image-alt') || '';
        if (src && image.getAttribute('src') !== src) {
            image.setAttribute('src', src);
            image.setAttribute('alt', alt);
        }
    }

    items.forEach((item) => {
        const trigger = item.querySelector('.b-accordion__trigger');
        const panel = item.querySelector('.b-accordion__panel');
        if (!trigger || !panel) return;

        // The first item renders pre-opened (see block-accordion.php) so
        // there's real content in the DOM with no JS - give its panel a
        // real starting height instead of the CSS default max-height: 0,
        // which would otherwise clip it shut until first interaction.
        if (trigger.getAttribute('aria-expanded') === 'true') {
            panel.style.maxHeight = 'none';
        }

        trigger.addEventListener('click', () => {
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';

            items.forEach((otherItem) => {
                if (otherItem === item) return;
                const otherTrigger = otherItem.querySelector('.b-accordion__trigger');
                const otherPanel = otherItem.querySelector('.b-accordion__panel');
                if (otherTrigger && otherPanel) closePanel(otherTrigger, otherPanel);
            });

            if (isOpen) {
                closePanel(trigger, panel);
            } else {
                openPanel(trigger, panel);
                setImage(trigger);
            }
        });
    });
}

document.querySelectorAll('.b-accordion').forEach(initAccordion);
