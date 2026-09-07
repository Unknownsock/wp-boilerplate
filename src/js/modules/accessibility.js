var visibleLinks = document.querySelectorAll('a');
visibleLinks.forEach(function(link) {
	link.setAttribute('tabindex', '0');
});
