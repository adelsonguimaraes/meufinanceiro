<?php
/*
	Projeto: Meu Financeiro.
	Project Owner: Adelson Guimarães Monteiro.
	Desenvolvedor: Adelson Guimarães Monteiro.
	Data de início: 16/02/2021.
	Data Atual: 16/02/2021.
*/

/* Trata request */
$_POST = $_REQUEST;
if (empty($_POST)) $_POST = file_get_contents("php://input");
if (!is_array($_POST)) $_POST = json_decode($_POST, true);

/*
	Requires
*/
require_once(__DIR__ . "/../../util/conexao.php"); // Conexao
require_once(__DIR__ . "/../../util/resolve_mysql_error.php"); // Resolve erros mysql
require_once(__DIR__ . "/../../util/uploadFiles.php"); //Upload images
require_once(__DIR__ . "/../../util/EnviaEmail.php"); //Envia Email
require_once(__DIR__ . "/../../util/GenericFunctions.php"); //Functions

/*
	Fun��o AutoLoad, Carrega as Classes quando
	tenta-se criar uma nova instancia de uma Classe.
	Exemplo: new Cupom(), new usuario_dao(), new EmpresaControl()... 
*/
function carregaClasses($class){
	/*
		Verifica se existe "Control" no nome da classe
	*/
//  	if(strripos($class, "Control")) {
	if(strrpos($class, "_control")) {

		/*	require na Control */ 
 		require_once __DIR__ . "/../control/".$class.".php";
 	}
 	/*
		Verifica se existe "Control" no nome da classe
	*/
 	else if(strrpos($class, "_dao")) {

		/* Monta o nome da Bean */
 		$bean = strtolower(substr($class, 0, strrpos($class, "_dao")));
 		/*	require na DAO */
 		require_once __DIR__ . "/../model/".$bean."/".$class.".php";
 	/*
		Se n�o for DAO nem Control � Model.
	*/
 	}else{
 		/* Monta o nome da Bean */
 		$bean = strtolower($class);
 		/*	require na model */
 		require_once __DIR__ . "/../model/".$bean."/".$class.".php";
 	}
}

/*
	Chama o AutoLoad
*/
spl_autoload_register("carregaClasses");

/*
	Geta o Rest
*/
// function getRest($class) {
// 	if($class) {
// 		require_once $class.".php";
// 	}
// }

/*
	Chama a fun��o GetRest
*/
// getRest($_POST['class']);

?>