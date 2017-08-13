require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({18:[function(require,module,exports){
'use strict';

(function () {

	var GhostedNav = require('./ghostNav'),
	    Rankings = require('./rankings'),
	    Recent = require('./recent');

	// ghost the nav when we're at the top of the page
	GhostedNav(document.getElementById('main-menu'), document.getElementById('home-header'));

	// rankings
	new Rankings(document.querySelector('.rankings'));

	// recent
	var recent = new Recent(document.querySelector('.recent-grid'), document.querySelector('.recent-content .btn.load-more'));
	recent.load();
})();

},{"./ghostNav":17,"./rankings":28,"./recent":30}],30:[function(require,module,exports){
(function (global){
'use strict';

(function () {
	'use strict';

	global.jQuery = require('jquery');
	var _ = require('lodash');

	var $ = jQuery;

	function Recent(holder, btn) {
		this.holder = $(holder);
		this.btn = $(btn);
		this.loadCount = 0;
		this.templates = [];

		this.getTemplates();
		this.attachEvents();
	}

	Recent.prototype.pageIDs = ['page-even', 'page-odd'];

	Recent.prototype.getTemplates = function () {
		var self = this;

		_.forEach(this.pageIDs, function (val, idx) {
			self.templates[idx] = $($('#' + val).text());
		});
	};

	Recent.prototype.attachEvents = function () {
		var self = this;

		this.btn.on('click', this.load.bind(self));
	};

	Recent.prototype.load = function () {
		var self = this;
		var url = this.btn.data('url');
		var tmpl;

		this.holder.addClass('loading');
		this.btn.attr('disabled', 'disabled');

		this.loadCount++;

		if (this.loadCount > 1) {
			tmpl = this.templates[this.loadCount % 2].clone();
			tmpl.appendTo(this.holder);
		}

		$.getJSON(url).done(this.done.bind(self)).fail(this.error.bind(self)).always(function () {
			self.holder.removeClass('loading');
		});
	};

	Recent.prototype.done = function (rsp) {
		var self = this;
		var next = rsp.next_page_url;
		var loading = this.holder.find('.recent--loading');
		var i = 0;
		var max = Math.min(rsp.per_page, rsp.data.length);
		var pageClass = "";

		if (this.loadCount > 1) {
			pageClass = 'recent-page--' + this.loadCount % 2;
		}

		for (i; i < max; i++) {
			var item = rsp.data[i];
			var newEl = $(item.rendered);
			var loadingEl = loading.eq(i);

			newEl.addClass(pageClass);

			if (loadingEl.length) {
				newEl.attr('class', newEl.attr('class') + ' ' + loadingEl.attr('class')).removeClass('recent--loading');

				loadingEl.replaceWith(newEl);
			} else {
				newEl.appendTo(self.holder);
			}
		}

		// hide and remove anything still set as loading
		var empty = this.holder.find('.recent--loading');
		if (this.loadCount > 1) {
			empty.fadeOut();
		} else {
			empty.removeClass('recent--loading').addClass('bg--smoke').empty();
		}

		if (next) {
			this.btn.data('url', next).removeAttr('disabled');
		} else {
			this.btn.remove();
		}
	};

	Recent.prototype.error = function (err) {
		console.error(err);
		alert('Error loading the recent content');
		this.holder.find('.recent--loading').remove();
		this.loadCount--;
	};

	module.exports = Recent;
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"jquery":6,"lodash":7}],28:[function(require,module,exports){
(function (global){
'use strict';

(function () {
	'use strict';

	global.jQuery = require('jquery');
	var moment = require('moment'),
	    formatter = require('./dateFormats');

	var $ = jQuery;

	function Rankings(el) {
		this.el = $(el);
		this.attachEvents();
	}

	Rankings.prototype.attachEvents = function () {
		var self = this;

		this.el.find('.pager a').on('click', function () {
			self.load($(this).attr('href'));
			$(this).parent().addClass('disabled');
			return false;
		});
	};

	Rankings.prototype.load = function (url) {
		var self = this;

		self.el.addClass('loading');

		$.getJSON(url).done(this.loaded.bind(self)).fail(this.error.bind(self)).always(function () {
			self.el.removeClass('loading');
		});
	};

	Rankings.prototype.loaded = function (rsp) {
		var rankings = rsp.data[0];
		rankings.start = moment(rankings.start);
		rankings.end = moment(rankings.end);

		// update the pager
		if (rsp.next_page_url) {
			this.el.find('.pager .next').removeClass('disabled').find('a').attr('href', rsp.next_page_url);
		}

		if (rsp.prev_page_url) {
			this.el.find('.pager .prev').removeClass('disabled').find('a').attr('href', rsp.prev_page_url);
		}

		// redraw the table body
		var body = this.el.find('tbody');
		body.empty();
		rankings.ranks.forEach(function (rank) {
			var tr = $('<tr></tr>').addClass('rank');
			if (rank.self) {
				tr.addClass('rank--self');
			}

			$('<th></th>').text(rank.rank).appendTo(tr);

			$('<td></td>').text(rank.team + (rank.tied ? '(tied)' : '')).appendTo(tr);

			body.append(tr);
		});

		// redraw the table footer
		this.el.find('tfoot td').html(formatter.dateSpan(rankings.start, rankings.end));
	};

	Rankings.prototype.error = function (err) {
		console.error(err);
		alert('Error loading rankings.');
	};

	module.exports = Rankings;
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"./dateFormats":14,"jquery":6,"moment":8}],17:[function(require,module,exports){
'use strict';

(function () {
    'use strict';

    module.exports = ghostNav;

    var _ = require('lodash');

    var el;
    var container;
    var className = 'ghosted';
    var scroller = document.getElementsByClassName('scroller')[0];
    var delay = 100;

    function ghostNav(element, context) {
        el = element;
        container = context;

        window.addEventListener('DOMContentLoaded', check);
        window.addEventListener('resize', _.debounce(check, delay));
        scroller.addEventListener('scroll', _.debounce(check, delay));
    }

    function ghost() {
        el.classList.add(className);
    }

    function unghost() {
        el.classList.remove(className);
    }

    function check() {
        // if el bounds is within container bounds add class
        // otherwise remove class
        if (scroller.scrollTop < container.clientHeight - el.clientHeight) {
            ghost();
        } else {
            unghost();
        }
    }
})();

},{"lodash":7}],14:[function(require,module,exports){
'use strict';

(function () {
	'use strict';

	var moment = require('moment');
	require('./moment.phpformat');

	/**
  * Basically the App\Providers\DateDirectiveServiceProvider in Moment.js
  * @type {{}}
  */
	var formats = {
		DAY: 'l',
		DAY_SHORT: 'D',
		DATE: 'n/j',
		STAMP: 'M jS \@ g:ia',
		DATE_SPAN: 'M j',
		TIME: 'g:ia',
		ISO: 'c'
	};

	module.exports = {
		day: day,
		date: date,
		dayWithDate: dayWithDate,
		dayWithDateTime: dayWithDateTime,
		dateSpan: dateSpan,
		time: time,
		stamp: stamp,
		iso: iso
	};

	function day(m, full) {
		var today = moment().startOf('date');
		var diff = m.startOf('date').diff(today, 'days');

		if (diff === 0) {
			return 'today';
		} else if (diff < 7) {
			return m.formatPHP(full === false ? formats.DAY_SHORT : formats.DAY);
		} else {
			return datWithDate(m, full);
		}
	}

	function date(m) {
		return m.formatPHP(formats.DATE);
	}

	function dayWithDate(m) {
		var format = (full === false ? formats.DAY_SHORT : formats.DAY) + ' ' + formats.DATE;
		return m.formatPHP(format);
	}

	function dayWithDateTime(m) {
		var day = dayWithDate(m, false);
		var midnight = day.startOf('date');
		var time;

		if (day.diff(midnight) > 0) {
			time = ' @ ' + time(m);
		} else {
			time = ' all day';
		}

		return day + time;
	}

	function dateSpan(from, to) {
		return from.formatPHP(formats.DATE_SPAN) + ' &ndash; ' + to.formatPHP(formats.DATE_SPAN);
	}

	function time(m) {
		return m.formatPHP(formats.TIME);
	}

	function stamp(m) {
		return m.formatPHP(formats.STAMP);
	}

	function iso(m) {
		return m.formatPHP(formats.ISO);
	}
})();

},{"./moment.phpformat":24,"moment":8}],24:[function(require,module,exports){
'use strict';

(function (m) {
	/*
  * PHP => moment.js
  * Will take a php date format and convert it into a JS format for moment
  * http://www.php.net/manual/en/function.date.php
  * http://momentjs.com/docs/#/displaying/format/
  */
	var moment = require('moment');

	var formatMap = {
		d: 'DD',
		D: 'ddd',
		j: 'D',
		l: 'dddd',
		N: 'E',
		S: function S() {
			return '[' + this.format('Do').replace(/\d*/g, '') + ']';
		},
		w: 'd',
		z: function z() {
			return this.format('DDD') - 1;
		},
		W: 'W',
		F: 'MMMM',
		m: 'MM',
		M: 'MMM',
		n: 'M',
		t: function t() {
			return this.daysInMonth();
		},
		L: function L() {
			return this.isLeapYear() ? 1 : 0;
		},
		o: 'GGGG',
		Y: 'YYYY',
		y: 'YY',
		a: 'a',
		A: 'A',
		B: function B() {
			var thisUTC = this.clone().utc(),

			// Shamelessly stolen from http://javascript.about.com/library/blswatch.htm
			swatch = (thisUTC.hours() + 1) % 24 + thisUTC.minutes() / 60 + thisUTC.seconds() / 3600;
			return Math.floor(swatch * 1000 / 24);
		},
		g: 'h',
		G: 'H',
		h: 'hh',
		H: 'HH',
		i: 'mm',
		s: 'ss',
		u: '[u]', // not sure if moment has this
		e: '[e]', // moment does not have this
		I: function I() {
			return this.isDST() ? 1 : 0;
		},
		O: 'ZZ',
		P: 'Z',
		T: '[T]', // deprecated in moment
		Z: function Z() {
			return parseInt(this.format('ZZ'), 10) * 36;
		},
		c: 'YYYY-MM-DD[T]HH:mm:ssZ',
		r: 'ddd, DD MMM YYYY HH:mm:ss ZZ',
		U: 'X'
	},
	    formatEx = /[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/g;

	moment.fn.formatPHP = function (format) {
		var that = this;

		return this.format(format.replace(formatEx, function (phpStr) {
			return typeof formatMap[phpStr] === 'function' ? formatMap[phpStr].call(that) : formatMap[phpStr];
		}));
	};
})();

},{"moment":8}]},{},[18]);
