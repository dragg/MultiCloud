(function() {
   'use strict';

    angular
        .module('app.clouds')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('clouds', {
          url: "/clouds",
          templateUrl: "build/views/clouds/clouds.html",
          controller: "Clouds",
          controllerAs: "cloudsCtrl",
          resolve: {
            deps: [
              '$ocLazyLoad',
              function ($ocLazyLoad) {
                return $ocLazyLoad.load({
                  name: 'app',
                  insertBefore: '#ng_load_plugins_before', // load the above css files before a LINK element with this ID. Dynamic CSS files must be loaded between core and theme css files
                  files: [
                    './build/views/contents/properties.contents.html',
                    './build/views/contents/submit-delete.contents.html',
                  ]
                });
              }
            ]
          }
        });
    }
})();