var subscribeModalInit = require('./schedule/subscribe-modal'),
	todayRow           = require('./schedule/today-row'),
	filterable         = require('./schedule/filterable');

// clicking subscribe opens the modal content
subscribeModalInit();

// adds to today row for reference
todayRow();

// handles filtering the table
filterable();