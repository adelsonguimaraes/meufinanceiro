angular.module(module).controller('consultor_agendaCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    $scope.title = 'Consultores';

    // listando agendas
    $scope.agendas = [];
    $scope.listarAgendas = function () {
        var criterios = JSON.parse(sessionStorage.getItem("consultor_criterios"));
        var dataRequest = { "idusuario": criterios.consultor.id};

        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": dataRequest, "class": "agenda", request: 'GET' };

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
    $scope.listarAgendas();

});