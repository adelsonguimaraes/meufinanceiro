<?php
    function formatCel($cel) {
        return "(" .substr($cel, 0, 2). ") " . substr($cel, 2, 5) . "-" . substr($cel, 7, 4); 
    }
    function formatMoeda ( $num ) {
        return 'R$ ' . number_format($num, 2, ',', '.'); // retorna R$000.000,00
    }
    function formatDate ( $date ) {
        $data = new DateTime($date);
        return $data->format('d/m/Y');
    }
    function formatCargo ( $perfil ) {
        return ($perfil==="LIDER") ? "Supervisor(a) de Vendas" : "Consultor(a) de Vendas";
    }
    function formatInteresse ( $interesse ) {
        switch ($interesse) {
            case 'AUTOMOVEL': $interesse = "Automóvel";
            case 'IMOVEL': $interesse = "Imóvel";
            case 'MOTO': $interesse = "Moto";
            case 'SERVICO': $interesse = "Serivico";
            case 'PESADO': $interesse = "Pesado";
        }
        return $interesse;
    }
    function getNomeMes ($n_mes) {
        $meses = array(
            array("nome"=>"JANEIRO", "abreviatura"=>"JAN"), array("nome"=>"FEVEREIRO", "abreviatura"=>"FEV"), array("nome"=>"MARÇO", "abreviatura"=>"MAR"),
            array("nome"=>"ABRIL", "abreviatura"=>"ABR"), array("nome"=>"MAIO", "abreviatura"=>"MAI"), array("nome"=>"JUNHO", "abreviatura"=>"JUN"),
            array("nome"=>"JULHO", "abreviatura"=>"JUL"),array("nome"=>"AGOSTO", "abreviatura"=>"AGO"), array("nome"=>"SETEMBRO", "abreviatura"=>"SET"),
            array("nome"=>"OUTUBRO", "abreviatura"=>"OUT"), array("nome"=>"NOVEMBRO", "abreviatura"=>"NOV"), array("nome"=>"DEZEMBRO", "abreviatura"=>"DEZ")
        );
        return $meses[$n_mes];
    }
?>