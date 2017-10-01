(function () {
	'use strict';

	global.jQuery = require('jquery');

  var PhotoSwipe = require('photoswipe');
  var PhotoSwipeUI_Default = require('photoswipe/dist/photoswipe-ui-default.js');
  var idToDownload = require('./shutterflyIdToUrl');

	var $ = jQuery;


	function PopupGallery(url) {
		this.url = url;
	}

	PopupGallery.prototype.load = function(url) {
		var self = this;

		if (!url) {
			url = this.url;
		}

		return $.getJSON(url)
			.done(this.loaded.bind(self))
			.fail(this.error.bind(self));
	};

	PopupGallery.prototype.loaded = function (items) {
		var pswpElement = document.querySelectorAll('.pswp')[0];
		var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, {
			shareButtons: [
				{id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
			],
      getImageURLForShare: function(btn) {
        var item = gallery.currItem;

        if (btn.download) {
          return idToDownload(item.shutterfly_id);
        } else {
          return item.src;
        }
      }
		});
		gallery.init();

		return gallery;
	};

	PopupGallery.prototype.error = function(err) {
		console.error(err);
		alert('Error loading the recent content');
	};

	module.exports = PopupGallery;

})();