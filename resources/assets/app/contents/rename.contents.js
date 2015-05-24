(function () {
  'use strict';

  angular
    .module('app.contents')
    .controller('RenameCtrl', RenameCtrl);

  RenameCtrl.$inject = ['$modalInstance'];

  /* @ngInject */
  function RenameCtrl($modalInstance)
  {
    /* jshint validthis: true */
    var vm = this;

    vm.activate = activate;
    vm.title = 'RenameCtrl';

    activate();

    ////////////////

    function activate() {
    }
  }
})();