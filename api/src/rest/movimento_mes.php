<?php

//inclui autoload
require_once 'autoload.php';

//verifica requisição
('movimento_mes_'.$_POST['metodo'])();

function movimento_mes_cadastrar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$movimentos = array();
	if (!empty($data['movimentos'])) {
		foreach($data['movimentos'] as $key) {
			$mov = $key;
			$mov['valor_pago'] = $data['valor_mensal'];
			$mov['data_pagamento'] = $data['data_pagamento'];
			array_push($movimentos, $mov);
		}
	}else { 
		array_push($movimentos, $data);
	}

	foreach($movimentos as $key) {

		// verifincando se já existe cadastro
		// considerando idmovimento e data_corrente
		$control = new movimento_mes_control();
		$resp = $control->buscarPorMovimentoDataCorrente($key['id'], substr($key['data_corrente'], 0, 10));
		if (!$resp['success']) die (json_encode($resp));
		if (!empty($resp['data'])) {
			$resp['success'] = false;
			$resp['msg'] = "Erro, este movimento já foi confirmado!";
			die(json_encode($resp));
		}

		$obj = new movimento_mes();
		$obj->setIdmovimento($key['id']);
		$obj->setData_corrente(substr($key['data_corrente'], 0, 10));
		$obj->setData_pagamento(substr($key['data_pagamento'], 0, 10));
		$obj->setValor($key['valor_pago']);
		$control = new movimento_mes_control($obj);
		$response = $control->cadastrar();
		if (!$response['success']) die(json_encode($response));
		$idmovimento_mes = $response['data'];
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
function movimento_mes_remover () {
	$data = $_POST['data'];


	$movimentos = array();
	if (!empty($data['movimentos'])) $movimentos = $data['movimentos'];
	else array_push($movimentos, $data);

	foreach($movimentos as $key) {
		$control = new movimento_mes_control(new movimento_mes($key['idmovimento_mes']));
		$resp = $control->remover();
		if (!$resp['success']) die(json_encode($resp));
	}

	$resp['success'] = true;
	$resp['data'] = true;

	echo json_encode($resp);
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>