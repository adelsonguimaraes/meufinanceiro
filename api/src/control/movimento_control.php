<?php

Class movimento_control {
	//atributos
	protected $con;
	protected $obj;
	protected $objDAO;

	//construtor
	public function __construct(movimento $obj=NULL) {
		$this->con = conexao::getInstance()->getConexao();
		$this->objDAO = new movimento_dao($this->con);
		$this->obj = $obj;
	}

	//metodos
	function cadastrar () {
		return $this->objDAO->cadastrar($this->obj);
	}
	function atualizar () {
		return $this->objDAO->atualizar($this->obj);
	}
	function atualizarCartao($idmovimento, $idcartao) {
		return $this->objDAO->atualizarCartao($idmovimento, $idcartao);
	}
	function buscarPorId () {
		return $this->objDAO->buscarPorId($this->obj);
	}
	function listar ($idusuario) {
		return $this->objDAO->listar($idusuario);
	}
	function listarMesesTimeline ($idusuario) {
		return $this->objDAO->listarMesesTimeline($idusuario);
	}
	function listarPorMesAno ($idusuario, $data) {
		return $this->objDAO->listarPorMesAno ($idusuario, $data);
	}
	function desativar ($idagenda) {
		return $this->objDAO->desativar($idagenda);
	}
	function remover () {
		return $this->objDAO->remover($this->obj);
	}
	function listarPaginado ($idusuario, $pagination) {
		return $this->objDAO->listarPaginado($idusuario, $pagination);
	}
	function qtdTotal () {
		return $this->objDAO->qtdTotal();
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>