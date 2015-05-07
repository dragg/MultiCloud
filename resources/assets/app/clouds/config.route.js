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
          controllerAs: "cloudsCtrl"
        });
    }
})();