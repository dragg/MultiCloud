(function () {
    'use strict';

    angular
        .module('filters')
        .filter('contentName', function() {
             return function(name) {
                 return name.substring(name.lastIndexOf('\/') + 1);
             }
        });
})();