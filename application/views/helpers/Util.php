<?php

class Zend_View_Helper_Util extends Zend_View_Helper_Abstract {

    private $widthLayout = 1100;
	private $widthLayoutIndex = 1200;
    private $heightLayout = 480;

    public function util() {
        return $this;
    }

    public function getTheme() {
        return "green_explora";  // black-tie hot-sneaks dark-hive le-frog green_explora blue_explora    dot-luv  excite-bike
    }

    public function getTitle() {
        return "Municipalidad Provincial de Huaral";
    }

    public function getEntidad() {
        return "Municipalidad de Huaral";
    }

    public function getRUC() {
        return "20131366702";
    }

    public function getAreaCobranza() {
        return "Sub Gerencia de Ejecutoria Coactiva";
    }

    public function getProtocol() {
        return "http://";
    }

    public function getHost() {
        // return str_replace(":" . $_SERVER["SERVER_PORT"], "", $_SERVER["HTTP_HOST"]);
        

		return $_SERVER["HTTP_HOST"];
    }
	
	public function getIp () {
		global $_SERVER;
			if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			else
				$ip = $_SERVER["REMOTE_ADDR"]; 

		
		$s_hxff =(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : null );
		$client_ip = $ip;
		//echo "FOR IP ".$s_hxff."<br>";
		//echo "CLIENT ".$client_ip."<br>";
		
		if ($s_hxff) {
			// los proxys van a?adiendo al final de esta cabecera
			// las direcciones ip que van "ocultando". Para localizar la ip real
			// del usuario se comienza a mirar por el principio hasta encontrar
			// una direcci?n ip que no sea del rango privado. En caso de no
			// encontrarse ninguna se toma como valor el REMOTE_ADDR
			//echo "ENTRE AQUI";
			
			$entries = split('[, ]', $s_hxff);
			//echo "ENTRIE ".var_dump($entries)."<br>";
			reset($entries);

			while (list(, $entry) = each($entries)) {
				$entry = trim($entry);
				//echo "ENTRIES 2 ".var_dump($entries)."<br>";
				if (preg_match('/^([0-9]+.[0-9]+.[0-9]+.[0-9]+)/', $entry, $client_ip)) {
					// http://www.faqs.org/rfcs/rfc1918.html
					$private_ip = array(
						'/^0./',
						'/^127.0.0.1/',
						'/^192.168..*/',
						'/^100.100..*/',
						'/^172.((1[6-9])|(2[0-9])|(3[0-1]))..*/',
						'/^10.*/'
					);
//
					//echo "<br>PRIVATE IP ".var_dump($private_ip)."<br>";
					$found_ip = preg_replace($private_ip, $entry, $client_ip);
					//echo "<br>PRIVATE IP LISTAAA ".$ip_list[1]."<br>";
					
					//echo "<br>FOUND ".var_dump($found_ip[1])."<br>";
					if ($client_ip != $found_ip[1]) {
						$client_ip = $found_ip[1];
						break;
					}
				}
			}
		}
	 	//echo "CLIENT FINAL ".$client_ip."<br>";
		return pg_escape_string($client_ip);
	}
	

    public function getPath() {
        $path = explode("/index.php", $_SERVER["PHP_SELF"]);
        return $this->getProtocol() . $this->getHost() . $path[0] . "/" . PATH;
    }

    public function getPath2() {
        $path = explode("/index.php", $_SERVER["PHP_SELF"]);
        return $this->getProtocol() . $this->getHost() . $path[0] . "/";
    }

    public function getPathReport() {
        return $this->getProtocol() . $this->getIp() . ':8080/titania_report/index.jsp?';
    }

    public function getLink($url) {
        return $this->getPath2() . "index.php/" . $url;
    }

    public function getImage($image) {
        return $this->getPath() . "img/$image";
    }

    public function getImageAdjunto($image) {
        return $this->getPath() . "uploadDdocuments/$image";
    }

	public function getCabecera($nompers) {
		if(trim($nompers != '' && $nompers != null)<>"")
		{
			$imgcabecera="barTitle";
			$this->widthLayout=1030;
		}
		else
			$imgcabecera="";
			
        return $imgcabecera;
    }
    public function getPhoto($image) {
        $file = getcwd() . "/fotos/" . $image . ".png";
        if (file_exists($file)) {
            $file = $this->getPath() . "fotos/$image.png";
        } else {
            $file = $this->getPath() . "fotos/0000000000.png";
        }
        return $file;
    }

