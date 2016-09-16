(function(){
    'use strict';

    window.playerlist = {
        byName: {!! json_encode($byName, JSON_PRETTY_PRINT) !!},
        byNameKey: {!! json_encode($byNameKey, JSON_PRETTY_PRINT) !!}
    };

})();