<?php
require_once 'Zend/Controller/Action.php';
class CoactivoController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $fecdesde = $this->_request->getPost('fecdesde');
        $fechasta = $this->_request->getPost('fechasta');
        if ($fechasta == '') {
            $fecdesde = date('d/m/Y', strtotime('yesterday - 6 day'));
            $fechasta = date('d/m/Y', strtotime('yesterday'));
            $this->view->fechaini = "01/01/1989";
            $this->view->fechafin = "01/01/1989";
        } else {
            $this->view->fechaini = $fecdesde;
            $this->view->fechafin = $fechasta;
        }
        $cn = new Model_DataAdapter();
        $procedure = 'COACTIVO.listar_pagostesoreria';
        $parameters[0] = $fecdesde;
        $parameters[1] = $fechasta;
        $records = $cn->executeAssocQuery($procedure, $parameters);
        $this->view->arrareas = json_encode($records);
    }


    public function uploadauxiliarcoactivoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //$this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $UsuarioReg = $ddatosuserlog->cidusuario;

            $ddatosuserlogA = new Zend_Session_Namespace('datosuserlogAuxi');
            $p_idsigma = $ddatosuserlogA->p_idsigma;
            $p_ciduser = $ddatosuserlogA->p_ciduser;
            $p_nestado = $ddatosuserlogA->p_nestado;

            $name = $_FILES['qqfile']['name'];
            $size = $_FILES['qqfile']['size'];
            // echo $p_ciduser;
            $ddatosuserlogA->p_file = $_FILES['qqfile']['name'];

            if ($size == 0) {
                return array('error' => 'File is empty.');
            }


            $nomadjunto = $p_ciduser . "_" . $_FILES['qqfile']['name'];
            $carpeta = "uploadDdocuments/";
            opendir($carpeta);
            $destino = $carpeta . $nomadjunto;
            copy($_FILES['qqfile']['tmp_name'], $destino);
            //closedir($carpeta);
            //                $ddatosuserlogA->p_ciduser = '';
            //  $ddatosuserlogA = new Zend_Session_Namespace('datosuserlogAuxi');
            $cn = new Model_DataAdapter();
            $procedure = 'COACTIVO.mant_auxiliarcoactivo';
            $parameters[0] = $p_idsigma;
            $parameters[1] = '';
            $parameters[2] = $name;
            $parameters[3] = '';
            $parameters[4] = '';
            $parameters[5] = '';
            $parameters[6] = $p_nestado;

            $recordsAdjunto = $cn->executeAssocQuery($procedure, $parameters);

            //  echo "<pre>";
            //print_r( $datoscajas);
            //   print_r( $parameters);
            // echo "</pre>";
            echo $this->_helper->json(array('success' => true));
        }
    }

    public function galeriafotosAction()
    {
        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();

        $firma = $this->_request->getParam('firma', '');

        $usu = $this->_request->getParam('usuario', '');
        $pintar = new Libreria_Pintar();

        $path = explode("/index.php", $_SERVER["PHP_SELF"]);


        $a = "";
        $cant = 1;
        if ($cant > 0) {
            $foto = trim($usu . '_' . $firma);
            $a .= "<img src=\"" . $path[0] . "/" . PATH . "uploadDdocuments/" . $foto . "\" />";
            // echo  $a;
            $val[] = array("slider", $a, 'html');
            $pintar->PintarValor($val);
        } else {
            echo "No hay Fotos";
        }

        $fun[] = array('$("#slider").nivoSlider();');

        $pintar->EjecutarFuncion($fun);
    }


    public function forzardescargaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $carpeta = "uploadDdocuments/";
        $file = $carpeta . $_GET['file'];
        header("Content-disposition: attachment; filename=$file");
        header("Content-type: application/octet-stream");
        readfile($file);
    }


    public function cargarauxiliarsesionAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //$this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $UsuarioReg = $ddatosuserlog->cidusuario;

            $ddatosuserlogA = new Zend_Session_Namespace('datosuserlogAuxi');
            $ddatosuserlogA->p_idsigma = $this->_request->getPost('idsigma');
            $ddatosuserlogA->p_usuario = $this->_request->getPost('usuario');
            $ddatosuserlogA->p_ciduser = $this->_request->getPost('ciduser');
            $ddatosuserlogA->p_cidpers = $this->_request->getPost('cidpers');
            $ddatosuserlogA->p_cbofiltro = $this->_request->getPost('cbofiltro_tipo');
            $ddatosuserlogA->p_nestado = $this->_request->getPost('nestado');

            $p_idsigma = $ddatosuserlogA->p_idsigma;
            $p_usuario = $ddatosuserlogA->p_usuario;
            $p_ciduser = $ddatosuserlogA->p_ciduser;
            $p_cidpers = $ddatosuserlogA->p_cidpers;
            $p_cbofiltro_tipo = $ddatosuserlogA->p_cbofiltro;
            $p_nestado = $ddatosuserlogA->p_nestado;
            $p_file = $ddatosuserlogA->p_file;
            if ($p_nestado == 1) {
                $p_nestado = 1;
            } else {
                $p_nestado = 0;
            }
            $cn = new Model_DataAdapter();
            $procedure = 'COACTIVO.mant_auxiliarcoactivo';
            $parameters[0] = $p_idsigma;
            $parameters[1] = $p_ciduser;
            $parameters[2] = $p_file;
            $parameters[3] = $p_cbofiltro_tipo;
            $parameters[4] = $this->view->util()->getHost();
            $parameters[5] = $UsuarioReg;
            $parameters[6] = $p_nestado;

            $recordsAdjunto = $cn->executeAssocQuery($procedure, $parameters);
            if ($recordsAdjunto[0]['a'] == '1') {
                $ddatosuserlogA->p_idsigma = $recordsAdjunto[0]['c'];
                echo ($recordsAdjunto[0]['b']);
            } else {
                echo ("Status: Error al Guardar ->" . $recordsAdjunto[0]['b'] . $ddatosuserlogA->p_file);
            }
        }
    }

    /**Auxiliares Coactivos*/
    public function auxiliarescoactivosAction()
    {
        $this->view->util()->registerScriptJSControllerAction($this->getRequest());
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;
    }

    public function manteauxiliarescoactivosAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $pintar = new Libreria_Pintar();
            $idtipo = '9999999999';
            $p_cidusuario = $this->_request->getPost('usuario');
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_cidpers = $this->_request->getPost('idsigmaperson');
            $ddatosuserlogA = new Zend_Session_Namespace('datosuserlogAuxi');
            $ddatosuserlogA->p_idsigma = '';
            $ddatosuserlogA->p_usuario = '';
            $ddatosuserlogA->p_ciduser = '';
            $ddatosuserlogA->p_cidpers = '';
            $ddatosuserlogA->p_cbofiltro = '';
            $ddatosuserlogA->p_nestado = '';

            $tipo = array(
                "1" => array("0" => "1", "1" => "Ejecutor"),
                "2" => array("0" => "2", "1" => "Auxiliar")

            );
            $dataAdapter = new Model_DataAdapter();

            $parametersu[] = $p_cidusuario; //p_cidusuario
            $parametersu[] = ""; //p_usuario
            $parametersu[] = ""; //p_cidpers
            $parametersu[] = ""; //p_cidarea
            $parametersu[] = "1"; //p_estado
            $parametersu[] = "U"; //p_tipousuario
            $datosusuarios = $dataAdapter->ejec_store_procedura_sql("seguridad.buscar_usuario", $parametersu);
            //en update no se puede seleccionar otro usuario ni caja por que se esta consultando por filtro si se debe selecionar otra caja entonces se debe eliminar el filtro

            $parameter[] = $p_idsigma; //p_cidusuario
            $parameter[] = ""; //p_usuario
            $datosAuxi = $dataAdapter->ejec_store_procedura_sql("coactivo.lst_auxiliarcoactivo", $parameter);

            if ($p_cidusuario != '') {

                $this->view->idsigma = $p_idsigma;
                $this->view->usuario = $datosusuarios[0][5]; //cambiar para que se pueda seleccionar otro usuario
                $this->view->ciduser = $p_cidusuario;
                $this->view->nomusuario = $datosusuarios[0][3];
                $this->view->cidpers = $p_cidpers;
                $this->view->nestado = $datosAuxi[0][5];
                $this->view->tipo = $datosAuxi[0][10];
                $this->view->firma = $datosAuxi[0][11];
                $idtipo = $datosAuxi[0][10];
            }

            $this->view->ctipoHTML = $pintar->ContenidoCombo($tipo, trim($idtipo), '2');

            $arrmusu = array();
            for ($i = 0; $i < count($datosusuarios); $i++) {
                $arrmusu[] = array("category" => "", "label" => $datosusuarios[$i][5], "_cidusuario" => $datosusuarios[$i][0], "_cidarea" => $datosusuarios[$i][1], "_cidpers" => $datosusuarios[$i][2], "_nombrepers" => $datosusuarios[$i][3], "_usuario" => $datosusuarios[$i][5]);
            }
            //print_r($arrmusu);
            $this->view->musuari = $arrmusu;
            //  echo "<pre>";
            //  print_r( $this->view->mcajas);
            // print_r( $this->view->musuari);
            //  echo "</pre>";

        }
    }
    /**FIN*/


    /**Costas Procesales*/
    public function costasprocesalesAction()
    {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }

    public function mantecostasprocesalesAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_tipodocumento = $this->_request->getPost('tipodocumento');
            $p_nrodocumento = $this->_request->getPost('nrodocumento');
            $p_fechadocumento = $this->_request->getPost('fechadocumento');
            $p_fechainicio = $this->_request->getPost('fechainicio');
            $p_fechafin = $this->_request->getPost('fechafin');
            $p_estado = $this->_request->getPost('estado');
            $p_vobserv = $this->_request->getPost('vobserv');

            $this->view->idsigma = "";
            $this->view->tipodocumento = ""; //cambiar para que se pueda seleccionar otro usuario
            $this->view->nrodocumento = "";
            $this->view->fechadocumento = "";
            $this->view->fechainicio = "";
            $this->view->fechafin = "";
            $this->view->estado = "";
            $this->view->vobserv = "";

            if ($p_idsigma != '') {

                $this->view->idsigma = $p_idsigma;
                $this->view->tipodocumento = $p_tipodocumento; //cambiar para que se pueda seleccionar otro usuario
                $this->view->nrodocumento = $p_nrodocumento;
                $this->view->fechadocumento = $p_fechadocumento;
                $this->view->fechainicio = $p_fechainicio;
                $this->view->fechafin = $p_fechafin;
                $this->view->estado = $p_estado;
                $this->view->vobserv = $p_vobserv;
            }
        }
    }
    public function mantecostasprocesalessaveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $UsuarioReg = $ddatosuserlog->cidusuario;
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_tipodoc = $this->_request->getPost('tipodoc');
            $p_numDoc = $this->_request->getPost('numDoc');
            $p_txtfecdoc = $this->_request->getPost('txtfecdoc');
            $p_fechaini = $this->_request->getPost('txtfecini');
            $p_fechafin = $this->_request->getPost('txtfecfin');
            $p_txtvobserv = $this->_request->getPost('txtvobserv');
            $p_nestado = $this->_request->getPost('nestado');
            if ($p_nestado == 1) {
                $p_nestado = 1;
            } else {
                $p_nestado = 0;
            }
            $dataAdapter = new Model_DataAdapter();

            $parametersu[0] = $p_idsigma;
            $parametersu[1] = $p_tipodoc;
            $parametersu[2] = $p_numDoc;
            $parametersu[3] = $p_txtfecdoc;
            $parametersu[4] = $p_fechaini;
            $parametersu[5] = $p_fechafin;
            $parametersu[6] = $p_txtvobserv;
            $parametersu[7] = $this->view->util()->getHost();
            $parametersu[8] = $UsuarioReg;
            $parametersu[9] = $p_nestado;

            $datosusuarios = $dataAdapter->ejec_store_procedura_sql("coactivo.mant_costasprocesales", $parametersu);
            // echo "<pre>";
            // print_r($datosusuarios);
            // echo "</pre>";

            if ($datosusuarios[0][0] == '1') {
                echo ($datosusuarios[0][1]);
            } else {
                echo ("Status: Error ->" . $datosusuarios[0][1]);
            }
        }
    }

    public function listarcostasprocesalesAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_pcostas = $this->_request->getParam('pcostas');
            $pcostasenvio = json_decode($p_pcostas);
            $dataAdapter = new Model_DataAdapter();
            $procedure = 'coactivo.obtener_tabla';
            $parametersaa[0] = '0000000003';
            $records = $dataAdapter->executeAssocQuery($procedure, $parametersaa);
            $this->view->arrareas = json_encode($records);
            $this->view->idsigma = $p_idsigma;
            // print_r(json_encode($records));
            //print_r(json_encode($pcostasenvio));
            $this->view->pcostas = json_encode($pcostasenvio);
        }
    }
    public function dcostasprocesalessaveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $pcostasenvio = $this->_request->getParam('pcostasenvio');
            $pidsigmacosta = $this->_request->getParam('pidsigmacosta');

            $pcostasenvio = json_decode($pcostasenvio);
            $cadcostas = "";
            foreach ($pcostasenvio as $values) {
                $cadcostas .= '|';
                $cadcostas .=  $values->pacosta . '|';
                $cadcostas .= $values->precio . '^';
            }
            $cadcostas = substr($cadcostas, 0, strlen($cadcostas) - 1);
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');

            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.guardar_dcostasprocesales';
            $parameters[0] =  $pidsigmacosta;
            $parameters[1] =  $cadcostas;
            $parameters[2] = '^';
            $parameters[3] = '|';
            $parameters[4] = $this->view->util()->getHost();
            $parameters[5] =  $ddatosuserlog->cidusuario;

            /*echo "<pre>";
            print_r($parameters);
            echo "</pre>";*/
            $records = $cn->executeAssocQuery($procedure, $parameters);
            $msg = $records[0]['b'];
            echo json_encode($msg);
        }
    }

    /**FIN*/

    /**CALIFICAR VALORES*/
    public function calificarvaloresAction()
    {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }
    public function calificarAction()
    {
        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;
        $pidsgima = $this->_request->getPost('idsigma');
        $cn = new Model_DataAdapter();
        $procedure = 'coactivo.sp_evaluar';
        $parameters[0] =  $pidsgima;
        $records = $cn->executeAssocQuery($procedure, $parameters);

        $para[] = 'Mensaje De respuesta';
        $para[] = $pidsgima;
        echo json_encode($records);
    }

    /**FIN*/

    /**Gestion de Expedientes*/
    public function gestionexpedientesAction()
    {
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }

    public function gendocumentosAction()
    {
        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();
        $p_idsigma = $this->_request->getPost('idsigma');
        $p_docum = $this->_request->getParam('iddocum');
        $p_nroexp = $this->_request->getPost('nroexp');
        $this->view->idsigma = $p_idsigma;
        $this->view->nroexp = $p_nroexp;
        $this->view->cborec = '0000015189';
        $this->view->cbotros = '0000015194';
    }

    public function expedientescostasAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_pcostas = $this->_request->getParam('idcostas');
            //$pcostasenvio = json_decode($p_pcostas);
            //$dataAdapter = new Model_DataAdapter();
            //$procedure = 'coactivo.obtener_tabla';
            //$parametersaa[0] = '0000000003';
            //$records = $dataAdapter->executeAssocQuery($procedure, $parametersaa);
            //$this->view->arrareas = json_encode($records);
            $this->view->codmdocum = $p_idsigma;
            $this->view->idsigma = $p_pcostas;
        }
    }

    public function gendocumentossaveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $UsuarioReg = $ddatosuserlog->cidusuario;
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_tipodoc = $this->_request->getPost('tipo');
            $p_nroexp = $this->_request->getPost('nroexp');
            $p_rec = $this->_request->getPost('cbomodrec');
            $p_otro = $this->_request->getPost('cbomodotras');
            $p_nestado = $this->_request->getPost('nestado');
            if ($p_nestado == 1) {
                $p_nestado = 1;
            } else {
                $p_nestado = 0;
            }
            $dataAdapter = new Model_DataAdapter();
            $tipo = "";
            if ($p_tipodoc == '02') {
                $tipo = '0000015192';
            } else if ($p_tipodoc == '01') {
                $tipo = $p_rec;
            } else if ($p_tipodoc == '03') {
                $tipo = $p_otro;
            }


            $parametersu[0] = $p_rec;
            $parametersu[1] = $tipo;
            $parametersu[2] = $p_nroexp;
            $parametersu[3] = $this->view->util()->getHost();
            $parametersu[4] = $UsuarioReg;
            $parametersu[5] = $p_nestado;
            $parametersu[6] = $p_idsigma;

            $datosusuarios = $dataAdapter->ejec_store_procedura_sql("coactivo.guardar_docemitido", $parametersu);
            /*echo "<pre>";
             print_r($datosusuarios);
             echo "</pre>";*/

            if ($datosusuarios[0][0] > '0') {
                echo ("Generado Correctamente");
            } else {
                echo ("Status: Error ->" . "El documento no se puede generar");
            }
        }
    }

    public function expdientescostassaveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $UsuarioReg = $ddatosuserlog->cidusuario;
            $p_idsigma = $this->_request->getPost('idsigmaexpcos');
            $p_mdocumento = $this->_request->getPost('codmdocum');
            $p_cantidad = $this->_request->getPost('cantidad');
            $p_nestado = $this->_request->getPost('nestado');
            $p_idcostas = $this->_request->getPost('codigo');
            if ($p_nestado == 1) {
                $p_nestado = 1;
            } else {
                $p_nestado = 0;
            }
            $dataAdapter = new Model_DataAdapter();

            $parametersu[0] = $p_idsigma;
            $parametersu[1] = $p_mdocumento;
            $parametersu[2] = $p_cantidad;
            $parametersu[3] = $this->view->util()->getHost();
            $parametersu[4] = $UsuarioReg;
            $parametersu[5] = 1;
            $parametersu[6] = $p_idcostas;


            $datosusuarios = $dataAdapter->ejec_store_procedura_sql("coactivo.guardar_expedcostas", $parametersu);
            //echo "<pre>";
            //print_r($datosusuarios);
            // echo "</pre>";
            if ($datosusuarios[0][0] == '2') {
                echo ($datosusuarios[0][1]);
            } else {
                echo ("Status: ->" . $datosusuarios[0][1]);
            }
        }
    }

    public function docemitidosAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_pcostas = $this->_request->getParam('idcostas');
            $this->view->codmdocum = $p_idsigma;
            $this->view->idsigma = $p_pcostas;
        }
    }

    /**FIN*/
    /**Consultas*/
    public function consultasAction()
    {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }
    /**FIN*/

    /**Reportes*/
    public function reportesAction()
    {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }

    /**FIN*/

    public function drutarecepdevolAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $ptype = $this->_request->getParam('ptype');
            $pcdruta = $this->_request->getParam('pcdruta');
            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.sp_druta_recepDevol_upd';
            $parameters[0] = $ptype;
            $parameters[1] = $pcdruta;
            $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($recordsresult);
        }
    }

    public function lstdocumentosAction()
    {
        $this->_helper->layout->disableLayout();

        $pmdocumento = $this->_request->getParam('pmdocumento');
        $cn = new Model_DataAdapter();
        $procedure = 'coactivo.sp_ArblHojaRuta_get_nivel2';
        $parameters[] = $pmdocumento;
        $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
        $this->view->jsonArbol = json_encode($recordsresult);
    }

    public function visordocsAction()
    {

        $img = $this->_request->getParam('img', '');
        $this->_helper->layout->disableLayout();
        $this->view->img = $img;
    }


    public function concluirexpAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pmruta = $this->_request->getParam('pmruta');
            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.concluirexp';
            $parameters[0] = $pmruta;
            $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($recordsresult);
        }
    }

    public function iframedocumentuploadAction()
    {
        $this->_helper->layout->disableLayout();
    }

    public function iframedocumentuploadreadyAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();


        //$temp = explode(".", $_FILES["file"]["name"]);
        //$extension = end($temp);
        /*
          if ($_FILES["file"]["error"] > 0)
          {
          echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
          }
          else
          {
          if (file_exists("uploadDdocuments/" . $_FILES["file"]["name"]))
          {
          echo $_FILES["file"]["name"] . " already exists. ";
          }
          else
          {
          move_uploaded_file($_FILES["file"]["tmp_name"],
          "uploadDdocuments/" . $_FILES["file"]["name"]);
          echo "uploadDdocuments/" . $_FILES["file"]["name"];
          }
          }
         */


        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $ddocumento = $ddatosuserlog->ddocumento;

        $name = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];
        //echo $name;

        if ($size == 0) {
            echo 'No selecciono ningun documento';
        }

        $nomadjunto = $ddocumento . "_" . $_FILES['file']['name'];
        $carpeta = "uploadDdocuments/";
        opendir($carpeta);
        $destino = $carpeta . $nomadjunto;
        copy($_FILES['file']['tmp_name'], $destino);
        //closedir($carpeta);


        $cn = new Model_DataAdapter();
        $procedure = 'coactivo.madjunto_ins';
        $parameters[0] = $ddocumento;
        $parameters[1] = $_FILES['file']['name'];
        $recordsAdjunto = $cn->executeAssocQuery($procedure, $parameters);

        echo "Guardado con exito";
    }

    /**Expediente coactivo */
    public function bandejadocsAction()
    {
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $opBusDefault = $this->_request->getParam('opbusdefault');
        $func = new Libreria_Pintar();
        $val[] = array('cboarea', $this->view->util()->getComboContenedorOtro('0000000001', $ddatosuserlog->areacod, 'coactivo.obtener_areas_coactivo'), 'html');
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
        $this->view->ntramite = $ddatosuserlog->ntramite;
        $this->view->opBusDefault = $opBusDefault;
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;
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
        }
        $func = new Libreria_Pintar();
        $cn = new Model_DataAdapter();

        $this->view->idsigma = $idsigma;
        $this->view->dstitulo = $titulo;
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->usuarioactual = $ddatosuserlog->cidusuario;

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
    }
}
