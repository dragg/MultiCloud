(function() {
   'use strict';

    angular
        .module('app.addCloud')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('clouds.add', {
          url: "/add",
          templateUrl: "build/views/addCloud/addCloud.html",
          controller: "AddCloud",
          controllerAs: "vm"
        });
    }
})();