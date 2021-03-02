<?php

//inclui autoload
require_once 'autoload.php';

//verifica requisição
('movimento_'.$_POST['metodo'])();

function movimento_cadastrar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$obj = new movimento();
	$obj->setIdusuario($data['idusuario']);
	$obj->setIdcartao((intval($data['idcartao']<=0) ? null : $data['idcartao'] ));
	$obj->setNome($data['nome']);
	$obj->setDescricao($data['descricao']);
	$obj->setValor_mensal($data['valor_mensal']);
	$obj->setQuantidade_parcela(intval($data['quantidade_parcela']));
	$obj->setData_inicial(substr($data['data_inicial'], 0, 10));
	$obj->setTipo($data['tipo']);
	$control = new movimento_control($obj);
	$response = $control->cadastrar();
	if (!$response['success']) die(json_encode($response));
	$idmovimento = $response['data'];

	// if (intval($data['idcartao'])>0) {
	// 	$control = new movimento_control();
	// 	$resp = $control->atualizarCartao($idmovimento, $data['idcartao']);
	// 	if (!$resp['success']) die(json_encode($resp));
	// }

	echo json_encode($response);
}
function movimento_atualizar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$obj = new movimento();
	$obj->setId($data['id']);
	$obj->setIdusuario($data['idusuario']);
	$obj->setIdcartao((intval($data['idcartao']<=0) ? null : $data['idcartao'] ));
	$obj->setNome($data['nome']);
	$obj->setDescricao($data['descricao']);
	$obj->setValor_mensal($data['valor_mensal']);
	$obj->setQuantidade_parcela(intval($data['quantidade_parcela']));
	$obj->setData_inicial(substr($data['data_inicial'], 0, 10));
	$obj->setTipo($data['tipo']);
	$control = new movimento_control($obj);
	$response = $control->atualizar();
	if (!$response['success']) die(json_encode($response));

	// if (intval($data['idcartao'])>0) {
	// 	$control = new movimento_control();
	// 	$resp = $control->atualizarCartao($data['id'], $data['idcartao']);
	// 	if (!$resp['success']) die(json_encode($resp));
	// }

	echo json_encode($response);
}
function movimento_buscarPorId () {
	$data = $_POST['data'];
	$control = new movimento_control($data['id']);
	$response = $control->buscarPorId();
	echo json_encode($response);
}
function movimento_listar () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];
	$idusuario = $usuario["idusuario"];

	if (!empty($data)) $idusuario = $data["idusuario"];
	$control = new movimento_control();
	$response = $control->listar($idusuario);
	echo json_encode($response);
}

function movimento_listarPaginado () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new movimento_control();
	$response = $control->listarPaginado($usuario["idusuario"], $data["start"], $data["limit"]);
	echo json_encode($response);
}

function movimento_listarMesesTimeline () {
	$usuario = $_POST['usuario'];

	$control = new movimento_control();
	$response = $control->listarMesesTimeline($usuario['idusuario']);
	echo json_encode($response);
}

function movimento_listarPorMesAno () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new movimento_control();
	$response = $control->listarPorMesAno($usuario['idusuario'], $data['data']);
	$movimentos = $response['data'];

	$movs = array();
	$cards = array();
	foreach ($movimentos as $key) {
		if (intval($key['idcartao'])>0) {
			$index = array_search($key['idcartao'], array_column($cards, 'idcartao'));
			if ($index===false) {
				array_push($cards , array(
					"idcartao" => $key['idcartao'],
					"idusuario"=> $key['idusuario'],
					"nome"=> $key['cartao_nome'],
					"descricao"=> "",
					"valor_mensal"=> 0,
					"quantidade_parcela"=> 0,
					"data_inicial"=> "",
					"tipo"=> "PAGAMENTO",
					"ativo"=> "SIM",
					"data_corrente"=> substr($key['data_corrente'], 0, 8) . ((intval($key['cartao_dia_vencimento'])<10) ? '0'.$key['cartao_dia_vencimento'] : $key['cartao_dia_vencimento']),
					"dia_mes"=> ($key['cartao_dia_vencimento'] . ' ' . substr($key['dia_mes'], -3)),
					"parcela_corrente"=> "",
					"status"=> $key['status'],
					"total_recebimento"=> $key['total_recebimento'],
					"total_pagamento"=> $key['total_pagamento'],
					"total_liquido"=> $key['total_liquido'],
					"periodo"=> $key['periodo'],
					"movimentos"=> array()
				));
				$index = array_search($key['idcartao'], array_column($cards, 'identificador_cartao'));
			}
			// somando o valor mensal
			$cards[$index]['valor_mensal'] += $key['valor_mensal'];
			array_push($cards[$index]['movimentos'], $key);
		}else{
			array_push($movs, $key);
		}
	}

	// mescando os movimentos e os cartões
	$movimentos = array_merge($movs, $cards);

	// ordernando os movimentos pela data corrente
	$data_corrente = array_column($movimentos, 'data_corrente');
	array_multisort($data_corrente, SORT_ASC, $movimentos);

	$response['success'] = true;
	$response['data'] = $movimentos;
	
	echo json_encode($response);
}

function movimento_desativar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	$control = new movimento_control();
	$response = $control->desativar($data['idagenda']);
	echo json_encode($response);
}
function movimento_deletar () {
	$data = $_POST['data'];
	$banco = new movimento();
	$banco->setId($data['id']);
	$control = new movimento_control($banco);
	echo json_encode($control->deletar());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>