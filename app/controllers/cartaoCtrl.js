angular.module(module).controller('cartaoCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Cartões';

    function reset() {
        $scope.novo = false;
        $scope.obj = {
            id: 0,
            idusuario: 0,
            idbandeira: 0,
            nome: '',
            validade: '',
            final: '',
            dias_vencimento: 0,
            ativo: '',
            sim: 'SIM',
        }
    }
    reset();

    // pagination
    // --------------------------------------------
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
    // --------------------------------------------

    $scope.dias_vencimento = [];
    function gerarDias () {
        let dia = 0;
        while (dia<28) {
            dia++;
            $scope.dias_vencimento.push(dia);
        }
    }
    gerarDias();

    $scope.bandeiras = [];
    $scope.cartoes = [
        {
            id: 1,
            nome: 'Nubank',
            bandeira: 'Master Card',
            validade: '02/2021',
            final: '0000',
            ativo: 'SIM'
        },
        {
            id: 2,
            nome: 'Caixa',
            bandeira: 'Elo',
            validade: '03/2021',
            final: '1111',
            ativo: 'NAO'
        }
    ];


    $scope.listarBandeiras = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": '', "class": "bandeira_cartao", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.bandeiras = response.data.data;
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarBandeiras();

    $scope.listarCartoes = function () {
        var dataRequest = {
            idusuario: $rootScope.usuario.idusuario,
            start: $scope.pagination.start,
            limit: $scope.pagination.limit
        };
        
        // verificando se o filtro está preenchido
        var data = { "metodo": "listarPaginado", "data": dataRequest, "class": "cartao", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.cartoes = response.data.data;
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarCartoes();

    $scope.novo = false;
    $scope.cadNovo = function () {
        $scope.novo = true;
    }
    $scope.cancelar = function () {
        reset();
    }
    $scope.salvar = function (obj) {

        // tratando a data da validade
        let copy = angular.copy(obj); // clonando obj
        copy.idusuario = $scope.usuario.idusuario; // adicionando id de usuario
        let mes = copy.validade.getMonth()+1; if (parseInt(mes)<10) mes = `0${mes}`;
        let ano = copy.validade.getFullYear();
        copy.validade = `${mes}/${ano}`;

        // decidindo o metodo
        var metodo = "cadastrar";
        if (copy.id>0) metodo = "atualizar";

        // montando o obj de request
        var data = { "metodo": metodo, "data": copy, "class": "cartao", request: 'POST' };

        // ativando modal de loading
        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $rootScope.loadoff(); // removendo modal de loading

                    // mostrando mensagem de sucesso
                    SweetAlert.swal({ html: true, title: "Sucesso", text: 'Ddados salvos com sucesso!', type: "success" });

                    $scope.cancelar(); // limpando e escondendo o formulário
                    $scope.listarCartoes(); // re-listando os itens da tabela
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
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

                                $scope.cancelar();
                                $scope.listarCartoes();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        }); 
                }else{
                    $scope.listarCartoes();
                }
            }
        );
    }

    $scope.editar = function (obj) {
        
        // tratando a data
        let split = obj.validade.split('/');
        let mes = parseInt(split[0])+1; if (mes<10) mes = `0${mes}`;
        let data = `${split[1]}-${mes}-01`;
        data = new Date(data);

        $scope.novo = true;
        $scope.obj = {
            id: obj.id,
            idusuario: obj.idusuario,
            idbandeira: obj.idbandeira,
            nome: obj.nome,
            validade: data,
            final: obj.final,
            dia_vencimento: Number(obj.dia_vencimento),
            ativo: obj.ativo
        }
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

});