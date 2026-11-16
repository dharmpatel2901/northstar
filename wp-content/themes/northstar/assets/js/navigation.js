/**
 * Accessible mobile navigation toggle.
 *
 * @package Northstar
 */
(function () {
	'use strict';

	var button = document.querySelector('.mobile-menu');
	var navigation = document.querySelector('#northstar-primary-navigation');

	if (!button || !navigation) {
		return;
	}

	button.addEventListener('click', function () {
		var isExpanded = button.getAttribute('aria-expanded') === 'true';
		button.setAttribute('aria-expanded', String(!isExpanded));
		navigation.classList.toggle('is-open', !isExpanded);
	});
})();
