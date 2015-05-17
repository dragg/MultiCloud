(function () {
  'use strict';

  angular
    .module('common')
    .service('Spinner', spinner);

  spinner.$inject = ['usSpinnerService'];

  /* @ngInject */
  function spinner(usSpinnerService) {
    var service = {
      startSpin: startSpin,
      stopSpin: stopSpin
    };

    return service;

    ////////////////

    function startSpin() {
      usSpinnerService.spin('spinner');
    }

    function stopSpin() {
      usSpinnerService.stop('spinner');
    }
  }
})();