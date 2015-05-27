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
          'share': {method: 'GET', params: {share: true}, isArray: true}
        }),
      service = {
        fetch: fetch,
        remove: remove,
        rename: rename,
        share: share,
        move: move
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

    function move(cloudId, path, newCloudId, pathTo) {
      return content.update({cloudId: cloudId, contentPath: path}, {newCloudId: newCloudId,newPath: pathTo}).$promise;
    }

    function share(cloudId, path) {
      return content.share({cloudId: cloudId, contentPath: path}).$promise;
    }
  }
})();