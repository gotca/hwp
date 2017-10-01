(function () {
  'use strict';

  const url = 'https://im1.shutterfly.com/procgtaserv/';

  module.exports = function (id) {
    id = id.split('');
    id[35] = 0;
    id = id.join('');

    return url + id;
  }

})();