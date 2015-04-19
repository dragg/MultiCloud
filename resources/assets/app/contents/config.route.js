(function() {
   'use strict';

    angular
        .module('app.contents')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('clouds.cloud.contents', {
          url: "/contents/:path",
          templateUrl: "build/views/contents/contents.html",
          controller: "Content",
          controllerAs: "vm"
        });
    }
})();