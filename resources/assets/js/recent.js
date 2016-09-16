(function () {
	'use strict';

	global.jQuery = require('jquery');
	var _ = require('lodash');

	var $ = jQuery;

	function Recent(holder, btn) {
		this.holder = $(holder);
		this.btn = $(btn);
		this.loadCount = 0;

		this.attachEvents();
	}

	Recent.prototype.classes = [
		'1-1', '2-1', '1-1',
		'1-1', '1-1', '1-2', '1-2',
		'2-1',
		'1-1', '1-1', '2-2',
		'1-1', '1-1'
	];

	Recent.prototype.otherClasses = [
		'1-1', '2-1', '1-1',
		'1-2', '1-2', '1-1', '1-1',
		'2-1',
		'2-2', '1-1', '1-1',
		'1-1', '1-1'
	];

	Recent.prototype.attachEvents = function() {
		var self = this;

		this.btn.on('click', this.load.bind(self));
	};

	Recent.prototype.load = function() {
		var self = this;
		var url = this.btn.data('url');

		this.holder.addClass('loading');
		this.btn.attr('disabled', 'disabled');

		$.getJSON(url)
			.done(this.done.bind(self))
			.fail(this.error.bind(self))
			.always(function() {
				self.holder.removeClass('loading');
			});
	};

	Recent.prototype.done = function(rsp) {
		var self = this;
		var next = rsp.next_page_url;
		var loading = this.holder.find('.recent--loading');
		var i = 0;
		var max = Math.min(rsp.per_page, rsp.data.length);
		var classes = this.classes;

		self.loadCount++;

		// flip layout for every other
		if (self.loadCount % 2) {
			classes = this.otherClasses;
		}

		for(i; i < max; i++) {
			var item = rsp.data[i];
			var newEl = $(item.rendered);
			var loadingEl = loading.eq(i);

			if (loadingEl.length) {
				newEl.attr('class', newEl.attr('class') + ' ' + loadingEl.attr('class'))
					.removeClass('recent--loading');

				loadingEl.replaceWith(newEl);
			} else {
				newEl.addClass('recent--' + classes[i]);
				newEl.appendTo(self.holder);
			}
		}

		if (next) {
			this.btn.data('url', next)
				.removeAttr('disabled');
		} else {
			this.btn.remove();
		}
	};

	Recent.prototype.error = function(err) {
		console.error(err);
		alert('Error loading the recent content');
	};


	module.exports = Recent;

})();