<?php

require_once 'Zend/Controller/Action.php';

class SistemaController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->util()->registerScriptJSController($this->getRequest());
        $map = new Zend_Session_Namespace("map");
        $map->data = false;
    }
    public function indexAction()
    {
        //  echo 'Sistema/Index';
    }
    public function usuarioAction()
    {
        $this->view->util()->registerScriptJSControllerAction($this->getRequest());
    }
    public function listardatosusuarioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ajaxContext')->initContext();
        $cidarea = '9999999999';
        $p_tipoUs = '9999999999';
        if ($this->getRequest()->isXmlHttpRequest()) {
            $pintar = new Libreria_Pintar();
            $cn = new Model_DataAdapter();

            $p_cidusuario = $this->_request->getPost('cidusuario');
            $cidarea = $this->_request->getPost('cidarea');
            $p_cidpersona = $this->_request->getPost('cidpers');
            $p_nombre = $this->_request->getPost('nombrepers');
            $p_tipoUs = $this->_request->getPost('tipou');
            $usuario = $this->_request->getPost('usuario');
            $clave = $this->_request->getPost('clave');
            $fechaini = $this->_request->getPost('fechaini');
            $fechafin = $this->_request->getPost('fechafin');
            $fechavenc = $this->_request->getPost('fechavenc');
            $nrocaja = $this->_request->getPost('nrocaja');
            $nestado = $this->_request->getPost('nestado');
            $opt = $this->_request->getPost('opt');

            $nombrestore = 'seguridad.buscar_area';
            $parametros[0] = ""; // $cidarea;
            $parametros[1] = "";
            $datosarea = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

            $datostipou = array(
                "1" => array("0" => "U", "1" => "U"),
                "2" => array("0" => "G", "1" => "G")
            );
            $this->view->opt = $opt;
            if ($p_cidusuario != '') {

                $this->view->cidusuario = $p_cidusuario;
                $this->view->mperson = $p_cidpersona;
                $this->view->vnombre = $p_nombre;
                $this->view->clave = $clave;
                $this->view->usuario = $usuario;
                $this->view->fechaini = $fechaini;
                $this->view->fechafin = $fechafin;
                $this->view->nrocaja = $nrocaja;
                $this->view->fechavenc = $fechavenc;
                $this->view->tipousu = $p_tipoUs;
                $this->view->nestado = $nestado;
            }

            $this->view->cidareaHTML = $pintar->ContenidoCombo($datosarea, $cidarea);
            $this->view->tipoUHTML = $pintar->ContenidoCombo($datostipou, $p_tipoUs);
        }
    }

    public function usuariosaveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pintar = new Libreria_Pintar();

            $cidusuario = $this->_request->getPost('cidusuari');
            $cidarea    = $this->_request->getPost('cidarea');
            $cidperson  = $this->_request->getPost('cidperson');
            $tipou      = $this->_request->getPost('tipou');
            $usuario    = $this->_request->getPost('usuari');
            $clave      = $this->_request->getPost('clave');
            $fechaini   = $this->_request->getPost('fechaini');
            $fechafin   = $this->_request->getPost('fechafin');
            $fechavenc  = $this->_request->getPost('fechavenc');
            $nrocaja    = $this->_request->getPost('nrocaja');
            $nestado    = $this->_request->getPost('nestado');
            if ($nestado == 1) {
                $nestado = 1;
            } else {
                $nestado = 0;
            }
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $userlogin = $ddatosuserlog->userlogin;
            $cn = new Model_DataAdapter();

            if ($fechavenc == '') {
                $fechavenc = "1900-01-01 00:00:00";
            }

            $nombrestore = 'seguridad.guardar_usuario';

            $parametros[0] = $cidusuario;
            $parametros[1] = $cidarea;
            $parametros[2] = $cidperson;
            $parametros[3] = $tipou;
            $parametros[4] = strtoupper($usuario);
            $parametros[5] = $clave;
            $parametros[6] = $fechaini;
            $parametros[7] = $fechafin;
            $parametros[8] = $fechavenc;
            $parametros[9] = "1900-01-01 00:00:00";
            $parametros[10] = $nrocaja;
            $parametros[11] = $nestado;
            $parametros[12] = $this->view->util()->getHost();
            $parametros[13] = $userlogin;

            $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

            echo $datos[0][1];
            if ($datos[0][0] == '1') {
            } else {
                header("Status: 400 Error al Guardar intentelo en otro momento o contacte al adminsitrador");
            }
        }
    }

    public function listardatospermisosusuAction()
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ajaxContext')->initContext();
        $cidarea = '9999999999';
        $p_tipoUs = '9999999999';
        if ($this->getRequest()->isXmlHttpRequest()) {
            $pintar = new Libreria_Pintar();
            $cn = new Model_DataAdapter();

            $p_cidusuario = $this->_request->getPost('cidusuario');
            $cidarea    = $this->_request->getPost('cidarea');
            $p_cidpersona = $this->_request->getPost('cidpers');
            $p_nombre   = $this->_request->getPost('nombrepers');
            $p_tipoUs   = $this->_request->getPost('tipou');
            $usuario    = $this->_request->getPost('usuario');
            $clave      = $this->_request->getPost('clave');
            $fechaini   = $this->_request->getPost('fechaini');
            $fechafin   = $this->_request->getPost('fechafin');
            $fechavenc  = $this->_request->getPost('fechavenc');
            $nrocaja    = $this->_request->getPost('nrocaja');
            $nestado    = $this->_request->getPost('nestado');

            $nombrestore = 'seguridad.buscar_area';
            $parametros[0] = "";
            $parametros[1] = "";
            $datosarea = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

            $datostipou = array(
                "1" => array("0" => "U", "1" => "U"),
                "2" => array("0" => "G", "1" => "G")
            );

            if ($p_cidusuario != '') {

                $this->view->cidusuario = $p_cidusuario;
                $this->view->mperson = $p_cidpersona;
                $this->view->vnombre = $p_nombre;
                $this->view->clave = $clave;
                $this->view->usuario = $usuario;
                $this->view->fechaini = $fechaini;
                $this->view->fechafin = $fechafin;
                $this->view->nrocaja = $nrocaja;
                $this->view->fechavenc = $fechavenc;
                $this->view->nestado = $nestado;
            }
            $this->pintararbolconten($p_cidusuario);
            //  $this->view->cidareaHTML =$pintar->ContenidoCombo($datosarea, $cidarea);
            // $this->view->tipoUHTML =$pintar->ContenidoCombo($datostipou, $p_tipoUs);
        }
    }

    public function usuariogruposaveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $usua = "";
            $grup = "";
            $cidus = $this->_request->getPost('idsigma');
            $cidgrupo = $this->_request->getPost('cusuario');
            $tipo = $this->_request->getPost('tipousu');
            $nestado = $this->_request->getPost('nestado');
            $opt = $this->_request->getPost('opt');
            $usorig = $this->_request->getPost('usorig');

            if ($nestado == 1) {
                $nestado = 1;
            } else {
                $nestado = 0;
            }
            if ($tipo == "G") {
                $usua = $cidgrupo;
                $grup = $cidus;
            }
            if ($tipo == "U") {
                $grup = $cidgrupo;
                $usua = $cidus;
            }


            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $userlogin = $ddatosuserlog->userlogin;
            $cn = new Model_DataAdapter();
            $nombrestore = 'seguridad.guardar_usuariogrupo';

            $parametros[0] = $usua;
            $parametros[1] = $grup;
            $parametros[2] = $nestado;
            $parametros[3] = $this->view->util()->getHost();
            $parametros[4] = $userlogin;
            $parametros[5] = $opt;
            $parametros[6] = $usorig;
            $parametros[7] = $tipo;

            $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

            echo $datos[0][1];
            if ($datos[0][0] == '1') {
            } else {
                header("Status: 400 Error al Guardar intentelo en otro momento o contacte al adminsitrador");
            }
        }
    }

    public function agregarusuariogrupoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $pintar = new Libreria_Pintar();
            $cn = new Model_DataAdapter();
            $p_idusuario = $this->_request->getPost('idusuario');
            $p_opt = $this->_request->getPost('opt');
            $p_tipo = $this->_request->getPost('tipousu');
            $cidgrupo = $this->_request->getPost('cidgrupo');
            $nestado = $this->_request->getPost('nestado');

            $this->view->opt = $p_opt;
            $this->view->tipo = $p_tipo;
            $this->view->nestado = $nestado;

            $parameters1[] = $p_tipo;
            $datoscajeros = $cn->ejec_store_procedura_sql("seguridad.buscar_usuariogrupocomb", $parameters1);

            $this->view->cusuarioHTML = $pintar->ContenidoCombo($datoscajeros, $p_idusuario);

            if ($p_tipo == 'G') {
                $this->view->cusuarioHTML = $pintar->ContenidoCombo($datoscajeros, $p_idusuario);
                $this->view->idusu = $cidgrupo;
                $this->view->usorig = $p_idusuario;
            }
            if ($p_tipo == 'U') {
                $this->view->cusuarioHTML = $pintar->ContenidoCombo($datoscajeros, $cidgrupo);
                $this->view->idusu = $p_idusuario;
                $this->view->usorig = $cidgrupo;
            }
        }
    }



    public function agregarobjetopermAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $pintar = new Libreria_Pintar();
            $cn = new Model_DataAdapter();
            $p_idusuario = $this->_request->getPost('idusuario');
            $p_opt      = $this->_request->getPost('opt');
            $p_tipo     = $this->_request->getPost('tipousu');
            $cidgrupo   = $this->_request->getPost('cidgrupo');
            $nestado    = $this->_request->getPost('nestado');

            $this->view->idusu = $p_idusuario;
            $this->view->opt = $p_opt;
            $this->view->tipo = $p_tipo;
            $this->view->nestado = $nestado;

            $parameters1[] = $p_tipo;
            $datoscajeros = $cn->ejec_store_procedura_sql("seguridad.buscar_usuariogrupocomb", $parameters1);

            $this->view->cusuarioHTML = $pintar->ContenidoCombo($datoscajeros, $p_idusuario);

            if ($p_tipo == 'G') {
                $this->view->cusuarioHTML = $pintar->ContenidoCombo($datoscajeros, $p_idusuario);
            }
            if ($p_tipo == 'U') {
                $this->view->cusuarioHTML = $pintar->ContenidoCombo($datoscajeros, $cidgrupo);
            }
        }
    }




    public function pintararbolconten($us)
    {
        $url = $this->view->util()->getPath();
        $func = new Libreria_Pintar();
        $mconten = new Zend_Session_Namespace('mcontendata');
        //echo $mconten->schemaid;
        //$this->view->schema = $mconten->schemaid;
        $nombrestore = 'seguridad.obt_objetos';  // $mconten->schemaid.'obt_mconten';
        $parametros[0] = '';
        $parametros[1] = '';
        $parametros[2] = $us;
        $parametros[3] = '0000000001';
        $parametros[4] = '';

        $cn = new Model_DataAdapter();
        $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

        $depth = 1;

        for ($i = 0; $i < count($datos); $i++) {
            $idsigma    = $datos[$i][0];
            $vdescri    = $datos[$i][1];
            $cidtabl    = $datos[$i][2];
            $hijos      = $datos[$i][3];
            $ctipdat    = $datos[$i][4];
            $vobser     = $datos[$i][5];
            $nlongit    = $datos[$i][6];
            $ndecima    = $datos[$i][7];
            $ndefault   = $datos[$i][8];
            $nestado    = $datos[$i][9];

            if ($idsigma == $cidtabl && $hijos > 0) {
                $arrayhijos = $this->buscarhijosmconten($idsigma, $depth, $datos);
                $arrayprinc[] = array('text' => $vdescri, 'id' => $idsigma, 'depth' => $depth, 'leaf' => false, 'checked' => true, 'children' => $arrayhijos, 'enlace' => $idsigma, 'canthijos' => $hijos, 'icon' => (($nestado == 1) ? '' : $url . 'css/images/drop-no.gif'));
            }
            if ($idsigma == $cidtabl && $hijos == 0) {
                $arrayprinc[] = array('text' => $vdescri, 'id' => $idsigma, 'depth' => $depth, 'leaf' => true, 'checked' => (($nestado == 1) ? true : false), 'children' => '', 'enlace' => $idsigma, 'canthijos' => $hijos, 'icon' => (($nestado == 1) ? '' : $url . 'css/images/drop-no.gif'));
            }
        }
        $arrayroot[0] = array('text' => 'OBJETOS', 'id' => '0000000000', 'depth' => '0', 'leaf' => false, 'checked' => true, 'children' => $arrayprinc);

        $cont = '<script type="text/javascript">

            Ext.BLANK_IMAGE_URL = "' . $url . 'css/images/s.gif";
            Ext.EventManager.onDocumentReady(function() {
            tree = new Ext.tree.TreePanel(\'tree-mconten\',{
            animate:true,
            loader: new Ext.tree.CustomUITreeLoader({baseAttr:{uiProvider: Ext.tree.CheckboxNodeUI}}),
          // loader: new Ext.tree.TreeLoader({}),
            enableDD:false,
            collapsible: true,
            animCollapse: false,
            containerScroll: true,
            rootUIProvider: Ext.tree.CheckboxNodeUI,
            selModel:new Ext.tree.CheckNodeMultiSelectionModel(),
            rootVisible:false
                   });

      /*  tree.on(\'check\', function() {
        //aki va el ajax para llenar los registros!!
        var registros = this.getChecked().join(\'^\');
        }, tree);*/

	    tree.on(\'check\', function() {
            //Ext.get(\'cn\').dom.value = this.getChecked().join(\',\');
            //aki va el ajax para llenar los registros!!
            var registros = this.getChecked().join(\'^\');
            // alert(registros);
          ColocarValorObjeto("registros",registros);
        }, tree);

        tree.on(\'click\', function(node, event) {
            //alert(node.attributes.id);
            $("#idObjPadre").val(node.attributes.id);
            buscarObjeto();
        }, tree);

        /*
        tree.on(\'click\', function(node, event){
        detallenodomcontenn(node.attributes.id,\'\');
        });*/

        // set the root node
       root = new Ext.tree.AsyncTreeNode({
        text: \'root\',
        draggable:false,
        id:\'source\',
       //uiProvider: Ext.tree.CheckboxNodeUI,
        children: ' . json_encode($arrayroot) . '
        });

        tree.setRootNode(root);

        // render the tree
        tree.render();
        root.expand(false, false, function() {
        root.firstChild.expand(true);
        });

        });
	</script>';
        echo $cont;
    }

    public function buscarhijosmconten($padre, $depth, $arraydatos)
    {
        $url = $this->view->util()->getPath();
        $depth++;
        for ($i = 0; $i < count($arraydatos); $i++) {
            $idsigma    = $arraydatos[$i][0];
            $vdescri    = $arraydatos[$i][1];
            $cidtabl    = $arraydatos[$i][2];
            $hijos      = $arraydatos[$i][3];
            $ctipdat    = $arraydatos[$i][4];
            $vobser     = $arraydatos[$i][5];
            $nlongit    = $arraydatos[$i][6];
            $ndecima    = $arraydatos[$i][7];
            $ndefault   = $arraydatos[$i][8];
            $nestado    = $arraydatos[$i][9];

            if ($idsigma != $cidtabl && $cidtabl == $padre) {
                if ($hijos == 0) {
                    $array[] = array('text' => $vdescri, 'id' => $idsigma, 'depth' => $depth, 'leaf' => true, 'checked' => (($nestado == 1) ? true : false), 'children' => '', 'enlace' => $idsigma, 'canthijos' => $hijos, 'icon' => (($nestado == 1) ? '' : $url . 'css/images/drop-no.gif'));
                } else {
                    $arrayhijos = $this->buscarhijosmconten($idsigma, $depth, $arraydatos);
                    $array[] = array('text' => $vdescri, 'id' => $idsigma, 'depth' => $depth, 'leaf' => false, 'checked' => (($nestado == 1) ? true : false), 'children' => $arrayhijos, 'enlace' => $idsigma, 'canthijos' => $hijos, 'icon' => (($nestado == 1) ? '' : $url . 'css/images/drop-no.gif'));
                }
            }
        }
        return $array;
    }

    public function guardarpermisosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $userlogin = $ddatosuserlog->userlogin;
            $host = $this->view->util()->getHost();
            $nombrestore = 'seguridad.guardar_permisos';
            $cn = new Model_DataAdapter();
            //print_r($host);

            $dobjeto = $this->_request->getPost('json');
            $datajson = json_decode($dobjeto);
            $data = $datajson->permisos;
            $cidperson = $datajson->usuario;
            $cantidad = count($data);

            $op = '1';
            $parametros[0] = $cidperson;
            $parametros[1] = $data[2];
            $parametros[2] = "1";
            $parametros[3] = $host;
            $parametros[4] = $userlogin;
            $parametros[5] = $op;
            $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

            $op = '2';
            for ($x = 2; $x < $cantidad; $x++) {
                $parametros[0] = $cidperson;
                $parametros[1] = $data[$x];
                $parametros[2] = "1";
                $parametros[3] = $host;
                $parametros[4] = $userlogin;
                $parametros[5] = $op;

                $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);
            }

            echo $datos[0][1];
            if ($datos[0][0] == '1') {
                //  echo '<script language=\"JavaScript\">window.open(\'' . $this->view->util()->getLink('sistema/mconten') . '\', \'_self\')</script>';
            } else {
                echo 'Error en el guardado...';
            }
        }
    }





    //objetos--mantenimiento

    public function objetosAction()
    {
        $this->pintararbolobjetos();
        $func = new Libreria_Pintar();
        $evt[] = array('btnnuevonodo', 'click', 'detallenodomconten(\'\',\'\')');
        echo "<script>pathReport='" . $this->view->util()->getPathReport() . "'</script>";
        $func->PintarEvento($evt);

        $mconten = new Zend_Session_Namespace('mcontendata');
        //echo $mconten->schemaid;
        $this->view->schema = $mconten->schemaid;
    }

    public function pintararbolobjetos()
    {
        $url = $this->view->util()->getPath();

        $mconten = new Zend_Session_Namespace('mcontendata');
        //echo $mconten->schemaid;
        //$this->view->schema = $mconten->schemaid;
        $nombrestore = 'seguridad.obt_objetosman';
        $parametros[0] = '';
        $parametros[1] = '';
        $parametros[2] = '0000000001';

        $cn = new Model_DataAdapter();
        $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

        $depth = 1;

        for ($i = 0; $i < count($datos); $i++) {
            $idsigma = $datos[$i][0];
            $vdescri = $datos[$i][1];
            $cidtabl = $datos[$i][2];
            $hijos = $datos[$i][3];
            $ctipdat = $datos[$i][4];
            $vobser = $datos[$i][5];
            $nlongit = $datos[$i][6];
            $ndecima = $datos[$i][7];
            $ndefault = $datos[$i][8];
            $nestado = $datos[$i][9];

            if ($idsigma == $cidtabl && $hijos > 0) {
                $arrayhijos = $this->buscarhijosmconten($idsigma, $depth, $datos);
                $arrayprinc[] = array('text' => $vdescri, 'id' => $idsigma, 'depth' => $depth, 'leaf' => false, 'checked' => false, 'children' => $arrayhijos, 'enlace' => $idsigma, 'canthijos' => $hijos, 'icon' => (($nestado == 1) ? '' : $url . 'css/images/drop-no.gif'));
            }
            if ($idsigma == $cidtabl && $hijos == 0) {
                $arrayprinc[] = array('text' => $vdescri, 'id' => $idsigma, 'depth' => $depth, 'leaf' => true, 'checked' => false, 'children' => '', 'enlace' => $idsigma, 'canthijos' => $hijos, 'icon' => (($nestado == 1) ? '' : $url . 'css/images/drop-no.gif'));
            }
        }
        $arrayroot[0] = array('text' => 'OBJETOS', 'id' => '0000000000', 'depth' => '0', 'leaf' => false, 'checked' => false, 'children' => $arrayprinc);

        $cont = '<script type="text/javascript">
            Ext.BLANK_IMAGE_URL = "' . $url . 'css/images/s.gif";
            Ext.EventManager.onDocumentReady(function() {
            tree = new Ext.tree.TreePanel(\'tree-mconten\',{
            animate:true,
            //loader: new Ext.tree.CustomUITreeLoader({baseAttr:{uiProvider: Ext.tree.CheckboxNodeUI}}),
            loader: new Ext.tree.TreeLoader({}),
            enableDD:false,
            collapsible : true,
            animCollapse: false,
            containerScroll: true,
            //rootUIProvider: Ext.tree.CheckboxNodeUI,
            //selModel:new Ext.tree.CheckNodeMultiSelectionModel(),
            rootVisible:false
        });

        tree.on(\'check\', function() {
        //aki va el ajax para llenar los registros!!
        var registros = this.getChecked().join(\'^\');
        }, tree);

        tree.on(\'click\', function(node, event){
        detallenodomconten(node.attributes.id,\'\');
        });

        // set the root node
        root = new Ext.tree.AsyncTreeNode({
        text: \'root\',
        draggable:false,
        id:\'source\',
        //uiProvider: Ext.tree.CheckboxNodeUI,
        children: ' . json_encode($arrayroot) . '
        });

        tree.setRootNode(root);

        // render the tree
        tree.render();
        root.expand(false, false, function() {
        root.firstChild.expand(false);
        });

        });
	</script>';
        echo $cont;
    }


    public function objetonodoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $idnodo = $this->_request->getPost('idnodo');
            $idpadre = $this->_request->getPost('idpadre');

            $func = new Libreria_Pintar();
            $cn = new Model_DataAdapter();

            $mconten = new Zend_Session_Namespace('mcontendata');
            $nombrestore = 'seguridad.obt_objetosman';

            $arrayestado = array(array('1', 'HABILITADO'), array('0', 'DESHABILITADO'));

            if ($idnodo == '' || $idnodo == null) {
                $parametros[0] = '';
                $parametros[1] = '';
                $parametros[2] = '0000000001';
                $datosmconten = $cn->ejec_store_procedura_sql($nombrestore, $parametros);
                // $val[] = array('cb_padre', $func->ContenidoCombo($datosmconten, ($idpadre == '') ? '' : $idpadre), 'html');
                // $val[] = array('cb_estado', $func->ContenidoCombo($arrayestado, '1'), 'html');
                $this->view->cbPadreHtml = $func->ContenidoCombo($datosmconten, ($idpadre == '') ? '' : $idpadre);
                $this->view->cbestadoHtml = $func->ContenidoCombo($arrayestado, '1');
            } else {
                $parametros1[0] = $idnodo;
                $parametros1[1] = '';
                $parametros1[2] = '0000000001';
                $datosnodo = $cn->ejec_store_procedura_sql($nombrestore, $parametros1);

                if (count($datosnodo) == 0) {
                    return;
                }

                $idsigma = $datosnodo[0][0];
                $vdescri = $datosnodo[0][1];
                $cidtabl = $datosnodo[0][2];
                $hijos = $datosnodo[0][3];
                $ctipdat = $datosnodo[0][4];
                $vobser = $datosnodo[0][5];
                $nlongit = $datosnodo[0][6];
                $ndecima = $datosnodo[0][7];
                $ndefault = $datosnodo[0][8];
                $nestado = $datosnodo[0][9];

                $val[] = array('c_codigo', $idsigma, 'html');
                $val[] = array('txt_descripcion', $vdescri, 'val');

                $parametros2[0] = '';
                $parametros2[1] = '';
                $parametros2[2] = '0000000001';
                $datosmconten = $cn->ejec_store_procedura_sql($nombrestore, $parametros2);
                // $val[] = array('cb_padre', $func->ContenidoCombo($datosmconten, $cidtabl), 'html');
                $this->view->cbPadreHtml = $func->ContenidoCombo($datosmconten, $cidtabl);
                $val[] = array('txa_observ', $vobser, 'val');
                $val[] = array('txt_long', $nlongit, 'val');
                $val[] = array('txt_decimal', $ndecima, 'val');
                $val[] = array('txt_defecto', $ndefault, 'val');
                // $val[] = array('cb_estado', $func->ContenidoCombo($arrayestado, $nestado), 'html');
                $this->view->cbestadoHtml = $func->ContenidoCombo($arrayestado, $nestado);

                $contenhijos = '';
                $tienehijos = 0;

                for ($i = 0; $i < count($datosmconten); $i++) {
                    $didsigma = $datosmconten[$i][0];
                    $dvdescri = str_replace(array("'", "/"), array("", ""), $datosmconten[$i][1]);
                    $dcidtabl = $datosmconten[$i][2];
                    $dhijos = $datosmconten[$i][3];
                    $dctipdat = $datosmconten[$i][4];
                    $dvobser = $datosmconten[$i][5];
                    $dnlongit = $datosmconten[$i][6];
                    $dndecima = $datosmconten[$i][7];
                    $dndefault = $datosmconten[$i][8];
                    $dnestado = $datosmconten[$i][9];

                    if ($dcidtabl == $idsigma && $didsigma != $idsigma) {
                        $tienehijos = 1;
                        $contenhijos .= '<tr><td style="width: 90px;">' . $didsigma
                            . '</td><td style="width: 246px">' . $dvdescri
                            . '</td><td style="width: 42px" align="center"><button onclick="detallenodomconten(\'' . $didsigma . '\',\'\');" >Buscar</button></td></tr>';
                    }
                }
                if ($tienehijos == 0) {
                    $contenhijos .= '<tr><td colspan="3">No tiene nodo hijos registrados</td></tr>';
                }

                $val[] = array('c_nodohijos', '<table id="tblDetalle" style="width: 372px; padding-left: 2px;" border="0" cellspacing="0" cellpadding="0"><tbody class="ui-table-body">' . $contenhijos . '</tbody></table>', 'html');
                $evt[] = array('btnaniadirnodo', 'click', 'detallenodomconten(\'\',\'' . $idsigma . '\');');
            }

            $evt[] = array('txa_observ', 'keypress', 'if(event.keyCode == 13){return false;}');
            $evt[] = array("txt_long", "keypress", "return validarnumeros(event);");
            //            $evt[] = array("txt_decimal", "keypress", "return validarnumeros(event);");

            $val[] = array('btnimprimir', $idnodo, 'val');

            //$fn[] = array("$('#bntguardarmcontennodo').unbind('click');");
            //$fn[] = array("$('#btnguardarmcontennodo').bind('click', guardarmcontennodo);");
            $fn[] = array("$('#tblDetalle button').button({icons: {primary: 'ui-icon-search'}, text: false});");
            $fn[] = array("mouseHover('tblDetalle');");
            $func = new Libreria_Pintar();
            $func->IniciaScript();
            $func->PintarValor($val);
            $func->PintarEvento($evt);
            $func->EjecutarFuncion($fn, "function");
            $func->FinScript();
        }
    }

    public function objetossaveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $cidobjeto = $this->_request->getPost('cidobjeto');
            $vobjeto = $this->_request->getPost('vobjeto');
            $cidtipo = $this->_request->getPost('cidtipo');
            $cidobjetopadre = $this->_request->getPost('cidobjetopadre');
            $accion = $this->_request->getPost('accion');
            $orden = $this->_request->getPost('orden');
            $objid = $this->_request->getPost('objid');
            $estado = $this->_request->getPost('estado');

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $cidpers = $ddatosuserlog->userlogin;
            //print_r($cidpers);
            $cn = new Model_DataAdapter();
            $nombrestore = 'seguridad.guardar_objetos';
            $parametros[0] = $cidobjeto;
            $parametros[1] = $vobjeto;
            $parametros[2] = "0000000001";
            $parametros[3] = $cidobjetopadre;
            $parametros[4] = $accion;
            $parametros[5] = $orden;
            $parametros[6] = $objid;
            $parametros[7] = $estado;
            $parametros[8] = $cidpers;
            $parametros[9] = $this->view->util()->getHost();;

            //echo "<pre>";
            //print_r($parametros);
            //echo "</pre>";
            $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);
            if ($datos[0][0] == '1') {
                echo $datos[0][1];
            } else {
                echo 'Error en el guardado...';
            }
        }
    }



    //botones
    public function listarbotonesobjAction()
    {
        $p_idsigma = $this->_request->getPost('idsigma');
        $this->view->idsigm = $p_idsigma;
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
    }

    public function listardatosbotonesmanAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $p_ctipdoc = 'SELECCIONE';
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            $pintar = new Libreria_Pintar();
            $cn = new Model_DataAdapter();
            $p_idsigma = $this->_request->getPost('idsigma');
            $p_padre = $this->_request->getPost('padre');

            if ($p_idsigma != '') {

                $p_vobjeto = $this->_request->getPost('vobjeto');
                $p_accion = $this->_request->getPost('accion');
                $p_orden = $this->_request->getPost('orden');
                $p_objid = $this->_request->getPost('objid');
                $p_nestado = $this->_request->getPost('nestado');
                //en update no se puede seleccionar otro usuario ni caja por que se esta consultando por filtro si se debe selecionar otra caja entonces se debe eliminar el filtro
                $this->view->idsigma = $p_idsigma;
                $this->view->vobjeto = $p_vobjeto;
                $this->view->accion = $p_accion;
                $this->view->orden = $p_orden;
                $this->view->objid = $p_objid;
                $this->view->nestado = $p_nestado;
            }
            $this->view->cidpadre = $p_padre;
            // echo "<pre>";
            // print_r( $datoscajeros);
            // print_r( $this->view->musuari);
            // echo "</pre>";
        }
    }

    public function botonesobjsaveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {

            $cidobjeto = $this->_request->getPost('cidobjeto');
            $vobjeto = $this->_request->getPost('vobjeto');
            //$cidtipo = $this->_request->getPost('cidtipo');
            $cidobjetopadre = $this->_request->getPost('cidobjetopadre');
            // $accion = $this->_request->getPost('accion');
            $orden = $this->_request->getPost('orden');
            $objid = $this->_request->getPost('txt_decimal');
            $estado = $this->_request->getPost('nestado');
            if ($estado == 1) {
                $estado = 1;
            } else {
                $estado = 0;
            }
            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $cidpers = $ddatosuserlog->userlogin;
            //print_r($cidpers);
            $cn = new Model_DataAdapter();
            $nombrestore = 'seguridad.guardar_objetos';
            $parametros[0] = $cidobjeto;
            $parametros[1] = $vobjeto;
            $parametros[2] = "0000000002";
            $parametros[3] = $cidobjetopadre;
            $parametros[4] = '';
            $parametros[5] = 0;
            $parametros[6] = $objid;
            $parametros[7] = $estado;
            $parametros[8] = $cidpers;
            $parametros[9] = $this->view->util()->getHost();;

            $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);

            if ($datos[0][0] == '1') {
                //header("Status: 400 Error al guardar".print_r($datos));
            } else {
                header("Status: 400 Error al Guardar intentelo en otro momento o contacte al adminsitrador");
                //header("Status: 400 $idsigma|$cnrocaja|$dlocal|$nestado|$oper|$id");
            }
        }
    }




    public function formobjetosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $cn = new Model_DataAdapter();

            $cidUsuario = $this->_request->getPost('cidUsuario');
            $idObjPadre = $this->_request->getPost('idObjPadre');

            $parameters[] = "";
            $parameters[] = "";
            $parameters[] = $cidUsuario;
            $parameters[] = "0000000002";
            $parameters[] = $idObjPadre;
            $datos = $cn->ejec_store_procedura_sql("seguridad.obt_objetos", $parameters);

            if (count($datos)) {
                $data = json_encode($datos);
            }

            echo $data;
        }
    }

    public function editobjetosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
        }
    }

    public function guardarpermisoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            $userlogin = $ddatosuserlog->userlogin;
            $host = $this->view->util()->getHost();
            $nombrestore = 'seguridad.guardar_permisos';
            $cn = new Model_DataAdapter();

            $cidUsuario = $this->_request->getPost('cidUsuario');
            $idObjeto = $this->_request->getPost('idObjeto');
            $estObjeto = $this->_request->getPost('estObjeto');

            $parametros[0] = $cidUsuario;
            $parametros[1] = $idObjeto;
            $parametros[2] = $estObjeto;
            $parametros[3] = $host;
            $parametros[4] = $userlogin;
            $parametros[5] = "2";

            $datos = $cn->ejec_store_procedura_sql($nombrestore, $parametros);
        }
    }
}
