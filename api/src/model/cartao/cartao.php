<?php
// model : agenda

/*
	Projeto: INCUBUS - Controle de Consultoria.
	Project Owner: Raquel Araújo Queiroz.
	Desenvolvedor: Adelson Guimarães Monteiro.
	Data de início: 2019-02-02T18:48:29.166Z.
	Data Atual: 02/02/2019.
*/

Class cartao implements JsonSerializable {
	//atributos
	private $id;
	private $idusuario;
	private $idbandeira;
	private $nome;
	private $validade;
	private $final;
	private $dia_vencimento;
	private $ativo;
	private $data_cadastro;
	private $data_edicao;

	//constutor
	public function __construct
	(
		$id = NULL,
		$idusuario = NULL,
		$idbandeira = NULL,
		$nome = NULL,
		$validade = NULL,
		$final = NULL,
		$dia_vencimento = NULL,
		$ativo = NULL,
		$data_cadastro = NULL,
		$data_edicao = NULL
	)
	{
		$this->id	= $id;
		$this->idusuario = $idusuario;
		$this->idbandeira	= $idbandeira;
		$this->nome	= $nome;
		$this->validade	= $validade;
		$this->final = $final;
		$this->dia_vencimento = $dia_vencimento;
		$this->ativo = $ativo;
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
	public function getidbandeira() {
		return $this->idbandeira;
	}
	public function setidbandeira($idbandeira) {
		$this->idbandeira = $idbandeira;
		return $this;
	}
	public function getnome() {
		return $this->nome;
	}
	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}
	public function getValidade() {
		return $this->validade;
	}
	public function setValidade($validade) {
		$this->validade = $validade;
		return $this;
	}
	public function getFinal() {
		return $this->final;
	}
	public function setFinal($final) {
		$this->final = $final;
		return $this;
	}
	public function getDia_vencimento() {
		return $this->dia_vencimento;
	}
	public function setDia_vencimento($dia_vencimento) {
		$this->dia_vencimento = $dia_vencimento;
		return $this;
	}
	public function getAtivo() {
		return $this->ativo;
	}
	public function setAtivo($ativo) {
		$this->ativo = $ativo;
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
			"nome"	=> $this->nome,
			"idusuario" => $this->idusuario,
			"idbandeira"	=> $this->idbandeira,
			"datahora"	=> $this->datahora,
			"validade"	=> $this->validade,
			"final" => $this->final,
			"dia_vencimento" => $this->dia_vencimento,
			"ativo" => $this->ativo,
			"data_cadastro"	=> $this->data_cadastro,
			"data_edicao"	=> $this->data_edicao
		];
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>