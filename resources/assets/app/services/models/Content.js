(function () {
  'use strict';

  angular
    .module('models')
    .factory('Content', Content);

  Content.$inject = ['$resource'];

  /* @ngInject */
  function Content($resource) {
    var content = $resource('/clouds/:cloudId/contents/:contentPath',
        {id: '@cloudId', path: '@contentPath'},
        {
          'update': {method: 'PUT'},
          'properties' : {method: 'GET', params: {properties: true}}
        }),
      service = {
        fetch: fetch,
        remove: remove,
        rename: rename,
        properties: properties
      };

    return service;

    ////////////////

    function fetch(cloudId, path) {
      return content.query({cloudId: cloudId, contentPath: path}).$promise;
    }

    function remove(cloudId, path) {
      return content.remove({cloudId: cloudId, contentPath: path}).$promise;
    }

    function rename(cloudId, path, newName) {
      return content.update({cloudId: cloudId, contentPath: path}, {newPath: newName}).$promise;
    }

    function properties(cloudId, path) {
      return content.properties({cloudId: cloudId, contentPath: path}).$promise;
    }
  }
})();