(function() {
   'use strict';

    angular
        .module('app.home')
        .config(getRoutes);

    getRoutes.$inject = ['$stateProvider'];

    function getRoutes($stateProvider) {
      $stateProvider
        .state('/', {
          url: "/contents/:path",
          templateUrl: "build/views/home/home.html",
          controller: "Home",
          controllerAs: "HomeCtrl"
        });
    }
})();