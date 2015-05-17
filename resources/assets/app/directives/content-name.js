(function () {
  'use strict';

  angular
    .module('directives')
    .directive('mcContentName', ContentName);

  //ContentName.$inject = [''];

  /* @ngInject */
  function ContentName()
  {
    // Usage:
    // 
    // Creates:
    // 
    var directive = {
      restrict: 'EA',
      replace: true,
      template: '<span>{{vm.name}}</span>',
      scope: {
        object: '=contentObject'
      },
      controllerAs: 'vm',
      controller: controller

    };
    return directive;

    function controller($scope) {
      var vm = this;

      vm.name = ($scope.object.display_name === undefined)
        ? filterName($scope.object.path)
        : $scope.object.display_name;
    }

    function filterName(name) {
      var response = name;

      if(name !== undefined && name !== null) {
        var index = name.lastIndexOf('\/');

        if(index !== -1) {
          response = name.substring(index + 1);
        }
      }
      return response;
    }
  }
})();