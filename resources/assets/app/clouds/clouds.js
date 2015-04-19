(function () {
    'use strict';

    angular
        .module('app.clouds')
        .controller('Clouds', Clouds);

    Clouds.$inject = ['Cloud'];

    /* @ngInject */
    function Clouds(Cloud) {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'Clouds';

        activate();

        ////////////////

        function activate() {
            vm.clouds = Cloud.fetch();
        }
    }
})();