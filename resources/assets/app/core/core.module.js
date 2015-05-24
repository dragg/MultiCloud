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
            'models', 'filters', 'directives',

            /*
             * 3rd Party modules
             */
            'ui.router', 'angularSpinner', 'ui.slimscroll', 'ui.bootstrap', 'oc.lazyLoad'
        ]);
})();