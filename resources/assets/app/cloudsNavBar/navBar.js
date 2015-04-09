(function () {
    'use strict';

    angular
        .module('app.cloudsNavBar')
        .controller('CloudsNavBar', CloudsNavBar);

    CloudsNavBar.$inject = [];

    /* @ngInject */
    function CloudsNavBar() {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'CloudsNavBar';
        vm.clouds = ['DropBox', 'YandexDisk', 'GoogleDrive'];

        activate();

        ////////////////

        function activate() {

        }
    }
})();