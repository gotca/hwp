require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({46:[function(require,module,exports){
'use strict';

var _ = require('lodash');

window.addEventListener('DOMContentLoaded', function () {

	var trs = document.querySelectorAll('tr[data-timestamp]');
	var now = Date.now();

	var before = _.find(trs, function (tr) {
		return parseTS(tr.dataset.timestamp) > now;
	});

	if (before) {
		// create the today row and inject it
		var tr = document.createElement('tr');
		var td = document.createElement('td');

		tr.classList.add('schedule-today');
		td.innerHTML = 'today';
		td.colSpan = document.querySelectorAll('table.schedule thead th').length;
		tr.appendChild(td);

		before.parentNode.insertBefore(tr, before);
	}

	function parseTS(ts) {
		return parseInt(ts, 10) * 1000;
	}
});

},{"lodash":22}]},{},[46]);
