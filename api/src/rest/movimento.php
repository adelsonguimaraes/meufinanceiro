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
	$obj->setIdcartao((intval($data['idcartao']<=0) ? 'null' : $data['idcartao'] ));
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
	$obj->setIdcartao((intval($data['idcartao']<=0) ? 'null' : $data['idcartao']));
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
	$response = $control->listarPaginado($usuario["idusuario"], $data["pagination"]);
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
					"total_recebimento"=> $movimentos[0]['total_recebimento'],
					"total_pagamento"=> $movimentos[0]['total_pagamento'],
					"total_liquido"=> $movimentos[0]['total_liquido'],
					"periodo"=> $key['periodo'],
					"valor_pago"=> 0,
					"data_pagamento"=> $key['data_pagamento'],
					"observacao"=> $key['observacao'],
					"movimentos"=> array()
				));
				$index = array_search($key['idcartao'], array_column($cards, 'idcartao'));
			}
			// somando o valor mensal
			$cards[$index]['valor_pago'] += $key['valor_pago'];
			// $cards[$index]['valor_mensal'] += ($key['valor_pago']>0) ? $key['valor_pago'] : $key['valor_mensal'];
			$cards[$index]['valor_mensal'] += $key['valor_mensal'];
			array_push($cards[$index]['movimentos'], $key);
		}else{
			$obj = $key;
			// $obj['valor_mensal'] = ($key['valor_pago']>0) ? $key['valor_pago'] : $key['valor_mensal'];
			$obj["total_recebimento"] = $movimentos[0]['total_recebimento'];
			$obj["total_pagamento"] = $movimentos[0]['total_pagamento'];
			$obj["total_liquido"] = $movimentos[0]['total_liquido'];
			array_push($movs, $obj);
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
function movimento_remover () {
	$data = $_POST['data'];

	// verificando se o movimento já possui confirmações
	$control_movimento_mes = new movimento_mes_control();
	$resp = $control_movimento_mes->listarPorMovimento($data['id']); // idmovimento
	if (!$resp['success']) die (json_encode($resp));
	if (!empty($resp['data'])) {
		$resp['success'] = false;
		$resp['msg'] = "Não é possível remover este movimento, pois já possui confirmações mensais! Use a opção de desativar.";
		die (json_encode($resp));
	}

	$obj = new movimento($data['id']);
	$control = new movimento_control($obj);
	echo json_encode($control->remover());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>