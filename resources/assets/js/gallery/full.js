(function () {
	'use strict';

	global.jQuery = require('jquery');
	require('jquery.loadtemplate');

	var PhotoSwipe           = require('photoswipe'),
		PhotoSwipeUI_Default = require('photoswipe/dist/photoswipe-ui-default.js');

	var $ = jQuery;
	var tmpl = $('#gallery-thumb-tmpl');

	var i = 0;
	function addIndex(el) {
		el.attr('data-offset', i);
		i++;
	}
	
	function FullGallery(el) {
		this.el = $(el);
		this.gallery = null;
		this.items = null;

		this.attachEvents();
		this.load(this.el.data('gallery-path'));
	}

	FullGallery.prototype.attachEvents = function() {
		var self = this;

		this.el.on('click', 'a.gallery-photo--thumb', this.click.bind(self));
	};

	FullGallery.prototype.load = function(url) {
		var self = this;

		$.getJSON(url)
			.done(this.loaded.bind(self))
			.fail(this.error.bind(self));
	};

	FullGallery.prototype.loaded = function(items) {
		this.items = items;

		this.el.loadTemplate(tmpl, items, {
			beforeInsert: addIndex
		});
	};

	FullGallery.prototype.error = function(err) {
		alert('Could not load gallery');
		console.log(err);
		this.el.empty();
	};

	FullGallery.prototype.click = function(e) {
		var item = $(e.target).parent('[data-offset]');
		var offset = parseInt(item.attr('data-offset'), 10);


		var pswpElement = document.querySelectorAll('.pswp')[0];
		this.gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, this.items, {
			index: offset,
			shareButtons: [
				{id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
			]
		});
		this.gallery.init();
		// this.gallery.close();

		// this.gallery.goTo(offset);

		return false;
	};
	
	module.exports = FullGallery;

})();