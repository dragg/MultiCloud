(function () {
    'use strict';

    angular
        .module('app.cloud')
        .controller('Cloud', Cloud);

    Cloud.$inject = ['Content', '$stateParams', '$state', '$location'];

    /* @ngInject */
    function Cloud(Content, $stateParams, $state, $location) {
        /* jshint validthis: true */
        var vm = this,
            cloudId = $stateParams.cloudId;

        vm.activate = activate;
        vm.fetch = fetch;
        vm.back = back;
        vm.title = 'cloud';
        vm.contents = [];
        vm.path = $stateParams.path;
        vm.changeDirectory = changeDirectory;
        vm.download = download;

        activate();

        ////////////////

        function activate() {
            fetch(vm.path);
        }

        function fetch(path) {

            return Content.fetch(cloudId, path.replace(/\//g, '\\')).then(function(data) {
                vm.contents = data;
                vm.path = path;
            });
        }

        function changeDirectory(path) {
            $state.go('clouds.cloud', {cloudId: cloudId, path: path});
        }

        function back() {
            changeDirectory(vm.path.substring(0, vm.path.lastIndexOf('\/')));
        }

        function download(path) {
            return Content.fetch(cloudId, path.replace(/\//g, '\\')).then(function(data) {
                window.open(data[0]);
                return data;
            });
        }
    }
})();