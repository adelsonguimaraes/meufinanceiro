<?php
// model : agenda

/*
	Projeto: INCUBUS - Controle de Consultoria.
	Project Owner: Raquel Araújo Queiroz.
	Desenvolvedor: Adelson Guimarães Monteiro.
	Data de início: 2019-02-02T18:48:29.166Z.
	Data Atual: 02/02/2019.
*/

Class meta implements JsonSerializable {
	//atributos
	private $id;
	private $idusuario;
	private $nome;
	private $descricao;
	private $valor_mensal;
	private $link_1;
	private $descricao_link_1;
	private $link_2;
	private $descricao_link_2;
	private $link_3;
	private $descricao_link_3;
	private $data_cadastro;
	private $data_edicao;

	//constutor
	public function __construct
	(
		$id = NULL,
		$idusuario = NULL,
		$nome = NULL,
		$descricao = NULL,
		$valor_mensal = NULL,
		$link_1 = NULL,
		$descricao_link_1 = NULL,
		$link_2 = NULL,
		$descricao_link_2 = NULL,
		$link_3 = NULL,
		$descricao_link_3 = NULL,
		$data_cadastro = NULL,
		$data_edicao = NULL
	)
	{
		$this->id	= $id;
		$this->idusuario = $idusuario;
		$this->nome	= $nome;
		$this->descricao	= $descricao;
		$this->valor_mensal = $valor_mensal;
		$this->link_1 = $link_1;
		$this->descricao_link_1 = $descricao_link_1;
		$this->link_2 = $link_2;
		$this->descricao_link_2 = $descricao_link_2;
		$this->link_3 = $link_3;
		$this->descricao_link_3 = $descricao_link_3;
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
	public function getIdusuario() {
		return $this->idusuario;
	}
	public function setIdusuario($idusuario) {
		$this->idusuario = $idusuario;
		return $this;
	}
	public function getNome() {
		return $this->nome;
	}
	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}
	public function getDescricao() {
		return $this->descricao;
	}
	public function setDescricao($descricao) {
		$this->descricao = $descricao;
		return $this;
	}
	public function getValor_mensal() {
		return $this->valor_mensal;
	}
	public function setValor_mensal($valor_mensal) {
		$this->valor_mensal = $valor_mensal;
		return $this;
	}
	public function getLink_1() {
		return $this->link_1;
	}
	public function setLink_1($link_1) {
		$this->link_1 = $link_1;
		return $this;
	}
	public function getDescricao_link_1() {
		return $this->descricao_link_1;
	}
	public function setDescricao_link_1($descricao_link_1) {
		$this->descricao_link_1 = $descricao_link_1;
		return $this;
	}
	public function getLink_2() {
		return $this->link_2;
	}
	public function setLink_2($link_2) {
		$this->link_2 = $link_2;
		return $this;
	}
	public function getDescricao_link_2() {
		return $this->descricao_link_2;
	}
	public function setDescricao_link_2($descricao_link_2) {
		$this->descricao_link_2 = $descricao_link_2;
		return $this;
	}
	public function getLink_3() {
		return $this->link_3;
	}
	public function setLink_3($link_3) {
		$this->link_3 = $link_3;
		return $this;
	}
	public function getDescricao_link_3() {
		return $this->descricao_link_3;
	}
	public function setDescricao_link_3($descricao_link_3) {
		$this->descricao_link_3 = $descricao_link_3;
		return $this;
	}
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
			"idusuario" => $this->idusuario,
			"nome"	=> $this->nome,
			"descricao"	=> $this->descricao,
			"valor_mensal" => $this->valor_mensal,
			"link_1" => $this->link_1,
			"descricao_link_1" => $this->descricao_link_1,
			"link_2" => $this->link_2,
			"descricao_link_2" => $this->descricao_link_2,
			"link_3" => $this->link_3,
			"descricao_link_3" => $this->descricao_link_3,
			"data_cadastro"	=> $this->data_cadastro,
			"data_edicao"	=> $this->data_edicao
		];
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>