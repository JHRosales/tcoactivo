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
        $titulo = 'Nuevo Expediente Coactivo';

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
            $this->view->dasunto_tipasunto = 'PROCESO COACTIVO';

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
    public function buscarpersonaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
        }
    }
    public function enviodocumentoAction()
    {
        $this->_helper->layout->disableLayout();
        $mdocumentos = $this->_request->getParam('mdocumentos');

        $func = new Libreria_Pintar();
        $val[] = array('cboacciondestino', $this->view->util()->getComboContenedorCoactivo('0000000007', '9999999999'), 'html');
        $val[] = array('hddselarrrow', $mdocumentos, 'val');
        //@idsigma
        $cn = new Model_DataAdapter();
        $procedure = 'coactivo.obtener_areas_coactivo';
        $parameters[0] = '0000000001';
        $records = $cn->executeAssocQuery($procedure, $parameters);
        $this->view->arrareas = json_encode($records);
        $parameters[0] =  '0000000007';
        $dtaccdestino = $cn->executeAssocQuery("coactivo.obtener_tabla", $parameters);
        $optionsjqcboaccion = '"9999999999" : "SELECCIONAR"';
        foreach ($dtaccdestino as $values) {
            $optionsjqcboaccion .= ' , "' . $values["idsigma"] . '" : "' . $values["vdescri"] . '"';
        }
        $optionsjqcboaccion = '{' . $optionsjqcboaccion . '}';
        $this->view->optionsjqcboaccion = $optionsjqcboaccion;
        $cadgrab = '';
        $qtDoc = 0;
        $mdocumento = '';
        foreach (json_decode($mdocumentos) as $values) {
            $qtDoc++;
            $cadgrab .= '|';
            $cadgrab .=  $values->pmdocumento . '|';
            $cadgrab .=  $values->pccocsini . '^';
            $mdocumento = $values->pmdocumento;
        }
        $cadgrab = substr($cadgrab, 0, strlen($cadgrab) - 1);

        $ctiprele = '0000000140';
        $nfolios = 0;
        if ($mdocumento != '') {
            $procedurex = 'coactivo.mdocument_get';
            $parametersx[0] =  $mdocumento;
            $recordsMdocument = $cn->executeAssocQuery($procedurex, $parametersx);
            $ctiprele = $recordsMdocument[0]["ctiprele"];
            $nfolios = $recordsMdocument[0]["nfolios"];
        }

        $parameters2[0] =  $cadgrab;
        $parameters2[1] = '^';
        $parameters2[2] = '|';
        $recordsareas = $cn->executeAssocQuery("coactivo.areasdestinoxdocumento", $parameters2);

        $xvnrodocu = '';
        $cadena = '';

        foreach ($recordsareas as $values2) {
            if ($xvnrodocu != $values2["vnrodocu"]) {
                $cadena = (strlen($cadena) > 0 ? substr($cadena, 0, strlen($cadena) - 2) : $cadena);
                $cadena .= ($cadena == '' ? '' : '<br>') . '<b>' . $values2["vnrodocu"] . '</b> : ';
                $xvnrodocu = $values2["vnrodocu"];
            }
            $cadena .= $values2["vcocsdes"] . ", ";
        }
        $this->view->qtDoc = $qtDoc;
        $this->view->ctiprele = $ctiprele;
        $this->view->nfolios = $nfolios;
        $this->view->mdocumento = $mdocumento;
        $cadena = substr($cadena, 0, strlen($cadena) - 2);
        $val[] = array('cadareas', $cadena, 'html');
        $func->PintarValor($val);
        echo '
		<script>
			themeComboBox("#cboareadestino");
			themeComboBox("#cboacciondestino");
		</script>';
    }
    public function enviousuariodocumentoAction()
    {
        $this->_helper->layout->disableLayout();
        $mdocumentos = $this->_request->getParam('mdocumentos');
        $cidarea = $this->_request->getParam('cidarea');
        $cn = new Model_DataAdapter();
        $mdocumentosarr = json_decode($mdocumentos);
        $parameters[0] = $cidarea;
        $parameters[1] = $mdocumentosarr[0]->pmdocumento;
        $records = $cn->executeAssocQuery("coactivo.sp_usuario_notdocument_get", $parameters);
        $combousu = array();

        foreach ($records as $values) {
            $combousu[] = array($values['cidusuario'], $values['vnombre']);
        }

        $func = new Libreria_Pintar();
        $val[] = array('cboacciondestino', $this->view->util()->getComboContenedorCoactivo('0000000007', '9999999999'), 'html');
        $val[] = array('cbousuario', $func->ContenidoCombo($combousu, "9999999999"), 'html');

        $val[] = array('hddselarrrow', $mdocumentos, 'val');

        $func->PintarValor($val);
        echo '
		<script>
		themeComboBox("#cbousuario");
		themeComboBox("#cboacciondestino");
		</script>';
    }
    public function hojarutaAction()
    {
        $this->_helper->layout->disableLayout();
        $mruta = $this->_request->getParam('mruta');
        $this->view->mruta = $mruta;
    }
    public function guardarexpedienteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->getRequest()->isXmlHttpRequest()) {
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
                echo json_encode($datos[0]);
            } else {
                echo json_encode(array("st" => "0", "msj" => 'Error en el guardado...'));
            }
        }
    }
    public function panelobsenvioAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $pctipjerar = $this->_request->getPost('pctipjerar');
            $pidsigma = $this->_request->getPost('pidsigma');

            // Obtener datos panel Expediente
            $cn = new Model_DataAdapter();
            $params[] =  $pctipjerar;
            $params[] =  $pidsigma;
            $panelobs = $cn->executeAssocQuery('coactivo.sp_obsenvio_get', $params);

            $this->view->ctipjerar = $pctipjerar;
            $this->view->idsigma = $pidsigma;
            $this->view->vobserv = $panelobs[0]['vobserv'];
            $this->view->vtipacc = $panelobs[0]['vtipacc'];
        }
    }
    public function obsbigpanelAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $idsigma = $this->_request->getPost('idsigma', '');
            $ctipjerar = $this->_request->getPost('ctipjerar', '');

            $cn = new Model_DataAdapter();
            $params[0] =  $ctipjerar;
            $params[1] =  $idsigma;
            $panelobs = $cn->executeAssocQuery('coactivo.sp_obsenvio_get', $params);

            $this->view->vobserv = $panelobs[0]['vobserv'];
        }
    }
    public function ddocumentoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();
        }

        $editable = $this->_request->getParam('editable'); # 0 : No Editable / 1: Editable
        $cidsigma = $this->_request->getParam('cidsigma');
        $ctipjerar = $this->_request->getParam('ctipjerar');
        $mdocumento = $this->_request->getParam('mdocumento');

        $cn = new Model_DataAdapter();

        $procedure = 'coactivo.Mdocument_get';
        $parameters[0] =  $mdocumento;
        $recordsMdocument1 = $cn->executeAssocQuery($procedure, $parameters);
        $stconcluido = $recordsMdocument1[0]["stconcluido"];

        $dasunto = $this->_request->getParam('dasunto');
        if ((string) $dasunto !== '') {
            $this->view->dasunto = (string) $dasunto;
        }
        if ($editable == '') {
            $editable = '1';
        }
        if ($cidsigma == '') {
            $cidsigma = "0000000001";
        }
        if ($ctipjerar == '') {
            $ctipjerar = "0000000113";
        }
        if ($mdocumento == '') {
            $mdocumento = '0000000001';
        }

        $this->view->ctipjerar = $ctipjerar;
        $this->view->cidsigma = $cidsigma;
        $this->view->mdocumento = $mdocumento;
        $this->view->editable = $editable;

        $this->view->stconcluido = $stconcluido;
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');

        $this->view->nvista = $ddatosuserlog->nvista;
        $this->view->cidusuario = '';
        if ($ddatosuserlog->nvista == '0') {
            $this->view->cidusuario = $ddatosuserlog->cidusuario;
        }

        $procedure2 = 'coactivo.mdocument_get';
        $parameters2[0] =  $mdocumento;
        $recordsMdocument = $cn->executeAssocQuery($procedure2, $parameters2);

        $val[] = array('mtxtnroexpe', $recordsMdocument[0]["vnrodocu"], 'val');
        $val[] = array('mtxtrecurrente', $recordsMdocument[0]["dsperson"], 'val');
        $val[] = array('mtxtfecingreso', $recordsMdocument[0]["dfecdocu"], 'val');
        $val[] = array('mtxtfolios', $recordsMdocument[0]["nfolios"], 'val');
        $func = new Libreria_Pintar();

        $func->PintarValor($val);
    }
    public function dasuntoreqAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $dasunto = $this->_request->getParam('dasunto');
            $mdocumento = $this->_request->getParam('mdocumento');
            if ((string) $dasunto !== '') {
                echo $this->view->util()->getAsuntoRequisitos($mdocumento, $dasunto);
            } else {
                echo '';
            }
        }
    }
    public function ddocumentomanteAction()
    {
        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();
        $type = $this->_request->getParam('type');
        $mdocumento = $this->_request->getParam('mdocumento');
        $ddocument = $this->_request->getParam('ddocument');
        $cidsigma = $this->_request->getParam('cidsigma');
        $ctipjerar = $this->_request->getParam('ctipjerar');
        $ccate = $this->_request->getParam('ccate');
        $reqdoc = $this->_request->getParam('ctipdocreq');

        $func = new Libreria_Pintar();
        $cn = new Model_DataAdapter();

        $procedure2 = 'coactivo.mdocument_get';
        $parameters2[0] = $mdocumento;
        $recordsMdocument = $cn->executeAssocQuery($procedure2, $parameters2);

        $cbotipodoc = '9999999999';
        $txtobservacion = "";
        $txtvfolios = '0';
        $txtnrodoc = '';
        $cborelevancia = '9999999999';
        $txtfecingresodoc = date("d/m/Y");

        $this->view->ddocumento = $ddocument;
        $this->view->cidsigma = $cidsigma;
        $this->view->ctipjerar = $ctipjerar;
        $this->view->ccate = $ccate;

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $ddatosuserlog->ddocumento = $ddocument;

        if ($type == 'N') {
            if ((string) $reqdoc !== '') {
                $cbotipodoc = (string) $reqdoc;
            }
            $cborelevancia = '0000000140';
        } elseif ($type == 'M' || $type == 'C') {
            $procedure = 'coactivo.ddocument_get';
            $parameters[0] =  '';
            $parameters[1] =  '';
            $parameters[2] =  $ddocument;
            $parameters[3] =  '';
            $recordsDdocument = $cn->executeAssocQuery($procedure, $parameters);
            $cbotipodoc = $recordsDdocument[0]["ctipdocu"];
            $txtvfolios = $recordsDdocument[0]["vfolios"];
            $txtobservacion = $recordsDdocument[0]["vobserv"];
            $txtnrodoc = $recordsDdocument[0]["vnrodocu"];
            $txtfecingresodoc = $recordsDdocument[0]["dfecdocu"];
            $cborelevancia = $recordsDdocument[0]["ctiprele"];
        }


        $prmtrs[0] =  $ddatosuserlog->cidusuario;
        $rDocuUsuAct = $cn->ejec_store_procedura_sql('coactivo.sp_mDocuUsuActivo', $prmtrs);
        $func->ContenidoCombo((is_array($rDocuUsuAct) ? $rDocuUsuAct : array(array("", "Sin documentos agignados"))), $cbotipodoc, (is_array($rDocuUsuAct) ? '1' : '0'));

        if ($ccate === '1') {
            $val[] = array('cbotipodoc', $func->ContenidoCombo((is_array($rDocuUsuAct) ? $rDocuUsuAct : array(array("9999999999", "Sin documentos agignados"))), $cbotipodoc, (is_array($rDocuUsuAct) ? '1' : '0')), 'html');
        } else {
            $val[] = array('cbotipodoc', $this->view->util()->getComboContenedorCoactivo('0000000001', $cbotipodoc), 'html');
        }

        $val[] = array('cborelevancia', $this->view->util()->getComboContenedorCoactivo('0000000006', $cborelevancia), 'html');
        $val[] = array('cbotipadjunto', $this->view->util()->getComboContenedorCoactivo('0000000012', '9999999999'), 'html');
        $this->view->observ = $txtobservacion;
        $val[] = array('txtnrodoc', $txtnrodoc, 'val');
        $val[] = array('txtvfolios', $txtvfolios, 'val');
        $val[] = array('txtnroexpe', $recordsMdocument[0]["vnrodocu"], 'val');
        $val[] = array('txtrecurrente', $recordsMdocument[0]["dsperson"], 'val');
        $val[] = array('txtfecingreso', $recordsMdocument[0]["dfecdocu"], 'val');
        $val[] = array('txtfolios', $recordsMdocument[0]["nfolios"], 'val');
        $val[] = array('txtfecingresodoc', $txtfecingresodoc, 'val');


        $func->PintarValor($val);

        echo '
            <script>
		themeComboBox("#cbotipodoc");
		themeComboBox("#cborelevancia");
		themeComboBox("#cbotipadjunto");
			
			 $("#txttipodoc").autocomplete({
                select: function(event, ui) {
                    optionvalue = $(ui.item.option).val()
                    $("#cbotipodoc").val(optionvalue);
                     ProcesoBuscarDocdet(optionvalue);

                }
            });
 		ProcesoBuscarDocdet("' . (string) $reqdoc . '");
		</script>';
        $evt[] = array("txtvfolios", "keypress", "return validarnumerossinespacios(event);");
        $func->PintarEvento($evt);
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

    public function docadjuntodeleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pmadjunto = $this->_request->getPost('pmadjunto');

            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.sp_madjunto_delete';
            $parameters[0] =  $pmadjunto;
            $records = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($records[0]);
        }
    }
    public function ddocumentodeleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pddocumento = $this->_request->getPost('pddocumento');

            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.sp_ddocumento_delete';
            $parameters[0] =  $pddocumento;
            $records = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($records[0]);
        }
    }
    public function visordocsAction()
    {

        $img = $this->_request->getParam('img', '');
        $this->_helper->layout->disableLayout();
        $this->view->img = $img;
    }
    public function rutarecepcionAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pmruta = $this->_request->getParam('pmruta');
            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.mrutaRecep_upd';
            $parameters[0] =  $pmruta;
            $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($recordsresult);
        }
    }
    public function mrutadeleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pmruta = $this->_request->getPost('pmruta');

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $userlogin =  $ddatosuserlog->cidusuario;
            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.sp_mrutaDelete';
            $parameters[0] =  $pmruta;
            $parameters[1] =  $userlogin;
            $records = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($records);
        }
    }

    public function cierreenvioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pmruta = $this->_request->getParam('pmruta');
            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.mrutaCierreEnv_upd';
            $parameters[0] = $pmruta;
            $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($recordsresult);
        }
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
    public function uploadddocumentsAction()
    {
        //$this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender();
        //$this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $ddocumento = $ddatosuserlog->ddocumento;

            $name = $_FILES['qqfile']['name'];
            $size = $_FILES['qqfile']['size'];
            //echo $name; 

            if ($size == 0) {
                return array('error' => 'File is empty.');
            }

            $nomadjunto = $ddocumento . "_" . $_FILES['qqfile']['name'];
            $carpeta = "uploadDdocuments/";
            opendir($carpeta);
            $destino = $carpeta . $nomadjunto;
            copy($_FILES['qqfile']['tmp_name'], $destino);
            //closedir($carpeta);


            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.madjunto_ins';
            $parameters[0] =  $ddocumento;
            $parameters[1] =  $_FILES['qqfile']['name'];
            $recordsAdjunto = $cn->executeAssocQuery($procedure, $parameters);



            echo $this->_helper->json(array('success' => true));
        }
    }
    public function ddocumentomantesaveAction()
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $type = $this->_request->getParam('type');
            $detalle = $this->_request->getParam('detalle');
            $pidsigma = $this->_request->getParam('pidsigma');
            $pctipdocu = $this->_request->getParam('pctipdocu');
            $pvnrodocu = $this->_request->getParam('pvnrodocu');
            $pvfolios = $this->_request->getParam('pvfolios');
            $pvobserv = $this->_request->getParam('pvobserv');
            $pctiprele = $this->_request->getParam('pctiprele');
            $pctipjerar = $this->_request->getParam('pctipjerar');
            $pcidsigma = $this->_request->getParam('pcidsigma');
            $pfecingresodoc = $this->_request->getParam('pfecingresodoc');
            $detalleDdocument = json_decode($detalle);
            $pchknrodoc = $this->_request->getParam('pchknrodoc');
            $pccate = $this->_request->getParam('ccate');
            $xmlDdocument = "";

            foreach ($detalleDdocument as $values) {
                $xmlDdocument .= '|';
                $xmlDdocument .= $values->idsigma . '|';
                $xmlDdocument .=  $values->mconfigdoc . '|';
                $xmlDdocument .=  $values->vdatoitem . '^';
            }

            $xmlDdocument = substr($xmlDdocument, 0, strlen($xmlDdocument) - 1);
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $cn = new Model_DataAdapter();
            $procedure = ' coactivo.ddocumento_ins';
            $parameters[0] =  $type;
            $parameters[1] = $pidsigma;
            $parameters[2] =  $pctipdocu;
            $parameters[3] =  $pvnrodocu;
            $parameters[4] =  $pvfolios;
            $parameters[5] = $pvobserv;
            $parameters[6] =  $pctiprele;
            $parameters[7] =  $pctipjerar;
            $parameters[8] =  $pcidsigma;
            $parameters[9] = $xmlDdocument;
            $parameters[10] =  $pchknrodoc;
            $parameters[11] =  $ddatosuserlog->cidusuario;
            $parameters[12] =  $pccate;
            $parameters[13] =  $pfecingresodoc;
            $parameters[14] = '^';
            $parameters[15] = '|';
            $recordsDdocument = $cn->executeAssocQuery($procedure, $parameters);

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $ddatosuserlog->ddocumento = $recordsDdocument[0]["ddocument"];
            echo json_encode($recordsDdocument);
        }
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
        $this->view->cidarea = $cidarea; //Cambiar con el area del Usuario
        $this->view->vestado = $vestado;
        $this->view->vnrodoc = $vnrodoc;
    }
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
}
