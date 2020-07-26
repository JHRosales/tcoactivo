<?php
require_once 'Zend/Controller/Action.php';

class PanelController extends Zend_Controller_Action {

    public function init() {
    	$this->_helper->layout()->setLayout('layoutwithpanel');
    }
    
    public function indexAction() {
    }


    public function contribuyentebloqueadoAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $_mperson = $this->_request->getPost('v_mperson', '');

            $params = null;
            $params[] = $_mperson; // p_mperson

            $cn = new Model_DataAdapter();
            $bloqueado = $cn->ejec_store_procedura_sql('registro.contribbloqueado_listar_mperson', $params);
            $cbloqueado = count($bloqueado);

            if($cbloqueado > 0){

                $result['bloqueado'] = '1';
                $result['vtipo'] = $bloqueado[0][9];
                $result['doc'] = $bloqueado[0][2];
                $result['obs'] = $bloqueado[0][3];

                if($bloqueado[0][8] == '1000003016'){
                    $result['bloqueado'] = '1';
                }elseif($bloqueado[0][8] == '1000003017'){
                    $result['bloqueado'] = '2';
                    /*
                    $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
                    $area = $ddatosuserlog->areacod;

                    if($area == '0000000014'){
                            $result['bloqueado'] = '0';
                    } else{
                        $result['doc'] = $bloqueado[0][9] . ' - DDJJ :' . $bloqueado[0][2];
                        if(strip_tags($bloqueado[0][3]) == ''){
                            $result['obs'] = 'SIN OBSERVACION';
                        }
                    }
                    // Area Fiscalizacion "0000000007" (seguridad.area)
                    // Area Informatica "0000000001" (seguridad.area)
                */}
            }else{
                $result['bloqueado'] = '0';
            }
            echo json_encode($result);
        }
    }



    public function resumenPersona($cidpers, $crazsoc) {
    	$parameters[] = $cidpers;
    	$dataAdapter = new Model_DataAdapter();   
    	$rows = $dataAdapter->executeAssocQuery("pl_function.panel_persona", $parameters);
    	if($rows != null) {
    		$mperson = $rows[0];
    	} else {
    		$mperson = array(
    			'cidpers' => $cidpers,
    			'crazsoc' => $crazsoc,
    			'vnrodoc' => 'No existe informaci&oacute;n',
    			'dfecdoc' => '',
    			'vmotivo' => '',
    			'ntotpre' => '0',
    			'ntotcom' => '0',
    			'nbaseim' => '0.00',
    			'nimpanu' => '0.00',
    			'vprofis' => 'No',
    			'vprotra' => 'No',
    			'dfecpag' => '',
    		);
    	}
    
    	$rows = $dataAdapter->executeAssocQuery("pl_function.resumen_saldo_personal", $parameters);
    	if($rows != null) {
    		$msaldos = $rows[0];
    	} else {
    		$msaldos = array(
    				'cidpers' => '',
    				'ntotals' => '',
    				'npendie' => '',
    				'ncancel' => '',
    				'nporcen' => '',
    				'vindica' => 'ff0000.png'
    		);
    	}
    	
    	return array("mperson" => $mperson, "msaldos" => $msaldos);
    }
    
    public function personaAction() {
    	$this->_helper->getHelper('ajaxContext')->initContext();
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    		
    		$this->view->cidpers = $this->_request->getParam('cidpers');
    		$this->view->vnombre = $this->_request->getParam('crazsoc');
    		
    		$result = $this->resumenPersona($this->view->cidpers, $this->view->vnombre);

    		//print_r($result);
    		$this->view->mperson = $result["mperson"];
    		$this->view->msaldos = $result["msaldos"];
    		
    		$ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
    		
    		$this->view->usuario = $ddatosuserlog->nompers;
    		$this->view->userlogin = $ddatosuserlog->userlogin;
    		
    		$nombrestore = '"public"."pxcobrowww"';
    		$arraydatos [0] = '1';
    		$arraydatos [1] = '';
    		$arraydatos [2] = '';
    		$cn = new Model_DataAdapter ();
    		$datosfecha = $cn->ejec_store_procedura_sql ( $nombrestore, $arraydatos );
    		$dfecha = explode ( " ", $datosfecha [0] [0] );
    		$fechahoy = $dfecha[0];
    		$this->view->fechahoy = $fechahoy;
    		
    		// andres : agregado 20/09/2013
    		// #
    		// Check de la deuda
    		$deudaChecked = true;
    		if($result["msaldos"]["npendie"] == 0){
    			$deudaChecked = false;
    		}
    		$this->view->deudaenabled = $deudaChecked;
    		
    		// Obtener estados
    		$arraydatos = null;
    		$arraydatos[] = '';
    		$arraydatos[] = '0';
    		$estados = $cn->ejec_store_procedura_sql('"public"."obt_mestados"', $arraydatos);
    		$cestados = count($estados);
    		$strestados = '';
    		for($i=0; $i<$cestados;++$i){
    			$strestados .= $estados[$i][0].'-';
    		}
    		$strestados = substr($strestados, 0, strlen($strestados)-1);
    		//print substr($strestados, 0, strlen($strestados)-1);
    		$this->view->estados = $strestados;
    		// #
    	}
    }
    
    public function jsonpersonaAction() {
    	$this->_helper->getHelper('ajaxContext')->initContext();
    	 
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		
    		$cidpers = $this->_request->getParam('cidpers');
    		$vnombre = $this->_request->getParam('crazsoc');
    		
    		$result = $this->resumenPersona($cidpers, $vnombre);
    		
    		$mperson = $result["mperson"];
    		$msaldos = $result["msaldos"];
    		
    		$data = array(
    			'cidpers' => $cidpers,
    			'vnombre' => $vnombre,
    			'mperson' => $mperson,
    			'msaldos' => $msaldos
    		);
    		
    		$this->_helper->json($data);
    	}
    }
    
    public function predioAction() {
        
    }
}