(function () {
    'use strict';

    angular
        .module('models')
        .factory('Cloud', CloudsModel);

    CloudsModel.$inject = ['$resource'];

    /* @ngInject */
    function CloudsModel($resource) {
        var clouds = $resource('/clouds/:cloudId', {cloudId: '@id'}),
            service = {
                fetch: fetch
            };

        return service;

        ////////////////

        function fetch() {
            return clouds.query();
        }
    }
})();