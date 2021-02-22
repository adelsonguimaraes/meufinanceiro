<?php

//inclui autoload
require_once 'autoload.php';

//verifica requisição
('bandeira_cartao_'.$_POST['metodo'])();

function bandeira_cartao_cadastrar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$obj = new bandeira_cartao();
	$obj->setNome($data['nome']);
	$control = new bandeira_cartao_control($obj);

	$response = $control->cadastrar();
	echo json_encode($response);
}
function bandeira_cartao_atualizar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	
	$obj = new bandeira_cartao();
	$obj->setId($data['id']);
	$obj->setNome($data['nome']);
	$control = new bandeira_cartao_control($obj);
	$response = $control->atualizar();
	echo json_encode($response);
}
function bandeira_cartao_buscarPorId () {
	$data = $_POST['data'];
	$control = new bandeira_cartao_control($data['id']);
	$response = $control->buscarPorId();
	echo json_encode($response);
}
function bandeira_cartao_listar () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];
	$idusuario = $usuario["idusuario"];

	if (!empty($data)) $idusuario = $data["idusuario"];
	$control = new bandeira_cartao_control();
	$response = $control->listar($idusuario);
	echo json_encode($response);
}

function bandeira_cartao_listarPaginado () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new bandeira_cartao_control();
	$response = $control->listarPaginado($usuario["idusuario"], $data["start"], $data["limit"]);
	echo json_encode($response);
}

function bandeira_cartao_desativar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	$control = new bandeira_cartao_control();
	$response = $control->desativar($data['idagenda']);
	echo json_encode($response);
}
function bandeira_cartao_deletar () {
	$data = $_POST['data'];
	$banco = new bandeira_cartao();
	$banco->setId($data['id']);
	$control = new bandeira_cartao_control($banco);
	echo json_encode($control->deletar());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>