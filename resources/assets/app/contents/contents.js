(function () {
    'use strict';

    angular
        .module('app.contents')
        .controller('Content', Content);

    Content.$inject = ['Content', '$stateParams', '$state', '$location'];

    /* @ngInject */
    function Content(Content, $stateParams, $state, $location) {
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
        vm.openRenameForm = false;

        //vm.activate = init;
        vm.select = select;
        vm.fetch = fetch;
        vm.back = back;
        vm.openFolder = changeDirectory;
        vm.download = download;
        vm.rename = rename;
        vm.renameForm = renameForm;
        vm.remove = remove;
        vm.properties = properties;
        vm.move = move;


        init();

        ////////////////

        function init() {
            fetch(vm.path);

            vm.selectedContents = [];
            setSelectedState();
        }

        function select(path) {
            reset();

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
            $state.go('clouds.cloud.contents', {path: path});
        }

        function changeDirectory() {
            openFolder(vm.selectedContents[0].path);
        }

        function back() {
            openFolder(vm.path.substring(0, vm.path.lastIndexOf('\/')));
        }

        function download() {
            var path = convertPath(vm.selectedContents[0].path);

            return Content.fetch(cloudId, path).then(function (data) {
                window.open(data[0]);
                return data;
            });
        }

        function renameForm() {
            vm.openRenameForm = vm.openRenameForm ? false: true;
        }

        function rename(newName) {
            var path = convertPath(vm.selectedContents[0].path);
            return Content.rename(cloudId, path, vm.path + '/' + newName).then(function(data) {
                init();
            });
        }

        function remove() {
            vm.selectedContents.forEach(function(content) {
                var path = convertPath(content.path);
                return Content.remove(cloudId, path).then(function(data) {
                    if(data.is_deleted === true) {
                        init();
                    } else {
                        console.log('Error by removing ' + path);
                    }
                });
            });
        }

        function properties() {
            console.log('properties');
        }

        function move() {
            console.log('move');
        }

        function convertPath(path) {
            return path.replace(/\//g, '\\');
        }

        function reset() {
            vm.openRenameForm = false;
        }
    }
})();