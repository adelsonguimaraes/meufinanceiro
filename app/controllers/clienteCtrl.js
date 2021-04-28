angular.module(module).controller('clienteCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Clientes';

    $scope.obj = {
        id: 0,
        idusuario: 0,
        nome: '',
        celular: '',
        email: '',
        interesse: '',
        valor: formataValor(0),
        entrada: formataValor(0),
        parcela: formataValor(0),
        observacao: '',
        status: 'PROSPECTO',
    }

    $scope.ordenador = "id";
    $scope.reverse = "false";
    $scope.ordernar = function (column) {
        $scope.ordenador = column;
        $scope.reverse = !$scope.reverse;
    }

    $scope.pagination = {
        start: 0,
        limit: 20
    };
    $scope.clientes = [];
    $scope.listarClientes = function () {
        var dataRequest = {
            idusuario: $rootScope.usuario.idusuario,
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
                    $scope.clientes = response.data.data;
                    $rootScope.loadoff();
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
            idusuario: 0,
            nome: '',
            celular: '',
            email: '',
            interesse: '',
            valor: formataValor(0),
            entrada: formataValor(0),
            parcela: formataValor(0),
            observacao: '',
            status: 'PROSPECTO'
        }
    }
    $scope.salvarNovo = function (obj) {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente prosseguir com a operação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, iniciar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
            function (isConfirm) {
                if (isConfirm) {
                    var copy = angular.copy(obj);
                    copy.celular = obj.celular.toString().replace(/[^\d]+/g,'');
                    copy.valor = desformataValor(obj.valor | 0);
                    copy.entrada = desformataValor(obj.entrada | 0);
                    copy.parcela = desformataValor(obj.parcela) | 0;
                    
                    var metodo = "cadastrar";
                    if (copy.id>0) metodo = "atualizar";

                    var data = { "metodo": metodo, "data": copy, "class": "cliente", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $rootScope.loadoff();
                                SweetAlert.swal({ html: true, title: "Sucesso", text: 'Cliente salvo com sucesso!', type: "success" });

                                $scope.cancelaNovo();
                                $scope.listarClientes();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        }); 
                }
            }); 
    }

    $scope.setStatus = function (obj) {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente prosseguir com a operação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, iniciar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: true,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm) {
                    var copy = angular.copy(obj);
                    copy.celular = obj.celular.replace(/[^\d]+/g, '');
                    copy.valor = desformataValor(obj.valor | 0);
                    copy.entrada = desformataValor(obj.entrada | 0);
                    copy.parcela = desformataValor(obj.parcela) | 0;

                   var data = { "metodo": 'atualizar', "data": copy, "class": "cliente", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $rootScope.loadoff();
                                // SweetAlert.swal({ html: true, title: "Sucesso", text: 'Dados atualizar com sucesso!', type: "success" });
                                MyToast.show('Dados atualizar com sucesso!', 3);

                                $scope.cancelaNovo();
                                $scope.listarClientes();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        }); 
                }else{
                    $scope.listarClientes();
                }
            }
        );
    }

    $scope.editar = function (obj) {
        $scope.novo = true;
        $scope.obj = {
            id: obj.id,
            idusuario: obj.idusuario,
            nome: obj.nome,
            celular: obj.celular,
            email: obj.email,
            interesse: obj.interesse,
            valor: formataValor(obj.valor | 0),
            entrada: formataValor(obj.entrada | 0),
            parcela: formataValor(obj.parcela | 0),
            observacao: obj.observacao,
            status: obj.status
        }
    }

    $scope.verNaHome = function (obj) {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente prosseguir com a operação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, iniciar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: false,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm) {
                    var copy = angular.copy(obj);
                    copy.celular = obj.celular.replace(/[^\d]+/g, '');
                    copy.valor = formataValor(obj.valor | 0),
                    copy.entrada = desformataValor(obj.entrada | 0);
                    copy.parcela = desformataValor(obj.parcela | 0);
                    copy.verhome = obj.verhome;
                    
                    var data = { "metodo": "atualizar", "data": copy, "class": "cliente", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $rootScope.loadoff();
                                SweetAlert.swal({ html: true, title: "Sucesso", text: 'Dados atualizados com sucesso!', type: "success" });

                                $scope.cancelaNovo();
                                $scope.listarClientes();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        });
                }else{
                    obj.verhome = !obj.verhome;
                }
            }); 
    }

    $scope.filtrar = function () {
        var modalInstance = $uibModal.open({
            templateUrl: 'app/views/modal/filtroCliente.html',
            controller: filtroClienteCtrl,
            size: 'lg',
            backdrop: 'static',
            resolve: {
                // obj: function () {
                //     return obj;
                // }
                parentScope: $scope
            }
        });

        function filtroClienteCtrl($scope, $uibModalInstance, parentScope) {
           $scope.obj = {
                nome: '',
                celular: '',
                status: 'TODOS',
                interesse: '',
                start: parentScope.pagination.start,
                limit: parentScope.pagination.limit,
            };

            $scope.ok = function (obj) {

                if (obj === undefined) {
                    SweetAlert.swal({ html: true, title: "Atenção", text: "Informe pelo menos um campo para filtrar", type: "error" });
                    return false;
                }

                var copy = angular.copy(obj);
                var data = { "metodo": "filtrar", "data": copy, "class": "cliente", request: 'GET' };

                $rootScope.loadon();

                genericAPI.generic(data)
                    .then(function successCallback(response) {
                        //se o sucesso === true
                        if (response.data.success == true) {
                            parentScope.clientes = response.data.data;
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

    // compartilhamento de cliente
    $scope.compartilhar = function () {
        var modalInstance = $uibModal.open({
            templateUrl: 'app/views/modal/compartilharCliente.html',
            controller: compartilharClienteCtrl,
            size: 'lg',
            backdrop: 'static',
            resolve: {
                // obj: function () {
                //     return obj;
                // }
                parentScope: $scope
            }
        });

        function compartilharClienteCtrl($scope, $uibModalInstance, parentScope) {
           $scope.obj = {
                idconsultor: '',
                clientes: [],
                removidos: []
            };

            // listando consultores
            function listarConsultores () {
                $scope.consultores = [];
                var data = { "metodo": "listarPorSuperior", "data": "", "class": "usuario", request: 'GET' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $scope.consultores = response.data.data;
                                $scope.obj.idconsultor = response.data.data[0].id;
                                $scope.listarClientes($scope.obj.idconsultor);
                                $rootScope.loadoff();
                            } else {
                                $rootScope.loadoff();
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        });
            }
            listarConsultores();

            $scope.clientes = [];
            // listando clientes para compartilhar
            $scope.listarClientes = function (idconsultor) {

                var dataRequest = {
                    idconsultor: idconsultor
                };
                var data = { "metodo": "listarParaCompartilhar", "data": dataRequest, "class": "cliente", request: 'GET' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            //se o sucesso === true
                            if (response.data.success == true) {
                                $scope.clientes = response.data.data;
                                $scope.clientesChecados = []; // limpando checados
                                $scope.clientesRemover = []; // limpando removidos
                                for (var i of $scope.clientes) if (i.checked) $scope.clientesChecados.push(i);
                                $rootScope.loadoff();
                            } else {
                                $rootScope.loadoff();
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        });
            };

            $scope.clientesRemover = [];
            $scope.clientesChecados = [];
            $scope.addCliente = function (obj) {
                // se checado adiciona se não remove
                if (obj.checked) {
                    $scope.clientesChecados.push(obj); // adicionando o cliente como checado
                    $scope.clientesRemover.splice($scope.clientesRemover.indexOf(obj), 1); // removendo de adicionados
                }else{
                    $scope.clientesChecados.splice($scope.clientesChecados.indexOf(obj), 1); // adicionando cliente como removido
                    $scope.clientesRemover.push(obj); // removendo de removidos
                }
            }

            $scope.ok = function (obj) {
                
                if ($scope.clientesChecados.length<=0) {
                    SweetAlert.swal({ html: true, title: "Atenção", text: "Selecione os clientes que deseja compartilhar!", type: "error" });
                    return false;
                }

                var copy = angular.copy(obj);
                copy.clientes = $scope.clientesChecados;
                copy.removidos = $scope.clientesRemover;
                var data = { "metodo": "compartilhar", "data": copy, "class": "cliente", request: 'GET' };

                $rootScope.loadon();

                genericAPI.generic(data)
                    .then(function successCallback(response) {
                        //se o sucesso === true
                        if (response.data.success == true) {
                            $scope.listarClientes($scope.obj.idconsultor);
                            // $rootScope.loadoff();
                            // $uibModalInstance.dismiss('cancel');
                            MyToast.show("Compartilhamentos realizados com sucesso!");
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