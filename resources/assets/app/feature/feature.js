(function () {
    'use strict';

    angular
        .module('app.feature')
        .controller('Feature', Feature);

    Feature.$inject = [];

    /* @ngInject */
    function Feature() {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'Feature';

        activate();

        ////////////////

        function activate() {

        }
    }
})();