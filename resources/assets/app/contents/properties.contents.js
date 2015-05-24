(function () {
  'use strict';

  angular
    .module('app.contents')
    .controller('PropertyCtrl', Modal);

  Modal.$inject = ['$modalInstance', 'content'];

  /* @ngInject */
  function Modal($modalInstance, content) {
    /* jshint validthis: true */
    var vm = this;

    vm.content = content;
    vm.ok = ok;

    ////////////////

    function ok() {
      $modalInstance.dismiss('cancel');
    }
  }
})();