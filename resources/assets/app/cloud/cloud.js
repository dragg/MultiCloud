(function () {
    'use strict';

    angular
        .module('app.cloud')
        .controller('Cloud', Cloud);

    //Cloud.$inject = [''];

    /* @ngInject */
    function Cloud() {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'cloud';

        activate();

        ////////////////

        function activate() {
        }
    }
})();