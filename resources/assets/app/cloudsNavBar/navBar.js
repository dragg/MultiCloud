(function () {
    'use strict';

    angular
        .module('app.cloudsNavBar')
        .controller('CloudsNavBar', CloudsNavBar);

    CloudsNavBar.$inject = ['Cloud'];

    /* @ngInject */
    function CloudsNavBar(Cloud) {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'CloudsNavBar';

        activate();

        ////////////////

        function activate() {
            vm.clouds = Cloud.fetch();
        }
    }
})();