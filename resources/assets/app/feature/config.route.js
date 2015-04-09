(function() {
   'use strict';

    angular
        .module('app.feature')
        .run(appRun);

    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return [
            {
                url: '/feature',
                config: {
                    templateUrl: 'build/views/feature/feature.html',
                    controller: 'Feature',
                    controllerAs: 'vm'
                }
            }
        ];
    }
})();