require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({27:[function(require,module,exports){
(function (global){
'use strict';

(function () {
	'use strict';

	var _ = require('lodash'),
	    mlPushMenu = require('./mlpushmenu'),
	    matchMenuHeight = _.debounce(require('./matchMenuHeight'), 300),
	    PopupGallery = require('./gallery/popup'),
	    FullGallery = require('./gallery/full'),
	    Note = require('./note');

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
			gallery.load().always(function () {
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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"./gallery/full":14,"./gallery/popup":15,"./matchMenuHeight":19,"./mlpushmenu":20,"./note":23,"jquery":5,"lodash":6}],23:[function(require,module,exports){
(function (global){
'use strict';

(function () {
	'use strict';

	var vex = require('vex-js');

	global.jQuery = require('jquery');
	var $ = jQuery;

	vex.defaultOptions.className = 'vex-theme-default';

	$(document).ready(function () {
		$(document).on('click', '.note', function () {
			var self = $(this);

			$(this).addClass('loading');
			$.get('/notes/' + $(this).data('note-id')).done(function (rsp) {
				vex.open({
					unsafeContent: rsp,
					className: 'vex-theme-note'
				});
			}).fail(function (err) {
				console.error(err);
				alert('could not load note');
			}).always(function () {
				self.removeClass('loading');
			});

			return false;
		});
	});
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"jquery":5,"vex-js":11}]