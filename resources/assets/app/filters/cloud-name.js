(function () {
  'use strict';

  angular
    .module('filters')
    .filter('cloudType',cloudType);

  function cloudType() {
    return filter;

    function filter(type) {
      var name;

      switch(type) {
        case 1:
          name = 'Dropbox';
          break;
        case 2:
          name = 'Yandex.Disk';
          break;
        case 3:
          name = 'Google Drive';
          break;
      }

      return name;
    }
  }
})();