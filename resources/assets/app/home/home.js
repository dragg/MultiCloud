(function () {
  'use strict';

  angular
    .module('app.home')
    .controller('Home', Home);

  //Home.$inject = [''];

  /* @ngInject */
  function Home() {
    /* jshint validthis: true */
    var vm = this;

    vm.activate = activate;
    vm.title = 'Home';

    activate();

    ////////////////

    function activate() {
    }
  }
})();