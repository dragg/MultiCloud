(function () {
  'use strict';

  angular
    .module('directives')
    .directive('mcCloudIcon', CloudIcon);

  //CloudIcon.$inject = [];

  /* @ngInject */
  function CloudIcon()
  {
    // Usage:
    // 
    // Creates:
    // 
    var directive = {
      restrict: 'EA',
      replace: true,
      template: '<img data-ng-src="{{IconCtrl.path}}" />',
      scope: {
        type: '=type',
        path: '=path',
        ext: '=ext'
      },
      controllerAs: 'IconCtrl',
      controller: controller

    };
    return directive;

    function controller($scope) {
      var vm = this,
        extension = $scope.ext !== undefined ? $scope.ext : 'png',
        name;

      switch($scope.type) {
        case 1:
          name = 'dropbox';
          break;
        case 2:
          name = 'yandex-disk';
          break;
        case 3:
          name = 'google-drive';
          break;
      }

      vm.path = $scope.path + name + '.' + extension;
    }
  }
})();