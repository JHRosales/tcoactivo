<?php

/**
 * ReportesController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class ReportesController extends Zend_Controller_Action {
    public function init()
    {

    }
	public function indexAction() {
	}

	public function prediosAction(){
		    $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            
        
        }
	}

    public function graficoconsumoAction(){
        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $url = $this->view->util()->getPath();

        $fini = $this->_request->getPost('fini');
        $ffin = $this->_request->getPost('ffin');
        $store = 'coactivo.obtener_data';
        $pgraf[0] = $fini;
        $pgraf[1] = $ffin;
        $cn = new Model_DataAdapter ();
        $datosgraf = $cn->ejec_store_procedura_sql($store, $pgraf);
        for ($i = 0; $i < count($datosgraf); $i++) {
            $data[]= array('0'=>  $datosgraf[$i][0],'1'=>  $datosgraf[$i][1]);
        }
        echo json_encode($data);
    }

}
