<?php

class LogeoController extends Zend_Controller_Action implements Zend_Auth_Adapter_Interface {
	public $flag = false;
	
	public function init() {
		$this->view->util()->registerScriptJSController($this->getRequest());
		$this->view->util()->registerLeaveControllerAction($this->getRequest());
	}
	
    public function indexAction(){
		$url = $this->view->util()->getPath();
		
		$evt[0] = array("txtuser","keypress","if(event.keyCode == 13){ValidarLogeo();}");
		$evt[1] = array("txtpass","keypress","if(event.keyCode == 13){ValidarLogeo();}");
		$evt[2] = array("btningreso","click","ValidarLogeo();");
		
		$att[0] = array('logo','src',$url.'img/logomuni.png');

		$func = new Libreria_Pintar();
		$func->IniciaScript();
		$func->PintarEvento($evt);
		$func->AtributoComponente($att);
		$func->FinScript();
    }
   
   	
	
    public function redirectAction() {
    }
    
	public function validarlogeoAction() {   
		$this->_helper->viewRenderer->setNoRender(); 
		$this->_helper->layout->disableLayout();
		$this->_helper->getHelper('ajaxContext')->initContext();
		
		if ($this->getRequest()->isXmlHttpRequest()) {
			
			$url = $this->view->util()->getPath();
						
			$user = trim($this->_request->getPost('user'));
			$pass = trim($this->_request->getPost('pass'));
			$local = trim($this->_request->getPost('cboLocal'));
			$nivel = $this->_request->getPost('nivel'); // Aun no se usa!! pero igual lo pongo xD!

			
			if($user == '' || $user == null || $pass == '' || $pass == null){
				echo 'Ingresar Usuario y/o Contrase&ntilde;a';
			}else{
				$nombrestore = 'seguridad.login';
				$arraydatos[]= $user;
				$arraydatos[]= $pass;
				
				$cn = new Model_DataAdapter();
				$datos = $cn->ejec_store_procedura_sql($nombrestore,$arraydatos);

				$caddatos = "";
				
				if($datos == '' || $datos == null || count($datos)<=0){
					echo '<font color="#FF0000">Datos Incorrectos...</font>';
					$this->flag = false;
				}
				else{
//					if($datos[0][7] >= $dias[0][2]){
//						echo '<font color="#FF0000">Usuario bloqueado por superar el limite de inactividad <br>... Comuniquese con la S/G de Informatica</font>';
//					}else
	
					if ($datos[0][5] == '1'){						
						$ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
						$ddatosuserlog->cidpers = $datos[0][0];
						$ddatosuserlog->cidusuario= $datos[0][0];
						$ddatosuserlog->nompers = $datos[0][1];
						$ddatosuserlog->userlogin = $datos[0][2];
						$ddatosuserlog->arealogin = $datos[0][3];
						$ddatosuserlog->codcajero = $datos[0][4];
						$ddatosuserlog->vhostnm = $this->view->util()->getIp(); //$_SERVER['SERVER_ADDR']; //'TEST';
						$ddatosuserlog->idlocal = $local;
						$ddatosuserlog->cidapertura = $datos[0][6];
						$ddatosuserlog->consult = $datos[0][7];
						$ddatosuserlog->passwd = $datos[0][8];
                        $ddatosuserlog->areacod = $datos[0][9];
						$ddatosuserlog->mperson = $datos[0][10];
						//echo $ddatosuserlog->vhostnm;
						//die();
						echo '<script language=\"JavaScript\">window.open(\''.$url.'index.php\', \'_self\')</script>';
					}else{
						
						echo '<font color="#FF0000">Usuario Inactivo...</font>';
					}
					$this->flag = true;
				}
				
				$auth = Zend_Auth::getInstance();
				$auth->authenticate($this);
			}
		}
	}

	public function logoutAction() {
		$url = $this->view->util()->getPath();
		Zend_Session::destroy();
		$this->_redirect($url.'index.php/');
	}
	
	public function authenticate()  {
		if ($this->flag) {
			$user = new Zend_Session_Namespace('datosuserlog');
			$result = new Zend_Auth_Result ( Zend_Auth_Result::SUCCESS, explode('|', $user->data), array ("Ok" ) );
		} else {
			$result = new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array ("Error" ) );
		}
		
		return $result;
	}
	
	public function cambiarpasswdAction() {
		$this->_helper->layout->disableLayout();
		echo $this->view->util()->registerScriptJSController($this->getRequest());
		$ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
		//echo $ddatosuserlog->cidusuario ;
		//echo $ddatosuserlog->nompers ;
		//echo $ddatosuserlog->userlogin ;
		//echo $ddatosuserlog->arealogin;
		
		
			
		$val[] = array("hdiduser",$ddatosuserlog->cidusuario,"val");
		$val[] = array("hdlogin",$ddatosuserlog->userlogin ,"val");
		$val[] = array("passwdant","","val");
		$evt[] = array("btnguardar","click","acccambiarpasswd();");
		$evt[] = array("btnsalir","click","closeDialog('jqDialog1');");
		//$fnc[] = array("themeComboBox();");
		
		$func = new Libreria_Pintar();
		$func->IniciaScript();
		$func->PintarValor($val);
		$func->PintarEvento($evt);
		//$func->EjecutarFuncion($fnc);
		$func->FinScript();
		
	}
	public function acccambiarpasswdAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$this->_helper->getHelper('ajaxContext')->initContext();
		if ($this->getRequest()->isXmlHttpRequest()) {
		
			$pass = trim($this->_request->getPost('passwd'));
			$passwdant = trim($this->_request->getPost('passwdant'));
			
			$ddatosuserlog = new Zend_Session_Namespace('datosuserlog');
			
			
			$nombrestore = 'seguridad.cambiarpasswd';
			$arraydatos[]= $ddatosuserlog->cidusuario;
			//$arraydatos[]= MD5($pass);
            $arraydatos[]= $pass;
			$arraydatos[]= $ddatosuserlog->userlogin;
			$arraydatos[]= $passwdant;
			
			
			$cn = new Model_DataAdapter();
			$datos = $cn->ejec_store_procedura_sql($nombrestore,$arraydatos);
			if($datos[0][0]=='1'){
				$ddatosuserlog->passwd=$datos[0][6];
			}
			echo json_encode($datos);
			
			
		}
	}	
	
	
}

