angular.module(module).controller('agendaCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Agenda';

    $scope.obj = {
        id: 0,
        idcliente: 0,
        data: new Date(),
        horario: '',
        tipo: 'CONTATO',
        observacao: '',
    }


    $scope.agendas = [];
    $scope.listarAgendas = function () {
        var dataRequest = {
            idusuario: $rootScope.usuario.idusuario
        };

        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": dataRequest, "class": "agenda", request: 'GET' };

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.agendas = response.data.data;
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });
    }
    $scope.listarAgendas();

    $scope.clientes = [];
    $scope.listarClientes = function () {
        var dataRequest = {
            idusuario: $rootScope.usuario.idusuario
        };
        
        // verificando se o filtro está preenchido
        var data = { "metodo": "listarTudo", "data": dataRequest, "class": "cliente", request: 'GET' };

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.clientes = response.data.data;
                    if ($scope.clientes.length>0) $scope.obj.idcliente = $scope.clientes[0].id;
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarClientes();

    $scope.novo = false;
    $scope.cadNovo = function () {
        $scope.novo = true;
    }
    $scope.cancelaNovo = function () {
        $scope.novo = false;
        $scope.obj = {
            id: 0,
            idcliente: 0,
            data: new Date(),
            horario: '',
            tipo: 'CONTATO',
            observacao: '',
        }
    }
    $scope.salvarNovo = function (obj) {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja relamente prosseguir?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, iniciar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
            function (isConfirm) {
                swal.close();
                if (isConfirm) {
                    var copy = angular.copy(obj);
                    copy.datahora = moment(obj.data).format('YYYY-MM-DD') + ' ' + moment(obj.horario).format('HH:mm:ss');
                    
                    var metodo = "cadastrar";
                    if (copy.id>0) metodo = "atualizar";

                    var data = { "metodo": metodo, "data": copy, "class": "agenda", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $rootScope.loadoff();
                                SweetAlert.swal({ html: true, title: "Sucesso", text: 'Agenda salva com sucesso!', type: "success" });

                                $scope.cancelaNovo();
                                $scope.listarAgendas();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        }); 
                }
            
            });
    }

    $scope.desativar = function (obj) {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente desativar esse agendamento?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, iniciar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
            function (isConfirm) {
                swal.close();
                if (isConfirm) {
                    var dataRequest = {
                        idagenda: obj.id
                    }
                    
                    var data = { "metodo": 'desativar', "data": dataRequest, "class": "agenda", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $rootScope.loadoff();
                                SweetAlert.swal({ html: true, title: "Sucesso", text: 'Agenda salva com sucesso!', type: "success" });

                                $scope.listarAgendas();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        });
                }

            });
    }

    $scope.editar = function (obj) {
        $scope.novo = true;
        $scope.obj = {
            id: obj.id,
            idcliente: obj.idcliente,
            data: new Date(obj.datahora),
            horario: new Date(obj.datahora),
            tipo: obj.tipo,
            observacao: obj.observacao,
        }
    }

    $scope.filtrar = function () {
        var modalInstance = $uibModal.open({
            templateUrl: 'app/views/modal/filtroCartaCredito.html',
            controller: filtroCartaCreditoCtrl,
            size: 'lg',
            backdrop: 'static',
            resolve: {
                // obj: function () {
                //     return obj;
                // }
                parentScope: $scope
            }
        });

        function filtroCartaCreditoCtrl($scope, $uibModalInstance, parentScope) {
           $scope.obj = {
                modalidade: '',
                valoracima:'',
                valorabaixo:'',
                entradaacima:'',
                entradaabaixo:'',
                parcelaacima:'',
                parecelaabaixo:''
           };
           $scope.modalidades = parentScope.modalidades;
           $scope.obj.modalidade = $scope.modalidades[0].id;

            $scope.ok = function (obj) {

                if (obj === undefined) {
                    SweetAlert.swal({ html: true, title: "Atenção", text: "Informe pelo menos um campo para filtrar", type: "error" });
                    return false;
                }

                var copy = angular.copy(obj);
                copy.valoracima = desformataValor(obj.valoracima);
                copy.valorabaixo = desformataValor(obj.valorabaixo);
                copy.entradaacima = desformataValor(obj.entradaacima);
                copy.entradaabaixo = desformataValor(obj.entradaabaixo);
                copy.parcelaacima = desformataValor(obj.parcelaacima);
                copy.parecelaabaixo = desformataValor(obj.parecelaabaixo);
           
                var data = { "metodo": "filtrar", "data": copy, "class": "cartacredito", request: 'GET' };

                $rootScope.loadon();

                genericAPI.generic(data)
                    .then(function successCallback(response) {
                        //se o sucesso === true
                        if (response.data.success == true) {
                            parentScope.cartas = response.data.data;
                            $rootScope.loadoff();
                            $uibModalInstance.dismiss('cancel');
                        } else {
                            SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                        }
                    }, function errorCallback(response) {
                        //error
                    }); 
                
            }
            $scope.cancel = function () {
                $uibModalInstance.dismiss('cancel');
            }
        }    
    }

});