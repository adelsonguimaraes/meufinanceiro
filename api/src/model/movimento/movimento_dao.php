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

		$this->sql = sprintf("INSERT INTO movimento(idusuario, idcartao, nome, descricao, valor_mensal, quantidade_parcela, data_inicial, tipo)
		VALUES(%d, {$obj->getIdcartao()}, '%s', '%s', %f, %d, '%s', '%s')",
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

		$this->sql = sprintf("UPDATE movimento SET idusuario = %d, idcartao = {$obj->getIdcartao()}, nome = '%s', descricao = '%s', valor_mensal = '%s', quantidade_parcela = %d, data_inicial = '%s', tipo = '%s', data_edicao = '%s' WHERE id = %d ",
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

	//buscar por nome
	function buscarPorNome ($nome) {
		$this->sql = "SELECT * FROM movimento WHERE nome = '{$nome}'";
		$result = mysqli_query($this->con, $this->sql);

		$this->superdao->resetResponse();

		if(!$result) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), get_class( $obj ), 'BuscarPorId' ) );
		}else{
			while($row = mysqli_fetch_assoc($result)) {
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
	function listarPaginado($idusuario, $pagination) {

		$where = "WHERE m.idusuario = {$idusuario}
		AND m.ativo = \"{$pagination['active']}\"";

		$this->sql = "SELECT m.*, c.nome AS 'cartao_nome',
		c.dia_vencimento AS 'cartao_dia_vencimento', c.final AS 'cartao_final'
		FROM movimento m
		LEFT JOIN cartao c ON c.id = m.idcartao
		$where
		limit {$pagination['start']}, {$pagination['limit']}";
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
			$this->superdao->setTotal( $this->qtdTotal($where) );
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

	function getDataMinima ($idusuario) {
		$sql = "SELECT MIN(m.data_inicial) AS 'data_minima'
		FROM movimento m
		WHERE m.idusuario = {$idusuario}";
		
		$result = mysqli_query ( $this->con, $sql );
		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'getDataMinima' ) );
		}else{
			$row = mysqli_fetch_assoc ( $result );
		}

		$this->superdao->setSuccess( true );
		$this->superdao->setData( $row['data_minima'] );

		return $this->superdao->getResponse();
	}

	function getTotalMes ($idusuario, $data_corrente, $tipo) {
		
		$sql = "SELECT
		SUM(
			IF(
				mm.id IS NULL,
				m.valor_mensal,
				mm.valor
			)
		) AS 'total'
		FROM movimento m
		LEFT JOIN movimento_mes mm ON mm.idmovimento = m.id
		AND DATE_FORMAT(mm.data_corrente, '%Y%m') = DATE_FORMAT('{$data_corrente}', '%Y%m')
		WHERE m.idusuario = {$idusuario}
		AND m.ativo = 'SIM'
		AND m.tipo = '{$tipo}'
		-- DATA INICIAL <= DATA CORRENTE
		AND DATE_FORMAT( m.data_inicial, '%Y%m') <=
			DATE_FORMAT( '{$data_corrente}', '%Y%m')
		-- DATA FINAL >= DATA CORRENTE
		-- OU PARCELA = 0
		AND (
			DATE_FORMAT( DATE_ADD( m.data_inicial, INTERVAL m.quantidade_parcela MONTH ), '%Y%m') >=
			DATE_FORMAT( '{$data_corrente}', '%Y%m')
			OR m.quantidade_parcela = 0
		)";

		$result = mysqli_query ( $this->con, $sql );
		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'getDataMinima' ) );
		}else{
			$row = mysqli_fetch_assoc ( $result );
		}

		$this->superdao->setSuccess( true );
		$this->superdao->setData( $row['total'] );

		return $this->superdao->getResponse();
	}

	function listarMesesTimeline ($idusuario) {
		
		// consultando o salto de meses pra timeline
		$resp = $this->getSaltoTimeLine ($idusuario);
		if (!$resp['success']) return $resp;
		$intervalo = $resp['data']; // o intervalo que controla quantos meses serão consultados

		$sql = '';
		$where = " WHERE m.idusuario = {$idusuario} AND m.ativo = 'SIM' ";

		for ($i=0; $i<=$intervalo; $i++) {
			// calculamos a data_inicial de cada movimento + quantidade de parcelas
			// para verificar se ainda está disponível no mês corrente
			// também verificamos se a quantidade de parcelas é = 0 que é equivalente a FIXO
			if ($i>=1) {
				$sql .= "UNION\r\n"; // se tiver mais de um intervalo fazemos union
				$ativo = 'NAO';
			}
			$sql .= "SELECT DATE_ADD(
				(SELECT MIN(m.data_inicial) 
				FROM movimento m
				$where), INTERVAL {$i} MONTH
			) AS 'data',
			CONCAT(
				UPPER(
					SUBSTR(
						MONTHNAME(
							DATE_ADD(
								(
									SELECT MIN(m.data_inicial) 
									FROM movimento m
									$where
								), 
								INTERVAL {$i} MONTH)
							), 
						1, 3
					)
				),
				' ', 
				DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL {$i} MONTH), '%Y')
			) AS 'mes_ano',
			IF(
				DATE_FORMAT(
					DATE_ADD(
						(SELECT MIN(m.data_inicial) 
						FROM movimento m
						$where), INTERVAL {$i} MONTH
					), '%m%Y'
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
				
				if (!empty($row['data'])) {
					$status = null;	
					$resp = $this->listarPorMesAno($idusuario, $row['data']);
					
					if (!$resp['success']) return $resp;
					$movimentos = $resp['data'];

					$confirmados=0; $abertos=0; $atrasados=0;
					foreach ($movimentos as $key) {
						if (!empty($key['status'])) {
							if ($key['status']==='CONFIRMADO') $confirmados++;
							if ($key['status']==='EMABERTO') $abertos++;
							if ($key['status']==='ATRASADO') $atrasados++;
						}
					}
					if ($confirmados>0) $status = "CONFIRMADO";
					if ($abertos>0) $status = "EMABERTO";
					if ($atrasados>0) $status = "ATRASADO";
					$row['status'] = $status;
					$row['valor'] = (empty($movimentos)) ? 0 : $movimentos[0]['total_pagamento'];
					array_push( $lista, $row);
				}
			}

			// mysqli_free_result($result);

			$this->superdao->setSuccess( true );
			$this->superdao->setData( $lista );
		}

		return $this->superdao->getResponse();
	}

	// passando um mês ano para compar se a data_inicial somada a quantidade de meses é > que o mes ano informado
	function listarPorMesAno ($idusuario, $data_corrente) {

		$where = "WHERE (
			m.idusuario = {$idusuario}
			AND m.ativo = 'SIM'
			-- ANOMES data_corrente >= ANOMES data_inicial
			AND DATE_FORMAT('{$data_corrente}', '%Y%m') >= DATE_FORMAT(m.data_inicial, '%Y%m')
			AND (
				-- E ANOMES data_corrente <= ANOMES data_final
				-- OU QUANTIDADE_PARCELA = ZERO
				(DATE_FORMAT('{$data_corrente}', '%Y%m') <= 
				DATE_FORMAT(DATE_ADD(m.data_inicial, INTERVAL (m.quantidade_parcela-1) MONTH), '%Y%m'))
				OR m.quantidade_parcela = 0
			)
		)";

		$sql = "SELECT m.*, c.nome AS 'cartao_nome', 
		mm.id AS 'idmovimento_mes', mm.valor as 'valor_pago', mm.data_pagamento, mm.observacao,
		c.dia_vencimento AS 'cartao_dia_vencimento', c.final AS 'cartao_final',
		-- data corrente
		CONCAT(DATE_FORMAT('{$data_corrente}', '%Y-%m-'), DATE_FORMAT(m.data_inicial, '%d')) AS 'data_corrente',
		-- dia e mes corrente - 01 FEV
		CONCAT(DATE_FORMAT(m.data_inicial, '%d'), ' ', UPPER(SUBSTR(MONTHNAME('{$data_corrente}'), 1, 3))) AS 'dia_mes',
		-- parcela corrente
		IF(m.quantidade_parcela<=0, '',
			CONCAT(
				' - ',
				m.quantidade_parcela -
				TIMESTAMPDIFF(
					MONTH, 
					DATE_FORMAT('{$data_corrente}', '%Y-%m-01'),
					DATE_FORMAT(DATE_ADD(m.data_inicial, INTERVAL m.quantidade_parcela MONTH), '%Y-%m-01')
				)+1, '/', m.quantidade_parcela
			)
		) AS 'parcela_corrente',
		-- descobringo o status do movimento
		IF (
			-- verificando se está CONFIRMADO
			mm.id IS NOT NULL,
			'CONFIRMADO',
			IF (
				-- caso não CONFIRMADO, verificamos se está em ATRASO
				(CONCAT(DATE_FORMAT('{$data_corrente}', '%Y-%m-'), DATE_FORMAT(m.data_inicial, '%d'))<CURDATE()),
				'ATRASADO',
				'EMABERTO'
			)
		) AS 'status',
		-- periodo mensal
		CONCAT(
			'De 01 ', 
			UPPER(SUBSTRING(MONTHNAME('{$data_corrente}'), 1, 3)),
			' até ', 
			DATE_FORMAT(LAST_DAY('{$data_corrente}'), '%d'),
			' ',
			UPPER(SUBSTRING(MONTHNAME('{$data_corrente}'), 1, 3))
		) AS 'periodo'
		FROM movimento m
		LEFT JOIN cartao c ON c.id = m.idcartao
		LEFT JOIN movimento_mes mm ON mm.idmovimento = m.id
		-- onde o mês e ano da dara corrente sejam iaguais
		AND (DATE_FORMAT(mm.data_corrente, '%m%Y') = DATE_FORMAT('{$data_corrente}', '%m%Y'))
		$where
		-- ordenando pela data corrente
		ORDER BY data_corrente ASC;";

		// echo $sql;
		// exit;

		$result = mysqli_query ( $this->con, $sql );

		$this->superdao->resetResponse();

		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'ListarPorMesAno' ) );
		}else{
			$lista = array();
			$total_pagamento = 0;
			$total_recebimento = 0;
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
				if ($row['tipo']==='PAGAMENTO') $total_pagamento += (!empty($row['idmovimento_mes']) ? $row['valor_pago'] : $row['valor_mensal']);
				else $total_recebimento += (!empty($row['idmovimento_mes']) ? $row['valor_pago'] : $row['valor_mensal']);
				array_push( $lista, $row);
			}

			$lista[0]['total_pagamento'] = $total_pagamento;
			$lista[0]['total_recebimento'] = $total_recebimento;
			$lista[0]['total_liquido'] = round($total_recebimento - $total_pagamento, 2);
			
			$this->superdao->setSuccess( true );
			$this->superdao->setData( $lista );
		}

		return $this->superdao->getResponse();
	}

	function listarPorDiasVencimento ($dias_para_vencer) {
		
		$sql = "SELECT m.*, u.nome AS 'usuario', u.email,
		CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m'), DATE_FORMAT(m.data_inicial, '-%d')) as 'data_corrente'
		FROM movimento m
		INNER JOIN usuario u ON u.id = m.idusuario
		LEFT JOIN movimento_mes mm ON mm.idmovimento = m.id
		AND mm.data_corrente = CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), DATE_FORMAT(m.data_inicial, '%d'))
		WHERE m.ativo = 'SIM'
		AND mm.id IS NULL
		AND (
			(
				DATE_FORMAT(m.data_inicial, '%Y%m') <= DATE_FORMAT(CURDATE(), '%Y%m')
				AND DATE_FORMAT(DATE_ADD(m.data_inicial, INTERVAL (m.quantidade_parcela-1) MONTH), '%Y%m') >= DATE_FORMAT(CURDATE(), '%Y%m')
			) OR
			(
				DATE_FORMAT(m.data_inicial, '%Y%m') <= DATE_FORMAT(CURDATE(), '%Y%m')
				AND m.quantidade_parcela = 0
			)
		)
		AND (
			TIMESTAMPDIFF(
				DAY,
				CURDATE(),
				CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m'), DATE_FORMAT(m.data_inicial, '-%d'))
			) = {$dias_para_vencer}
		)";


		$result = mysqli_query ( $this->con, $sql );
		$this->superdao->resetResponse();

		$lista = array();
		if ( !$result ) {
			$this->superdao->setMsg( resolve( mysqli_errno( $this->con ), mysqli_error( $this->con ), 'movimento' , 'getDataMinima' ) );
		}else{
			while ( $row = mysqli_fetch_assoc ( $result ) ) {
				array_push( $lista, $row);
			}
		}

		$this->superdao->setSuccess( true );
		$this->superdao->setData( $lista );

		return $this->superdao->getResponse();
	}

	//deletar
	function remover (movimento $obj) {
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
	function qtdTotal($where) {
		$this->sql = "SELECT count(*) AS quantidade
		FROM movimento m
		$where";

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