(function () {

	var GhostedNav = require('./ghostNav'),
		Rankings   = require('./rankings'),
		Recent     = require('./recent');

	require('css-polyfills');


	// ghost the nav when we're at the top of the page
	GhostedNav(document.getElementById('main-menu'), document.getElementById('home-header'));

	// rankings
	new Rankings(document.querySelector('.rankings'));

	// recent
	var recent = new Recent(document.querySelector('.recent-grid'), document.querySelector('.recent-content .btn.load-more'));
	recent.load();

})();