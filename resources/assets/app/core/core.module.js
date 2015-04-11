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
            'blocks.cloudsModel',

            /*
             * 3rd Party modules
             */
            'ui.router'
        ]);
})();