angular.module(module).controller('taxaadministrativaCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Taxas Administrativas';

    $scope.obj = {
        id: 0,
        idtipotaxa: 0,
        codigo: '',
        taxa: '',
        porcentagem: ''
    }

    $scope.tipostaxa = [];
    $scope.listarTiposTaxa = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": '', "class": "tipotaxa", request: 'GET' };

        // $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.tipostaxa = response.data.data;
                    $scope.obj.idtipotaxa = response.data.data[0].id;
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            }); 
    }
    $scope.listarTiposTaxa();

    $scope.taxas = [];
    $scope.listartaxas = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": '', "class": "taxaadministrativa", request: 'GET' };

        // $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.taxas = response.data.data;
                    // $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            }); 
    }
    $scope.listartaxas();

    $scope.novo = false;
    $scope.cadNovo = function () {
        $scope.novo = true;
        $scope.obj.idtipotaxa = $scope.tipostaxa[0].id;
    }
    $scope.cancelaNovo = function () {
        $scope.novo = false;
        $scope.obj = {
            id: 0,
            idtipotaxa: 0,
            codigo: '',
            taxa: '',
            porcentagem: ''
        }
    }
    $scope.salvarNovo = function (obj) {
        var copy = angular.copy(obj);
        copy.taxa = desformataValor(obj.taxa);
        copy.porcentagem = desformataValor(obj.porcentagem);

        var metodo = "cadastrar";
        if (copy.id>0) metodo = "atualizar";

        var data = { "metodo": metodo, "data": copy, "class": "taxaadministrativa", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $rootScope.loadoff();
                    SweetAlert.swal({ html: true, title: "Sucesso", text: 'Carta cadastrada com sucesso!', type: "success" });

                    $scope.cancelaNovo();
                    $scope.listartaxas();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            }); 
    }

    $scope.editar = function (obj) {
        $scope.novo = true;
        $scope.obj = {
            id: obj.id,
            idtipotaxa: obj.idtipotaxa,
            codigo: obj.codigo,
            taxa: formataValor(obj.taxa),
            porcentagem: formataValor(obj.porcentagem)
        }
    }

});