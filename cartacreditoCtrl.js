angular.module(module).controller('cartacreditoCtrl', function ($rootScope, $scope, $location, genericAPI, $uibModal, SweetAlert, $timeout, especialCharMask) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Cartas de Crédito';

    $scope.obj = {
        id: 0,
        idmodalidade: 0,
        idtaxaadministrativa: '',
        valor: '',
        entrada: '',
        parcela: ''
    }

    $scope.modalidades = [];
    $scope.listarModalidades = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": '', "class": "tipotaxa", request: 'GET' };

        // $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    response.data.data.unshift({
                        descricao: "Todos",
                        id: 0
                    });
                    $scope.modalidades = response.data.data;
                    // $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            }); 
    }
    $scope.listarModalidades();

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
                    $scope.obj.idtaxaadministrativa = $scope.taxas[0].id;
                    // $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            }); 
    }
    $scope.listartaxas();

    $scope.listarCartas = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listar", "data": '', "class": "cartacredito", request: 'GET' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.cartas = response.data.data;
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarCartas();

    $scope.novo = false;
    $scope.cadNovo = function () {
        $scope.novo = true;
        $scope.obj.idtaxaadministrativa = $scope.taxas[0].id;
    }
    $scope.cancelaNovo = function () {
        $scope.novo = false;
        $scope.obj = {
            id: 0,
            idtaxaadministrativa: '',
            valor: '',
            entrada: '',
            parcela: ''
        }
    }
    $scope.salvarNovo = function (obj) {
        var copy = angular.copy(obj);
        copy.valor = desformataValor(obj.valor);
        copy.entrada = desformataValor(obj.entrada);
        copy.parcela = desformataValor(obj.parcela);

        var metodo = "cadastrar";
        if (copy.id>0) metodo = "atualizar";

        var data = { "metodo": metodo, "data": copy, "class": "cartacredito", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $rootScope.loadoff();
                    SweetAlert.swal({ html: true, title: "Sucesso", text: 'Carta cadastrada com sucesso!', type: "success" });

                    $scope.cancelaNovo();
                    $scope.listarCartas();
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
            idtaxaadministrativa: obj.idtaxaadministrativa,
            valor: formataValor(obj.valor),
            entrada: formataValor(obj.entrada),
            parcela: formataValor(obj.parcela)
        }
    }


    // $scope.cadastrar = function (obj) {
        
    //     var data = { 
    //         "metodo": "cadastrar", 
    //         "data": obj,
    //         "class": "visitante", 
    //         request: 'POST' 
    //     };

    //     $rootScope.loadon();

    //     genericAPI.generic(data)
    //         .then(function successCallback(response) {
    //             //se o sucesso === true
    //             if (response.data.success == true) {
    //                 $rootScope.loadoff();
    //                 SweetAlert.swal({ html: true, title: "Sucesso", text: 'Visita cadastrada com sucesso!', type: "success" });

    //                 $scope.obj = {
    //                     idvisitante: 0,
    //                     nome: '',
    //                     cpfcnpj: '',
    //                     data: '',
    //                     horario: '',
    //                 }
    //             } else {
    //                 SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
    //             }
    //         }, function errorCallback(response) {
    //             //error
    //         });	
    // }

    $scope.simular = function (obj) {
        var modalInstance = $uibModal.open({
            templateUrl: 'app/views/modal/simulacao.html',
            controller: simulacaoCartaCreditoCtrl,
            size: 'lg',
            backdrop: 'static',
            resolve: {
                carta: function () {
                    return obj;
                },
                parentScope: $scope
            }
        });

        function simulacaoCartaCreditoCtrl($scope, $uibModalInstance, carta, parentScope) {
            let entrada = ((+carta.entrada)+(+carta.parcela)).toFixed(2); // calculando a entrada

            $scope.obj = {
                modalidade: carta.modalidade,
                valor: formataValor(carta.valor),
                inclusao: formataValor(carta.entrada),
                entrada: formataValor(entrada),
                parcela: formataValor(carta.parcela),
                // negociado
                valornegociado: formataValor(carta.valor),
                taxa: carta.taxa+'%',
                valorcomtaxa: calculaValorComTaxa(carta.valor, carta.taxa),
                valorfinal: 0,
                parcelamento: 0,
                ocultar: true,
                valorconsultor: calculaValorConsultor(carta.valor)
            };
            // $scope.obj.valorfinal = calculaValorFinal($scope.obj.valorcomtaxa, carta.entrada);
            $scope.obj.valorfinal = calculaValorFinal($scope.obj.valorcomtaxa, $scope.obj.entrada);
            $scope.obj.parcelamento = calculaParcelamento($scope.obj.valorfinal, carta.parcela);

            console.log($scope.obj);

            $scope.clientes = [];
            $scope.listarClientes = function () {
                // verificando se o filtro está preenchido
                var data = { "metodo": "listar", "data": '', "class": "cliente", request: 'GET' };
        
                // $rootScope.loadon();
        
                genericAPI.generic(data)
                    .then(function successCallback(response) {
                        //se o sucesso === true
                        if (response.data.success == true) {
                            $scope.clientes = response.data.data;
                            $scope.obj.idcliente = $scope.clientes[0].id;
                            // $rootScope.loadoff();
                        } else {
                            SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                        }
                    }, function errorCallback(response) {
                        //error
                    }); 
            }
            // $scope.listarClientes();

            $scope.modalidades = parentScope.modalidades;
            $scope.cartas = parentScope.cartas;
           
            function calculaValorComTaxa (valor, taxa) {
                valor = desformataValor(valor);
                valor = parseFloat(valor)+parseFloat(taxa)/100*parseFloat(valor);
                return formataValor(valor.toFixed(2));
            }
            function calculaValorFinal (valor, entrada) {
                valor = desformataValor(valor);
                entrada = desformataValor(entrada);
                let result = parseFloat(valor)-parseFloat(entrada);
                return formataValor(result.toFixed(2));
            }
            function calculaParcelamento (valor, parcela) {
                valor = desformataValor(valor);
                //Math.trunc pegando apenas a parte inteira do valor
                let result = parseFloat(valor)/parseFloat(parcela);
                return Math.trunc(result.toFixed(2)) + 'x de ' + formataValor(parcela);
            }
            function calculaValorConsultor (valor) {
                valor = desformataValor(valor);
                let result = 0.7/100*parseFloat(valor);
                return formataValor(Math.round(result));
            }

            $scope.alteraValor = function (item, $event) {
                // if (+desformataValor(item.valornegociado) < +obj.entrada) {
                //     item.valornegociado = formataValor(obj.entrada);
                //     SweetAlert.swal({ html: true, title: "Atenção", text: "Valor Negociado não pode ser abaixo do valor da Entrada!", type: "error" });
                //     return false;
                // }

                // if (($event.keyCode>=48 && $event.keyCode<=57) || ($event.keyCode>=96 && $event.keyCode<=105)) {
                    item.valorcomtaxa = calculaValorComTaxa(item.valornegociado, carta.taxa);
                    item.valorfinal = calculaValorFinal(item.valorcomtaxa, $scope.obj.entrada);
                    item.parcelamento = calculaParcelamento(
                        item.valorfinal, 
                        carta.parcela
                    );
                // }
            }

            $scope.ok = function (obj) {

                SweetAlert.swal({
                    title: "Atenção",
                    text: "Deseja realmente salvar essa simulação para o cliente?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "Sim, salvar!",
                    cancelButtonText: "Não, cancele!",
                    closeOnConfirm: false,
                    closeOnCancel: true
                },
                    function (isConfirm) {
                        if (isConfirm) {
                            console.log(obj);
                            return false;

                            var data = { "metodo": "cadastrar", "data": obj, "class": "cartacredito", request: 'GET' };

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
                    }
                )
                
            }
            $scope.cancel = function () {
                $uibModalInstance.dismiss('cancel');
            }
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
                parcelaabaixo:''
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
                copy.parcelaabaixo = desformataValor(obj.parcelaabaixo);
           
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