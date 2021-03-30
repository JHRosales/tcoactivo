<?php

require_once 'Zend/Controller/Action.php';

class Tramite1Controller extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function explegalAction()
    {
        //$func = new Libreria_Pintar();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->getHelper('ajaxContext')->initContext();

            $mdocumento = $this->_request->getPost('mdocumento');
            $vnrodocu = $this->_request->getPost('vnrodocu');
            $pmruta = $this->_request->getPost('pmruta');
            $accion = $this->_request->getPost('accion');

            $cn = new Model_DataAdapter();
            $parametros = null;
            $parametros[] =  $mdocumento;

            $datos = $cn->executeAssocQuery(
                'legal.buscar_explegal',
                $parametros
            );

            $this->view->mdocumento = $mdocumento;
            $this->view->vnrodocu = $vnrodocu;
            $this->view->pmruta = $pmruta;
            $this->view->accion = $accion;
            $cdatos = count($datos);
            if ($cdatos == 0) {
                $this->view->vexpjud = '';
                $this->view->vcedula = '';
                $this->view->cidespe = '9999999999';
                $this->view->cidmateria = '9999999999';
                $this->view->vpdelitos = '';
                $this->view->cidjuzgado = '9999999999';
                $this->view->ciddemandante = '';
                $this->view->vdemandante = '';
                $this->view->ciddenunciado = '';
                $this->view->vdenunciado = '';
                $this->view->cidagraviado = '';
                $this->view->vagraviado = '';
                $this->view->nmontoejec = '0';
                $this->view->ninteres = '0';
                $this->view->nmontopend = '0';
                $this->view->dfecreqpago = '01/01/1989';
                $this->view->vobserv = '';
                $this->view->dfecpago = '01/01/1989';
                $this->view->nmontodem = '0';
                $this->view->nmontoamor = '0';
                $this->view->ninteresamor = '0';
                $this->view->ncodinterno = '';
            } else {
                $this->view->vexpjud = $datos[0]['vexpjud'];
                $this->view->vcedula = $datos[0]['vcedula'];
                $this->view->cidespe = $datos[0]['cidespe'];
                $this->view->cidmateria = $datos[0]['cidmateria'];
                $this->view->vpdelitos = $datos[0]['vpdelitos'];
                $this->view->cidjuzgado = $datos[0]['cidjuzgado'];
                $this->view->ciddemandante = $datos[0]['ciddemandante'];
                $this->view->vdemandante = $datos[0]['vdemandante'];
                $this->view->ciddenunciado = $datos[0]['ciddenunciado'];
                $this->view->vdenunciado = $datos[0]['vdenunciado'];
                $this->view->cidagraviado = $datos[0]['cidagraviado'];
                $this->view->vagraviado = $datos[0]['vagraviado'];
                $this->view->nmontoejec = $datos[0]['nmontoejec'];
                $this->view->ninteres = $datos[0]['ninteres'];
                $this->view->nmontopend = $datos[0]['nmontopend'];
                $this->view->dfecreqpago = $datos[0]['dfecreqpago'];
                $this->view->vobserv = $datos[0]['vobserv'];
                $this->view->dfecpago = $datos[0]['dfecpago'];
                $this->view->nmontodem = $datos[0]['nmontodem'];
                $this->view->nmontoamor = $datos[0]['nmontoamor'];
                $this->view->ninteresamor = $datos[0]['ninteresamor'];
                $this->view->ncodinterno = $datos[0]['ncodinterno'];
            }
        }
    }
    public function guardarexplegalAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->getHelper('ajaxContext')->initContext();

            $mdocumento = $this->_request->getPost('mdocumento');
            $vexpjud = $this->_request->getPost('vexpjud');
            $vcedula = $this->_request->getPost('vcedula');
            $cidespe = $this->_request->getPost('cidespe');
            $cidmateria = $this->_request->getPost('cidmateria');
            $vpdelitos = $this->_request->getPost('vpdelitos');
            $cidjuzgado = $this->_request->getPost('cidjuzgado');
            $ciddemandante = $this->_request->getPost('ciddemandante');
            $ciddemandado = $this->_request->getPost('ciddemandado');
            $cidagraviado = $this->_request->getPost('cidagraviado');
            $nmontoejec = $this->_request->getPost('nmontoejec');
            $ninteres = $this->_request->getPost('ninteres');
            //$nmontopend = $this->_request->getPost('nmontopend');
            $dfecreqpago = $this->_request->getPost('dfecreqpago');
            $vobserv = $this->_request->getPost('vobserv');
            $dfecpago = $this->_request->getPost('dfecpago');
            $nmontodem = $this->_request->getPost('nmontodem');
            $nmontoamor = $this->_request->getPost('nmontoamor');
            $ninteresamor = $this->_request->getPost('ninteresamor');
            $ncodinterno = $this->_request->getPost('ncodinterno');
            /*JR*/
            //$vquejante = $this->_request->getPost('vquejante');
            $proceso = $this->_request->getPost('proceso');
            $entidad = $this->_request->getPost('entidad');
            $instancia = $this->_request->getPost('instancia');
            $dcontralor = $this->_request->getPost('dcontralor');
            $dfechinicio = $this->_request->getPost('dfechinicio');
            $djudicial = $this->_request->getPost('djudicial');
            $dquejado = $this->_request->getPost('dquejado');
            $mqueja = $this->_request->getPost('mqueja');


            $vhostnm = 'local';
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $vusernm =  $ddatosuserlog->cidusuario;

            $cn = new Model_DataAdapter();

            $userdata = new Zend_Session_Namespace('datosuserlog');
            $nombrestore = 'legal.guardar_explegal';

            $parametros[0] =  $mdocumento;
            $parametros[1] =  $vexpjud;
            $parametros[2] =  $vcedula;
            $parametros[3] =  $cidespe;
            $parametros[4] =  $cidmateria;
            $parametros[5] =  $vpdelitos;
            $parametros[6] =  $cidjuzgado;
            $parametros[7] =  $ciddemandante;
            $parametros[8] =  $ciddemandado;
            $parametros[9] =  $cidagraviado;
            $parametros[10] =  $nmontoejec;
            $parametros[11] =  $ninteres;
            $parametros[12] =  $dfecreqpago;
            $parametros[13] =  $vobserv;
            $parametros[14] =  $vhostnm;
            $parametros[15] =  $vusernm;
            $parametros[16] =  $dfecpago;
            $parametros[17] =  $nmontodem;
            $parametros[18] =  $nmontoamor;
            $parametros[19] =  $ninteresamor;
            $parametros[20] =  $ncodinterno;
            /*JR*/

            $parametros[21] =  $proceso;
            $parametros[22] = $entidad;
            $parametros[23] = $instancia;
            $parametros[24] =  $dcontralor;
            $parametros[25] =  $dfechinicio;
            $parametros[26] =  $djudicial;
            $parametros[27] =  $dquejado;
            $parametros[28] =  $mqueja;

            $datos = $cn->executeAssocQuery($nombrestore, $parametros);
            $cdatos = json_encode($datos[0]);
            echo $cdatos;
        }
    }

    public function guardardemquejAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('ajaxContext')->initContext();

            // $idsigma = $this->_request->getPost('idsigma');
            $mdocumento = $this->_request->getPost('mdocumento');
            $mperson = $this->_request->getPost('mperson');
            $flag = $this->_request->getPost('flag');

            $vhostnm = 'local';
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $vusernm =  $ddatosuserlog->cidusuario;

            $cn = new Model_DataAdapter();

            $userdata = new Zend_Session_Namespace('datosuserlog');
            $nombrestore = 'LEGAL.guardar_legaldem';

            $parametros[0] = "";
            $parametros[1] =  $mdocumento;
            $parametros[2] =  $mperson;
            $parametros[3] =  $flag;

            $datos = $cn->executeAssocQuery($nombrestore, $parametros);
            $cdatos = json_encode($datos[0]);
            echo $cdatos;
        }
    }

    public function obtenermaterialegalAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ajaxContext')->initContext();

        $cidespe = $this->_request->getPost('cidespe');

        echo $this->view->util()->getComboContenedorMateriaLegal($cidespe, '');
    }

    public function obtenerjuzgadolegalAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ajaxContext')->initContext();

        $cidespe = $this->_request->getPost('cidespe');

        echo $this->view->util()->getComboContenedorJuzgadoLegal($cidespe, '');
    }

    public function asuntoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $idsigma = $this->_request->getPost('idsigma');

            $cn = new Model_DataAdapter();
            $params[0] = $idsigma;
            $asunto = $cn->executeAssocQuery('coactivo.listar_masunto', $params);
            $casunto = count($asunto);

            if ($casunto > 0) {
                $this->view->idsigma = $asunto[0]['idsigma'];
                $this->view->vdescri = $asunto[0]['vdescri'];
                $this->view->dfecini = $asunto[0]['dfecini'];
                $this->view->dfecfin = $asunto[0]['dfecfin'];
                $this->view->varticulo = $asunto[0]['varticulo'];
                $this->view->nestado = $asunto[0]['nestado'];
            }
        }
    }

    public function guardarasuntoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $cn = new Model_DataAdapter();

            $idsigma = $this->_request->getPost('idsigma');
            $vdescri = $this->_request->getPost('vdescri');
            $dfecini = $this->_request->getPost('dfecini');
            $dfecfin = $this->_request->getPost('dfecfin');
            $varticulo = $this->_request->getPost('varticulo');
            $nestado = $this->_request->getPost('nestado');

            $detalle = $this->_request->getParam('dasuntodata');
            $detalleDdocument = json_decode($detalle, true);

            $params[0] =  $idsigma;
            $params[1] =  strtoupper($vdescri);
            $params[2] =  $nestado;
            $params[3] =  $dfecini;
            $params[4] =  $dfecfin;
            $params[5] =  strtoupper($varticulo);

            $grupoasunto = $cn->ejec_store_procedura_sql('coactivo.guardar_masunto', $params);

            $cdetalleDdocument = count($detalleDdocument);
            for ($i = 0; $i < $cdetalleDdocument; ++$i) {
                $params = null;
                $params[0] =  $detalleDdocument[$i]['idsigma'];
                $params[1] =  $detalleDdocument[$i]['masunto'];
                $params[2] = $detalleDdocument[$i]['ctipasunto'];
                $params[3] =  $detalleDdocument[$i]['ndias'];
                $params[4] =  $detalleDdocument[$i]['ctipccos'];
                $params[5] =  $detalleDdocument[$i]['ctiptra'];
                $params[6] =  $detalleDdocument[$i]['nestado'];

                $dasunto = $cn->ejec_store_procedura_sql('coactivo.guardar_dasunto', $params);

                $reqAsunto = json_decode($detalleDdocument[$i]['requisitos'], true);
                $creqAsunto = count($reqAsunto);

                for ($j = 0; $j < $creqAsunto; ++$j) {
                    $params = null;
                    $params[0] = $reqAsunto[$j]['idsigma'];
                    $params[1] =  $dasunto[0][0];
                    $params[2] =  $reqAsunto[$j]['ctipdoc'];
                    $params[3] =  $reqAsunto[$j]['nestado'];
                    $params[4] =  $reqAsunto[$j]['vobserv'];
                    $params[5] =  $reqAsunto[$j]['vhostnm'];
                    $params[6] =  $reqAsunto[$j]['vusernm'];

                    $requisito = $cn->ejec_store_procedura_sql('coactivo.guardar_dasunto_req', $params);
                }
            }
            echo $grupoasunto[0][0];
        }
    }

    public function dasuntoreqAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $dasunto = $this->_request->getParam('dasunto');
            $mdocumento = $this->_request->getParam('mdocumento');
            if ((string) $dasunto !== '') {
                echo $this->view->util()->getAsuntoRequisitos($mdocumento, $dasunto);
            } else {
                echo '';
            }
        }
    }

    public function nrequisitoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->view->tipodocs = $this->view->util()->getComboContenedorTramite('0000000001', '');
        }
    }

    public function dasuntoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $func = new Libreria_Pintar();
            $cn = new Model_DataAdapter();

            $idsigma = $this->_request->getPost('idsigma');

            $params[0] =  $idsigma;
            $params[1] =  '%';
            $dasunto = $cn->executeAssocQuery("coactivo.listar_dasunto", $params);
            $cdasunto = count($dasunto);

            if ($cdasunto > 0) {
                $this->view->idsigma = $dasunto[0]['idsigma'];
                $this->view->ctipasunto = $dasunto[0]['ctipasunto'];
                $this->view->dtipoasunto = $dasunto[0]['dtipoasunto'];
                $this->view->ndias = $dasunto[0]['ndias'];
                $this->view->ctipccos = $dasunto[0]['ctipccos'];
                $this->view->ctiptra = $dasunto[0]['ctiptra'];
                $this->view->nestado = $dasunto[0]['nestado'];
            }
            $evt[] = array("txtndias", "keypress", "return validarnumerossinespacios(event);");
            $func->PintarEvento($evt);

            //$this->view->tipodocs = $this->view->util()->getComboContenedorTramite('0000000001','');
        }
    }

    public function guardardasuntoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $cn = new Model_DataAdapter();

            $idsigma = $this->_request->getPost('idsigma');
            $ctipasunto = $this->_request->getPost('ctipasunto');
            $ndias = $this->_request->getPost('ndias');
            $ctipccos = $this->_request->getPost('ctipccos');
            $ctiptra = $this->_request->getPost('ctiptra');
            $nestado = $this->_request->getPost('nestado');

            $detalle = $this->_request->getParam('detalle');
            $detalleDdocument = json_decode($detalle);

            $params[0] =  $idsigma;
            $params[1] =  $ctipasunto;
            $params[2] =  $ndias;
            $params[3] =  $ctipccos;
            $params[4] =  $ctiptra;
            $params[5] = $nestado;

            $dasunto = $cn->ejec_store_procedura_sql("coactivo.guardar_dasunto", $params);
            echo $dasunto[0][0];
        }
    }

    public function listarmasuntoAction()
    {
    }

    public function listarexpedienteAction()
    {
        $cidarea = '';
        $vestado = '';
        $vnrodoc = $this->_request->getParam('vnrodoc');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $post_cidarea = $this->_request->getPost('cidarea');
            $post_vestado = $this->_request->getPost('vestado');

            if (isset($post_cidarea)) {
                $cidarea = $post_cidarea;
            }
            if (isset($post_vestado)) {
                $vestado = $post_vestado;
            }
        }
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cusuario = $ddatosuserlog->cidusuario;
        $this->view->cidareauser = $ddatosuserlog->areacod;
        $this->view->cidarea = $cidarea;
        $this->view->vestado = $vestado;
        $this->view->vnrodoc = $vnrodoc;
    }

    public function listardasuntoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $cidsigma = $this->_request->getParam('ctryid');

            $ctiptra = $this->_request->getParam('ctiptra');

            if ($ctiptra == '9999999999') {
                $ctiptra = '';
            }
            $this->view->ctiptra = $ctiptra;
        }
    }
    public function buscarpersonaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
        }
    }

    public function expedienteAction()
    {
        $idsigma = '0000000000';
        $titulo = 'Nuevo Expediente';

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->getHelper('ajaxContext')->initContext();
            $idsigma = $this->_request->getPost('idsigma');
            $titulo = $this->_request->getPost('dstitulo');
        } else {
            #$idsigma = $this->_request->getPost('idsigma');
            #$titulo = $this->_request->getPost('dstitulo');

        }
        $func = new Libreria_Pintar();
        $cn = new Model_DataAdapter();

        $this->view->idsigma = $idsigma;
        $this->view->dstitulo = $titulo;
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->usuarioactual = $ddatosuserlog->cidusuario;

        // if($cdatos == 0){
        if ($idsigma == '0000000000') {
            $this->view->dsadministrado = '';
            $this->view->ndias = '';

            $this->view->vnrodocu = '';
            $this->view->dasunto = '';
            $this->view->dasunto_tipasunto = '';
            $this->view->dfecdocu = date('d/m/Y');
            $this->view->ctiprtram = '';
            if ($this->_request->getParam('tiptram') == '0000000620') {
                $this->view->ctiprtram = $this->_request->getParam('tiptram');
            }
            $this->view->dasunto = '0000000010';
            $this->view->dasunto_tipasunto = 'DOCUMENTO INTERNO';

            $this->view->dtiprtram = '';
            $this->view->nfolios = '';
            $this->view->ccosini = '0000000003';

            $this->view->ctiprele = '0000000140';
            $this->view->vccosini = '';
            $this->view->vasunto = '';
            $this->view->vobserv = '';
            $this->view->usuarioregistro = '';
            $this->view->vnrodocini = '';
            // Panel Administrado
            $this->view->mperson = '';
            $this->view->ctipper = '0000000111';
            $this->view->vperson = '';
            $this->view->vdirecc = '';
            $this->view->vdocper = '';

            // -- Representante Legal
            $this->view->crepres = '';
            $this->view->vrepres = '';
            $this->view->vdocrep = '';

            // Expediente precedente
            $this->view->mdocumento = '';
            $this->view->vmdocumento = '';
        } else {

            $parametros[0] =  $idsigma;
            $parametros[1] =  "";
            $parametros[2] =  "";
            $parametros[3] =  "";
            $parametros[4] =  "";
            $parametros[5] =  "";

            $userdata = new Zend_Session_Namespace('datosuserlog');
            $nombrestore = 'coactivo.listar_mdocumento';
            $datos = $cn->executeAssocQuery($nombrestore, $parametros);

            $cdatos = count($datos);

            // Obtener datos panel Expediente
            $panelExp = $cn->executeAssocQuery(
                'coactivo.panel_expediente',
                $parametros
            );

            // Obtener datos panel Administrado
            $parametros = null;
            $parametros[] =  $datos[0]['mperson'];

            $panelPers = $cn->executeAssocQuery('coactivo.buscar_persona', $parametros);

            $this->view->dsadministrado = $datos[0]['ds_administrado'];
            $this->view->ndias = $datos[0]['ndias'];

            $this->view->vnrodocu = $datos[0]['vnrodocu'];
            $this->view->dasunto = $datos[0]['dasunto'];
            $this->view->dasunto_tipasunto = $datos[0]['dasunto_tipasunto'];
            $this->view->dfecdocu = $datos[0]['dfecdocu'];
            $this->view->ctiprtram = $datos[0]['ctiprtram'];
            $this->view->dtiprtram = $datos[0]['dtiprtram'];
            $this->view->nfolios = $datos[0]['nfolios'];
            $this->view->ccosini = $datos[0]['ccosini'];
            $this->view->ctiprele = $datos[0]['ctiprele'];
            $this->view->vccosini = $datos[0]['vccosini'];
            $this->view->vasunto = $datos[0]['vasunto'];
            $this->view->vobserv = $datos[0]['vobserv'];
            $this->view->usuarioregistro = $datos[0]['usuario_registro'];
            $this->view->vnrodocini = $datos[0]['vnrodocini'];
            // Panel Administrado
            $this->view->mperson = $panelPers[0]['cidpers'];
            $this->view->ctipper = $panelPers[0]['ctipper'];
            $this->view->vperson = $panelPers[0]['crazsoc'];
            $this->view->vdirecc = $panelPers[0]['direccf'];
            $this->view->vdocper = $panelPers[0]['vnrodoc'];

            // -- Representante Legal
            $this->view->crepres = $datos[0]['crepres'];
            $this->view->vrepres = $datos[0]['vrepres'];
            $this->view->vdocrep = $datos[0]['vdocrep'];

            // Expediente precedente
            $this->view->mdocumento = $datos[0]['mdocumento'];
            $this->view->vmdocumento = $datos[0]['vmdocumento'];
        }
        // ECHO $this->view->ccosini;
    }



    public function paneladministradoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $buttons = $this->_request->getPost('buttons');
            $this->view->buttons = $buttons;
            $idsigma = $this->_request->getPost('idsigma');
            $tiptram = $this->_request->getPost('tiptram');
            $this->view->tiptram = $tiptram;
            if ($idsigma == "") {
                $idsigma = '0000000000';
            }
            $cn = new Model_DataAdapter();
            $params = null;
            $params[0] =  $idsigma;
            $params[1] =  "";
            $params[2] =  "";
            $params[3] =  "";
            $params[4] =  "";
            $params[5] =  "";
            $datos = $cn->executeAssocQuery(
                'coactivo.listar_mdocumento',
                $params
            );

            $cdatos = count($datos);
            if ($cdatos > 0) {
                $params = null;
                $params[0] =  $datos[0]['mperson'];
                $params[1] =  "";
                $params[2] =  "";
                $params[3] =  "";
                $params[4] =  "";
                $panelPers = $cn->executeAssocQuery(
                    'coactivo.buscar_persona',
                    $params
                );
                // Panel Administrado
                $this->view->mperson = $panelPers[0]['cidpers'];
                $this->view->ctipper = $panelPers[0]['ctipper'];
                $this->view->vperson = $panelPers[0]['crazsoc'];
                $this->view->vdirecc = $panelPers[0]['direccf'];
                $this->view->vdocper = $panelPers[0]['vnrodoc'];

                // -- Representante Legal
                $this->view->crepres = $datos[0]['crepres'];
                $this->view->vrepres = $datos[0]['vrepres'];
                $this->view->vdocrep = $datos[0]['vdocrep'];

                // -- Persona que entrega el documento
                $this->view->centrega = $datos[0]['centrega'];
                $this->view->ventrega = $datos[0]['ventrega'];
                $this->view->dentrega = $datos[0]['dentrega'];
                $this->view->flagdentrega = $datos[0]['flagdentrega'];
            }
        }
    }

    public function panelexpedienteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $idsigma = $this->_request->getPost('idsigma');

            // Panel Expediente
            //if (count($datos)!=0){
            if ($idsigma !== '0000000000') {
                // Obtener datos panel Expediente
                $cn = new Model_DataAdapter();
                $params[0] = $idsigma;

                $panelExp = $cn->executeAssocQuery(
                    'coactivo.panel_expediente',
                    $params
                );
                $cpanelExp = count($panelExp);
                $params[1] =  "";
                $params[2] =  "";
                $params[3] =  "";
                $params[4] =  "";
                $params[5] =  "";
                $userdata = new Zend_Session_Namespace('datosuserlog');
                $nombrestore = 'coactivo.listar_mdocumento';
                $datos = $cn->executeAssocQuery(
                    $nombrestore,
                    $params
                );

                $cdatos = count($datos);


                if ($cpanelExp > 0) {
                    $this->view->vaccion = $panelExp[0]['vtipacc'];
                    $this->view->carea = $panelExp[0]['carea'];
                    $this->view->varea = $panelExp[0]['varea'];
                }
                if ($cdatos > 0) {
                    $this->view->vnrodocu = $datos[0]['vnrodocu'];
                    $this->view->vindicador = $datos[0]['vindicador'];
                    $this->view->dtiprtram = $datos[0]['dtiprtram'];
                    $this->view->diastranscurridos = $datos[0]['ndias_transcurridos'];
                    $this->view->diasrestantes = $datos[0]['ndias_restantes'];
                }
            } else {
                $this->view->vindicador = '00ff00-2.png';
            }
        }
    }

    public function guardarexpedienteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->getHelper('ajaxContext')->initContext();

            $idsigma = $this->_request->getPost('idsigma');
            $dasunto = $this->_request->getPost('dasunto');
            $mperson = $this->_request->getPost('mperson');
            $dfecdocu = $this->_request->getPost('dfecdocu');
            $nfolios = $this->_request->getPost('nfolios');
            $ctiprtram = $this->_request->getPost('ctiprtram');
            $vasunto = $this->_request->getPost('vasunto');
            $ccosini = $this->_request->getPost('ccosini');
            $ctiprele = $this->_request->getPost('ctiprele');
            $vobserv = $this->_request->getPost('vobserv');
            $vnrodocu = $this->_request->getPost('vnrodocu');
            $crepres = $this->_request->getPost('crepres');
            $centrega = $this->_request->getPost('centrega');
            $mdocumento = $this->_request->getPost('mdocumento');
            $dentrega = $this->_request->getPost('dentrega');
            $flagdentrega = $this->_request->getPost('flagdentrega');
            $ccossender = $this->_request->getPost('ccossender');

            $dsadministrado = $this->_request->getPost('dsadministrado');
            $ndias = $this->_request->getPost('ndias');

            $vnrodocini = $this->_request->getPost('vnrodocini');

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $cusuario_registro = $ddatosuserlog->cidusuario;
            $cidpers = $ddatosuserlog->cidpers;

            $cn = new Model_DataAdapter();

            $userdata = new Zend_Session_Namespace('datosuserlog');
            $nombrestore = 'coactivo.guardar_mdocumento_1' . ($userdata->nvista);

            $parametros[0] =  $idsigma;
            $parametros[1] =  $dasunto;
            $parametros[2] =  $mperson;
            $parametros[3] =  $dfecdocu;
            $parametros[4] =  $nfolios;
            $parametros[5] =  $ctiprtram;
            $parametros[6] =  $vasunto;
            $parametros[7] =  $ccosini;
            $parametros[8] =  $ctiprele;
            $parametros[9] = $vobserv;
            $parametros[10] =  strtoupper($vnrodocu);
            $parametros[11] =  $crepres;
            $parametros[12] =  '';
            $parametros[13] =  '';
            $parametros[14] =  $centrega;
            $parametros[15] =  $cusuario_registro;
            $parametros[16] =  $mdocumento;
            $parametros[17] =  strtoupper($dentrega);
            $parametros[18] = $flagdentrega;
            $parametros[19] =  strtoupper($dsadministrado);
            $parametros[20] =  $ndias;
            $parametros[21] =  $ccossender;
            $parametros[22] = strtoupper($vnrodocini);

            $datos = $cn->executeAssocQuery($nombrestore, $parametros);
            $cdatos = count($datos);

            if ($cdatos == 1) {
                #if ($ctiprtram == '0000000620') {
                echo json_encode($datos[0]);
                #echo '<script>window.open(\'' . $this->view->util()->getLink('documentos/bandejadocs') . '\', \'_self\');</script>';
                #} else {
                #    echo '<script>window.open(\'' . $this->view->util()->getLink('coactivo/listarexpediente') . '\', \'_self\');</script>';
                #}
            } else {
                echo json_encode(array("st" => "0", "msj" => 'Error en el guardado...'));
            }
        }
    }

    public function validardocumentomigradoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $cn = new Model_DataAdapter();

            $mdocumento = $this->_request->getPost('mdocumento');
            $params[] =  $idsigma;

            $result = $cn->ejec_store_procedura_sql("sp_validacionDocumentomigrado", $params);
            echo json_encode($result[0]);
        }
    }
}
