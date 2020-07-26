<?php
	if(!defined("_CLASSES_"))
	{
		/** 
		  * Clase: TDatos
		  */
		  
		class TSPResult {
			// Constants
			//-----------------------------------
			const CASE_LOWER = 0;
			const CASE_NORMAL = 1;
			const CASE_UPPER = 2;
			//-----------------------------------
			const RESULT_BOF = 0;
			const RESULT_EOF = 1;
			const RESULT_CUR = 2;
			//-----------------------------------
			const PARAM_ALPHA = 0;
			const PARAM_NUMBER = 1;
			const PARAM_DATE = 3;
			//-----------------------------------
			const ORDER_ASC = 0;
			const ORDER_DESC = 1;
			//-----------------------------------
			
			//******* Properties
			protected $mConeccion; //coneccion
			protected $mCampos_Select = array(); //campos para seleccionar
			protected $mCampos_From = array(); //tablas de donde vamos a seleccionar
			protected $mCampos_Condicion= array(); //condiciones del where
			protected $mParametros = array(); // valores de los parametros  para filtrar
			protected $mCampos_Orden = array(); //campos field para ordenar
			protected $mCampos_Busca = array(); // campos field para la busqueda 
			protected $mParametro_Busca = array();// valores de los parametros de la Busqueda 
			protected $mNombre_SP; //Nombre del Procedimiento o Funcion
			protected $mRow; //Filas que almacenan la informaci�n
			protected $mCursor_Pos; //Posicion del cursor
			protected $mRow_Limite; //Limite de las Filas
			protected $mOffset;//
			protected $mTipo_Orden;// Tipo de ordenamiento
			protected $mEsquemas;// Nombre del Esquema o nombres de los esquemas
			protected $mStrCase;
			public $mRs; //nombre del recorser
			private $mNum_Rows; // numero de filas del recorset
			public $mTotal_paginas; //numero total de paginas
			private $mRow_por_Pagina;
			private $mCriterio_Bus;
			private $mConsulta;
			
			
			//******* Methods
						/**
			  * @Param $pConn	Recurso de Conexi�n a la BD BassByRef
			  * @Param $pSPName	Nombre del SP
			  */
			function __construct($pConn, $pSPName = "") {
				global $esquema;
				global $case;
				
				$this->mConeccion = $pConn;
				$this->mNum_Rows = -1;
				$this->mRow_Limite = "ALL";
				$this->mOffset = "";
				$this->mNombre_SP = $pSPName;
				$this->mTipo_Orden = " ASC";
				$this->mEsquemas = "logistica";
				$this->mStrCase = (isset($case)) ? $case : self::CASE_NORMAL;
				$this->mParametro_Busca = "";
			}
			
			/**
			  */
			function __destruct() {
				$this->Close();
			}
			/**
			  * @Param $pSchema	Nombre del Esquema de la Fucnci�n
			  */
			public function pg_Poner_Esquema($pSchema) {
				if($pSchema == "")
					$pSchema = "logistica";
				$this->mEsquemas = $pSchema;
			}
			
			public function pg_Obtener_Esquema() {
				return $this->mEsquemas;
			}
			
			public function pg_Poner_Criterio_Bus($criterio) {
				$this->mCriterio_Bus = $criterio;
			}
			
			public function pg_Paginacion ($vTamPag, $vPagina = "" )
			{
				
				if(is_integer($vTamPag) && $vTamPag > 0 )
				{
					$this->mRow_por_Pagina=$vTamPag;
				}
				else
					$this->mRow_por_Pagina = "ALL";
				
				if(is_integer($pOffset) && $vTamPag > 0 ) 
					$this->mOffset = $pOffset - 1;
				else
					$this->mOffset = "";
				
				if($vPagina=="")
				{
					$this->mOffset = "";
					$pagina=1;
				}
				else
				{
					//if(is_integer($this->mOffset) && $vTamPag > 0 )
						$this->mOffset = ($vPagina - 1) * $this->mRow_por_Pagina;
				}
				
			}
			
			public function pg_Total_paginas($vRowsPag)
			{
				if($vRowsPag>0)
					$this->mTotal_paginas = ceil($this->pg_Num_Rows() / $vRowsPag); 
				else
					$this->mTotal_paginas =1;
				return $this->mTotal_paginas;
			}
			
			public function pg_Total_paginasSQL($vRowsPag)
			{
				if($vRowsPag>0)
					$this->mTotal_paginas = ceil($this->pg_Total_Rows() / $vRowsPag); 
				else
					$this->mTotal_paginas =1;
				return $this->mTotal_paginas;
			}
			/**
			  * @Param $pLimit 	Cantidad del L�mite
			  * @Param $pOffset	Inidce del Registro de Inicio
			  */
			/*public function pg_Set_limite($pLimit, $pOffset = "") {
				if(is_integer($pLimit) && $pLimit > 0 ) 
					$this->mRow_Limite = $pLimit;
				else
					$this->mRow_Limite = "ALL";
				if(is_integer($pOffset) && $pLimit > 0 ) 
					$this->mOffset = $pOffset - 1;
				else
					$this->mOffset = "";
			}*/
			/**
			  * @Param $pSchema	Nombre del Esquema de la Fucnci�n
			  */
			public function setStrCase($pCase = self::CASE_NORMAL) {
				if($pCase < 0  || $pCase > 2)
					$pCase = self::CASE_NORMAL;
				$this->mCase = $pCase;
			}
			
			public function getStrCase() {
				return $this->mCase;
			}
			
			/**
			  * @Param $pName 	Nombre del Par�metro del SP
			  * @Param $pValue	Valor que toma el Par�metro del SP
			  */
			public function pg_Parametros_SP($pName, $pValue, $pType = self::PARAM_ALPHA) {
//				$pValue = addslashes($pValue);
//				if($pType == self::PARAM_ALPHA)
//					$pValue = "'$pValue'";
//				else
//				{ 
					/*if ($pValue=="0" && $pType=='N' )		$pValue="0";
					else*/ if ($pValue=="")		$pValue="NULL";
					else if($pValue=="NULL")	$pValue = "NULL";	
					else if(strtoupper(substr($pValue,0,5))=="ARRAY")	$pValue=$pValue;
					else 				$pValue = "'$pValue'";	
//				}
				$this->mParametros[$pName] = $pValue;
				
			}
			/**
			  * @Param $pName 	Nombre del Par�metro del Campo a Ordenar
			  */
			public function pg_Campos_A_Ordenar($pName, $pOrder) {
				if($pOrder == self::ORDER_DESC || $pOrder == 'DESC') 
					$ord = " DESC";
				else
					$ord = " ASC"; 
				$this->mCampos_Orden[$pName] = array('field' => $pName, 'order' => $ord);
			}
			
			/**
			  * @Param $pName 	Nombre del Campo a Filtrar
			  */
			public function pg_Campos_Busqueda($pName) {
				if(trim($pName) != "") 
					$this->mCampos_Busca[$pName] = $pName;
			}
			
			/**
			  * @Param $pParam Par�metro de B�squeda
			  */
			public function pg_Parametro_Busqueda($pParam) {
				$this->$mParametro_Busca = $pParam;
			}
			
			
			
			/**
			  */
			public function pg_Get_Count() {
				return $this->mRow_Limite;
			}
			
			/**
			  */
			public function pg_Get_Offset() {
				return $this->mOffset + 1;
			}
			
			/**
			  */
			public function pg_Campos_Select($vCampos) {
				$this->mCampos_Select=$vCampos;
			}
			/**
			  */
			public function pg_Campos_From($vCampos) {
				$this->mCampos_From=$vCampos;
			}
			/**
			  */
			public function pg_Campos_Condicion($vCampos) {
				$this->mCampos_Condicion=$vCampos;
			}
			/**
			  */
			public function pg_Store_Procedure() {
				$query = "select ".$this->mCampos_Select." from " . $this->mEsquemas . ".$this->mNombre_SP  (";
				switch($this->mStrCase) {
					case self::CASE_LOWER:
						$query = strtolower($query);
						break;
					case self::CASE_UPPER:
						$query = strtoupper($query);
						break;
				}
				foreach($this->mParametros as $Value)
					$query .= $Value . ",";
				$len = strlen($query);
				if(count($this->mParametros) > 0) $len -= 1;
				$query = substr($query, 0, $len) . ")";
				$offset = "";
				//echo "OFSETSITO ".$this->mOffset."  ";
				if($this->mOffset <> "" ) 
				{
					//echo "HOLA ";
					$offset = " OFFSET $this->mOffset";
				}
				$query .= $this->pg_Filtrar_Por() . $this->pg_Ordenar_Por() . " LIMIT $this->mRow_por_Pagina  ".$offset;
				return $query;
			}
			
			public function pg_Consulta_SQL() {
				$query = "SELECT ".$this->mCampos_Select." FROM" . $this->mCampos_From;
				
				if($this->mCampos_Condicion<>"")
				{
					$query = $query." WHERE ".$this->mCampos_Condicion;
				}
				$offset = "";
				//echo "OFSETSITO ".$this->mOffset."  ";
				if($this->mOffset <> "" ) 
				{
					//echo "HOLA ";
					$offset = " OFFSET $this->mOffset";
				}
				$query .=  " LIMIT $this->mRow_por_Pagina  ".$offset;
				return $query;
			}
			
			/**
			  */
			private function pg_Ordenar_Por() {
				$orderstring = "";
				if(count($this->mCampos_Orden) > 0) {
					foreach($this->mCampos_Orden as $Field) {
						$orderstring .= ", " . $Field['field'] . $Field['order'];
					}
					$orderstring = " order by" . substr($orderstring, 1);
				}
				switch($this->mStrCase) {
					case self::CASE_LOWER:
						$orderstring = strtolower($orderstring);
						break;
					case self::CASE_UPPER:
						$orderstring = strtoupper($orderstring);
						break;
				}
				return $orderstring;
			}
			
			/**
			  */
			/*private function pg_Filtrar_Por() {
				$filterstring = "";
				if(count($this->mCampos_Busca) > 0) {
					$aux = "";
					foreach($this->mCampos_Busca as $Field) {
						$aux .= "||admincon.ifisnull(" . $Field. ",'')";
					}
					$filterstring  = " where " . substr($aux, 2) . " like '%" . strtoupper($this->mParametro_Busca) . "%'";
				}
				return $filterstring;
			}*/
			private function pg_Filtrar_Por() {
				$filterstring = "";
				if(strlen($this->mCriterio_Bus)>0)
					$filterstring = " WHERE ".$this->mCriterio_Bus;
				return $filterstring;
			}
			
			/**
			  */
			public function pg_Rows_Count() {
			//echo "HOLA ".$this->mNum_Rows;
			
				$query = "select count(*) from " . $this->mEsquemas . ". $this->mNombre_SP (";
				switch($this->mStrCase) {
					case self::CASE_LOWER:
						$query = strtolower($query);
						break;
					case self::CASE_UPPER:
						$query = strtoupper($query);
						break;
				}
				foreach($this->mParametros as $Value)
					$query .= $Value . ",";
				$len = strlen($query);
				if(count($this->mParametros) > 0) $len -= 1;
				$query = substr($query, 0, $len) . ") " . $this->pg_Filtrar_Por() . ";";
				$AuxRes = pg_query($this->mConeccion, $query);
				$Count = pg_fetch_array($AuxRes);
				pg_free_result($AuxRes);  
				return $Count['count'];
			}
			
			public function pg_Total_Rows() {
		  
				$Total_rows=pg_num_rows($this->mRs);
				return $Total_rows;
			}
			/**
			  */
			public function Escribir_Consulta() {  
				return $this->mConsulta;
			}
			public function Poner_MSQL($SQL) {  
				return $this->mConsulta=$SQL;
			}
			public function executeMSQL() {
				$query = $this->mConsulta;
				$esquema="SET search_path = ".$this->mEsquemas.";";
				$this->mConsulta = $query;
				//echo htmlentities($esquema.$query)."<br>";
				$this->mRs = pg_query($this->mConeccion, $esquema.$query);
				//echo $this->mResource;
				//echo "count: ".pg_num_rows($this->mResource);
				if($this->mRs) {
					$this->mNum_Rows = pg_num_rows($this->mRs);
					$this->pg_Move_First();
				}
				return $this->mRs;
								
			}
			public function executeSP() {
				$query = $this->pg_Store_Procedure();
				$this->mConsulta = $query;
				//echo htmlentities($query)."<br>";
				//$contenido= "\n".date("d/m/Y")."-".time()."=>".nl2br(htmlentities($query));
				//$fp=fopen("C:\AppServ\www\gisp\consulta.txt","a");
				//fwrite($fp,$contenido);
				//fclose($fp);
				$this->mRs = pg_query($this->mConeccion, $query);
				//echo $this->mResource;
				//echo "count: ".pg_num_rows($this->mResource);
				if($this->mRs) {
					$this->mNum_Rows = pg_num_rows($this->mRs);
					$this->pg_Move_First();
				}
				return $this->mRs;
			}
			public function executeCSQL() {
				$query = $this->pg_Consulta_SQL();
				$esquema="SET search_path = ".$this->mEsquemas.";";
				$this->mConsulta = $query;
				//echo htmlentities($esquema.$query)."<br>";
				//$contenido= "\n".date("d/m/Y")."-".time()."=>".nl2br(htmlentities($query));
				//$fp=fopen("C:\AppServ\www\gisp\consulta.txt","a");
				//fwrite($fp,$contenido);
				//fclose($fp);				
				
				$this->mRs = pg_query($this->mConeccion, $esquema.$query);
				//echo $this->mResource;
				//echo "count: ".pg_num_rows($this->mResource);
				if($this->mRs) {
					$this->mNum_Rows = pg_num_rows($this->mRs);
					$this->pg_Move_First();
				}
				return $this->mRs;
			}
			
			/**
    		  * @param $pNumRows	N�mero de Registros a Mover
		      * @param $pRefPoint	Punto de Referencia
		      */
			public function pg_Move($pNumRows, $pRefPoint = self::RESULT_BOF) {
				$result = false;
				if(isset($this->mRs)) {
					switch($pRefPoint) {
						case self::RESULT_BOF:
							$base = -1;
							break;
						case self::RESULT_EOF:
							$base = $this->mNum_Rows;
							break;
				  		default:
							$base = $this->mCursor_Pos - 1;
					}
					$base += $pNumRows;
					if($result = ($base >= 0 && $base < $this->mNum_Rows)) {
						$result = pg_result_seek($this->mRs, $base);
				  		$this->mCursor_Pos = $base + 1;
				  		$this->mRow = pg_fetch_array($this->mRs);  
					}
			  	}
			  	return $result;
			}
			
			/**
			  */
			  
			  public function pg_Set_Cursor($cursor,$NRows)
			{
				
				if($cursor<0)
				{	
					$base = -1;
				}
				elseif($cursor>$NRows)
				{	
					$base = $this->mNum_Rows;
				}
				else
				{	
					$base=$cursor-1;
				}
				$result = pg_result_seek($this->mRs,$base);
				$this->mCursor_Pos = $base + 1;
				$this->mRow = pg_fetch_array($this->mRs); 
				return $result;
				
			}
			public function pg_Move_First() {
				return $this->pg_Move(1, self::RESULT_BOF);
			}
			
			/**
			  */
			public function pg_Move_Previous()	{
				return $this->pg_Move(-1, self::RESULT_CUR);
			}
			
			/**
			  */
			public function pg_Move_Next()	{
				return $this->pg_Move(1, self::RESULT_CUR);
			}
			
			/**
			  */
			public function pg_Move_Last() {
				return $this->pg_Move(1, self::RESULT_EOF);
			}
			
			/**
			  */
			public function pg_Num_Rows() {
				return $this->mNum_Rows;
			}
			
			/**
			  */
			public function pg_Cursor_Pos() {
				return $this->mCursor_Pos;
			}
			
			/**
			  */
			public function pg_SP_Nombre() {
				return $this->mNombre_SP;
			}
			
			/**
			  */
			public function pg_Get_Row() {
				return $this->mRow;
			}


			/**
			  */
			public function pg_Rs_objecto() {
				if(isset($this->mRs))
					if(pg_result_seek($this->mRs, $this->mCursor_Pos))
  				  $result = pg_fetch_object($this->mRs);
				return $result;
			}
			
			/**
			  */
			public function pg_Rs_Array()	{
				if(isset($this->mRs))
					if(pg_result_seek($this->mRs, $this->mCursor_Pos))
						$result = pg_fetch_array($this->mRs);
				return $result;
			}
			
			/**
			  */
			public function pg_Rs_Assoc()	{
				if(isset($this->mRs))
					if(pg_result_seek($this->mRs, $this->mCursor_Pos))
						$result = pg_fetch_assoc($this->mRs);
				return $result;
			}
			
			/**
			  */
			public function pg_Rs_Row() {
				//if(isset($this->mRs))
				//	if(pg_result_seek($this->mRs, $this->mCursor_Pos))
				  		$result = pg_fetch_row($this->mRs);
			  	//return $result;
			}
			
			public function Close() {
				if(isset($this->mRs) && $this->mRs !== FALSE) pg_free_result($this->mRs);
				unset($this->mRs);
				unset($this->mNum_Rows);
				unset($this->mRow);
				$this->mCursor_Pos = -1;
			}
			
		}
		
	}
	
?>