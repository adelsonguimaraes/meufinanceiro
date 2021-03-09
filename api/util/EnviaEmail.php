<?php

if (!function_exists("PHPMailer")) {
	// require_once('PHPMailer_5.2.2/class.phpmailer.php');
	require_once("phpmailer/PHPMailerAutoload.php");
}

require_once(__DIR__ . "/../src/rest/autoload.php");
require_once(__DIR__ . "/email_env.php");

class EnviaEmail {
	// atributos
	private $usuario = EMAIL_USUARIO;
	private $senha = EMAIL_SENHA;
	private $remetente;
	private $emails;
	private $assunto;
	private $mensagem;

	function __construct 
	(
		$remetente = NULL,
		$assunto = NULL,
		$emails = NULL,
		$mensagem = NULL,
		$anexo = NULL
	)
	{
		$this->remetente = $remetente;
		$this->assunto = $assunto;
		$this->emails = $emails;
		$this->mensagem = $mensagem;
		$this->anexo = $anexo;
	}

	// metodos
	public function getusuario () {
		return $this->usuario;
	}
	public function setusuario ( $usuario ) {
		$this->usuario = $usuario;
		return $this;
	}
	public function getSenha () {
		return $this->senha;
	}
	public function setSenha ( $senha ) {
		$this->senha = $senha;
		return $this;
	}
	public function getRemetente () {
		return $this->remetente;
	}
	public function setRemetente ( $remetente ) {
		$this->remetente = $remetente;
		return $this;
	}
	public function getEmails () {
		return $this->emails;
	}
	public function setEmails ( $emails ) {
		$this->emails = $emails;
		return $this;
	}
	public function getAssunto () {
		return $this->assunto;
	}
	public function setAssunto ( $assunto ) {
		$this->assunto = $assunto;
		return $this;
	}
	public function getMensagem () {
		return $this->mensagem;
	}
	public function setMensagem ( $mensagem ) {
		$this->mensagem = $mensagem;
		return $this;
	}

	public function enviar () {
		// echo '<pre>';
		if ( empty($this->emails) ) return false; // se não haver emails
		
		$Host = EMAIL_HOST;//.substr(strstr($this->usuario, '@'), 1); //'mail.dominio.com.br';
		$Username = $this->usuario;
		$Password = $this->senha;
		$Port = "587";

		$mail = null;
		unset($mail);
		$mail = new PHPMailer();

		$body = $this->mensagem;
		$mail->IsSMTP(); // telling the class to use SMTP


		$mail->SMTPOptions = array(
		    'ssl' => array(
		        'verify_peer' => false,
		        'verify_peer_name' => false,
		        'allow_self_signed' => true
		    )
		);

		$mail->Host = $Host; // SMTP server
		// $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
		// 1 = errors and messages1
		// 2 = messages only
		$mail->SMTPAuth = true; // enable SMTP authentication
		$mail->AuthType = 'PLAIN';
		$mail->SMTPSecure = "";
		$mail->Port = $Port; // set the SMTP port for the service server
		$mail->Username = $Username; // account username
		$mail->Password = $Password; // account password
		$mail->CharSet = 'UTF-8';

		$mail->SetFrom( str_replace("=", "@", $this->usuario), $this->remetente );
		$mail->Subject = $this->assunto;
		$mail->MsgHTML($body);
		
		if ( !empty($this->anexo) && filesize( $this->anexo ) < 10485760 ) { // caso não exceda o limite 10MB
			$mail->AddAttachment( $this->anexo );
		}

		$count_email = 0;
		foreach ( $this->emails as $key ) {
			// verificando se o email já foi enviado
			$control = new log_email_control();
			$resp = $control->listarPorEmailConteudo (trim($key), $this->mensagem);
			if (!$resp['success']) die(json_encode($resp));
			if (empty($resp['data'])) {
				$mail->AddAddress( trim($key), "" );
				$count_email++;
			}
		}

		if ($count_email>0) {

			// chamando a classe de email log
			$obj = new log_email();
			$obj->setAssunto($this->assunto)
				->setConteudo($this->mensagem)
				->setDestinatario(implode(', ', $this->emails));

			if(!$mail->Send()) { // caso de erro
				$response = $mail->ErrorInfo;

				// salvando no log
				$obj->setStatus('ERRO')
					->setRetorno($mail->ErrorInfo);
				$control = new log_email_control($obj);
				$resp = $control->cadastrar();
				if ($resp['success']==false) return $resp;

			} else { // caso sucesso no envio
				$response = true;
				
				// salvando no log
				$obj->setStatus('ENVIADO')
					->setRetorno("Email enviado com sucesso");
				$control = new log_email_control($obj);
				$resp = $control->cadastrar();
				if ($resp['success']==false) return $resp;
			}

			return $response;
		}
	}


}

// $data = array(
// 	"nome"=>"Adelson Guimarães",
// 	"email"=>"adelsonguimaraes@gmail.com",
// 	"celular"=>"92991905809",
// 	"interesse"=>"IMOVEL",
// 	"valor"=>25000.00,
// 	"entrada"=>100,
// 	"parcela"=>400
// );

// $consultor = array("nome"=>"Raquel Queiroz", "perfil"=>"LIDER", "celular"=>92999999999);

// enviando menu informando consultor
// require_once "../util/GenericFunctions.php";
// require_once "../src/email/avisa_consultor_cadastro_atendimento.php";
// $html = ob_get_contents();
// ob_end_clean();

// echo $html;
// exit;

// // como usar
// $obj = new EnviaEmail();
// $obj->setRemetente('Incubus')
// 	->setAssunto('Consultoria de Vendas')
// 	->setEmails(array('adelsonguimaraes@gmail.com'))
// 	->setMensagem($html);
// echo $obj->enviar();

// como tratar o erro
// if ($obj->enviar()===true) {
// 	echo "enviado com sucesso";
// }else{
// 	echo "erro";
// }


?>
