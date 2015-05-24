(function () {
  'use strict';

  angular
    .module('app.contents')
    .controller('SubmitDeleteCtrl', SubmitDeleteCtrl);

  SubmitDeleteCtrl.$inject = ['$modalInstance', 'contents'];

  /* @ngInject */
  function SubmitDeleteCtrl($modalInstance, contents)
  {
    /* jshint validthis: true */
    var vm = this;

    vm.contents = contents;
    vm.ok = ok;
    vm.cancel = cancel;

    ////////////////

    function ok() {
      $modalInstance.close();
    }

    function cancel() {
      $modalInstance.dismiss('cancel');
    }

  }
})();
