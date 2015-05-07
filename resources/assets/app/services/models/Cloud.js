(function () {
  'use strict';

  angular
    .module('models')
    .factory('CloudModel', CloudsModel);

  CloudsModel.$inject = ['$resource'];

  /* @ngInject */
  function CloudsModel($resource) {
    var clouds = $resource('/clouds/:cloudId', {cloudId: '@id'}, {
        'save': {method: 'PUT', params: {cloudId: '@cloudId', name: '@name'}}
      });

    return {
      fetch: fetch,
      get: get,
      rename: rename,
      remove: remove
    };

    ////////////////

    function fetch() {
      return clouds.query();
    }

    function get(cloudId) {
      return clouds.get({ cloudId: cloudId });
    }

    function rename(cloudId, name) {
      return clouds.save({ cloudId: cloudId, name: name });
    }

    function remove(cloudId) {
      return clouds.remove({ cloudId: cloudId });
    }
  }
})();