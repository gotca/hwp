require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({29:[function(require,module,exports){
(function (global){
'use strict';

(function () {
	'use strict';

	global.jQuery = require('jquery');
	require('jquery.loadtemplate');

	var engine = require('./live/engine'),
	    linker = require('./nameLinker'),
	    _ = require('lodash'),
	    $ = jQuery;

	var game = engine.game(recap.game_id);
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

		var newUpdate = $('<div></div>');
		var scope = {
			msg: linker(data.msg),
			score: data.score[0] + '-' + data.score[1],
			timestampFormatted: data.moment.format('LT')
		};

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

	game.quarterStarted.add(quarterStarted);
	game.quarterEnded.add(quarterEnded);
	game.updated.add(updated);
	game.ended.add(gameEnded);

	function processUpdates() {
		_.forEach(recap.updates, function (update) {
			engine.process(update);
		});
	}
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"./live/engine":17,"./nameLinker":22,"jquery":5,"jquery.loadtemplate":4,"lodash":6}],22:[function(require,module,exports){
'use strict';

(function () {
	'use strict';

	var _ = require('lodash');

	var playerlist = window.playerlist;

	var tmpl = _.template('<a href="<%= url %>" title="view player"><%= title %></a>');

	/**
  * Matches cap number (and it's variants) plus name
  * $1 = #[cap number]
  * $2 = Name
  * @type {RegExp}
  */
	var regex = /(#\d{1,2}(?:(?:[a-zA-Z]|\/)?\d{0,2})?) ((?:\b\w+) (?:\b\w+))/g;

	function linker(str) {
		return str.replace(regex, replace);
	}

	function replace(match, cap, name, offset, string) {
		var url = _.get(playerlist.byName, name, false);
		if (url) {
			return tmpl({
				url: url,
				title: match
			});
		} else {
			return match;
		}
	}

	module.exports = linker;
})();

},{"lodash":6}],17:[function(require,module,exports){
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

},{"./game":18,"lodash":6,"moment":7}],18:[function(require,module,exports){
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

},{"signals":10}],10:[function(require,module,exports){
/*jslint onevar:true, undef:true, newcap:true, regexp:true, bitwise:true, maxerr:50, indent:4, white:false, nomen:false, plusplus:false */
/*global define:false, require:false, exports:false, module:false, signals:false */

/** @license
 * JS Signals <http://millermedeiros.github.com/js-signals/>
 * Released under the MIT license
 * Author: Miller Medeiros
 * Version: 1.0.0 - Build: 268 (2012/11/29 05:48 PM)
 */

(function(global){

    // SignalBinding -------------------------------------------------
    //================================================================

    /**
     * Object that represents a binding between a Signal and a listener function.
     * <br />- <strong>This is an internal constructor and shouldn't be called by regular users.</strong>
     * <br />- inspired by Joa Ebert AS3 SignalBinding and Robert Penner's Slot classes.
     * @author Miller Medeiros
     * @constructor
     * @internal
     * @name SignalBinding
     * @param {Signal} signal Reference to Signal object that listener is currently bound to.
     * @param {Function} listener Handler function bound to the signal.
     * @param {boolean} isOnce If binding should be executed just once.
     * @param {Object} [listenerContext] Context on which listener will be executed (object that should represent the `this` variable inside listener function).
     * @param {Number} [priority] The priority level of the event listener. (default = 0).
     */
    function SignalBinding(signal, listener, isOnce, listenerContext, priority) {

        /**
         * Handler function bound to the signal.
         * @type Function
         * @private
         */
        this._listener = listener;

        /**
         * If binding should be executed just once.
         * @type boolean
         * @private
         */
        this._isOnce = isOnce;

        /**
         * Context on which listener will be executed (object that should represent the `this` variable inside listener function).
         * @memberOf SignalBinding.prototype
         * @name context
         * @type Object|undefined|null
         */
        this.context = listenerContext;

        /**
         * Reference to Signal object that listener is currently bound to.
         * @type Signal
         * @private
         */
        this._signal = signal;

        /**
         * Listener priority
         * @type Number
         * @private
         */
        this._priority = priority || 0;
    }

    SignalBinding.prototype = {

        /**
         * If binding is active and should be executed.
         * @type boolean
         */
        active : true,

        /**
         * Default parameters passed to listener during `Signal.dispatch` and `SignalBinding.execute`. (curried parameters)
         * @type Array|null
         */
        params : null,

        /**
         * Call listener passing arbitrary parameters.
         * <p>If binding was added using `Signal.addOnce()` it will be automatically removed from signal dispatch queue, this method is used internally for the signal dispatch.</p>
         * @param {Array} [paramsArr] Array of parameters that should be passed to the listener
         * @return {*} Value returned by the listener.
         */
        execute : function (paramsArr) {
            var handlerReturn, params;
            if (this.active && !!this._listener) {
                params = this.params? this.params.concat(paramsArr) : paramsArr;
                handlerReturn = this._listener.apply(this.context, params);
                if (this._isOnce) {
                    this.detach();
                }
            }
            return handlerReturn;
        },

        /**
         * Detach binding from signal.
         * - alias to: mySignal.remove(myBinding.getListener());
         * @return {Function|null} Handler function bound to the signal or `null` if binding was previously detached.
         */
        detach : function () {
            return this.isBound()? this._signal.remove(this._listener, this.context) : null;
        },

        /**
         * @return {Boolean} `true` if binding is still bound to the signal and have a listener.
         */
        isBound : function () {
            return (!!this._signal && !!this._listener);
        },

        /**
         * @return {boolean} If SignalBinding will only be executed once.
         */
        isOnce : function () {
            return this._isOnce;
        },

        /**
         * @return {Function} Handler function bound to the signal.
         */
        getListener : function () {
            return this._listener;
        },

        /**
         * @return {Signal} Signal that listener is currently bound to.
         */
        getSignal : function () {
            return this._signal;
        },

        /**
         * Delete instance properties
         * @private
         */
        _destroy : function () {
            delete this._signal;
            delete this._listener;
            delete this.context;
        },

        /**
         * @return {string} String representation of the object.
         */
        toString : function () {
            return '[SignalBinding isOnce:' + this._isOnce +', isBound:'+ this.isBound() +', active:' + this.active + ']';
        }

    };


/*global SignalBinding:false*/

    // Signal --------------------------------------------------------
    //================================================================

    function validateListener(listener, fnName) {
        if (typeof listener !== 'function') {
            throw new Error( 'listener is a required param of {fn}() and should be a Function.'.replace('{fn}', fnName) );
        }
    }

    /**
     * Custom event broadcaster
     * <br />- inspired by Robert Penner's AS3 Signals.
     * @name Signal
     * @author Miller Medeiros
     * @constructor
     */
    function Signal() {
        /**
         * @type Array.<SignalBinding>
         * @private
         */
        this._bindings = [];
        this._prevParams = null;

        // enforce dispatch to aways work on same context (#47)
        var self = this;
        this.dispatch = function(){
            Signal.prototype.dispatch.apply(self, arguments);
        };
    }

    Signal.prototype = {

        /**
         * Signals Version Number
         * @type String
         * @const
         */
        VERSION : '1.0.0',

        /**
         * If Signal should keep record of previously dispatched parameters and
         * automatically execute listener during `add()`/`addOnce()` if Signal was
         * already dispatched before.
         * @type boolean
         */
        memorize : false,

        /**
         * @type boolean
         * @private
         */
        _shouldPropagate : true,

        /**
         * If Signal is active and should broadcast events.
         * <p><strong>IMPORTANT:</strong> Setting this property during a dispatch will only affect the next dispatch, if you want to stop the propagation of a signal use `halt()` instead.</p>
         * @type boolean
         */
        active : true,

        /**
         * @param {Function} listener
         * @param {boolean} isOnce
         * @param {Object} [listenerContext]
         * @param {Number} [priority]
         * @return {SignalBinding}
         * @private
         */
        _registerListener : function (listener, isOnce, listenerContext, priority) {

            var prevIndex = this._indexOfListener(listener, listenerContext),
                binding;

            if (prevIndex !== -1) {
                binding = this._bindings[prevIndex];
                if (binding.isOnce() !== isOnce) {
                    throw new Error('You cannot add'+ (isOnce? '' : 'Once') +'() then add'+ (!isOnce? '' : 'Once') +'() the same listener without removing the relationship first.');
                }
            } else {
                binding = new SignalBinding(this, listener, isOnce, listenerContext, priority);
                this._addBinding(binding);
            }

            if(this.memorize && this._prevParams){
                binding.execute(this._prevParams);
            }

            return binding;
        },

        /**
         * @param {SignalBinding} binding
         * @private
         */
        _addBinding : function (binding) {
            //simplified insertion sort
            var n = this._bindings.length;
            do { --n; } while (this._bindings[n] && binding._priority <= this._bindings[n]._priority);
            this._bindings.splice(n + 1, 0, binding);
        },

        /**
         * @param {Function} listener
         * @return {number}
         * @private
         */
        _indexOfListener : function (listener, context) {
            var n = this._bindings.length,
                cur;
            while (n--) {
                cur = this._bindings[n];
                if (cur._listener === listener && cur.context === context) {
                    return n;
                }
            }
            return -1;
        },

        /**
         * Check if listener was attached to Signal.
         * @param {Function} listener
         * @param {Object} [context]
         * @return {boolean} if Signal has the specified listener.
         */
        has : function (listener, context) {
            return this._indexOfListener(listener, context) !== -1;
        },

        /**
         * Add a listener to the signal.
         * @param {Function} listener Signal handler function.
         * @param {Object} [listenerContext] Context on which listener will be executed (object that should represent the `this` variable inside listener function).
         * @param {Number} [priority] The priority level of the event listener. Listeners with higher priority will be executed before listeners with lower priority. Listeners with same priority level will be executed at the same order as they were added. (default = 0)
         * @return {SignalBinding} An Object representing the binding between the Signal and listener.
         */
        add : function (listener, listenerContext, priority) {
            validateListener(listener, 'add');
            return this._registerListener(listener, false, listenerContext, priority);
        },

        /**
         * Add listener to the signal that should be removed after first execution (will be executed only once).
         * @param {Function} listener Signal handler function.
         * @param {Object} [listenerContext] Context on which listener will be executed (object that should represent the `this` variable inside listener function).
         * @param {Number} [priority] The priority level of the event listener. Listeners with higher priority will be executed before listeners with lower priority. Listeners with same priority level will be executed at the same order as they were added. (default = 0)
         * @return {SignalBinding} An Object representing the binding between the Signal and listener.
         */
        addOnce : function (listener, listenerContext, priority) {
            validateListener(listener, 'addOnce');
            return this._registerListener(listener, true, listenerContext, priority);
        },

        /**
         * Remove a single listener from the dispatch queue.
         * @param {Function} listener Handler function that should be removed.
         * @param {Object} [context] Execution context (since you can add the same handler multiple times if executing in a different context).
         * @return {Function} Listener handler function.
         */
        remove : function (listener, context) {
            validateListener(listener, 'remove');

            var i = this._indexOfListener(listener, context);
            if (i !== -1) {
                this._bindings[i]._destroy(); //no reason to a SignalBinding exist if it isn't attached to a signal
                this._bindings.splice(i, 1);
            }
            return listener;
        },

        /**
         * Remove all listeners from the Signal.
         */
        removeAll : function () {
            var n = this._bindings.length;
            while (n--) {
                this._bindings[n]._destroy();
            }
            this._bindings.length = 0;
        },

        /**
         * @return {number} Number of listeners attached to the Signal.
         */
        getNumListeners : function () {
            return this._bindings.length;
        },

        /**
         * Stop propagation of the event, blocking the dispatch to next listeners on the queue.
         * <p><strong>IMPORTANT:</strong> should be called only during signal dispatch, calling it before/after dispatch won't affect signal broadcast.</p>
         * @see Signal.prototype.disable
         */
        halt : function () {
            this._shouldPropagate = false;
        },

        /**
         * Dispatch/Broadcast Signal to all listeners added to the queue.
         * @param {...*} [params] Parameters that should be passed to each handler.
         */
        dispatch : function (params) {
            if (! this.active) {
                return;
            }

            var paramsArr = Array.prototype.slice.call(arguments),
                n = this._bindings.length,
                bindings;

            if (this.memorize) {
                this._prevParams = paramsArr;
            }

            if (! n) {
                //should come after memorize
                return;
            }

            bindings = this._bindings.slice(); //clone array in case add/remove items during dispatch
            this._shouldPropagate = true; //in case `halt` was called before dispatch or during the previous dispatch.

            //execute all callbacks until end of the list or until a callback returns `false` or stops propagation
            //reverse loop since listeners with higher priority will be added at the end of the list
            do { n--; } while (bindings[n] && this._shouldPropagate && bindings[n].execute(paramsArr) !== false);
        },

        /**
         * Forget memorized arguments.
         * @see Signal.memorize
         */
        forget : function(){
            this._prevParams = null;
        },

        /**
         * Remove all bindings from signal and destroy any reference to external objects (destroy Signal object).
         * <p><strong>IMPORTANT:</strong> calling any method on the signal instance after calling dispose will throw errors.</p>
         */
        dispose : function () {
            this.removeAll();
            delete this._bindings;
            delete this._prevParams;
        },

        /**
         * @return {string} String representation of the object.
         */
        toString : function () {
            return '[Signal active:'+ this.active +' numListeners:'+ this.getNumListeners() +']';
        }

    };


    // Namespace -----------------------------------------------------
    //================================================================

    /**
     * Signals namespace
     * @namespace
     * @name signals
     */
    var signals = Signal;

    /**
     * Custom event broadcaster
     * @see Signal
     */
    // alias for backwards compatibility (see #gh-44)
    signals.Signal = Signal;



    //exports to multiple environments
    if(typeof define === 'function' && define.amd){ //AMD
        define(function () { return signals; });
    } else if (typeof module !== 'undefined' && module.exports){ //node
        module.exports = signals;
    } else { //browser
        //use string because of Google closure compiler ADVANCED_MODE
        /*jslint sub:true */
        global['signals'] = signals;
    }

}(this));

},{}]},{},[29]);
