(function() {
   'use strict';

    angular
        .module('app.cloudsNavBar')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('clouds', {
          url: "/clouds",
          templateUrl: "build/views/cloudsNavBar/navBar.html",
          controller: "CloudsNavBar",
          controllerAs: "vm"
        });
    }
})();