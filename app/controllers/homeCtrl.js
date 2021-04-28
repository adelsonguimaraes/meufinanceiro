angular.module(module).controller('homeCtrl', function ($rootScope, $scope, authenticationAPI, genericAPI, $location, SweetAlert, $uibModal, $timeout) {
    //Verifica Sessao e permissão de acesso
    if (!$rootScope.usuario) { $location.path("/login"); return false; }

    $scope.title = 'Home';

    sliderHorizontal();

    // scroll to active content
    function scrollActiveContent () {
        setTimeout(()=>{
            let timeline_mes = document.querySelector('div .timeline-mes');
            let first_content = document.querySelector('.content');
            let content_active = document.querySelector('div .content-active');
            
            if (content_active!==null) {
                let scroll_x = content_active.offsetLeft - first_content.offsetLeft;
                timeline_mes.scrollTo({ left: scroll_x, behavior: 'smooth' });
            }
        }, 500);
    }

    // scrollando a timeline com o mouse scroll
    $timeout(()=>{
        document.querySelector('div .timeline-mes').addEventListener('wheel', (e)=>{
            document.querySelector('div .timeline-mes').scrollLeft += e.deltaY*2;
        });
    }, 1000);

    // totais
    $scope.total = {
        periodo: '',
        recebimento: 0,
        pagamento: 0,
        liquido: 0
    }

    $scope.meses = [];
    // modelo
    // {
    //     id: 1,
    //     mes: 'JAN',
    //     ano: '2021',
    //     valor: '800.00',
    //     status: 'PAGO', // PAGO, ATRASADO, EMABERTO
    //     ativo: false
    // }

    $scope.listarMesesTimeline = function () {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listarMesesTimeline", "data": '', "class": "movimento", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.meses = response.data.data;
                    $scope.meses.forEach((e)=> {if(e.ativo==='SIM') listarPorMesAno(e)});
                    $rootScope.loadoff();
                    scrollActiveContent ();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }
    $scope.listarMesesTimeline();

    $scope.movimentos = [];
    // {
    //     ativo: "SIM",
    //     data_cadastro: "2021-02-18 12:46:03",
    //     data_corrente: "2021-02-22",
    //     data_edicao: "2021-02-22 01:19:02",
    //     data_inicial: "2021-02-20",
    //     descricao: "",
    //     id: "1",
    //     idcartao: null,
    //     idusuario: "1",
    //     nome: "Internet",
    //     parcela_corrente: "",
    //     quantidade_parcela: "0",
    //     tipo: "PAGAMENTO",
    //     valor_mensal: "50.55"
    // }

    function listarPorMesAno (obj) {
        // verificando se o filtro está preenchido
        var data = { "metodo": "listarPorMesAno", "data": obj, "class": "movimento", request: 'POST' };

        $rootScope.loadon();

        genericAPI.generic(data)
            .then(function successCallback(response) {
                //se o sucesso === true
                if (response.data.success == true) {
                    $scope.movimentos = response.data.data;
                    // alimentando os totais
                    $scope.total.periodo = ($scope.movimentos.length<=0) ? 0 : $scope.movimentos[0].periodo;
                    $scope.total.recebimento = ($scope.movimentos.length<=0) ? 0 : $scope.movimentos[0].total_recebimento;
                    $scope.total.pagamento = ($scope.movimentos.length<=0) ? 0 : $scope.movimentos[0].total_pagamento;
                    $scope.total.liquido = ($scope.movimentos.length<=0) ? 0 : $scope.movimentos[0].total_liquido;
                    $rootScope.loadoff();
                } else {
                    SweetAlert.swal({ html: true, title: "Atenção", text: response.data.msg, type: "error" });
                }
            }, function errorCallback(response) {
                //error
            });	
    }

    $scope.onClickMes = (obj) => {

        if (obj.ativo==='NAO') {
            $scope.meses.forEach((e)=> {
                if(e!==obj) e.ativo = 'NAO';
                else e.ativo = 'SIM';
            });
            listarPorMesAno(obj);
            scrollActiveContent();
            $scope.onClickGoBackDetalhes();
        }
    }

    $scope.onClickValue = (obj) => {
        $scope.selecionado = angular.copy(obj);
        $scope.selecionado.idmovimento = obj.id;
        $scope.selecionado.data_pagamento = (!obj.data_pagamento) ? new Date(obj.data_corrente + ' 00:00:00') : new Date(obj.data_pagamento + ' 00:00:00');
        $scope.selecionado.valor_pago = (obj.status === 'CONFIRMADO') ? formataValor(obj.valor_pago) : formataValor(obj.valor_mensal);

        // se for um cartão formatamos os valores de cada item
        if ($scope.selecionado.movimentos!==undefined) {
            let movs = [];
            $scope.selecionado.movimentos.forEach(e => {
                e.valor_mensal = (e.status === 'CONFIRMADO') ? formataValor(e.valor_pago) : formataValor(e.valor_mensal);
                movs.push(e);
            });
            $scope.selecionado.movimentos = movs;
        }
        
        document.querySelector('.detalhes').classList.add('detalhes_active');
        document.querySelector('.list-values').classList.add('list-values-collaapse');
    }

    $scope.setSubValue = () => {
        let total_pago = 0;
        $scope.selecionado.movimentos.forEach(e => {
            total_pago += Number(desformataValor(e.valor_mensal));
        });
        $scope.selecionado.valor_pago = formataValor(total_pago);
    }

    $scope.onClickGoBackDetalhes = () => {
        $scope.selecionado = null;
        document.querySelector('.detalhes').classList.remove('detalhes_active');
        document.querySelector('.list-values').classList.remove('list-values-collaapse');
    }

    $scope.onClickConfirmarMovimento = (obj) => {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja realmente prosseguir com a operação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, confirmar!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: true,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm) {

                    // caso seja um cartão formatamos o valor mensal
                    if (obj.movimentos!==undefined) {
                        obj.movimentos.forEach((e)=>{
                            e.valor_mensal = desformataValor(e.valor_mensal);
                        });
                    }

                    obj.valor_pago = desformataValor(obj.valor_pago);
                    // data
                    obj.data = obj.data_corrente;

                    let data = { "metodo": "cadastrar", "data": obj, "class": "movimento_mes", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            $rootScope.loadoff();
                            //se o sucesso === true
                            if (response.data.success == true) {
                                listarPorMesAno(obj);
                                $scope.onClickGoBackDetalhes();
                            } else {
                                swal.close();
                                setTimeout(()=>{
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

    $scope.onClickCancelarMovimento = (obj) => {
        SweetAlert.swal({
            title: "Atenção",
            text: "Deseja cancelar a confirmação? Isso tornará o movimentos aberto novamente.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "Sim, prosseguir!",
            cancelButtonText: "Não, cancele!",
            closeOnConfirm: true,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm) {

                    obj.valor_pago = desformataValor(obj.valor_pago);
                    // data
                    obj.data = obj.data_corrente;

                    let data = { "metodo": "remover", "data": obj, "class": "movimento_mes", request: 'POST' };

                    $rootScope.loadon();

                    genericAPI.generic(data)
                        .then(function successCallback(response) {
                            $rootScope.loadoff();
                            //se o sucesso === true
                            if (response.data.success == true) {
                                listarPorMesAno(obj);
                                $scope.onClickGoBackDetalhes();
                            } else {
                                swal.close();
                                setTimeout(()=>{
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