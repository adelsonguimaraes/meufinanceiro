<?php

//inclui autoload
require_once 'autoload.php';

//verifica requisição
('cartao_'.$_POST['metodo'])();

function cartao_cadastrar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$obj = new cartao();
	$obj->setIdUsuario($data['idusuario']);
	$obj->setIdBandeira($data['idbandeira']);
	$obj->setNome($data['nome']);
	$obj->setValidade($data['validade']);
	$obj->setFinal($data['final']);
	$obj->setDia_vencimento($data['dia_vencimento']);
	$control = new cartao_control($obj);

	$response = $control->cadastrar();
	echo json_encode($response);
}
function cartao_atualizar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	
	$obj = new cartao();
	$obj->setId($data['id']);
	$obj->setIdUsuario($data['idusuario']);
	$obj->setIdBandeira($data['idbandeira']);
	$obj->setNome($data['nome']);
	$obj->setValidade($data['validade']);
	$obj->setFinal($data['final']);
	$obj->setDia_vencimento($data['dia_vencimento']);
	$control = new cartao_control($obj);
	$response = $control->atualizar();
	echo json_encode($response);
}
function cartao_buscarPorId () {
	$data = $_POST['data'];
	$control = new cartao_control($data['id']);
	$response = $control->buscarPorId();
	echo json_encode($response);
}
function cartao_listar () {
	$usuario = $_POST['usuario'];
	$idusuario = $usuario["idusuario"];

	$control = new cartao_control();
	$response = $control->listar($idusuario);
	echo json_encode($response);
}

function cartao_listarPaginado () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new cartao_control();
	$response = $control->listarPaginado($usuario["idusuario"], $data["start"], $data["limit"]);
	echo json_encode($response);
}

function cartao_desativar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	$control = new cartao_control();
	$response = $control->desativar($data['idagenda']);
	echo json_encode($response);
}
function cartao_deletar () {
	$data = $_POST['data'];
	$banco = new cartao();
	$banco->setId($data['id']);
	$control = new cartao_control($banco);
	echo json_encode($control->deletar());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>