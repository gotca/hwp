var _   = require('lodash'),
	vex = require('vex-js');


window.addEventListener('DOMContentLoaded', function () {

	// subscribe modal dialog
	var subscribeModelContent = document.getElementById('subscribe-modal').textContent;
	var subscribeBtn = document.querySelector('button.subscribe');
	subscribeBtn.addEventListener('click', function() {
		vex.open({
			unsafeContent: subscribeModelContent,
			className: 'vex-theme-note'
		});
	});


	// Add today to the proper spot in the table
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