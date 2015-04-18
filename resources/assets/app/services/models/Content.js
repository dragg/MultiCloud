(function () {
    'use strict';

    angular
        .module('models')
        .factory('Content', Content);

    Content.$inject = ['$resource'];

    /* @ngInject */
    function Content($resource) {
        var content = $resource('/clouds/:cloudId/contents/:contentPath',
                {id: '@cloudId', path: '@contentPath'}),
            service = {
            fetch: fetch
        };

        return service;

        ////////////////

        function fetch(cloudId, path) {
            return content.query({cloudId: cloudId, contentPath: path}).$promise.then(function(data){
                return data;
            });
        }
    }
})();