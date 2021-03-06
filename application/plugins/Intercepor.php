<?php

require_once dirname(__FILE__) . '/../../library/Log4PHP/Logger.php';

class Application_Plugin_Intercepor extends Zend_Controller_Plugin_Abstract {

    private $_logger = null;
    private $_auth = null;
    private $_blackList = null;
    private $_executeController = null;
    private $_executeView = null;
    private $_cajexecuteView = null;

    private $_executeController2 = null;
    private $_executeView2 = null;

    private $_parametersObserverController = null;
    private $_parametersObserver = null;

    public function __construct(Zend_Auth $auth, array $options) {
        date_default_timezone_set(DATE_ZONE);
        Logger::configure(LOG_ACCESS);

        $this->_logger = Logger::getLogger(__CLASS__);
        $this->_auth = $auth;
        $this->_blackList = $options['blackList'];
        $this->_executeController = $options['executeController'];
        $this->_executeView = $options['executeView'];

        $this->_cajexecuteView = $options['cajexecuteView'];

        $this->_executeController2 = $options['executeController2'];
        $this->_executeView2 = $options['executeView2'];

        $this->_parametersObserver = $options['parametersObserver'];
        $this->_parametersObserverController = $options['parametersObserverController'];
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $resource = $controller . ':' . $action;
        $mensaje="Ok";
        $auth = " [Acceso autorizado.]";

        $login = new Zend_Session_Namespace('datosuserlog');
        $nompers = $login->nompers;

        if (!in_array($resource, $this->_blackList) && ($nompers == '' || $nompers == null)) {
            if ($request->isXmlHttpRequest()) {
                $request->setControllerName('logeo')->setActionName('redirect');
            } else {
                $request->setControllerName('logeo')->setActionName('index');
            }
            $auth = " [Acceso no autorizado.]";
        }
        #$login->codcajero='04';
        if (strlen($login->codcajero)==0
            && in_array($controller, $this->_executeController)
            && !(in_array($action, $this->_executeView) || in_array($login->codcajero, $this->_cajexecuteView))
        ){
            $mensaje="No esta registrado como cajero";
            $request->setControllerName('cajaflujo')
                ->setActionName('aperturar')
                ->setParam('mensaje', $mensaje);
        }

        if (strlen($login->codcajero) != 0
            && in_array($controller, $this->_executeController)
            && !(in_array($action, $this->_executeView)
                && in_array($login->codcajero, $this->_cajexecuteView))
        ) {
            /*
            nestado = '0' then 'Habilitada'
            nestado = '1' then 'Apeturada'
            nestado = '2' then 'Cerrada'
            nestado = '3' then 'Cerrado del Dia'
            */
            $nombrestore = 'tesoreria.verif_est_caja';
            $arraydatos[] = $login->codcajero;

            $cn = new Model_DataAdapter();
            $datos = $cn->ejec_store_procedura_sql($nombrestore, $arraydatos);
            if (count($datos)) {
                if($datos[0][0] == '1'){

                    $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
                    $ddatosuserlog->cidapertura=$datos[0][2];
                    $mensaje='Ok';
                    #no se puede cobrar el dia de hoy con la apertura de ayer.
                    if($datos[0][3]=='0'){
                        $mensaje=$datos[0][1];
                    }
                }else{
                    $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
                    $mensaje=$datos[0][1];
                    $ddatosuserlog->cidapertura='';
                }
            } else {
                $mensaje = 'Comuniquese con el cajero central para habilitar por primera vez su caja.';
            }
            $this->_logger->info($mensaje);
            #$mensaje='Ok';
            if ($mensaje != 'Ok') {
                $request->setControllerName('cajaflujo')
                    ->setActionName('aperturar')
                    ->setParam('mensaje', $mensaje);
            }
        }
        if (strlen($login->codcajero) != 0 && in_array($controller, $this->_executeController2) && !in_array($action, $this->_executeView2)) {
            $nombrestore = 'tesoreria.verif_est_caja';
            $arraydatos[] = $login->codcajero;
            $cn = new Model_DataAdapter();
            $datos = $cn->ejec_store_procedura_sql($nombrestore, $arraydatos);
            if (count($datos)) {
                if ($datos[0][0]=='0' & $action=='aperturacaja'){
                    if($datos[0][3]=='0'){
                        $mensaje  =$datos[0][1];
                    }elseif($datos[0][3]=='1'){
                        $mensaje  ='Ok';
                    }
                }elseif ($datos[0][0]=='0' & $action=='cerrarcaja'){
                    $mensaje  =$datos[0][1];
                }elseif($datos[0][0]=='1' & $action=='aperturacaja'){
                    $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
                    $ddatosuserlog->cidapertura=$datos[0][2];
                    $mensaje  =$datos[0][1];
                }elseif($datos[0][0]=='1' & $action=='cerrarcaja'){
                    $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
                    $ddatosuserlog->cidapertura=$datos[0][2];
                    $mensaje  ='Ok';
                }elseif($datos[0][0]=='2'){
                    $mensaje  =$datos[0][1];
                }elseif($datos[0][0]=='3'){
                    $mensaje  =$datos[0][1];
                }
            } else {
                $mensaje = 'Comuniquese con el cajero central para habilitar su caja.';
            }
            if ($mensaje != 'Ok') {
                $request->setControllerName('cajaflujo')
                    ->setActionName('aperturar')
                    ->setParam('mensaje', $mensaje);#.' - '.$controller .' - '.$action
            }
        }
        $this->_logger->info($resource . $auth);
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        $controller = $request->getControllerName();
        $parameters = $request->getParams();
        $rows = null;

        if (in_array($controller, $this->_parametersObserverController)) {
            foreach ($this->_parametersObserver as $key => $value) {
                $eval = explode('.', $value );
                if($eval[0] == $controller) {
                    $param = @$parameters[$eval[1]];
                    if (isset($param)) {
                        $dataSet = new Zend_Session_Namespace($controller.$eval[1]);
                        $rows = $dataSet->data;

                        if(is_array($rows)) {
                            if(!in_array($param, $rows)) {
                                if(count($rows) < 5) {
                                    $rows[] = $param;
                                }
                                for($i = count($rows) - 1; $i > 0 ; $i--){
                                    $rows[$i] = $rows[$i - 1];
                                }
                                $rows[0] = $param;
                            }
                        } else {
                            $rows = array($param);
                        }
                        $dataSet->data = $rows;
                    }
                }
            }

            $body  = "\n";
            $body .= "<script type='text/javascript'>\n";
            $body .= "\t$(document).ready(function(){";
            $body .= "\t\tconsole.log('postDispatch() called')\n";
            $body .= "\t});\n";
            $body .= "</script>\n";

            // $this->getResponse()->appendBody($body);
        }
    }
}