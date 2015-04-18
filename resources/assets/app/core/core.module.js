(function () {
    'use strict';

    angular
        .module('app.core', [
            /*
             * Angular modules
             */
            'ngAnimate', 'ngResource',

            /*
             * Our reusable cross app code modules
             */
            /*'blocks.exception', 'blocks.logger', 'blocks.router',*/
            'models', 'filters',

            /*
             * 3rd Party modules
             */
            'ui.router'
        ]);
})();