(function () {

  var fabric = require('fabric').fabric;
  var gridPattern = require('./shareables/parts/gridPattern');
  var gradients = require('./shareables/parts/gradients');

  var gameSquare = require('./shareables/game.square');
  var gameRectangle = require('./shareables/game.rectangle');
  var gamePlayerSquare = require('./shareables/game-player.square');
  var gamePlayerRectangle = require('./shareables/game-player.rectangle');
  var playerSquare = require('./shareables/player.square');
  var playerRectangle = require('./shareables/player.rectangle');
  var updateSquare = require('./shareables/update.square');
  var updateRectangle = require('./shareables/update.rectangle');

  var types = {
    'game.square': gameSquare
  };

  function getData(url) {
    return fetch(url)
      .then(function (response) {
        return response.json();
      })
  }

  Promise.all([
    (() => new Promise((resolve) => {
      window.addEventListener('load', () => resolve())
    }))(),
    document.fonts.ready
  ])
    .then(() => {
      go();
    });

  function go() {

    fabric.Object.prototype.set({
      selectable: false,
      fontFamily: 'sans-serif'
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
      padding: 57,
      gridPattern: null,
      gradients: gradients,
      promises: []
    };

    gridPattern()
      .then((pattern) => {
        defs.gridPattern = pattern;

        getData('shareables/rectangle/update?game_id=311&mentions=["IanWorst", "HarrisonFriar"]')
          .then((rsp) => {
            canvas.setHeight(rsp.dimensions.height);
            canvas.setWidth(rsp.dimensions.width);

            // gameSquare(rsp, defs);
            // gameRectangle(rsp, defs);
            // gamePlayerSquare(rsp, defs);
            // gamePlayerRectangle(rsp, defs);
            // playerSquare(rsp, defs);
            // playerRectangle(rsp, defs);

            var update = {
              "msg": "Hudsonville Goal! #21 Ian Worst, his 1st, with the Assist by #11 Harrison Friar",
              "ts": 1478993803,
              "score": [5, 5],
              "game_id": 311,
              "title": "3rd",
              "opponent": "AA Huron ",
              "team": "V",
              "moment": "2016-11-12T23:36:43.000Z",
              "quarterTitle": "Second Quarter",
              "mentions": ["IanWorst", "HarrisonFriar"]
            };

            // updateSquare(update, rsp, defs);
            updateRectangle(update, rsp, defs);

            Promise.all(defs.promises)
              .then(() => {
                defs.canvas.renderAll();

                var data = defs.canvas.toDataURL({multiplier: 1, format: 'png'});
                document.getElementById('o').src = data;
              })
          });
      });

  };

})();