(function () {
    'use strict';

    angular
        .module('app.addCloud')
        .controller('AddCloud', AddCloud);

    //AddCloud.$inject = [''];

    /* @ngInject */
    function AddCloud() {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'addCloud';

        activate();

        ////////////////

        function activate() {
        }
    }
})();