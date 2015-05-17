(function () {
  'use strict';

  angular
    .module('app.navigate')
    .controller('Navigate', Navigate);

  Navigate.$inject = ['$state'];

  /* @ngInject */
  function Navigate($state) {
    /* jshint validthis: true */
    var vm = this;

    vm.activate = activate;
    vm.title = 'Navigate';
    vm.is = is;

    activate();

    ////////////////

    function activate() {
    }

    function is(view) {
      return $state.is(view);
    }
  }
})();