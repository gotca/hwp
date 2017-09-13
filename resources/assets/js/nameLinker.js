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

	function matcher(str) {
    var matched;
    var nameKeys = [];

    while((matched = regex.exec(str)) !== null) {
      var name = matched[2];
      var url = _.get(playerlist.byName, name, false);
      if (url) {
        nameKeys.push(url.replace('/players/', ''));
      }
    }

    return nameKeys;
  }

  function finder(str) {
		var matched;
		var found = [];

    while((matched = regex.exec(str)) !== null) {
      found.push(matched);
    }

    return found;
	}

	module.exports = {
		linker: linker,
		matcher: matcher,
		finder: finder
	};

})();