    public function getPhotosType($image) {
        $file = getcwd() . "/fotos/" . $image;

        if (file_exists($file)) {
            $file = $this->getPath() . "fotos/$image";
        } else {
            $file = $this->getPath() . "fotos/0000000000.png";
        }
        return $file;
    }

    public function getScript($pathJS) {
        $script = "\n";
        $files = $this->readFile($pathJS, $this->getPath());
        foreach ($files as $value) {
            if (!is_array($value)) {
                $script .= "\t\t<script type='text/javascript' src='$value'></script>\n";
            }
        }
        return $script;
    }

    public function isMap() {
        $session = new Zend_Session_Namespace("map");

        if ($session->data) {
           /* $script = "<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>";
		   
		   */
            $script = "";
            $session->data = false;
        } else {
            $script = "";
        }

        return $script;
    }

    public function registerScriptJSController(Zend_Controller_Request_Abstract $request) {
        $controller = $request->getControllerName();
        $script = "\t\t<script type='text/javascript' src='" . $this->getPath() . "js/js_" . $controller . ".js'/></script>\n";
        $session = new Zend_Session_Namespace("scriptController");
        $session->data = $script;
        return $script;
    }

    public function registerScriptJSControllerAction(Zend_Controller_Request_Abstract $request) {
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $script = "\t\t<script type='text/javascript' src='" . $this->getPath() . "js/" . $controller . "/" . $action . ".js'/></script>\n";
        $session = new Zend_Session_Namespace("scriptControllerAction");
        $session->data = $script;
        return $script;
    }

    public function registerLeaveControllerAction(Zend_Controller_Request_Abstract $request) {
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $script = "\t\t<script type='text/javascript'/>var __rw = " . 'false' . ";</script>\n";
        $session = new Zend_Session_Namespace("leave");
        $session->data = $script;
        return $script;
    }

    public function getScriptJSController() {
        $session = new Zend_Session_Namespace("scriptController");
        $script = $session->data;
        $session->data = "";
        return $script;
    }

    public function getScriptJSControllerAction() {
        $session = new Zend_Session_Namespace("scriptControllerAction");
        $script = $session->data;
        $session->data = "";
        return $script;
    }

    public function getScriptLeave() {
        $session = new Zend_Session_Namespace("leave");
        $script = $session->data;
        $session->data = "";
        return $script;
    }

    public function getStyle() {
        $files = $this->readFile("css", $this->getPath());
        $script = $this->style($files);
        return $script;
    }

    public function getWidthLayout() {
        return $this->widthLayout . "px";
    }
	public function getWidthLayoutIndex() {
        return $this->widthLayoutIndex . "px";
    }

    public function getSubstractWidthLayout($size) {
        return ($this->widthLayout - $size) . "px";
    }

    public function getHeightLayout() {
        return $this->heightLayout;
    }

    public function getSubstractHeightLayout($size) {
        return ($this->heightLayout - $size) . "px";
    }

    public function getNow() {
        return date("Y-m-d H:i:s");
    }
    public function getNowDMA() {
        return date("Y-m-d");
    }
    public function getPeriodos() {
        $result = "<select id='cboPeriodo' style='width: 60px;'>";
        $year = (int) date("Y");
        for ($i = $year; $i >= 1996; $i--) {
            $result .= "\n\t<option value='$i'>$i</option>";
        }
        $result .= "\n</select>";

        return $result;
    }

    public function getPeriodosDeclaradoCheckBox($mhresum) {
        $parameters[] = $mhresum;
        $dataAdapter = new Model_DataAdapter();
        $rows = $dataAdapter->ejec_store_procedura_sql("pl_function.listar_periodoGIT", $parameters);
        $result = "<div id='dialogPeriodoCheckBox' style='width: 120px;'><table align='center' cellpadding='2' cellspacing='2'>";
        $anioact = date("Y");
        if (count($rows) > 0) {
            foreach ($rows as $k => $v) {
                $result .= "<tr><td><input type='checkbox' class='chkPnlPeriodo' ".($anioact==$v[0] ?"checked" : "")." value='" . $v[0]. "' /></td><td><b>" . $v[0] . "</b></td></tr>";
            }
        }
        $result .= "\n</table><br/><center><button id='btnDialogPeriodoCheckBoxAceptar'>Aceptar</button><button id='btnDialogPeriodoCheckBoxCancelar'>Cancelar</button></center></div>";

        return $result;
    }

