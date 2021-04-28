angular.module(module).controller('mainCtrl', function ($rootScope, $scope, authenticationAPI, genericAPI, $http, $location, $uibModal, $timeout) {
    authenticationAPI.sessionCtrl();

    $rootScope.api = api;
    
    $rootScope.loading = 'none';
    $scope.title = 'Meu Financeiro';

    $rootScope.loadon = function () {
        var load = document.getElementById('loading');
        load.style.display = 'block';
    }
    $rootScope.loadoff = function () {
        var load = document.getElementById('loading');
        load.style.display = 'none';
    }

    $rootScope.menu = [
        {
            'nome': 'Home',
            'icon': 'fa fa-home',
            'url': 'home'
        },
        {
            'nome': 'Movimento',
            'icon': 'fa fa-bar-chart',
            'url': 'movimento'
        },
        {
            'nome': 'Cartão',
            'icon': 'fa fa-credit-card',
            'url': 'cartao'
        },
        {
            'nome': 'Meta',
            'icon': 'fa fa-heart',
            'url': 'meta'
        },
    ];

    $scope.meusDados = function () {
        setTimeout(function(){
            var mh = document.querySelector("#mymenu #menu-header")
            mh.addEventListener("click", function (e) {
                // chama tela de meus dados
                window.location.replace('#meusdados');
                MyMenu.close();
            });
        },300);
    }
    $scope.meusDados();


    $rootScope.setValuesMyMenu = function () {
        if ($rootScope.usuario) {
            // getando menu usuário
            // $rootScope.getMenu();
            MyMenu.setNameinMenu($rootScope.usuario.nome); // nome do usuario
            // if ($rootScope.usuario.foto!=null && $rootScope.usuario.foto!=undefined) MyMenu.setMenuProfile(api + $rootScope.usuario.foto + '?' + moment().valueOf()); // foto do usuario
            // rodape
            MyMenu.setFooter('<span class="version"> v' + version + '</span><a onclick="angular.element(this).scope().logout()"><i class="fa fa-power-off"></i> Sair</a>');
            MyMenu.setMenuItens($rootScope.menu); // itens do menu
            // $rootScope.loadoff();   
        }    
    }
    $rootScope.setValuesMyMenu();

    function verificarVencimento () {
        $http({ 
            method: 'POST',
            url: `${api}src/rest/movimento.php`,
            timeout: 300,
            data: {
                metodo: 'listarPorDiasVencimento'
            }
        });
    }
    verificarVencimento();
});