(function() {
   'use strict';

    angular
        .module('app.cloudsNavBar')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('clouds.cloud', {
          url: "/:cloudId/contents/:path",
          templateUrl: "build/views/cloud/cloud.html",
          controller: "Cloud",
          controllerAs: "vm"
        });
    }
})();