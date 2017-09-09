(function() {

  var fabric = require('fabric').fabric;
  var gridPattern = require('./shareables/parts/gridPattern');

  var gameSquare = require('./shareables/game.square');
  var gameRectangle = require('./shareables/game.rectangle');

  var types = {
    'game.square': gameSquare
  };

  function getData(url) {
    return fetch(url)
      .then(function(response) { return response.json(); })
  }

  window.addEventListener('load', function() {

    fabric.Object.prototype.set({
      selectable: false
    });

    var canvas = new fabric.Canvas('c');
    canvas.selection = false;

    var shadow = new fabric.Shadow({
      color: 'rgba(0,0,0,.8)',
      offsetX: 0,
      offsetY: 2,
      blur: 5
    });

    var defs = {
      canvas: canvas,
      shadow: shadow,
      gridPattern: null,
      promises: []
    };

    gridPattern()
      .then((pattern) => {
        defs.gridPattern = pattern;

        getData('/shareables/rectangle/game?game_id=288')
          .then((rsp) => {
            canvas.setHeight(rsp.dimensions.height);
            canvas.setWidth(rsp.dimensions.width);

            // gameSquare(rsp, defs);
            gameRectangle(rsp, defs);

            Promise.all(defs.promises)
              .then(() => {
                defs.canvas.renderAll();

                var data = defs.canvas.toDataURL({ multiplier: 1, format: 'png' });
                document.getElementById('o').src = data;
              })
          });
      });

  });

})();