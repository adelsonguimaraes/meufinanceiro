angular.module(module).controller('homeCtrl', function ($rootScope, $scope, authenticationAPI, genericAPI, $location, SweetAlert, $uibModal, $timeout) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Home';

    console.log($rootScope.usuario);
    
});