<?php

class DocumentosController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        
    }

   public function mrutadeleteAction() {
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
            echo $this->_helper->json($records[0]);
        }
    }

    public function docadjuntodeleteAction() {
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

    public function ddocumentodeleteAction() {
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

    public function ddocumentoAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('ajaxContext')->initContext();
            $this->_helper->layout->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();
        }


        $editable = $this->_request->getParam('editable');# 0 : No Editable / 1: Editable
        $cidsigma = $this->_request->getParam('cidsigma');
        $ctipjerar = $this->_request->getParam('ctipjerar');
        $mdocumento = $this->_request->getParam('mdocumento');
        
        $cn = new Model_DataAdapter();
         
        $procedure = 'coactivo.Mdocument_get';
        $parameters[0] =  $mdocumento;
        $recordsMdocument1 = $cn->executeAssocQuery($procedure, $parameters);
        $stconcluido = $recordsMdocument1[0]["stconcluido"];
        
        // todo : Revisar
        $dasunto = $this->_request->getParam('dasunto');
        if ((string) $dasunto !== '') {
            //$val[] = array('div_requisitos',$this->view->util()->getAsuntoRequisitos($mdocumento, $dasunto), 'html');
            //$val[] = array('div_requisitos',$dasunto.'a', 'html');
            $this->view->dasunto = (string) $dasunto;
        }
        if ($editable==''){
            $editable ='1';
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
        //$this->view->ctipjerar ="0000000113";
        //$this->view->cidsigma ="0000000001";
        $this->view->ctipjerar = $ctipjerar;
        $this->view->cidsigma = $cidsigma;
        $this->view->mdocumento = $mdocumento;
        $this->view->editable = $editable;
        
        $this->view->stconcluido = $stconcluido;//modificado 0206
        

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');

        $this->view->nvista = $ddatosuserlog->nvista;
        $this->view->cidusuario = '';
        if ($ddatosuserlog->nvista == '0') {
            $this->view->cidusuario = $ddatosuserlog->cidusuario;
        }


        
        $procedure2 = 'coactivo.mdocument_get';
        //$parameters2[0] = array("@idsigma","0000000001");
        $parameters2[0] =  $mdocumento;
        $recordsMdocument = $cn->executeAssocQuery($procedure2, $parameters2);

        $val[] = array('mtxtnroexpe', $recordsMdocument[0]["vnrodocu"], 'val');
        $val[] = array('mtxtrecurrente', $recordsMdocument[0]["dsperson"], 'val');
        $val[] = array('mtxtfecingreso', $recordsMdocument[0]["dfecdocu"], 'val');
        $val[] = array('mtxtfolios', $recordsMdocument[0]["nfolios"], 'val');
        $func = new Libreria_Pintar();

        $func->PintarValor($val);
    }

    public function ddocumentomanteAction() {
        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();

        $this->_helper->getHelper('ajaxContext')->initContext();
        $this->_helper->layout->disableLayout();

        $type = $this->_request->getParam('type');
        $mdocumento = $this->_request->getParam('mdocumento');
        $ddocument = $this->_request->getParam('ddocument');
        $cidsigma = $this->_request->getParam('cidsigma');
        $ctipjerar = $this->_request->getParam('ctipjerar');
        $ccate = $this->_request->getParam('ccate');

        // todo : Revisar
        $reqdoc = $this->_request->getParam('ctipdocreq');

        $func = new Libreria_Pintar();
        $cn = new Model_DataAdapter();

        $procedure2 = 'coactivo.mdocument_get';
        $parameters2[0] = $mdocumento;
        $recordsMdocument = $cn->executeAssocQuery($procedure2, $parameters2);

        $cbotipodoc = '9999999999';
        $txtobservacion = "";
        $txtvfolios = '0';//MODIFICADO 0106
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
            // todo : Revisar
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
            $txtvfolios=$recordsDdocument[0]["vfolios"];//MODIFICADO 0106
            $txtobservacion = $recordsDdocument[0]["vobserv"];
            $txtnrodoc = $recordsDdocument[0]["vnrodocu"];
            $txtfecingresodoc = $recordsDdocument[0]["dfecdocu"];
            $cborelevancia = $recordsDdocument[0]["ctiprele"];
        }


        $prmtrs[0] =  $ddatosuserlog->cidusuario;

        $rDocuUsuAct = $cn->ejec_store_procedura_sql('coactivo.sp_mDocuUsuActivo', $prmtrs);
        $func->ContenidoCombo((is_array($rDocuUsuAct) ? $rDocuUsuAct : array(array("", "Sin documentos agignados"))), $cbotipodoc, (is_array($rDocuUsuAct) ? '1' : '0'));

        //$val[] = array('cbotipodoc', $this->view->util()->getComboContenedorTramite('0000000001', $cbotipodoc), 'html');
        if ($ccate === '1') {
            $val[] = array('cbotipodoc', $func->ContenidoCombo((is_array($rDocuUsuAct) ? $rDocuUsuAct : array(array("9999999999", "Sin documentos agignados"))), $cbotipodoc, (is_array($rDocuUsuAct) ? '1' : '0')), 'html');
        } else {
            $val[] = array('cbotipodoc', $this->view->util()->getComboContenedorCoactivo('0000000001', $cbotipodoc), 'html');
        }

        $val[] = array('cborelevancia', $this->view->util()->getComboContenedorCoactivo('0000000006', $cborelevancia), 'html');
        $val[] = array('cbotipadjunto', $this->view->util()->getComboContenedorCoactivo('0000000012', '9999999999'), 'html');
        //$val[] = array('txtobservacion',$txtobservacion, 'val');
        $this->view->observ = $txtobservacion;
        $val[] = array('txtnrodoc', $txtnrodoc, 'val');
        $val[] = array('txtvfolios', $txtvfolios, 'val');//MODIFICADO 0106
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
        $evt[] = array("txtvfolios", "keypress", "return validarnumerossinespacios(event);");//modificado 0106
        $func->PintarEvento($evt);//modificado 0106
    }

    public function ddocumentomantesaveAction() {

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
            //print_r($detalleDdocument);

            $pchknrodoc = $this->_request->getParam('pchknrodoc');
            $pccate = $this->_request->getParam('ccate');
            $xmlDdocument = "";


            foreach ($detalleDdocument as $values) {
                $xmlDdocument .= '|';
                $xmlDdocument .= $values->idsigma . '|';
                $xmlDdocument .=  $values->mconfigdoc . '|';
                $xmlDdocument .=  $values->vdatoitem . '^';

               // $xmlDdocument .='<r ';
               // $xmlDdocument .=' xidsigma= "' . $values->idsigma . '" ';
                //$xmlDdocument .=' xmconfigdoc= "' . $values->mconfigdoc . '" ';
                //$xmlDdocument .=' xvdatoitem= "'.$values->vdatoitem.'" ';
               // $xmlDdocument .=' xvdatoitem= "' . str_replace(">", "~", str_replace("<", "^", $values->vdatoitem)) . '" ';
                //$xmlDdocument .='>';
               // $xmlDdocument .='</r>';
            }
           // $xmlDdocument = "<d>" . $xmlDdocument . "</d>";

            $xmlDdocument = substr($xmlDdocument, 0, strlen($xmlDdocument) - 1);


            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');


            $cn = new Model_DataAdapter();
            $procedure = ' coactivo.ddocumento_ins';
            $parameters[0] =  $type;
            $parameters[1] = $pidsigma;
            $parameters[2] =  $pctipdocu;
            $parameters[3] =  $pvnrodocu;
            $parameters[4] =  $pvfolios;//modificado 0106
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

    public function uploadddocumentsAction() {
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

    public function forzardescargaAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $carpeta = "uploadDdocuments/";
        $file = $carpeta . $_GET['file'];
        header("Content-disposition: attachment; filename=$file");
        header("Content-type: application/octet-stream");
        readfile($file);
    }

    public function bandejadocsAction() {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $opBusDefault = $this->_request->getParam('opbusdefault');
        $func = new Libreria_Pintar();
        $val[] = array('cboarea', $this->view->util()->getComboContenedorOtro('0000000001',$ddatosuserlog->areacod, 'coactivo.obtener_areas_coactivo'), 'html');
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
        $this->view->ntramite = $ddatosuserlog->ntramite;
        $this->view->opBusDefault = $opBusDefault;
        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;
    }

    public function rutarecepcionAction() {
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

    public function enviodocumentoAction() {
        $this->_helper->layout->disableLayout();
        $mdocumentos = $this->_request->getParam('mdocumentos');

        $func = new Libreria_Pintar();
        //$val[] = array('cboareadestino',$this->view->util()->getComboContenedor('0000000001','9999999999'), 'html');
        $val[] = array('cboacciondestino', $this->view->util()->getComboContenedorCoactivo('0000000007', '9999999999'), 'html');
        $val[] = array('hddselarrrow', $mdocumentos, 'val');

        //
        //@idsigma
        $cn = new Model_DataAdapter();
        $procedure = 'coactivo.obtener_areas_coactivo';
        $parameters[0] = '0000000001';
        $records = $cn->executeAssocQuery($procedure, $parameters);
        $this->view->arrareas = json_encode($records);

        $parameters[0] =  '0000000007';
        $dtaccdestino = $cn->executeAssocQuery("coactivo.obtener_tabla", $parameters);
        //print_r($dtaccdestino);

        $optionsjqcboaccion = '"9999999999" : "SELECCIONAR"';
        foreach ($dtaccdestino as $values) {
            $optionsjqcboaccion .=' , "' . $values["idsigma"] . '" : "' . $values["vdescri"] . '"';
        }
        $optionsjqcboaccion = '{' . $optionsjqcboaccion . '}';
        $this->view->optionsjqcboaccion = $optionsjqcboaccion;
        //echo $optionsjqcboaccion;
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
        if($mdocumento!=''){
            $procedurex = 'coactivo.mdocument_get';
            $parametersx[0] =  $mdocumento;
            $recordsMdocument = $cn->executeAssocQuery($procedurex, $parametersx);
            $ctiprele = $recordsMdocument[0]["ctiprele"];
            $nfolios=$recordsMdocument[0]["nfolios"];
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
                $cadena .=($cadena == '' ? '' : '<br>') . '<b>' . $values2["vnrodocu"] . '</b> : ';
                $xvnrodocu = $values2["vnrodocu"];

                
                //$mdocumento = $values2["mdocumento"];
                //$ctiprele = $values2["ctiprele"];
                //$nfolios = $values2["nfolios"];
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

    public function updaterelevanciadocAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $ctiprele = $this->_request->getParam('pctiprele');
            $pfolios = $this->_request->getParam('pfolios');
            $mdocumento = $this->_request->getParam('pmdocumento');

            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.mDocumentoUpdateRelevancia';
            $parameters[0] =  $ctiprele;
            $parameters[1] =  $mdocumento;
            $parameters[2] =  $pfolios;
            $records = $cn->executeAssocQuery($procedure, $parameters);

            echo json_encode($records);
        }
    }

    public function enviodocumentosaveAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {

            //$pccocsdes = $this->_request->getParam('pccocsdes');
            //$mdocumento = $this->_request->getParam('pmdocumento');
            //$ctiprele = $this->_request->getParam('pctiprele');
            //$pfolios = $this->_request->getParam('pfolios');
            
            $pareadestino = $this->_request->getParam('pareadestino');
            $pctipacc = $this->_request->getParam('pctipacc');
            $mdocumentos = $this->_request->getParam('pmdocumentos');
            $pvobserv = $this->_request->getParam('pvobserv');
            $ptodos = $this->_request->getParam('ptodos');

            $mdocumentosarr = json_decode($mdocumentos);
            $pareadestinoarr = json_decode($pareadestino);


            $cad = '';

            foreach ($mdocumentosarr as $values) {
                $cad .= '|';
                $cad .= $values->pidsigma . '|';
                $cad .= $values->pmdocumento . '|';
                $cad .= $values->pccocsini . '|';
                $cad .= $values->pmruta . '^';
            }
            $cad = substr($cad, 0, strlen($cad) - 1);
            $cadareades = "";
            foreach ($pareadestinoarr as $values) {
                $cadareades .= '|';
                $cadareades .=  $values->parea . '|';
                $cadareades .= ($ptodos == 'true' ? $pctipacc : $values->jqcboaccion )  . '|';
                $cadareades .= ($ptodos == 'true' ? $pvobserv : $values->vobserv) . '^';
               /* $xmlareades .=' xparea= "' . $values->parea . '" ';
                $xmlareades .=' xjqcboaccion= "' . ($ptodos == 'true' ? $pctipacc : $values->jqcboaccion ) . '" ';
                //$xmlareades .=' xvobserv= "'.str_replace(str_replace(($ptodos=='true'? $pvobserv:$values->vobserv), "<", "^"),">","~").'" ';
                $xmlareades .=' xvobserv= "' . 
                            str_replace(">", "~", 
                                str_replace("<", "^", 
                                        str_replace("&", "|", 
                                            ($ptodos == 'true' ? $pvobserv : $values->vobserv)
                                        )
                                    )
                                ) . '" ';
                //$xmlareades .=' xvobserv= "" ';
                $xmlareades .='>';
                $xmlareades .='</r>';*/
            }
            $cadareades = substr($cadareades, 0, strlen($cadareades) - 1);

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
            
            
            
            $cn = new Model_DataAdapter();
            

            $procedure = ' coactivo.mruta_insupd';
            $parameters[0] =  $cad;
            $parameters[1] =  $cadareades;
            $parameters[2] = '^';
            $parameters[3] = '|';
            $parameters[4] =  $ddatosuserlog->cidusuario;
            $records = $cn->executeAssocQuery($procedure, $parameters);

            /*foreach ($pareadestinoarr as $valuesArea) {
                foreach ($mdocumentosarr as $valuesDoc) {
                    $this->view->util()->sendMailArea(
                            $valuesArea->parea, 
                            $valuesDoc->pmdocumento, 
                                    ($ptodos == 'true' ? $pvobserv : $valuesArea->vobserv)
                    );
                }
            }*/

            echo json_encode($records);
        }
    }
    public function obsbigpanelAction(){
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
    public function hojarutaAction() {
        $this->_helper->layout->disableLayout();
        $mruta = $this->_request->getParam('mruta');
        $this->view->mruta = $mruta;
    }

    public function enviousuariodocumentoAction() {
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

    public function enviousuariodocumentosaveAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {

            $cidusuario = $this->_request->getParam('cidusuario');
            $pctipacc = $this->_request->getParam('pctipacc');
            $mdocumentos = $this->_request->getParam('pmdocumentos');
            $pvobserv = $this->_request->getParam('pvobserv');
            $mdocumentosarr = json_decode($mdocumentos);

            $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');


            $xml = "";

            $cadgrab = '';
            foreach ($mdocumentosarr as $values) {
                $cadgrab .= '|';
                $cadgrab .= $values->pidsigma  . '|';
                $cadgrab .= $values->pmdocumento. '|';
                $cadgrab .= $values->pccocsini . '^';
            }
            $cadgrab = substr($cadgrab, 0, strlen($cadgrab) - 1);

            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.druta_insupd';
            $parameters[0] = $cidusuario;
            $parameters[1] =  $pctipacc;
            $parameters[2] =  $pvobserv;
            $parameters[3] =  $cadgrab;
            $parameters[4] =  '^';
            $parameters[5] =  '|';
            $parameters[6] =  $ddatosuserlog->cidusuario;
            $records = $cn->executeAssocQuery($procedure, $parameters);

            /*foreach ($mdocumentosarr as $values) {
                $this->view->util()->sendMail_tramite(
                        $cidusuario, $values->pmdocumento, $pvobserv
                );
            }*/

            echo json_encode($records);
        }
    }

    public function docvencidosAction() {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidarea = $ddatosuserlog->cidarea;
    }

    public function panelobsenvioAction() {

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

    public function bandejadocsuserAction() {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }

    /**Consultas*/
    public function consultasAction() {

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
    public function reportesAction() {

        $ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
        $this->view->cidusuario = $ddatosuserlog->cidusuario;

        $func = new Libreria_Pintar();
        $getdate = date("d/m/Y");
        $val[] = array('fecdesde', $getdate, 'val');
        $val[] = array('fechasta', $getdate, 'val');
        $func->PintarValor($val);
    }
    /**FIN*/

    public function drutarecepdevolAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $ptype = $this->_request->getParam('ptype');
            $pcdruta = $this->_request->getParam('pcdruta');
            $cn = new Model_DataAdapter();
            $procedure = 'coactivo.sp_druta_recepDevol_upd';
            $parameters[0] =  $ptype;
            $parameters[1] =  $pcdruta;
            $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
            echo $this->_helper->json($recordsresult);
        }
    }

    public function lstdocumentosAction() {
        $this->_helper->layout->disableLayout();

        $pmdocumento = $this->_request->getParam('pmdocumento');
        $cn = new Model_DataAdapter();
        $procedure = 'coactivo.sp_ArblHojaRuta_get_nivel2';
        $parameters[] =  $pmdocumento;
        $recordsresult = $cn->executeAssocQuery($procedure, $parameters);
        $this->view->jsonArbol = json_encode($recordsresult);
    }

    public function visordocsAction() {

        $img = $this->_request->getParam('img', '');
        $this->_helper->layout->disableLayout();
        $this->view->img = $img;
    }

    public function cierreenvioAction() {
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
            // [coactivo].[mrutaRecep_upd]
            //@pmruta
        }
    }
    
    public function concluirexpAction() {
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
    
    public function iframedocumentuploadAction() {
        $this->_helper->layout->disableLayout();
    }

    public function iframedocumentuploadreadyAction() {
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
        $parameters[0] =  $ddocumento;
        $parameters[1] =  $_FILES['file']['name'];
        $recordsAdjunto = $cn->executeAssocQuery($procedure, $parameters);



        echo "Guardado con exito";
    }

    public function graficocajerosAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('ajaxContext')->initContext();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $url = $this->view->util()->getPath();
            // Graficas
            $vfecha1 = $this->_request->getPost('fecha1');
            $vfecha2 = $this->_request->getPost('fecha2');
            $store = 'tesoreria.obtener_montoxcajero';
            $pgraf[] = $vfecha1;
            $pgraf[] = $vfecha2;
            $cn = new Model_DataAdapter ();
            $datosgraf = $cn->ejec_store_procedura_sql($store, $pgraf);
            $cadgrafcategorias = '';
            $cadgrafcancelado = '';
            $cadgrafanulado = '';
            $cadgrafdatospie = '';
            $montorecaudado=0.00;
            $montoanulado=0.00;
            //print_r( $datosgraf);
            for ($i = 0; $i < count($datosgraf); $i++) {
                $cadgrafcategorias .="<category label='" . $datosgraf[$i][1] . "'/>";
                $cadgrafcancelado .="<set value='" . $datosgraf[$i][2] . "' />";
                $cadgrafanulado .="<set value='" . $datosgraf[$i][3] . "' />";
                $cadgrafdatospie .="<set label='" . $datosgraf[$i][1] . "' value='" . $datosgraf[$i][2] . "'/>";
                $montorecaudado+= $datosgraf[$i][2];
                $montoanulado+= $datosgraf[$i][3];
            }
            $montorecaudado	=	number_format($montorecaudado, 2, '.', ',');
            $montoanulado	=	number_format($montoanulado, 2, '.', ',');
            $htmltotales ="<b>Monto Recaudado  S/. $montorecaudado &nbsp;&nbsp;&nbsp;&nbsp; Monto Anulado  S/. $montoanulado<b>";
            if (count($datosgraf) > 0) {
                $cadgrafbarras = "<script >
				var chart = new FusionCharts('" . $url . "graf/MSColumn3D.swf', 'ChartbarrasId', '990', '500', '0', '0');
				chart.setDataXML(\"<chart palette='1' caption='Monto por cajeros' xAxisName='Cajero' yAxisName='Monto' numberPrefix='S/.' decimals='2'   formatNumberScale='0'    showValues='0'  rotateNames='1'  slantLabels='1' rotateValues='1' placeValuesInside='1' forceYAxisValueDecimals='1'  yAxisValueDecimals='2'>";
                $cadgrafbarras .= "<categories>" . $cadgrafcategorias . "</categories>";
                $cadgrafbarras .= "<dataset seriesname='Monto Cancelado' color='00CC33' showValues='1'>" . $cadgrafcancelado . " </dataset>";
                $cadgrafbarras .= "<dataset seriesname='Monto Anulado' color='F1C7D2'  showValues='1'> " . $cadgrafanulado . " </dataset>";

                $cadgrafbarras .= "</chart>\");
				chart.render(\"chartdivbarras\");
				</script> ";

                $cadgrafpie = "<script>
				var chartpie = new FusionCharts('" . $url . "graf/Pie3D.swf', 'ChartpieId', '690', '300', '0', '0');
				chartpie.setDataXML(\"<chart palette='1' caption='Monto por cajeros' numberPrefix='S/.' decimals='2' formatNumberScale='0'  enableSmartLabels='1' enableRotation='1' bgColor='99CCFF,FFFFFF' bgAlpha='40,100' bgRatio='0,100' bgAngle='360' showBorder='1' startingAngle='70'>" . $cadgrafdatospie . "</chart>\");
				chartpie.render(\"chartdivpie\");

				$('#totalrecaudado').html('".$htmltotales."');
				</script>";

                echo $cadgrafbarras . $cadgrafpie;

            }
        }
    }

}