    public function getHrAnteriorDeclaradoCheckBox($mperson,$usuarioLog) {
        $parameters[] = $mperson;
        $dataAdapter = new Model_DataAdapter();
        $rows = $dataAdapter->ejec_store_procedura_sql("pl_function.listar_hresum_anteriores", $parameters);
        $result = "<div id='dialogHrAnteriorCheckBox' style='width: 400px; height: 50px; overflow-y:scroll;'><table align='center' cellpadding='2' cellspacing='8'>";
        $anioact = date("Y");
        if (count($rows) > 0) {
            foreach ($rows as $k => $v) {
                $result .= "<tr>
                    <td><input type='radio' name='radioHr' class='rdPnlHrAnterior' ".($anioact==$v[0] ?"checked" : "")." value='" . $v[0]. "' /></td>
                        <td><b>" . $v[2] . "</b></td>
                        <td><b>" . $v[4] . "</b></td>
                        <td><b>" . $v[3] . "</b></td>
                            </tr>";
            }
        }
        $result .= "\n</table><br/><center><button id='btnDialogHrAnteriorCheckBoxAceptar'>Aceptar</button><button id='btnDialogHrAnteriorCheckBoxCancelar'>Cancelar</button></center>
            <input type='hidden' name='usualioLog' class='usualioLog' value='" .$usuarioLog. "' /></div>";

        return $result;
    }
    
    public function getPeriodosDeclarado($mhresum, $selected) {
        $parameters[] = $mhresum;
        $dataAdapter = new Model_DataAdapter();
        $rows = $dataAdapter->ejec_store_procedura_sql("pl_function.listar_periodoGIT", $parameters);
        $result = "<select id='cboPeriodo' style='width: 60px;'>";
        if (count($rows) > 0) {
            foreach ($rows as $k => $v) {
                if ($v[0] == $selected) {
                    $result .= "<option value=\"" . $v[0] . "\" selected=\"selected\">" . $v[0] . "</option>";
                } else {
                    $result .= "<option value=\"" . $v[0] . "\">" . $v[0] . "</option>";
                }
            }
        }
        $result .= "\n</select>";

        return $result;
    }

    public function getComboContenedor($idsigma, $selected) {
        $procedure = 'public.obtener_tabla';
        $parameters[0] = $idsigma;
        $dataAdapter = new Model_DataAdapter();
        $records = $dataAdapter->ejec_store_procedura_sql($procedure, $parameters);

        $library = new Libreria_Pintar();
        $html = $library->ContenidoCombo($records, $selected);

        return $html;
    }

    public function getComboContenedorOtro($idsigma, $selected, $procedure) {
        $parameters[0] = $idsigma;
        $dataAdapter = new Model_DataAdapter();
        $records = $dataAdapter->ejec_store_procedura_sql($procedure, $parameters);

        $library = new Libreria_Pintar();
        $html = $library->ContenidoCombo($records, $selected);
        return $html;
    }
    public function getComboContenedorCoactivo($idsigma, $selected) {
        $procedure = 'coactivo.obtener_tabla';
        $parameters[0] = $idsigma;
        $dataAdapter = new Model_DataAdapter();
        $records = $dataAdapter->ejec_store_procedura_sql($procedure, $parameters);
        $library = new Libreria_Pintar();
        $html = $library->ContenidoCombo($records, $selected);
       // echo "CONSULTA ".$selected."<br><br>";
        return $html;
    }
    function getRecursiveArraySearch($needle,$haystack) {
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && $this->getRecursiveArraySearch($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }


    private function style($files) {
        $script = "\n";
        foreach ($files as $value) {
            if (!is_array($value)) {
                $ext = explode(".", $value);
                if (strtolower($ext[count($ext) - 1]) == 'css') {
                    $script .= "\t<link href='$value' rel=\"stylesheet\" type=\"text/css\"/>\n";
                }
            } else {
                $script .= $this->style($value);
            }
        }
        return $script;
    }

    private function readFile($carpeta, $path) {
        $script = array();
        if (is_dir(PATH . $carpeta)) {
            if (($_carpeta = opendir(PATH . $carpeta))) {
                while (($archivo = readdir($_carpeta)) !== false) {
                    if (is_dir(PATH . $carpeta . "/" . $archivo) && $archivo != "." && $archivo != "..") {
                        $script[] = $this->readFile($carpeta . "/" . $archivo, $path);
                    } else {
                        if ($archivo != "." && $archivo != "..") {
                            $script[] = $path . $carpeta . "/" . $archivo;
                        }
                    }
                }
                closedir($_carpeta);
            }
        }

        sort($script);
        return $script;
    }

}
