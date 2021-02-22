<?php

Class meta_control {
	//atributos
	protected $con;
	protected $obj;
	protected $objDAO;

	//construtor
	public function __construct(meta $obj=NULL) {
		$this->con = conexao::getInstance()->getConexao();
		$this->objDAO = new meta_dao($this->con);
		$this->obj = $obj;
	}

	//metodos
	function cadastrar () {
		return $this->objDAO->cadastrar($this->obj);
	}
	function atualizar () {
		return $this->objDAO->atualizar($this->obj);
	}
	function buscarPorId () {
		return $this->objDAO->buscarPorId($this->obj);
	}
	function listar ($idusuario) {
		return $this->objDAO->listar($idusuario);
	}
	function desativar ($idagenda) {
		return $this->objDAO->desativar($idagenda);
	}
	function deletar () {
		return $this->objDAO->deletar($this->obj);
	}
	function listarPaginado ($idusuario, $start, $limit) {
		return $this->objDAO->listarPaginado($idusuario, $start, $limit);
	}
	function qtdTotal () {
		return $this->objDAO->qtdTotal();
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>