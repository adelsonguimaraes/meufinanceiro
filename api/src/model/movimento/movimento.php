<?php
// model : agenda

/*
	Projeto: INCUBUS - Controle de Consultoria.
	Project Owner: Raquel Araújo Queiroz.
	Desenvolvedor: Adelson Guimarães Monteiro.
	Data de início: 2019-02-02T18:48:29.166Z.
	Data Atual: 02/02/2019.
*/

Class movimento implements JsonSerializable {
	//atributos
	private $id;
	private $idusuario;
	private $nome;
	private $descricao;
	private $valor_mensal;
	private $quantidade_parcela;
	private $data_inicial;
	private $tipo;
	private $ativo;
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
		$quantidade_parcela = NULL,
		$data_inicial = NULL,
		$tipo = NULL,
		$ativo = NULL,
		$data_cadastro = NULL,
		$data_edicao = NULL
	)
	{
		$this->id	= $id;
		$this->idusuario = $idusuario;
		$this->nome	= $nome;
		$this->descricao	= $descricao;
		$this->valor_mensal = $valor_mensal;
		$this->quantidade_parcela = $quantidade_parcela;
		$this->data_inicial = $data_inicial;
		$this->tipo = $tipo;
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
	public function getQuantidade_parcela() {
		return $this->quantidade_parcela;
	}
	public function setQuantidade_parcela($quantidade_parcela) {
		$this->quantidade_parcela = $quantidade_parcela;
		return $this;
	}
	public function getData_inicial() {
		return $this->data_inicial;
	}
	public function setData_inicial($data_inicial) {
		$this->data_inicial = $data_inicial;
		return $this;
	}
	public function getTipo() {
		return $this->tipo;
	}
	public function setTipo($tipo) {
		$this->tipo = $tipo;
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
			"idusuario" => $this->idusuario,
			"nome"	=> $this->nome,
			"descricao"	=> $this->descricao,
			"valor_mensal" => $this->valor_mensal,
			"quantidade_parcela" => $this->quantidade_parcela,
			"data_inicial" => $this->data_inicial,
			"tipo" => $this->tipo,
			"ativo" => $this->ativo,
			"data_cadastro"	=> $this->data_cadastro,
			"data_edicao"	=> $this->data_edicao
		];
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>