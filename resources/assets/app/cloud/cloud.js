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

        vm.title = 'cloud';
        vm.contents = [];
        vm.path = $stateParams.path;
        vm.isSelect = false;
        vm.selectedContents = [];
        vm.isSelectFile = false;
        vm.isSelectFolder = false;
        vm.isMulti = false;

        //vm.activate = init;
        vm.select = select;
        vm.fetch = fetch;
        vm.back = back;
        vm.openFolder = changeDirectory;
        vm.download = download;
        vm.rename = rename;
        vm.remove = remove;
        vm.properties = properties;
        vm.move = move;


        init();

        ////////////////

        function init() {
            fetch(vm.path);
        }

        function select(path) {
            var index = vm.selectedContents.indexOf(path);
            if (index === -1) {
                vm.selectedContents.push(path);
                path.selected = true;
            } else {
                vm.selectedContents.splice(index, 1);
                path.selected = false;
            }

            setSelectedState();
        }

        function setSelectedState() {
            var lengthSelectedContents = vm.selectedContents.length;
            vm.isSelect = lengthSelectedContents > 0;
            vm.isMulti = lengthSelectedContents > 1;
            vm.isFile = lengthSelectedContents === 1 && vm.selectedContents[0].is_dir === false;
            vm.isFolder = lengthSelectedContents === 1 && vm.selectedContents[0].is_dir === true;
        }

        function fetch(path) {
            return Content.fetch(cloudId, path.replace(/\//g, '\\')).then(function (data) {
                vm.contents = data;
                vm.path = path;
            });
        }

        function openFolder(path) {
            $state.go('clouds.cloud', {cloudId: cloudId, path: path});
        }

        function changeDirectory() {
            openFolder(vm.selectedContents[0].name);
        }

        function back() {
            openFolder(vm.path.substring(0, vm.path.lastIndexOf('\/')));
        }

        function download() {
            var path = vm.selectedContents[0].name;

            return Content.fetch(cloudId, path.replace(/\//g, '\\')).then(function (data) {
                window.open(data[0]);
                return data;
            });
        }

        function rename() {
            console.log('rename');
        }

        function remove() {
            console.log('remove');
        }

        function properties() {
            console.log('properties');
        }

        function move() {
            console.log('move');
        }
    }
})();