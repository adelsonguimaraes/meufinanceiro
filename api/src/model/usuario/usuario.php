<?php
// model : usuario

/*
	Projeto: INCUBUS - Controle de Consultoria.
	Project Owner: Raquel Araújo Queiroz.
	Gerente de Projeto: Adelson Guimarães Monteiro.
	Data de início: 2019-02-02T18:18:31.633Z.
	Data Atual: 26/07/2019.
*/

Class usuario implements JsonSerializable {
	//atributos
	private $id;
	private $nome;
	private $email;
	private $senha;
	private $seguranca;
	private $ativo;
	// private $auth;
	private $data_cadastro;
	private $data_edicao;

	//constutor
	public function __construct
	(
		$id = NULL,
		$nome = NULL,
		$email = NULL,
		$senha = NULL,
		$seguranca = NULL,
		$ativo = NULL,
		// $auth = NULL,
		$data_cadastro = NULL,
		$data_edicao = NULL
	)
	{
		$this->id	= $id;
		$this->nome	= $nome;
		$this->email	= $email;
		$this->senha	= $senha;
		$this->seguranca = $seguranca;
		$this->ativo	= $ativo;
		// $this->auth	= $auth;
		$this->data_cadastro	= $data_cadastro;
		$this->data_edicao	= $data_edicao;
	}

	//Getters e Setters
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getNome() {
		return $this->nome;
	}
	public function setNome( $nome ) {
		$this->nome = $nome;
		return $this;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
	public function getSenha() {
		return $this->senha;
	}
	public function setSenha($senha) {
		$this->senha = $senha;
		return $this;
	}
	public function getSeguranca() {
		return $this->seguranca;
	}
	public function setSeguranca($seguranca) {
		$this->seguranca = $seguranca;
		return $this;
	}
	public function getAtivo() {
		return $this->ativo;
	}
	public function setAtivo($ativo) {
		$this->ativo = $ativo;
		return $this;
	}
	// public function getAuth() {
	// 	return $this->auth;
	// }
	// public function setAuth($auth) {
	// 	$this->auth = $auth;
	// 	return $this;
	// }
	public function getData_cadastro() {
		return $this->data_cadastro;
	}
	public function setData_cadastro($data_cadastro) {
		$this->data_cadastro = $data_cadastro;
		return $this;
	}
	public function getData_edicao() {
		return $this->data_edicao;
	}
	public function setData_edicao($data_edicao) {
		$this->data_edicao = $data_edicao;
		return $this;
	}

	//Json Serializable
	public function JsonSerialize () {
		return [
			"id"	=> $this->id,
			"nome"	=> $this->nome,
			"email"	=> $this->email,
			"senha"	=> $this->senha,
			"seguranca"	=> $this->seguranca,
			"ativo"	=> $this->ativo,
			// "auth"	=> $this->auth,
			"data_cadastro"	=> $this->data_cadastro,
			"data_edicao"	=> $this->data_edicao
		];
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>