(function() {
    'use strict';

    const trackingCategory = 'Scavenger Hunt';

    module.exports = {
        trackStep: function(action, ...rest) {
            ga('send', 'event', trackingCategory, `Step ${action}`, ...rest);
        }
    };

})();