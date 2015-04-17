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

        activate();

        ////////////////

        function activate() {
            vm.clouds = CloudsModel.fetch();
        }
    }
})();