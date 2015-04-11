(function () {
    'use strict';

    angular
        .module('app.cloudsNavBar')
        .controller('CloudsNavBar', CloudsNavBar);

    CloudsNavBar.$inject = ['CloudsModel'];

    /* @ngInject */
    function CloudsNavBar(CloudsModel) {
        /* jshint validthis: true */
        var vm = this;

        vm.activate = activate;
        vm.title = 'CloudsNavBar';
        vm.clouds = ['DropBox', 'YandexDisk', 'GoogleDrive'];

        activate();

        ////////////////

        function activate() {
            var clouds = CloudsModel.fetch();
            console.log(clouds);
        }
    }
})();