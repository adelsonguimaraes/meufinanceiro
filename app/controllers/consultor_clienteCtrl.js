angular.module(module).controller('consultor_clienteCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    
    $scope.pagination = {
        start: 0,
        limit: 20
    };

    $scope.clientes = [];
    $scope.listarClientes = function () {
        var criterios = JSON.parse(sessionStorage.getItem("consultor_criterios"));
        var dataRequest = { 
            idusuario: criterios.consultor.id,
            start: $scope.pagination.start,
            limit: $scope.pagination.limit
        };

        // verificando se o filtro está preenchido
        var data = { "metodo": "listarPaginado", "data": dataRequest, "class": "cliente", request: 'GET' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.agendas = response.data.data;
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarClientes();

    $scope.ordenador = "nome";
    $scope.reverse = "false";
    $scope.ordernar = function (column) {
        $scope.ordenador = column;
        $scope.reverse = !$scope.reverse;
    }

});