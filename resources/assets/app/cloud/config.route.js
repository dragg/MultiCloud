(function() {
   'use strict';

    angular
        .module('app.cloudsNavBar')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('clouds.cloud', {
          url: "/cloud",
          templateUrl: "build/views/cloud/cloud.html",
          controller: "Cloud",
          controllerAs: "vm"
        });
    }
})();