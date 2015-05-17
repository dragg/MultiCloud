(function() {
   'use strict';

    angular
        .module('app.navigate')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('navigate', {
          controller: "Navigate",
          controllerAs: "NavigateCtrl"
        });
    }
})();