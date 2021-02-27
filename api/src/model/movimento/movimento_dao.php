<?php

Class movimento_dao {
	//atributos
	private $con;
	private $sql;
	private $obj;
	private $lista = array();
	private $superdao;

	//construtor
	public function __construct($con) {
		$this->con = $con;
		$this->superdao = new super_dao('movimento');
	}

	//cadastrar
	function cadastrar (movimento $obj) {

		$this->sql = sprintf("INSERT INTO movimento(idusuario, nome, descricao, valor_mensal, quantidade_parcela, data_inicial, tipo)
		VALUES(%d, '%s', '%s', %f, %d, '%s', '%s')",
			mysqli_real_escape_string($this->con, $obj->getIdusuario()),
			mysqli_real_escape_string($this->con, $obj->getNome()),
			mysqli_real_escape_string($this->con, $obj->getDescricao()),
			mysqli_real_escape_string($this->con, $obj->getValor_mensal()),
			mysqli_real_escape_string($this->con, $obj->getQuantidade_parcela()),
			mysqli_real_escape_string($this->con, $obj->getData_inicial()),
			mysqli_real_escape_string($this->con, $obj->getTipo())
		);
		$this->superdao->resetResponse();

		if(!mysqli_query($this->con, $this->sql)) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), get_class( $obj ), 'Cadastrar' ) );
		}else{
			$id = mysqli_insert_id( $this->con );

			$this->superdao->setSuccess( true );
			$this->superdao->setData( $id );
		}
		return $this->superdao->getResponse();
	}

	//atualizar
	function atualizar (movimento $obj) {

		$this->sql = sprintf("UPDATE movimento SET idusuario = %d, nome = '%s', descricao = '%s', valor_mensal = '%s', quantidade_parcela = %d, data_inicial = '%s', tipo = '%s', data_edicao = '%s' WHERE id = %d ",
			mysqli_real_escape_string($this->con, $obj->getIdusuario()),
			mysqli_real_escape_string($this->con, $obj->getNome()),
			mysqli_real_escape_string($this->con, $obj->getDescricao()),
			mysqli_real_escape_string($this->con, $obj->getValor_mensal()),
			mysqli_real_escape_string($this->con, $obj->getQuantidade_parcela()),
			mysqli_real_escape_string($this->con, $obj->getData_inicial()),
			mysqli_real_escape_string($this->con, $obj->getTipo()),
			mysqli_real_escape_string($this->con, date('Y-m-d H:i:s')),
			mysqli_real_escape_string($this->con, $obj->getId()));
		$this->superdao->resetResponse();

		if(!mysqli_query($this->con, $this->sql)) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), get_class( $obj ), 'Atualizar' ) );
		}else{
			$this->superdao->setSuccess( true );
			$this->superdao->setData( true );
		}
		return $this->superdao->getResponse();
	}

	function atualizarCartao ($idmovimento, $idcartao) {

		$this->sql = "UPDATE movimento 
		SET idcartao = {$idcartao}, 
		data_edicao = \"".date('Y-m-d')."\" 
		WHERE id = {$idmovimento}";

		$this->superdao->resetResponse();

		if(!mysqli_query($this->con, $this->sql)) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), get_class( $obj ), 'Atualizar' ) );
		}else{
			$this->superdao->setSuccess( true );
			$this->superdao->setData( true );
		}
		return $this->superdao->getResponse();
	}

	//buscarPorId
	function buscarPorId (movimento $obj) {
		$this->sql = sprintf("SELECT * FROM movimento WHERE id = %d",
			mysqli_real_escape_string($this->con, $obj->getId()));
		$result = mysqli_query($this->con, $this->sql);

		$this->superdao->resetResponse();

		if(!$result) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), get_class( $obj ), 'BuscarPorId' ) );
		}else{
			while($row = mysqli_fetch_object($result)) {
				$this->obj = $row;
			}
			$this->superdao->setSuccess( true );
			$this->superdao->setData( $this->obj );
		}
		return $this->superdao->getResponse();
	}

	//listar
	function listar ($idusuario) {
		$this->sql = "SELECT m.*
		FROM movimento m
		where m.ativo = 'SIM' and m.idusuario = {$idusuario}
		order by m.nome";
		$result = mysqli_query($this->con, $this->sql);

		$this->superdao->resetResponse();

		if(!$result) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'Listar' ) );
		}else{
			while($row = mysqli_fetch_object($result)) {
				array_push($this->lista, $row);
			}
			$this->superdao->setSuccess( true );
			$this->superdao->setData( $this->lista );
		}
		return $this->superdao->getResponse();
	}

	//listar paginado
	function listarPaginado($idusuario, $start, $limit) {
		$this->sql = "SELECT m.*
		FROM movimento m
		WHERE m.idusuario = {$idusuario}
		limit {$start}, {$limit}";
		$result = mysqli_query ( $this->con, $this->sql );

		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'ListarPaginado' ) );
		}else{
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
				array_push( $this->lista, $row);
			}

			$this->superdao->setSuccess( true );
			$this->superdao->setData( $this->lista );
			$this->superdao->setTotal( $this->qtdTotal() );
		}

		return $this->superdao->getResponse();
	}

	function getSaltoTimeLine ($idusuario, $salto=NULL) {
		if(!$salto) $salto = 10;
		$sql = "SELECT 
		IF(
			TIMESTAMPDIFF(
				MONTH,
				DATE_FORMAT(
					MIN(m.data_inicial),
					'%Y-%m-01'
				),
				DATE_FORMAT(
					MAX(
						DATE_ADD(
							m.data_inicial,
							INTERVAL (m.quantidade_parcela-1) MONTH
						)
					),
					'%Y-%m-01'
				)
			)<{$salto},
			{$salto},
			TIMESTAMPDIFF(
				MONTH,
				DATE_FORMAT(
					MIN(m.data_inicial),
					'%Y-%m-01'
				),
				DATE_FORMAT(
					MAX(
						DATE_ADD(
							m.data_inicial,
							INTERVAL (m.quantidade_parcela-1) MONTH
						)
					),
					'%Y-%m-01'
				)
			)
		) AS 'salto'
		FROM movimento m
		WHERE m.idusuario = {$idusuario}
		AND m.ativo = 'SIM'";

		$result = mysqli_query ( $this->con, $sql );
		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'ListarPaginado' ) );
		}else{
			$row = mysqli_fetch_assoc ( $result );
		}

		$this->superdao->setSuccess( true );
		$this->superdao->setData( $row['salto'] );

		return $this->superdao->getResponse();
	}

	function listarMesesTimeline ($idusuario) {
		
		// consultando o salto de meses pra timeline
		$resp = $this->getSaltoTimeLine ($idusuario);
		if (!$resp['success']) return $resp;
		$intervalo = $resp['data']; // o intervalo que controla quantos meses serão consultados

		$sql = '';

		for ($i=0; $i<=$intervalo; $i++) {
			// calculamos a data_inicial de cada movimento + quantidade de parcelas
			// para verificar se ainda está disponível no mês corrente
			// também verificamos se a quantidade de parcelas é = 0 que é equivalente a FIXO
			if ($i>=1) {
				$sql .= "UNION\r\n"; // se tiver mais de um intervalo fazemos union
				$ativo = 'NAO';
			}
			$sql .= "SELECT DATE_ADD((SELECT MIN(m.data_inicial) FROM movimento m), INTERVAL {$i} MONTH) AS 'data',
			CONCAT(UPPER(SUBSTR(MONTHNAME(DATE_ADD((SELECT MIN(m.data_inicial) FROM movimento m), INTERVAL {$i} MONTH)), 1, 3)),' ', DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL {$i} MONTH), '%Y')) AS 'mes_ano',
			IF(
				DATE_FORMAT(
					DATE_ADD((SELECT MIN(m.data_inicial) FROM movimento m), INTERVAL {$i} MONTH),
					'%m%Y'
				) = DATE_FORMAT(CURDATE(), '%m%Y'),
				'SIM', 'NAO'
			) AS 'ativo'\r\n";

		}

		$result = mysqli_query ( $this->con, $sql );
		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'ListarPaginado' ) );
		}else{
			$lista = array();
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
				
				$status = null;	
				$resp = $this->listarPorMesAno($idusuario, $row['data']);
				
				if (!$resp['success']) return $resp;
				$movimentos = $resp['data'];
				
				$confirmados=0; $abertos=0; $atrasados=0;
				foreach ($movimentos as $key) {
					if ($key['status']==='CONFIRMADO') $confirmados++;
					if ($key['status']==='EMABERTO') $abertos++;
					if ($key['status']==='ATRASADO') $atrasados++;
				}
				if ($confirmados>0) $status = "CONFIRMADO";
				if ($abertos>0) $status = "EMABERTO";
				if ($atrasados>0) $status = "ATRASADO";
				$row['status'] = $status;
				$row['valor'] = $movimentos[0]['total_pagamento'];
				array_push( $lista, $row);
			}

			// mysqli_free_result($result);

			$this->superdao->setSuccess( true );
			$this->superdao->setData( $lista );
		}

		return $this->superdao->getResponse();
	}

	// passando um mês ano para compar se a data_inicial somada a quantidade de meses é > que o mes ano informado
	function listarPorMesAno ($idusuario, $data) {

		$this->con->next_result();

		$sql = "call listar_movimentos({$idusuario}, '{$data}')";

		// $where = "WHERE m.idusuario = {$idusuario} AND
		// -- onde a data de (movimento + parcelas) seja maior que a data corrente
		// (DATE_FORMAT(DATE_ADD(data_inicial,INTERVAL m.quantidade_parcela MONTH), '%m%Y') > 
		// DATE_FORMAT(DATE('{$data}'), '%m%Y') OR m.quantidade_parcela<=0)";


		// $this->sql = "SELECT m.*, 
		// -- data corrente
		// CONCAT(DATE_FORMAT('{$data}', '%Y-%m-'), DATE_FORMAT(m.data_inicial, '%d')) AS 'data_corrente',
		// -- dia e mes corrente
		// CONCAT(DATE_FORMAT(m.data_inicial, '%d'), ' ', UPPER(SUBSTR(MONTHNAME('{$data}'), 1, 3))) AS 'dia_mes',
		// -- parcela corrente
		// IF(m.quantidade_parcela<=0, '',
		// 	CONCAT(
		// 		m.quantidade_parcela -
		// 		TIMESTAMPDIFF(
		// 			MONTH, 
		// 			'{$data}',
		// 			DATE_ADD(m.data_inicial, INTERVAL m.quantidade_parcela MONTH)
		// 		)+1, '/', m.quantidade_parcela
		// 	)
		// ) AS 'parcela_corrente',
		// -- IF(CONCAT(DATE_FORMAT('{$data}', '%Y-%m-'), DATE_FORMAT(m.data_inicial, '%d'))<CURDATE(), 'ATRASADO', 'EMABERTO') as 'status',
		// IF (
		// 	-- verificando se está CONFIRMADO
		// 	mm.id IS NOT NULL,
		// 	'CONFIRMADO',
		// 	IF (
		// 		-- caso não CONFIRMADO, verificamos se está em ATRASO
		// 		(CONCAT(DATE_FORMAT('{$data}', '%Y-%m-'), DATE_FORMAT(m.data_inicial, '%d'))<CURDATE()),
		// 		'ATRASADO',
		// 		'EMABERTO'
		// 	)
		// ) AS 'status',
		// -- total recebimento mensal
		// IFNULL((SELECT SUM(m.valor_mensal)
		// FROM movimento m
		// $where AND m.tipo='RECEBIMENTO'), 0) AS 'total_recebimento',
		// -- total pagamento mensal
		// IFNULL((SELECT SUM(m.valor_mensal)
		// FROM movimento m
		// $where AND m.tipo='PAGAMENTO'), 0) AS 'total_pagamento',
		// -- total do liquido (recebimentos-pagamento)
		// IFNULL(
		// 	(IFNULL((SELECT SUM(m.valor_mensal)
		// 	FROM movimento m
		// 	$where AND m.tipo='RECEBIMENTO'), 0) -
		// 	IFNULL((SELECT SUM(m.valor_mensal)
		// 	FROM movimento m
		// 	$where AND m.tipo='PAGAMENTO'), 0)), 0
		// ) AS 'total_liquido',
		// -- periodo mensal
		// CONCAT(
		// 	'De 01 ', 
		// 	UPPER(SUBSTRING(MONTHNAME('{$data}'), 1, 3)), 
		// 	' até ', 
		// 	DATE_FORMAT(LAST_DAY('{$data}'), '%d'),
		// 	' ',
		// 	UPPER(SUBSTRING(MONTHNAME('{$data}'), 1, 3))
		// ) AS 'periodo'
		// FROM movimento m
		// LEFT JOIN movimento_mes mm ON mm.idmovimento = m.id
		// -- onde o mês e ano da dara corrente sejam iaguais
		// AND (DATE_FORMAT(mm.data_corrente, '%m%Y') = DATE_FORMAT('{$data}', '%m%Y'))
		// $where
		// -- ordenando pela data corrente
		// ORDER BY data_corrente ASC";

		$result = mysqli_query ( $this->con, $sql );

		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'ListarPorMesAno' ) );
		}else{
			$lista = array();
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
				// $control_moviment_mes = new movimento_mes_control();
				// $resp = $control_moviment_mes->buscarPorMovimentoDataCorrente($row['id'], $row['data_corrente']);
				// if (!$resp['success']) return $resp;
				// if (!empty($resp['data'])) $row['status'] = 'CONFIRMADO';
				array_push( $lista, $row);
			}

			$this->superdao->setSuccess( true );
			$this->superdao->setData( $lista );
		}

		return $this->superdao->getResponse();
	}

	//deletar
	function deletar (movimento $obj) {
		$this->superdao->resetResponse();

		// buscando por dependentes
		$dependentes = $this->superdao->verificaDependentes($obj->getId());
		if ( $dependentes > 0 ) {
			$this->superdao->setMsg( resolve( '0001', $dependentes, get_class( $obj ), 'Deletar' ));
			return $this->superdao->getResponse();
		}

		$this->sql = sprintf("DELETE FROM movimento WHERE id = %d",
			mysqli_real_escape_string($this->con, $obj->getId()));
		$result = mysqli_query($this->con, $this->sql);

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), get_class( $obj ), 'Deletar' ));
			return $this->superdao->getResponse();
		}

		$this->superdao->setSuccess( true );
		$this->superdao->setData( true );

		return $this->superdao->getResponse();
	}

	//quantidade total
	function qtdTotal() {
		$this->sql = "SELECT count(*) as quantidade FROM movimento";
		$result = mysqli_query ( $this->con, $this->sql );
		if (! $result) {
			die ( '[ERRO]: ' . mysqli_error ( $this->con ) );
		}
		$total = 0;
		while ( $row = mysqli_fetch_object ( $result ) ) {
			$total = $row->quantidade;
		}
		return $total;
	}
}

// Classe gerada com BlackCoffeePHP 2.0 - by Adelson Guimarães
?>