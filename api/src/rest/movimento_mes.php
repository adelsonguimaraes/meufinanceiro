<?php

//inclui autoload
require_once 'autoload.php';

//verifica requisição
('movimento_mes_'.$_POST['metodo'])();

function movimento_mes_cadastrar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	// verifincando se já existe cadastro
	// considerando idmovimento e data_corrente
	$control = new movimento_mes_control();
	$resp = $control->buscarPorMovimentoDataCorrente($data['idmovimento'], substr($data['data_corrente'], 0, 10));
	if (!$resp['success']) die (json_encode($resp));
	if (!empty($resp['data'])) {
		$resp['success'] = false;
		$resp['msg'] = "Erro, este movimento já foi confirmado!";
		die(json_encode($resp));
	}

	$obj = new movimento_mes();
	$obj->setIdmovimento($data['idmovimento']);
	$obj->setData_corrente(substr($data['data_corrente'], 0, 10));
	$obj->setData_pagamento(substr($data['data_pagamento'], 0, 10));
	$obj->setValor($data['valor_pago']);
	$control = new movimento_mes_control($obj);
	$response = $control->cadastrar();
	if (!$response['success']) die(json_encode($response));
	$idmovimento_mes = $response['data'];

	if (intval($data['idcartao'])>0) {
		$control = new movimento_mes_control();
		$resp = $control->atualizarCartao($idmovimento_mes, $data['idcartao']);
		if (!$resp['success']) die(json_encode($resp));
	}

	echo json_encode($response);
}
function movimento_mes_atualizar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$obj = new movimento_mes();
	$obj->setId($data['id']);
	$obj->setIdmovimento($data['idmovimento']);
	$obj->setData_corrente(substr($data['data_corrente'], 0, 10));
	$obj->setData_pagamento(substr($data['data_pagamento'], 0, 10));
	$obj->setValor($data['valor_pago']);
	$control = new movimento_mes_control($obj);
	$response = $control->atualizar();
	if (!$response['success']) die(json_encode($response));

	if (intval($data['idcartao'])>0) {
		$control = new movimento_mes_control();
		$resp = $control->atualizarCartao($data['id'], $data['idcartao']);
		if (!$resp['success']) die(json_encode($resp));
	}

	echo json_encode($response);
}
function movimento_mes_buscarPorId () {
	$data = $_POST['data'];
	$control = new movimento_mes_control($data['id']);
	$response = $control->buscarPorId();
	echo json_encode($response);
}
function movimento_mes_listar () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];
	$idusuario = $usuario["idusuario"];

	if (!empty($data)) $idusuario = $data["idusuario"];
	$control = new movimento_mes_control();
	$response = $control->listar($idusuario);
	echo json_encode($response);
}

function movimento_mes_listarPaginado () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new movimento_mes_control();
	$response = $control->listarPaginado($usuario["idusuario"], $data["start"], $data["limit"]);
	echo json_encode($response);
}

function movimento_mes_listarMesesTimeline () {
	$usuario = $_POST['usuario'];

	$control = new movimento_mes_control();
	$response = $control->listarMesesTimeline($usuario['idusuario']);
	echo json_encode($response);
}

function movimento_mes_listarPorMesAno () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new movimento_mes_control();
	$response = $control->listarPorMesAno($usuario['idusuario'], $data['data']);
	echo json_encode($response);
}

function movimento_mes_desativar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	$control = new movimento_mes_control();
	$response = $control->desativar($data['idagenda']);
	echo json_encode($response);
}
function movimento_mes_deletar () {
	$data = $_POST['data'];
	$banco = new movimento_mes();
	$banco->setId($data['id']);
	$control = new movimento_mes_control($banco);
	echo json_encode($control->deletar());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>