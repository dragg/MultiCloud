(function () {
    'use strict';

    angular
        .module('app', [
            'app.core',

            /*
             * Feature areas
             */
            'app.clouds',
            'app.addCloud',
            'app.cloud',
            'app.contents',
            'app.navigate',
            'app.home',
            'common'
        ]);
})();
