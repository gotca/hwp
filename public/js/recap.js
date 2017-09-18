require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({84:[function(require,module,exports){
(function (global){
'use strict';

(function () {
  'use strict';

  global.jQuery = require('jquery');
  require('jquery.loadtemplate');

  var engine = require('./live/engine'),
      linker = require('./nameLinker').linker,
      matcher = require('./nameLinker').matcher,
      shareable = require('./shareables'),
      _ = require('lodash'),
      $ = jQuery;

  var game = engine.game(recap.game_id);
  game.quarterStarted.add(quarterStarted);
  game.quarterEnded.add(quarterEnded);
  game.updated.add(updated);
  game.ended.add(gameEnded);

  var quarterTitles = {
    '1st': 'First Quarter',
    '2nd': 'Second Quarter',
    '3rd': 'Third Quarter',
    '4th': 'Fourth Quarter',
    '1st OT': 'First Overtime',
    '2nd OT': 'Second Overtime',
    'Shootout': 'Shootout'
  };
  var started = false;
  var score = [0, 0];
  var recapHolder;
  var loader;
  var currentQuarter;
  var currentQuarterTitle;
  var quarterTmpl;
  var updateTmpl;

  window.addEventListener('DOMContentLoaded', function () {
    recapHolder = $('.recap').first();
    quarterTmpl = $('#quarter-tmpl');
    updateTmpl = $('#update-tmpl');
    loader = $('.recap-loader');

    processUpdates();
  });

  function processUpdates() {
    _.forEach(recap.updates, function (update) {
      engine.process(update);
    });
  }

  function createNewQuarter(data, titleKey) {
    var newQuarter, titleSplit, scope, title;

    title = quarterTitles[titleKey];
    titleSplit = title.split(' ');

    scope = {
      quarterNameFirst: titleSplit[0],
      quarterNameRemaining: titleSplit[1],
      status: '',
      scoreUs: data.score[0],
      scoreThem: data.score[1],
      opponent: data.opponent
    };

    newQuarter = $('<div></div>');
    newQuarter.loadTemplate(quarterTmpl, scope);
    recapHolder.append(newQuarter);
    currentQuarter = newQuarter;
    currentQuarterTitle = title;
  }

  function updateScore(score) {
    var classUs, classThem;

    if (score[0] > score[1]) {
      classUs = 'result--win';
      classThem = 'result--loss';
    } else if (score[0] < score[1]) {
      classUs = 'result--loss';
      classThem = 'result--win';
    } else {
      classUs = classThem = 'result--tie';
    }

    currentQuarter.find('.score--us').removeClass('result--win result--loss result--tie').addClass(classUs).find('h2').text(score[0]).end().end().find('.score--them').removeClass('result--win result--loss result--tie').addClass(classThem).find('h2').text(score[1]).end();
  }

  function isQuarterStarted(data) {
    if (!started) {
      createNewQuarter(data, '1st');
      loader.remove();
      loader = false;
      started = true;
    }
  }

  function quarterStarted(data, title) {
    createNewQuarter(data, title);
    started = true;

    if (loader) {
      loader.remove();
      loader = false;
    }
  }

  function updated(data) {
    isQuarterStarted(data);

    data.quarterTitle = currentQuarterTitle;
    data.mentions = matcher(data.msg);

    var newUpdate = $('<div></div>');
    var scope = {
      msg: linker(data.msg),
      score: data.score[0] + '-' + data.score[1],
      timestampFormatted: data.moment.format('LT'),
      json: JSON.stringify(data),
      shareable: '/shareables/square/update?game_id=' + data.game_id + '&mentions=' + JSON.stringify(data.mentions)
    };

    // retweet?
    if (data.twitter_id) {
      scope.retweet = 'https://twitter.com/intent/retweet?tweet_id=' + data.twitter_id;
    }

    newUpdate.loadTemplate(updateTmpl, scope);
    currentQuarter.find('.body.container').append(newUpdate);
    updateScore(data.score);
  }

  function updateQuarterStatus(data, title) {
    isQuarterStarted(data);

    currentQuarter.find('.recap-quarter-status').text(title);

    updateScore(data.score);
  }

  function quarterEnded(data) {
    var title = 'End of the ' + currentQuarterTitle.replace('Quarter', '');
    updateQuarterStatus(data, title);
  }

  function gameEnded(data) {
    var title = 'Final Result';
    updateQuarterStatus(data, title);
  }
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"./live/engine":74,"./nameLinker":80,"./shareables":96,"jquery":27,"jquery.loadtemplate":26,"lodash":28}],74:[function(require,module,exports){
'use strict';

(function () {
	'use strict';

	var Game = require('./game'),
	    _ = require('lodash'),
	    moment = require('moment');

	var games = {};

	var engine = {
		process: process,
		game: getOrCreateGame,
		getGame: getGame,
		createGame: createGame,
		hasGame: hasGame
	};

	/**
  * Takes the supplied data and does all the necessary processing and triggers the game events
  * @param data
  */
	function process(data) {
		var game = getOrCreateGame(data.game_id);
		var quarterRegex = /Start of the (\d\w+( .+)?) --/i;

		data.moment = moment.unix(data.ts);

		// trigger different events based on the update
		// TODO - this should eventually be based on control messages from the live scoring panel

		// start of game
		if (_.startsWith(data.msg, 'Start of Hudsonville')) {
			game.started.dispatch(data);
			game.quarterStarted.dispatch(data, '1st');
		}

		// end of quarter
		if (_.startsWith(data.msg, 'At the end of the')) {
			game.quarterEnded.dispatch(data);
		}

		// start of quarter
		if (_.startsWith(data.msg, 'Start of the')) {
			var matched = data.msg.match(quarterRegex);
			game.quarterStarted.dispatch(data, matched[1]);
		}

		// end of the game
		if (_.startsWith(data.msg, 'Final Result')) {
			game.quarterEnded.dispatch(data);
			game.ended.dispatch(data);
		}

		// start of shoot-out
		if (_.endsWith(data.msg, 'Shoot-Out!')) {
			game.shootOutStarted.dispatch(data);
		}

		game.updated.dispatch(data);
	}

	/**
  * Get's or creates a Game with the supplied id
  * @param gameId
  * @returns {Game}
  */
	function getOrCreateGame(gameId) {
		return getGame(gameId) || createGame(gameId);
	}

	/**
  * Get's the game with the specified id
  * @param gameId
  * @returns {Game|undefined}
  */
	function getGame(gameId) {
		return _.get(games, gameId, undefined);
	}

	/**
  * Creates a game with the specified id
  * @param gameId
  * @returns {Game}
  */
	function createGame(gameId) {
		var game = new Game(gameId);
		_.set(games, gameId, game);

		return game;
	}

	/**
  * Checks for the game in the game cache
  * @param gameId
  * @returns {boolean}
  */
	function hasGame(gameId) {
		return _.has(games, gameId);
	}

	module.exports = engine;
})();

},{"./game":75,"lodash":28,"moment":29}],75:[function(require,module,exports){
'use strict';

(function () {

	var Signal = require('signals');

	function Game(gameId) {
		this.connected = new Signal();
		this.started = new Signal();
		this.ended = new Signal();
		this.quarterStarted = new Signal();
		this.quarterEnded = new Signal();
		this.shootOutStarted = new Signal();
		this.updated = new Signal();
	}

	module.exports = Game;
})();

},{"signals":41}]},{},[84]);
