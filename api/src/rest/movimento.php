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

	// atualizando saldo do mes anterior
	// -- consulta o total do saldo anterior
	$data_anterior = Date("Y-m-d", strtotime("{$data['data']} -1 Month"));
	$control = new movimento_control();
	$resp = $control->getTotalMes ($usuario['idusuario'], $data_anterior, 'PAGAMENTO');
	if (!$resp['success']) die (json_encode($resp));
	$total_pagamento_anterior = $resp['data'];

	$resp = $control->getTotalMes ($usuario['idusuario'], $data_anterior, 'RECEBIMENTO');
	if (!$resp['success']) die (json_encode($resp));
	$total_recebimento_anterior = $resp['data'];
	$total_liquido_anterior = round($total_recebimento_anterior-$total_pagamento_anterior);

	// --- verificando se o movimento referente a saldo já existe
	$nome = "SALDO " . getNomeMes(intval(substr($data_anterior, 5, 2))-1)['abreviatura'];
	$resp = $control->buscarPorNome ($nome);
	if (!$resp['success']) die (json_encode($resp));

	// montando o obj
	$obj = new movimento();
	if (intval($resp['data']['id']>0)) $obj->setId($resp['data']['id']);
	$obj->setIdusuario($usuario['idusuario']);
	$obj->setIdcartao('null');
	$obj->setNome(empty($resp['data']['nome']) ? $nome : $resp['data']['nome']);
	$obj->setDescricao($resp['data']['descricao']);
	// $obj->setValor_mensal(empty($resp['data']['valor_mensal']) ? $total_liquido_anterior : $resp['data']['valor_mensal']);
	$obj->setValor_mensal($total_liquido_anterior); // passando o valor atualizado
	$obj->setQuantidade_parcela(empty($resp['data']['quantidade_parcela']) ? 1 : $resp['data']['quantidade_parcela']);
	$obj->setData_inicial(empty($resp['data']['data_inicial']) ? (substr($data['data'], 0, 7) . '-01') : substr($resp['data']['data_inicial'], 0, 10));
	$obj->setTipo('RECEBIMENTO');

	// se já existe, atualiza, se não cadastra
	if (!empty($resp['data'])) {
		$control = new movimento_control($obj);
		$resp = $control->atualizar();
		if (!$resp['success']) die (json_encode($resp));
	}else{
		$control = new movimento_control($obj);
		$resp = $control->cadastrar();
		if (!$resp['success']) die (json_encode($resp));
	}

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

function movimento_listarPorDiasVencimento () {
	$control = new movimento_control();
	$dias_para_vencer = 5;
	$dias_atraso = -3;
	$vencendo_hoje = 0;

	// movimentos que vencem em 5 dias
	$resp = $control->listarPorDiasVencimento($dias_para_vencer); // faltam 5 dias para vencer
	if (!$resp['success']) die (json_encode($resp));
	$lista = $resp['data'];
	
	if (!empty($lista)) {
		$usuarios = array();
		foreach($lista as $key) {
			$index = array_search($key['idusuario'], array_column($usuarios, 'idusuario'));
			if ($index===false) {
				array_push(
					$usuarios,
					array(
						"idusuario" => $key['idusuario'],
						"nome" => $key['usuario'],
						"email" => $key['email'],
						"vencimento" => $key['data_corrente'],
						"movimentos" => array()
					)
				);
				$index = array_search($key['idusuario'], array_column($usuarios, 'idusuario'));
			}
			array_push($usuarios[$index]['movimentos'], array(
				"nome" => $key['nome'],
				"valor" => $key['valor_mensal']
			));
		}

		foreach ($usuarios as $data) {
			// enviando email informando vencimentos
			require_once "../email/aviso_movimento_vencimento.php";
			$html = ob_get_contents();
			ob_end_clean();
	
			$obj = new EnviaEmail();
			$obj->setRemetente('Meu Financeiro')
			->setAssunto('Aviso de Vencimento ' . formatDate($data['vencimento'])) 
			->setEmails(array($data['email']))
			->setMensagem($html);
			$obj->enviar();
		}
	}

	// verificando movimentos que vencem no dia atual
	$resp = $control->listarPorDiasVencimento($vencendo_hoje); // vencem hoje
	if (!$resp['success']) die (json_encode($resp));
	$lista = $resp['data'];
	
	if (!empty($lista)) {
		$usuarios = array();
		foreach($lista as $key) {
			$index = array_search($key['idusuario'], array_column($usuarios, 'idusuario'));
			if ($index===false) {
				array_push(
					$usuarios,
					array(
						"idusuario" => $key['idusuario'],
						"nome" => $key['usuario'],
						"email" => $key['email'],
						"vencimento" => $key['data_corrente'],
						"movimentos" => array()
					)
				);
				$index = array_search($key['idusuario'], array_column($usuarios, 'idusuario'));
			}
			array_push($usuarios[$index]['movimentos'], array(
				"nome" => $key['nome'],
				"valor" => $key['valor_mensal']
			));
		}

		foreach ($usuarios as $data) {
			// enviando email informando vencimentos
			require_once "../email/aviso_movimento_vencimento.php";
			$html = ob_get_contents();
			ob_end_clean();
	
			$obj = new EnviaEmail();
			$obj->setRemetente('Meu Financeiro')
			->setAssunto('Aviso de Vencimento ' . formatDate($data['vencimento'])) 
			->setEmails(array($data['email']))
			->setMensagem($html);
			$obj->enviar();
		}
	}

	// movimentos atrasados a 3 dias
	$resp = $control->listarPorDiasVencimento($dias_atraso); // atrasados a 3 dias
	if (!$resp['success']) die (json_encode($resp));
	$lista = $resp['data'];
	

	if (!empty($lista)) {
		$usuarios = array();
		foreach($lista as $key) {
			$index = array_search($key['idusuario'], array_column($usuarios, 'idusuario'));
			if ($index===false) {
				array_push(
					$usuarios,
					array(
						"idusuario" => $key['idusuario'],
						"nome" => $key['usuario'],
						"email" => $key['email'],
						"vencimento" => $key['data_corrente'],
						"movimentos" => array()
					)
				);
				$index = array_search($key['idusuario'], array_column($usuarios, 'idusuario'));
			}
			array_push($usuarios[$index]['movimentos'], array(
				"nome" => $key['nome'],
				"valor" => $key['valor_mensal']
			));
		}

		foreach ($usuarios as $data) {
			// enviando email informando vencimentos
			require_once "../email/aviso_movimento_atrasado.php";
			$html = ob_get_contents();
			ob_end_clean();
	
			$obj = new EnviaEmail();
			$obj->setRemetente('Meu Financeiro')
			->setAssunto('Aviso de Atraso ' . formatDate($data['vencimento'])) 
			->setEmails(array($data['email']))
			->setMensagem($html);
			$obj->enviar();
		}
	}
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