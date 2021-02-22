<?php
session_start();

// header("Access-Control-Allow-Origin: *");

/* Inclui a Class de autoLoad */
require_once 'autoload.php';

('authentication_'.$_POST['metodo'])();

/*
	Metodos
*/

function authentication_acessar() {

    $data = $_POST['data'];

    $senha= $data['senha'];
    $email = $data['email']; 

    $email = stripslashes       ( strip_tags( trim( $email ) ) ); 
    $senha = stripslashes ( strip_tags( trim( $senha ) ) ); 

    $usuario_control = new usuario_control();
	$response = $usuario_control->acessar($email, $senha);
	

    echo json_encode( $response );
}

function mudarSenha() {

    $con = conexao::getInstance()->getConexao();

    $data = $_POST;

    $idusuario = $data['idusuario'];
    $senhaatual= $data['senhaatual'];
    $novasenha = $data['novasenha'];

    $usuario_control = new usuario_control();
    $response = $usuario_control->mudarSenha($idusuario, $senhaatual, $novasenha);

    echo json_encode( $response );
}

function auth () {
    $usuario = $_POST['usuario'];
    $control = new usuario_control();
    if (empty($usuario['auth'])) die (json_encode(array("success"=>false, "msg"=>"Usuário não autenticado!")));
    echo json_encode($control->auth($usuario['idusuario'], $usuario['auth']));
}

function getMenu() {
    $usuario = $_POST["usuario"];
    $usuario_control = new usuario_control();
    echo json_encode($usuario_control->getMenu($usuario["idusuario"]));
}