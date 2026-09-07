// Replaces a trailing fullstop in any heading with a small hexagon
// glyph instead - editors just type "." as normal, no need to leave it
// out of the field. fill="currentColor" on the SVG means it inherits
// whatever colour the heading itself is set to, no JS colour logic needed.
const HEX_SVG =
    '<svg width="10" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg" class="c-heading-fullstop" aria-hidden="true"><path d="M9.91767 4.18711L8.43255 1.56454C8.43255 1.56454 8.43267 1.56454 8.43279 1.56454L7.72435 0.313084C7.61481 0.119541 7.41236 0.000252997 7.19317 0.000252997H3.23053L3.23016 0H2.80641C2.77936 0 2.75244 0.00202398 2.72565 0.00569244C2.72528 0.00569244 2.72478 0.00569244 2.72441 0.00581894C2.71188 0.00758992 2.69935 0.0097404 2.68695 0.0122704C2.68608 0.0123969 2.68521 0.0126499 2.68434 0.0127764C2.64663 0.0206193 2.60954 0.0320042 2.57381 0.0470575C2.56997 0.048702 2.56612 0.050473 2.56228 0.0521174C2.55732 0.0543944 2.55223 0.0564184 2.54739 0.0586954H2.54764C2.43525 0.112204 2.33924 0.199741 2.27523 0.312958L0.565577 3.33299L0.373548 3.67137L0.37392 3.67162L0.0819076 4.18736C-0.0276281 4.3809 -0.0276281 4.61935 0.0819076 4.81289L1.56703 7.43546C1.56703 7.43546 1.5669 7.43546 1.56678 7.43546L2.27523 8.68692C2.38476 8.88046 2.58721 8.99975 2.80641 8.99975H6.76904L6.76941 9H7.19317C7.22021 9 7.24713 8.99798 7.27392 8.99431C7.2743 8.99431 7.27479 8.99431 7.27517 8.99418C7.28769 8.99241 7.30022 8.99026 7.31263 8.98773C7.3135 8.9876 7.31436 8.98735 7.31523 8.98722C7.35294 8.97938 7.39004 8.968 7.42576 8.95294C7.42961 8.9513 7.43345 8.94953 7.4373 8.94788C7.44226 8.94561 7.44735 8.94358 7.45218 8.94131H7.45194C7.56432 8.8878 7.66034 8.80026 7.72435 8.68704L9.43412 5.66689L9.62603 5.32876L9.62565 5.3285L9.91767 4.81277C10.0272 4.61923 10.0272 4.38078 9.91767 4.18723V4.18711Z" fill="currentColor"/></svg>';

// True if the fullstop being converted sits directly against bold text,
// either inside a <strong>/<b> or immediately after one - walking back
// past only empty/whitespace text, since a real word in between (eg.
// "Zero outsourcing" after an earlier bold line) means it isn't bold.
// function isAgainstBold( textNode ) {
// 	if ( textNode.parentNode.nodeName === 'STRONG' || textNode.parentNode.nodeName === 'B' ) {
// 		return true;
// 	}
//
// 	let sibling = textNode.previousSibling;
// 	while ( sibling ) {
// 		if ( sibling.nodeType === Node.TEXT_NODE ) {
// 			if ( sibling.nodeValue.trim() === '' ) {
// 				sibling = sibling.previousSibling;
// 				continue;
// 			}
// 			return false;
// 		}
// 		return sibling.nodeName === 'STRONG' || sibling.nodeName === 'B';
// 	}
//
// 	return false;
// }

function hexagonateFullstop(textNode) {
    if (!/\.\s*$/.test(textNode.nodeValue)) {
        return;
    }

    // const bold = isAgainstBold( textNode );

    textNode.nodeValue = textNode.nodeValue.replace(/\.\s*$/, '');

    const wrapper = document.createElement('span');
    wrapper.innerHTML = HEX_SVG;
    const svg = wrapper.firstElementChild;
    // if ( bold ) {
    // 	svg.classList.add( 'c-heading-fullstop--bold' );
    // }
    textNode.parentNode.insertBefore(svg, textNode.nextSibling);
}

// <br> tags split a heading into separate lines/sentences, each of which
// can end in its own fullstop, so every segment needs checking rather than
// just the heading's overall last text node.
function replaceTrailingFullstop(heading) {
    if (heading.querySelector('.c-heading-fullstop')) {
        return;
    }

    const walker = document.createTreeWalker(
        heading,
        NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT,
        {
            acceptNode(node) {
                if (
                    node.nodeType === Node.TEXT_NODE ||
                    node.nodeName === 'BR'
                ) {
                    return NodeFilter.FILTER_ACCEPT;
                }
                return NodeFilter.FILTER_SKIP;
            },
        }
    );

    let lastTextNode = null;
    let node;
    while ((node = walker.nextNode())) {
        if (node.nodeName === 'BR') {
            if (lastTextNode) {
                hexagonateFullstop(lastTextNode);
            }
            lastTextNode = null;
            continue;
        }
        if (node.nodeValue.trim()) {
            lastTextNode = node;
        }
    }

    if (lastTextNode) {
        hexagonateFullstop(lastTextNode);
    }
}

function initHeadingFullstops() {
    document.querySelectorAll('h1, h2').forEach(replaceTrailingFullstop);
}

document.addEventListener('DOMContentLoaded', initHeadingFullstops);
