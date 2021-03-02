<?php

Class movimento_mes_control {
	//atributos
	protected $con;
	protected $obj;
	protected $objDAO;

	//construtor
	public function __construct(movimento_mes $obj=NULL) {
		$this->con = conexao::getInstance()->getConexao();
		$this->objDAO = new movimento_mes_dao($this->con);
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
	function buscarPorMovimentoDataCorrente ($idmovimento, $data_corrente) {
		return $this->objDAO->buscarPorMovimentoDataCorrente($idmovimento, $data_corrente);
	}
	function listar ($idusuario) {
		return $this->objDAO->listar($idusuario);
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
	function listarPaginado ($idusuario, $start, $limit) {
		return $this->objDAO->listarPaginado($idusuario, $start, $limit);
	}
	function qtdTotal () {
		return $this->objDAO->qtdTotal();
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>