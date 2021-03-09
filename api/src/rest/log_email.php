<?php
// rest : log_email

/*
	Projeto: INCUBUS - Gestão de Consultoria de Vendas.
	Project Owner: Raquel Queiroz.
	Desenvolvedor: Adelson Guimarães Monteiro.
	Data de início: 2019-08-07T23:16:08.179Z.
	Data Atual: 08/08/2019.
*/

//inclui autoload
require_once 'autoload.php';

//verifica requisição
switch ($_POST['metodo']) {
	case 'cadastrar':
		cadastrar();
		break;
	case 'buscarPorId':
		buscarPorId();
		break;
	case 'listar':
		listar();
		break;
	case 'atualizar':
		atualizar();
		break;
	case 'deletar':
		deletar();
		break;
}

function cadastrar () {
	$data = $_POST['data'];
	$obj = new log_email(
		NULL,
		$data['idclasse'],
		$data['classe'],
		$data['assunto'],
		$data['conteudo'],
		$data['destinatario'],
		$data['status'],
		$data['retorno']
	);
	$control = new log_email_control($obj);
	$response = $control->cadastrar();
	echo json_encode($response);
}
function buscarPorId () {
	$data = $_POST['data'];
	$control = new log_email_control(new log_email($data['id']));
	$response = $control->buscarPorId();
	echo json_encode($response);
}
function listar () {
	$control = new log_email_control(new log_email);
	$response = $control->listar();
	echo json_encode($response);
}
function atualizar () {
	$data = $_POST['data'];
	$obj = new log_email(
		$data['id'],
		$data['idclasse'],
		$data['classe'],
		$data['assunto'],
		$data['conteudo'],
		$data['destinatario'],
		$data['status'],
		$data['retorno']
	);
	$control = new log_email_control($obj);
	$response = $control->atualizar();
	echo json_encode($response);
}
function deletar () {
	$data = $_POST['data'];
	$banco = new log_email();
	$banco->setId($data['id']);
	$control = new log_email_control($banco);
	echo json_encode($control->deletar());
}


// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>