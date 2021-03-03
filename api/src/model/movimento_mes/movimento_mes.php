<?php

Class movimento_mes implements JsonSerializable {
	//atributos
	private $id;
	private $idmovimento;
	private $valor;
	private $data_corrente;
	private $data_pagamento;
	private $observacao;
	private $data_cadastro;
	private $data_edicao;

	//constutor
	public function __construct
	(
		$id = NULL,
		$idmovimento = NULL,
		$valor = NULL,
		$data_corrente = NULL,
		$data_pagamento = NULL,
		$observacao = NULL,
		$data_cadastro = NULL,
		$data_edicao = NULL
	)
	{
		$this->id	= $id;
		$this->idmovimento = $idmovimento;
		$this->valor	= $valor;
		$this->data_corrente	= $data_corrente;
		$this->data_corrente = $data_pagamento;
		$this->observacao = $observacao;
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
	public function getIdmovimento() {
		return $this->idmovimento;
	}
	public function setIdmovimento($idmovimento) {
		$this->idmovimento = $idmovimento;
		return $this;
	}
	public function getValor() {
		return $this->valor;
	}
	public function setValor($valor) {
		$this->valor = $valor;
		return $this;
	}
	public function getData_corrente() {
		return $this->data_corrente;
	}
	public function setData_corrente($data_corrente) {
		$this->data_corrente = $data_corrente;
		return $this;
	}
	public function getData_pagamento() {
		return $this->data_pagamento;
	}
	public function setData_pagamento($data_pagamento) {
		$this->data_pagamento = $data_pagamento;
		return $this;
	}
	public function getObservacao() {
		return $this->observacao;
	}
	public function setObservacao($observacao) {
		$this->observacao = $observacao;
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
			"idmovimento" => $this->idmovimento,
			"valor"	=> $this->valor,
			"data_corrente"	=> $this->data_corrente,
			"data_pagamento"	=> $this->data_pagamento,
			"observacao" => $this->observacao,
			"data_cadastro"	=> $this->data_cadastro,
			"data_edicao"	=> $this->data_edicao
		];
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>