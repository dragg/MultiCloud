(function () {
  'use strict';

  angular
    .module('app.clouds')
    .controller('Clouds', Clouds);

  Clouds.$inject = ['CloudModel', '$state', '$scope'];

  /* @ngInject */
  function Clouds(CloudModel, $state, $scope) {
    /* jshint validthis: true */
    var vm = this;

    vm.activate = activate;
    vm.refresh = refresh;
    vm.title = 'Clouds';
    vm.needReload = false;

    activate();

    ////////////////

    function activate() {
      vm.refresh();
    }

    function refresh() {
      CloudModel.fetch().$promise
        .then(function (clouds) {
          vm.clouds = clouds;
        });
    }

  }
})();