// Editors bold text in headings using <strong>, but we want that styled
// via a class (u-strong) rather than relying on the browser's default
// bold rendering, so swap the tag on load.
function convertStrongToSpan(heading) {
    heading.querySelectorAll('strong').forEach((strong) => {
        const span = document.createElement('span');
        span.className = 'u-strong';
        span.innerHTML = strong.innerHTML;
        strong.replaceWith(span);
    });
}

function initStrongToSpan() {
    document.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach(convertStrongToSpan);
}

document.addEventListener('DOMContentLoaded', initStrongToSpan);
