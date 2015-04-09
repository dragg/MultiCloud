(function () {
    'use strict';

    angular
        .module('app', [
            'app.core',

            /*
             * Feature areas
             */
            'app.cloudsNavBar',
            'app.addCloud',
            'app.cloud'
        ]);
})();
