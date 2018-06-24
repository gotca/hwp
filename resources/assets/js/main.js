(function () {
	'use strict';

	var _               = require('lodash'),
		mlPushMenu      = require('./mlpushmenu'),
		matchMenuHeight = _.debounce(require('./matchMenuHeight'), 300),
		PopupGallery    = require('./gallery/popup'),
		FullGallery     = require('./gallery/full'),
		Note            = require('./note'),
	  	shareable				= require('./shareables'),
		seasonSwitching = require('./seasonSwitching');

	global.jQuery = require('jquery');
	var $ = jQuery;


	new mlPushMenu(document.getElementById('mp-menu'), document.getElementById('trigger'));

	window.onresize = matchMenuHeight;
	document.addEventListener('DOMContentLoaded', matchMenuHeight);

	// popup galleries
	$(document).ready(function () {
		$('body').on('click', '.popup-gallery', function () {
			var url = $(this).data('gallery-path');
			var el = $(this);
			var gallery = new PopupGallery(url);

			el.addClass('loading');
			gallery.load()
				.always(function () {
					el.removeClass('loading');
				});

			return false;
		});
	});

	// full galleries
	$(document).ready(function () {
		$('.full-gallery').each(function (el) {
			new FullGallery(this);
		});
	});

})();