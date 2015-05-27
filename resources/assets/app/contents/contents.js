(function () {
    'use strict';

    angular
        .module('app.contents')
        .controller('Content', Content);

    Content.$inject = ['Content', '$stateParams', '$state', 'Spinner', '$modal'];

    /* @ngInject */
    function Content(Content, $stateParams, $state, Spinner, $modal) {
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
      vm.share = share;


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
            $state.go('clouds.cloud.contents', {path: removeSlash(path)});
        }

        function changeDirectory() {
          var index,
            path;
          index = vm.selectedContents[0].path.slice();
          path = vm.path + (index !== -1 ? '/' : '') + vm.selectedContents[0].path.slice(index !== -1 ? index : 0);
          openFolder(path);
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
          Spinner.startSpin();
            var path = convertPath(vm.selectedContents[0].path);
            return Content.rename(cloudId, path, getPath() + newName).then(function() {
                init();
              Spinner.stopSpin();
            });
        }

        function remove() {
          var modalInstance = $modal.open({
            animation: true,
            templateUrl: 'submit-delete-modal.html',
            controller: 'SubmitDeleteCtrl',
            controllerAs: 'vm',
            resolve: {
              contents: function () {
                return vm.selectedContents;
              }
            }
          });

          modalInstance.result.then(function () {
            Spinner.startSpin();
            var count = 0;
            vm.selectedContents.forEach(function(content) {
              count++;
              var path = convertPath(content.path);
              return Content.remove(cloudId, path).then(function(data) {
                if(data.is_deleted === true) {
                  count--;
                  if(count === 0) {
                    init();
                    Spinner.stopSpin();
                  }
                } else {
                  console.log('Error by removing ' + path);
                }
              });
            });
          });
        }

        function properties() {
          $modal.open({
            animation: true,
            templateUrl: 'properties.html',
            controller: 'PropertyCtrl',
            controllerAs: 'vm',
            resolve: {
              content: function () {
                return vm.selectedContents[0];
              }
            }
          });
        }

        function move(newName) {
          var path = convertPath(vm.selectedContents[0].path);
          Content.move(cloudId, path, cloudId, newName).then(function(data) {
            console.log(data);
          });
        }

        function convertPath(path) {
            return path.replace(/\//g, '\\');
        }

        function reset() {
            vm.openRenameForm = false;
        }

      function removeSlash(path) {
        var last = path.length - 1;
        if(path[last] === '/') {
          return path.slice(0, last);
        }
        return path;
      }

      function getPath() {
        return (vm.path !== '/') ? (vm.path + '/') : ('/');
      }

      function share() {
        var path = convertPath(vm.selectedContents[0].path);
        Content.share(cloudId, path).then(function(data) {
          //console.log(data);
        })
      }
    }
})();