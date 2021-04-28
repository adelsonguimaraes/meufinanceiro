angular.module(module).controller('consultorCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Consultores';

    $scope.obj = {
        consultor: "",
        alvo: "cliente"
    }

    $scope.view = "";

    // listando consultores
    $scope.consultores = [];
    $scope.listarConsultores = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listarPorSuperior", "data": "", "class": "usuario", request: 'GET' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.consultores = response.data.data;
                    if ($scope.consultores.length) $scope.obj.consultor = $scope.consultores[0];
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarConsultores();

    $scope.filtrar = function (obj) {
        sessionStorage.setItem("consultor_criterios", JSON.stringify(obj));
        $scope.view = `app/views/consultor_${obj.alvo}.html`;
    }
    $scope.limparFiltro = function (obj) {
        sessionStorage.removeItem("consultor_criterios");
        $scope.view = "";
    }

    $scope.novo = false;
    $scope.novoCadastro = function () {
        $scope.novo = true;
        reset();
    }
    $scope.cancelar = function () {
        $scope.novo = false;
        reset();
    }

    function reset() {
        $scope.objcad = {
            id: "",
            email: "",
            nome: "",
            procentagem: "",
        }
    }  
    reset();

    $scope.editar = function (obj) {
        $scope.novo = true;
        reset();
        $scope.objcad = {
            id: obj.id,
            email: obj.email,
            nome: obj.nome,
            porcentagem: obj.porcentagem
        }
    }

    $scope.salvar = function (objcad) {

        objcopy = angular.copy(objcad);
        objcopy.porcentagem = desformataValor(objcopy.porcentagem);

        var data = { 
            "metodo": (+objcad.id<=0) ? "cadastrar" : "atualizar",
            "data": objcopy, 
            "class": "usuario",
            request: 'POST' 
        };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.listarConsultores();
                    $scope.cancelar();
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                    $rootScope.loadoff();
                }
            }, function errorCallback(response) {
                //error
            });
    }

    // desativar usuario e importar dados
    $scope.objdes = {
        importar: "SIM"
    }
    $scope.desativar = function (obj, user) {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente prosseguir com a operação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, desativar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: false,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm) {
                    var objcopy = angular.copy(obj);
                    objcopy.idusuario = user.id;
                    
                    var data = {
                        "metodo": "desativar",
                        "data": objcopy,
                        "class": "usuario",
                        request: 'POST'
                    };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $scope.listarConsultores();
                                $scope.cancelar();
                                $rootScope.loadoff();

                                SweetAlert.swal({ html: true, title: "Sucesso", text: 'O consultor foi desativado!', type: "success" });
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                                $rootScope.loadoff();
                            }
                        }, function errorCallback(response) {
                            //error
                        });
                }
            });
    }
});