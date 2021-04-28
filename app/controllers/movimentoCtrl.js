angular.module(module).controller('movimentoCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Meus Movimentos';

    function reset() {
        $scope.novo = false;
        $scope.obj = {
            id: 0,
            idusuario: 0,
            idcartao: 0,
            nome: '',
            descricao: '',
            valor_mensal: formataValor(0),
            dia_vencimento: 0,
            tipo: '',
            ativo: 'SIM'
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
        limit: 200,
        page: 1,
        total_page: 1,
        limits: [20, 50, 100, 200, 500],
        active: 'SIM'
    };
    $scope.prev = function () {
        if ($scope.pagination.page>1) {
            $scope.pagination.page--;
            $scope.pagination.start -= $scope.pagination.limit;
            $scope.listarMovimentos();
        }
    }
    $scope.next = function () {
        if ($scope.pagination.page<$scope.pagination.total_page) {
            $scope.pagination.page++;
            $scope.pagination.start += $scope.pagination.limit;
            $scope.listarMovimentos();
        }
    }
    $scope.selectLimit = function () {
        $scope.pagination.page=1;
        $scope.pagination.start=0;
        $scope.listarMovimentos();
    }
    $scope.selectActive = function () {
        $scope.listarMovimentos();
    }
    $scope.refresh = function () {
        $scope.listarMovimentos();
    }

    // --------------------------------------------

    $scope.parcelas = ['FIXO'];
    function gerarParcelas () {
        let parcela = 0;
        while (parcela<72) {
            parcela++;
            $scope.parcelas.push(parcela.toString());
        }
    }
    gerarParcelas();

    $scope.cartoes = [];
    $scope.listarCartoes = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": '', "class": "cartao", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.cartoes = response.data.data;
                    $scope.cartoes.unshift({id: '0', nome: 'SEM', final: 'CARTAO'});

                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarCartoes();

    $scope.movimentos = [];
    $scope.listarMovimentos = function () {
        var dataRequest = {
            idusuario: $rootScope.usuario.idusuario,
            pagination: $scope.pagination
        };
        
        // verificando se o filtro está preenchido
        var data = { "metodo": "listarPaginado", "data": dataRequest, "class": "movimento", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.movimentos = response.data.data;
                    $scope.pagination.total_page = Math.round(Number(response.data.total)/Number($scope.pagination.limit));
                    if ($scope.pagination.total_page<=0) $scope.pagination.total_page=1;
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarMovimentos();

    $scope.novo = false;
    $scope.cadNovo = function () {
        $scope.novo = true;
    }

    $scope.selecionaCartao = function (obj) {
        if (Number(obj.idcartao)>0) {
            obj.tipo = 'PAGAMENTO';
        }
    }

    $scope.cancelar = function () {
        reset();
    }
    $scope.salvar = function (obj) {

        // tratando a data da validade
        let copy = angular.copy(obj); // clonando obj
        copy.idusuario = $scope.usuario.idusuario; // adicionando id de usuario
        copy.valor_mensal = desformataValor(copy.valor_mensal);

        // verificando se o dia do movimento é o mesmo do cartão
        if (Number(copy.idcartao)>0) {
            let mes = (copy.data_inicial.getMonth()+1);
            mes = (mes<10) ? '0'+mes : mes;
            let ano = copy.data_inicial.getFullYear();
            $scope.cartoes.forEach((e) => {
                if (parseInt(e.id)===parseInt(copy.idcartao)) {
                    copy.data_inicial = (ano + '-' + mes + '-'+ e.dia_vencimento);
                }
            });
        }
        
        // decidindo o metodo
        var metodo = "cadastrar";
        if (copy.id>0) metodo = "atualizar";

        // montando o obj de request
        var data = { "metodo": metodo, "data": copy, "class": "movimento", request: 'POST' };

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
                    $scope.listarMovimentos(); // re-listando os itens da tabela
                } else {
                    swal.close();
                    $timeout(()=>{
                        SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                    }, 200);
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
                                $scope.listarMovimentos();
                            } else {
                                SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                            }
                        }, function errorCallback(response) {
                            //error
                        }); 
                }else{
                    $scope.listarMovimentos();
                }
            }
        );
    }

    $scope.editar = function (obj) {

        $scope.novo = true;
        $scope.obj = {
            id: obj.id,
            idusuario: obj.idusuario,
            idcartao: (obj.idcartao===null) ? '0' : obj.idcartao,
            nome: obj.nome,
            descricao: obj.descricao,
            valor_mensal: formataValor(obj.valor_mensal),
            quantidade_parcela: ((obj.quantidade_parcela<=0) ? 'FIXO' : obj.quantidade_parcela),
            data_inicial: new Date(obj.data_inicial + ' 00:00:00'),
            tipo: obj.tipo,
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

    $scope.remover = function (obj) {

        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente remover este movimento?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, remover!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: true,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm) {

                    // tratando a data da validade
                    let copy = angular.copy(obj); // clonando obj
                    copy.idusuario = $scope.usuario.idusuario; // adicionando id de usuario
                    
                    // montando o obj de request
                    var data = { "metodo": 'remover', "data": copy, "class": "movimento", request: 'POST' };

                    // ativando modal de loading
                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            $rootScope.loadoff(); // removendo modal de loading
                            //se o sucesso === true
                            if (response.data.success == true) {
                                // mostrando mensagem de sucesso
                                SweetAlert.swal({ html: true, title: "Sucesso", text: 'Ddados salvos com sucesso!', type: "success" });

                                $scope.cancelar(); // limpando e escondendo o formulário
                                $scope.listarMovimentos(); // re-listando os itens da tabela
                            } else {
                                swal.close();
                                $timeout(()=>{
                                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                                }, 200);
                            }
                        }, function errorCallback(response) {
                            //error
                        });
                }
            }
        );
    }

});