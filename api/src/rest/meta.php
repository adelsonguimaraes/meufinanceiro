<?php

//inclui autoload
require_once 'autoload.php';

//verifica requisição
('meta_'.$_POST['metodo'])();

function meta_cadastrar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];

	$obj = new meta();
	$obj->setIdUsuario($data['idusuario']);
	$obj->setNome($data['nome']);
	$obj->setDescricao($data['descricao']);
	$obj->setValor_mensal($data['valor_mensal']);
	$control = new meta_control($obj);

	$response = $control->cadastrar();
	echo json_encode($response);
}
function meta_atualizar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	
	$obj = new meta();
	$obj->setId($data['id']);
	$obj->setIdusuario($data['idusuario']);
	$obj->setNome($data['nome']);
	$obj->setDescricao($data['descricao']);
	$obj->setValor_mensal($data['valor_mensal']);
	$control = new meta_control($obj);
	$response = $control->atualizar();
	echo json_encode($response);
}
function meta_buscarPorId () {
	$data = $_POST['data'];
	$control = new meta_control($data['id']);
	$response = $control->buscarPorId();
	echo json_encode($response);
}
function meta_listar () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];
	$idusuario = $usuario["idusuario"];

	if (!empty($data)) $idusuario = $data["idusuario"];
	$control = new meta_control();
	$response = $control->listar($idusuario);
	echo json_encode($response);
}

function meta_listarPaginado () {
	$data = $_POST["data"];
	$usuario = $_POST['usuario'];

	$control = new meta_control();
	$response = $control->listarPaginado($usuario["idusuario"], $data["start"], $data["limit"]);
	echo json_encode($response);
}

function meta_desativar () {
	$data = $_POST['data'];
	$usuario = $_POST['usuario'];
	$control = new meta_control();
	$response = $control->desativar($data['idagenda']);
	echo json_encode($response);
}
function meta_deletar () {
	$data = $_POST['data'];
	$banco = new meta();
	$banco->setId($data['id']);
	$control = new meta_control($banco);
	echo json_encode($control->deletar());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>