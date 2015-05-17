(function () {
  'use strict';

  angular
    .module('app.cloud')
    .controller('Cloud', Cloud);

  Cloud.$inject = ['CloudModel', '$stateParams', '$scope', '$state'];

  /* @ngInject */
  function Cloud(CloudModel, $stateParams, $scope, $state) {
    /* jshint validthis: true */
    var vm = this,
      cloudId = $stateParams.cloudId;

    // variables
    vm.title = 'cloud';
    vm.cloudInfo = undefined;
    vm.info = {
      used: 0,
      available: 0,
      email: '',
      name: ''
    };
    vm.showProperties = false;
    vm.showRenameForm = false;

    //functions
    vm.activate = activate;
    vm.properties = properties;
    vm.renameForm = renameForm;
    vm.remove = remove;
    vm.rename = rename;
    vm.resetRenameForm = resetRenameForm;

    activate();

    ////////////////

    function activate() {
      CloudModel.get(cloudId).$promise
        .then(function (info) {
          vm.cloudInfo = info;
          if(vm.cloudInfo.cloud.type === 1) {
            vm.info = {
              used: info.quota_info.normal,
              available: info.quota_info.quota,
              login: info.email,
              name: info.display_name
            };
          } else if (vm.cloudInfo.cloud.type === 2) {
            vm.info = {
              used: info.usedBytes,
              available: info.availableBytes,
              login: info.login,
              name: info.name
            };
          }

          vm.resetRenameForm();
        });
    }

    function properties() {
      vm.showProperties = !vm.showProperties;
    }

    function renameForm() {
      vm.showRenameForm = !vm.showRenameForm;
    }

    function remove() {
      CloudModel.remove(cloudId).$promise
        .then(function() {
          $state.go('clouds');//go to list of clouds
          $scope.cloudsCtrl.refresh();//refresh list of clouds
        })
    }

    function rename(name) {
      CloudModel.rename(cloudId, name).$promise
        .then(function(data) {
          vm.cloudInfo.cloud = data;
          $scope.cloudsCtrl.refresh();//refresh list of clouds
        })
    }

    function resetRenameForm() {
      vm.name = vm.cloudInfo.cloud.name;
    }
  }
})();