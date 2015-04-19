(function () {
    'use strict';

    angular
        .module('filters')
        .filter('contentName', function() {
             return function(name) {
                 var response = name;
                 if(name !== undefined && name !== null) {
                     var index = name.lastIndexOf('\/');

                     if(index !== -1) {
                         response = name.substring(index + 1);
                     }
                 }
                 return response;
             }
        });
})();