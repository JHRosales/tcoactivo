<?php
define('FPDF_FONTPATH','font/');
require_once('fpdf.php');
require_once('gisp_admincon.php');
require_once('qrcode.class.php');
require_once("Connections/coneccionReporte.php");
require_once("Connections/funciones_pg.php");
//include_once('rotation.php');

class PDF extends FPDF{

var $angle=0;
var $javascript;
var $n_js;

	function IncludeJS($script) {
		$this->javascript=$script;
	}

	function _putjavascript() {
		$this->_newobj();
		$this->n_js=$this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
		$this->_out('>>');
		$this->_out('endobj');
		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS '.$this->_textstring($this->javascript));
		$this->_out('>>');
		$this->_out('endobj');
	}


function AutoPrint($dialog=false)
{
	//Open the print dialog or start printing immediately on the standard printer
	$param=($dialog ? 'true' : 'false');
	$script="print($param);";
	$this->IncludeJS($script);
}

function AutoPrintToPrinter($server, $printer, $dialog=false)
{
	//Print on a shared printer (requires at least Acrobat 6)
	$script = "var pp = getPrintParams();";
	if($dialog)
		$script .= "pp.interactive = pp.constants.interactionLevel.full;";
	else
		$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
	$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
	$script .= "print(pp);";
	$this->IncludeJS($script);
}


	function _putresources() {
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}

	function _putcatalog() {
		parent::_putcatalog();
		if (!empty($this->javascript)) {
			$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
		}
	}
	

function Rotate($angle,$x=-1,$y=-1)
{
	if($x==-1)
		$x=$this->x;
	if($y==-1)
		$y=$this->y;
	if($this->angle!=0)
		$this->_out('Q');
	$this->angle=$angle;
	if($angle!=0)
	{
		$angle*=M_PI/180;
		$c=cos($angle);
		$s=sin($angle);
		$cx=$x*$this->k;
		$cy=($this->h-$y)*$this->k;
		$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
	}
}

function _endpage()
{
	if($this->angle!=0)
	{
		$this->angle=0;
		$this->_out('Q');
	}
	parent::_endpage();
}

function RotatedText($x,$y,$txt,$angle)
{
    //Text rotated around its origin
    $this->Rotate($angle,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
}

function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
	
	function Header(){
		//datos SQL
		global $login;
		global $centro_costo;
		global $nom_oficina;
		global $cod_actproy;
		global $des_actproy;
		global $cod_nemonico;
		global $des_nemonico;
		global $title;
		global $nro_nec;
		global $ano_eje;
		global $fch_nec;
		global $tipo_bien;
		global $reqs_anuales;			
		global $topIni;
		global $marIni;
		global $wcel;
		global $empresaM;
		global $distrito;
		global $departamento;
		global $provincia;
		global $logoCabIzq;
		global $logoSupP1;
		global $logoSupP2;
		global $logoSupP3;
		global $logoSupP4;
		global $marcaAguaP1;
		global $marcaAguaL1;
		global $marcaAguaL2;
		global $marcaAguaL3;
		global $marcaAguaL4;global $marcaAguaP2;
		global $marcaAguaP3;
		global $marcaAguaP4;
		global $marcaDeAgua;
		global $repositorioFirmas;
		global $RUC;
		global $numero_recibo;
		global $codigoContrib;
		global $nombreContrib;
		global $nroDoc;
		global $direccionContrib;
		global $cajero;
		global $fechaEmision;
		global $fechaImpresion;
		global $fechaProyectado1;
		global $err;
		global $fechaRecargos;
		global $usuario;
		global $colCab;
		global $colSubCab;
                global $vadicon;
		
		
		$tamInfoCuadro = -16;
                $posInfoCuadro = 26;
                $tamInfoCuadro2 = -9;
                $posInfoCuadro2 = 9;
                $tamcabecera=30;
                
		$lw = $marIni;
		$ln = $topIni;
		$ls = 3;
		$lh = 0;
		//MArca de Agua
		$this->Image('../img/marca/escudomarcaagua4.png',$marcaAguaL1-20,$marcaAguaL2,$marcaAguaL3+50,$marcaAguaL4+50,'PNG');
		
		//Logo
		$this->SetFont('arial','B',9);
		//$this->SetFillColor(206,232,212); //verde agua
		$this->SetFillColor(188,217,190); //verde agua
		$this->SetXY($lw-10,$lh);
		$this->Cell(220,8,"",0,1,'L',true);
		$this->SetFillColor(243);
		$this->SetFont('arial','B',9);
		$this->SetXY($lw-10,$lh);
		$this->MultiCell(50,8,'Calle 9 de Junio Nro 100',1,'C');
		$this->SetXY($lw+40,$lh);
		$this->MultiCell(50,8,'Huaral - Lima - Peru',1,'C');
		$this->SetXY($lw+90,$lh);
		$this->MultiCell(50,8,'www.munihuaral.gob.pe',1,'C');
		$this->SetXY($lw+140,$lh);
		$this->MultiCell(60,8,'Telefono 246 3617 - 219 6201',1,'C');
		//$this->RoundedRect(-10, 0, 220, 8, 1,'S');
		$this->Image('../img/marca/munipuentepiedra.png',$lw,$ln,62,18,'PNG');
		
		//$this->SetFillColor(145,185,154); //verde agua
		$this->SetFillColor(255);
		$lh = $lh+9;
		$this->SetXY($lw+70,$lh);
		$this->Cell(160,12,"",0,1,'L',true);
		$this->SetFont('arial','B',14);
		$this->SetXY($lw+60,$lh);
		$this->MultiCell(140,10,'Estado de Cuenta Corriente del Contribuyente',0,'C');
		
		
		//definimos  ancho y color de rectangulo
		$lh = $lh+20;
		
                
               
                
		$nombreContrib1=utf8_decode($nombreContrib);
                if(strlen(trim($nombreContrib1))>45 and strlen(trim($nombreContrib1))<=60){
                   $this->SetFont('narrow','',10); 
                }elseif(strlen(trim($nombreContrib1))>60 and strlen(trim($nombreContrib1))<=90 ){
                    $this->SetFont('narrow','',8);
                    
                }elseif(strlen(trim($nombreContrib1))>90){
                    $this->SetFont('narrow','',6);
                    
                }else{
                    $this->SetFont('narrow','',12); 
                }
		$this->SetLineWidth(0.3);
		$this->SetFillColor(255);
		$this->RoundedRect($lw-5, $lh, 110+$tamcabecera, 27, 1, '');
		$this->SetFont('narrow','',12);
		$this->SetXY($lw,$lh+2);
		$this->MultiCell(100,5,$nombreContrib1.' '.$vadicon,0,'J');
		$this->SetFont('narrow','',9);
		$this->SetXY($lw+10,$lh+12);
		$this->MultiCell(80,5,utf8_decode($direccionContrib),0,'J');
		
		//$qrcode = new QRcode(utf8_encode('Hola'), $err);
		//$qrcode->disableBorder();
		
		//aqui va el codigo de barras
		//$qrcode->displayHTML();
		//$this->SetXY($lw+10,$lh+12);
		//$qrcode->displayPNG(50);
		//$this->Image(,$lw,$ln,62,18,'PNG');
		
		//$this->SetXY($lw,$lh + 17 );
		$this->Image('../img/marca/barraBoucher_2dat.gif',$lw-3,$lh+12,13,13,'GIF','');
		
		//$this->RoundedRect($lw+110, $lh, 85, 27, 1, '');
                //$posInfoCuadro
                //+$tamInfoCuadro
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+110+$posInfoCuadro,$lh);
		$this->Cell(42+$tamInfoCuadro,8,"",1,1,'L',true);
		$this->SetXY($lw+112+$posInfoCuadro,$lh+2);
		$this->MultiCell(40+$tamInfoCuadro,5,'Codigo Contribuyente',0,'J');
		$this->SetXY($lw+152+$posInfoCuadro2,$lh);
		$this->Cell(43+$tamInfoCuadro2,8,"",1,1,'L',true);
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+2);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43+$tamInfoCuadro2,5,$codigoContrib,0,'R');
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110+$posInfoCuadro,$lh+8);
		$this->Cell(42+$tamInfoCuadro,8,"",1,1,'L',true);
		$this->SetXY($lw+112+$posInfoCuadro,$lh+9);
		$this->MultiCell(40+$tamInfoCuadro,5,'Recargos Proyec.',0,'J');
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+8);
		$this->Cell(43+$tamInfoCuadro2,8,"",1,1,'L',true);
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+9);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43+$tamInfoCuadro2,5,$fechaProyectado1,0,'R');
		
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110+$posInfoCuadro,$lh+14);
		$this->Cell(42+$tamInfoCuadro,6,"",1,1,'L',true);
		$this->SetXY($lw+112+$posInfoCuadro,$lh+15);
		$this->MultiCell(40+$tamInfoCuadro,4,'Usuario Imprime',0,'J');
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+14);
		$this->Cell(43+$tamInfoCuadro2,6,"",1,1,'L',true);
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+15);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43+$tamInfoCuadro2,4,$usuario,0,'R');
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110+$posInfoCuadro,$lh+20);
		$this->Cell(42+$tamInfoCuadro,7,"",1,1,'L',true);
		$this->SetXY($lw+112+$posInfoCuadro,$lh+21);
		$this->MultiCell(40+$tamInfoCuadro,4,'Fecha Impresion',0,'J');
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+20);
		$this->Cell(43+$tamInfoCuadro2,7,"",1,1,'L',true);
		$this->SetXY($lw+152+$posInfoCuadro2,$lh+21);
		$this->SetFont('f25bankprinter','',7);
		$this->MultiCell(43+$tamInfoCuadro2,4,$fechaImpresion,0,'R');
		$lh = $lh +35;
		
		$ls=-5;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		$this->Cell($colCab[0],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+1);
		$this->MultiCell($colCab[0],3,'Descripcion',0,'C');
		$this->SetXY($lw+$ls,$lh+5);
		$this->Cell($colSubCab[0][0],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+6);
		$this->MultiCell($colSubCab[0][0],3,'Periodo',0,'C');
		$this->SetXY($lw+$ls+27,$lh+5);
		$this->Cell($colSubCab[0][2],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls+25,$lh+5.5);
		$this->MultiCell($colSubCab[0][2],4,'Est',0,'C');
		$this->SetXY($lw+$ls+43,$lh+5);
		$this->Cell($colSubCab[0][2],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls+43,$lh+6);
		$this->MultiCell($colSubCab[0][2],3,'Fecha Venc',0,'C');
		
		$ls = 56;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		$this->Cell($colCab[1],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+1);
		$this->MultiCell($colCab[1],3,'Deuda',0,'C');
		$this->SetXY($lw+$ls,$lh+5);
		$this->Cell($colSubCab[1][0],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+6);
		$this->MultiCell($colSubCab[1][0],3,'Insoluto',0,'C');
		$this->SetXY($lw+$ls+18,$lh+5);
		$this->Cell($colSubCab[1][1],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls+18,$lh+6);
		$this->MultiCell($colSubCab[1][1],3,'Recar+GED',0,'C');
		$this->SetXY($lw+$ls+38,$lh+5);
		$this->Cell($colSubCab[1][2],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls+38,$lh+6);
		$this->MultiCell($colSubCab[1][2],3,'Total',0,'C');
		
		$ls = 119;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		$this->Cell($colCab[2],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+1);
		$this->MultiCell($colCab[2],3,'Abonos',0,'C');
		$this->SetXY($lw+$ls,$lh+5);
		$this->Cell($colSubCab[2][0],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+6);
		$this->MultiCell($colSubCab[2][0],3,'Fecha Pag',0,'C');
		$this->SetXY($lw+$ls+18,$lh+5);
		$this->Cell($colSubCab[2][1],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls+18,$lh+6);
		$this->MultiCell($colSubCab[2][1],3,'Caj',0,'C');
		$this->SetXY($lw+$ls+26,$lh+5);
		$this->Cell($colSubCab[2][2],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls+26,$lh+6);
		$this->MultiCell($colSubCab[2][2],3,'Monto',0,'C');
		
		$ls = 170;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		$this->Cell($colCab[3],10,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+2);
		$this->MultiCell($colCab[3],6,'Saldo',0,'C');
		
		
		
		
		
		/*$this->SetXY(14,1);
		$this->Cell(5,5,'Municipalidad Distrital de Puente Piedra',0,0,'L');
		$this->SetXY(14,55);
		$this->Cell(5,5,'Municipalidad Distrital de Puente Piedra',0,0,'L');
		//$this->SetXY(5,10);
		$this->RotatedText(5,58,'Municipalidad Distrital de Puente Piedra',90);
		//$this->SetXY(10,10);
		$this->RotatedText(72,5,'Municipalidad Distrital de Puente Piedra',270);
		$this->Image('../img/marca/puenteavanza.jpg',10,6,58,48,'JPG');
		$this->SetX(1);
		$this->SetFont('narrow','',12);
		$this->Cell(5,17,'Municipalidad Distrital de Puente Piedra',0,0,'L',0);
		$this->ln(3);
		$this->SetX(1);
		$this->Cell(1,22,'Sub Gerencia de Tesoreria',0,0,'L',0);
		$this->ln(3);
		$this->SetX(1);
		$this->Cell(5,27,'RUC '.$RUC,0,0,'L',0);
		$this->ln(3);
		$this->Image('../img/marca/escudo.png',59,66,18,18,'PNG');
		
		$ls = 5; 
		$lw = $marini;
		$lh = $topini;
		
		$this->SetXY($lw,$lh += $ls);
		$this->SetFont('f25bankprinter','B',14);
		$this->Cell(5,25,'RECIBO',0,0,'L',0);
		$this->SetFont('f25bankprinter','B',14);
		$this->SetX(26);
		$this->Cell(40,25,$numero_recibo,0,0,'L',0);
		$this->SetXY($lw,$lh += $ls);
		$this->Cell(5,25,'Codigo ',0,0,'L',0);
		$this->SetX(15);
		$this->Cell(9,25,':',0,0,'L',0);
		$this->SetX(26);
		$this->SetFont('f25bankprinter','B',14);
		$this->Cell(40,25,$codigoContrib,0,0,'L',0);
		$this->SetXY($lw,$lh += $ls);
		$this->SetFont('arial','',10);
		$this->Cell(5,25,'Nombre ',0,0,'L',0);
		$this->SetX(15);
		$this->Cell(9,25,':',0,0,'L',0);
		$this->SetXY($lw+16,$lh+11);
		$this->MultiCell(60,4,trim($nombreContrib),0,'J');
		$this->SetXY($lw,$lh += 2*$ls);
		$this->SetFont('narrow','',12);
		$this->Cell(5,25,'Nro. Doc. ',0,0,'L',0);
		$this->SetX(15);
		$this->Cell(9,25,':',0,0,'L',0);
		$this->SetXY($lw+16,$lh+11);
		$this->MultiCell(60,4,trim($nroDoc),0,'J');
		//$this->SetXY(5,25);
		//$this->MultiCell(170,3,trim($nroDoc),0,'L');
		$this->SetXY($lw,$lh += $ls);
		$this->SetFont('arial','',10);
		$this->Cell(5,25,'Dir. Fis. ',0,0,'L',0);
		$this->SetX(15);
		$this->Cell(9,25,':',0,0,'L',0);
		$this->SetXY($lw+16,$lh+11);
		$this->MultiCell(60,4,trim($direccionContrib),0,'J');
		$this->SetXY($lw,$lh += 3*$ls);
		$this->Image('../img/marca/barraBoucher_2dat.gif',$lw+50,$lh+6,25,25,'GIF','');
		$this->SetFont('narrow','',12);
		$this->Cell(5,25,'Fecha ',0,0,'L',0);
		$this->SetX(15);
		$this->Cell(9,25,':',0,0,'L',0);
		$this->SetXY($lw+16,$lh+11);
		$this->MultiCell(60,4,trim($fechaEmision),0,'J');
		
		
		$this->SetXY($lw,$lh += $ls);
		$this->SetFont('narrow','',12);
		$this->Cell(5,25,'Usuario ',0,0,'L',0);
		$this->SetX(15);
		$this->Cell(9,25,':',0,0,'L',0);
		$this->SetXY($lw+16,$lh+=11);
		$this->MultiCell(60,4,trim($cajero),0,'J');
		$this->SetXY($lw,$lh += $ls);
		$this->SetFont('f25bankprinter','',12);
		$this->MultiCell(50,4,'Impuesto Predial',0,'C');
		$this->SetXY($lw,$lh += $ls);
		$this->SetFont('f25bankprinter','',12);
		$this->MultiCell(50,4,'2013',0,'C');
		$this->SetFont('arial','B',10);
		$this->ln(3);
		$this->SetX(1);
		$this->Cell(75,4,'',1,0,'C',0);
		$this->SetX(1);
		$this->Cell(30,4,'Descripcion',0,0,'L',0);
		$this->Cell(45,4,'Monto   ',0,0,'R',0);
		
		*/
		
		
		
		
	}
	
	function Footer(){
		global $marini;
		global $nro_nec;
		global $ano_eje;
		global $nombreGispAdmincon;
		//Posición: a 1,5 cm del final
		$this->SetXY($marini,-15);
		//Arial italic 8
		$this->SetFont('Arial','',10);
		//Número de página
		$this->Cell(79,5,'Municipalidad Provincial de Huaral',0,0,'L',0);
		$this->SetFont('Arial','',8);
		$this->SetXY($marini,-10);
		$this->Cell(0,5,utf8_decode('Pagina N° ').$this->PageNo().' de {nb}',0,0,'R');
		$this->SetXY($marini,-10);
		$this->Cell(79,5,'Titania - Gestor Integrado Tributario ',0,0,'L',0);
		
	}
	
	function WriteResumen($x,$h, $corte,$topmar){
		//datos SQL
		global $Coneccion;
		global $ano_eje;
		global $nro_nec;

		//datos DOC
		global $marini;
		global $topmar;
		global $repositorioFirmas;
		
		$x = $x-5;
		
		
						
		$this->SetXY($x,$h +=4);
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
		$this->SetFont('Arial','',10);
		//Número de página
		#$Texto = "* Todos los Montos con Amnistia Tributaria fueron calculados en Base a la Ordenanza 230-2013/MDPP, por tanto el Contribuyente debe de cumplir con las condiciones especificadas en dicha Ordenanza para acceder a los BENEFICIOS";
		$this->SetXY($x,$h);
		$this->MultiCell(200,4,$Texto,0,'J');
		
		
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
                
                
                $this->SetXY($x,$h +=8);
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
                
		$this->SetFont('Arial','',10);
		//Número de página
		#$Texto = "* Todos los Montos con Beneficio Contribuyente Puntual 2014 fueron calculados en Base a la Ordenanza 229-2013/MDPP, que otorga el descuento del 30%  en arbitrios 2014 si se paga TODO el Impuesto Predial 2014, por tanto el Contribuyente debe de cumplir con las condiciones especificadas en dicha Ordenanza para acceder a los BENEFICIOS";
		$this->SetXY($x,$h);
		$this->MultiCell(200,4,$Texto,0,'J');
		
                $this->SetXY($x,$h +=15);
		
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
		
		
		
		
		
		//Arial italic 8
		$this->SetFont('Arial','',10);
		//Número de página
		$this->MultiCell(55,5,'Comuniquese con nosotros al: ',0,'L');
		$this->SetXY($x+55,$h);
		$this->MultiCell(80,5,'enrique.gordillo@munihuaral.gob.pe',0,'L');
		$this->SetXY($x+135,$h);
		$this->MultiCell(50,5,' TEL: 219 6200 - 219 6201 ',0,'L');
		
		
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
		$this->SetXY($x,$h+5);
		
		
		$this->MultiCell(80,5,'Visitanos en: www.munihuaral.gob.pe',0,'L');
		$this->SetXY($x+85,$h+5);
		#$this->MultiCell(110,5,'https://www.facebook.com/municipalidaddistritaldepuentepiedra',0,'L');
	}
	
	function CheckPageBreak($h){
		if($h >= 265){
			$this->AddPage();	
			return 1;
		}else{
			$this->SetY($h);
			return 0;
		}
		/*if($h >= 250){
			$this->AddPage('P','mm','A4');
			return true;
		}*/
	}

}


$usuario = $_GET['usuario'];
$fechaProyectado =  $_GET['fechaProyectado'];
$contrib =  $_GET['contrib'];
$predio =  $_GET['predio'];
$estado =  $_GET['estado'];
$desde =  $_GET['desde'];
$hasta =  $_GET['hasta'];
$tributo =  $_GET['tributo'];

$Rs_grabar = new TSPResult($ConeccionRatania,"");
$Rs_grabar->Poner_MSQL("select  case when public.listas_adicional_per(cidpers) is null
                                                    then '' else public.listas_adicional_per(cidpers) 
                                                    end vadicon,
                                            cidpers, 
                                            vrazsoc, 
                                            vdirfis, 
                                            to_char('2015-09-24'::date,'dd/mm/YYYY') as fecproy,
                                            coalesce(dfecven,'') as dfecven,
                                            coalesce(dfecpag,'') as dfecpag,
                                            cperiod, dperiod, cperanio,

                                            sum(nimpins-imp_insol_amorti1) as nimpins, 

                                            sum(nimprea) as nimprea, 
                                            sum(nimpmor)  as nimpmor, 
                                            sum(recargo)as recargo, 
                                            sum(ncosemi-costo_emis_amorti) as ncosemi, 

                                            sum(0) as descuento, 
                                            sum(0) as abonado, 
                                            sum(round( ntotals,2)) as ntotals, 
                                            vdescriptiping, ctiping, vestado,  
                                            sum(ntotals2) as ntotals2, 
                                            dimpres, 
                                            sum(nmonrea) as nmonrea, 
                                            Orden, 
                                            coalesce(TipoDe,'SIN REFERENCIA') as TipoDe, /*,mhresum*/
                                            sum(tmp_nimprea)as tmp_nimprea,
                                            sum(tmp_nimpmor)as tmp_nimpmor,
                                            sum(nfacrea),
                                            sum( nfacmor),
                                            sum(tmp_ndiaven),
                                            nestad2,
                                            nestad3,
                                            case when ctiping='0000000273' then '' else varbresum end as  varbresum,
                                            sum(nimpins) as nimpins2,

                                            /* Mora */
                                            round(sum(case when COALESCE(nestad2, '0')='0' 
                                                                    then ( (nimpins-imp_insol_amorti1) * nfacrea * nfacmor * tmp_ndiaven )-imp_mora_amorti
                                                                    else tmp_nimpmor end /*+ case when nestad3='I' then tmp_nimpmor else 0 end*/
                                                                    ),2) as nmora2,

                                            /* recargo2 Mora*/
                                            round(sum(case when COALESCE(nestad2, '0')='0'
                                                                    then (((nimpins-imp_insol_amorti1) * nfacrea * nfacmor * tmp_ndiaven)-imp_mora_amorti)::decimal(18,2) 
                                                                    else tmp_nimpmor end /*+ case when nestad3='I' then tmp_nimpmor else 0 end*/
                                                                    ),2) as recargo2,

                                            /*  Reajuste + Mora + cosemi */
                                            trunc(round(sum( ( (nimpins-imp_insol_amorti1) * 1 * (case when nfacrea = 0 then 1 else nfacrea  end))-nreajuste_amorti)+ 
                                                                    sum(case when COALESCE(nestad2, '0')='0' 
                                                                            then ( ( (nimpins-imp_insol_amorti1) * nfacrea * nfacmor * tmp_ndiaven)-imp_mora_amorti)::decimal(18,2) 
                                                                                    else tmp_nimpmor end/* + case when nestad3='I' then tmp_nimpmor else 0 end*/
                                                                                    ),2)+ sum(ncosemi-costo_emis_amorti),2) as total3,


                                            round(sum(nimpins * 1 * (case when nfacrea = 0 then 1 else nfacrea  end)),2 ) + 	
                                                    round(sum(case when COALESCE(nestad2, '0')='0' 
                                                            then nimpins * nfacrea * nfacmor * tmp_ndiaven 
                                                            else tmp_nimpmor end /*+ case when nestad3='I' then tmp_nimpmor else 0 end*/
                                                            ),2),
                                            sum(totalanmi1)AS totalanmi1,
                                            sum(totalanmi2)As totalanmi2
                                                                
								from (select a.cidpers, b.crazsoc as vrazsoc, b.direccf as  vdirfis,
											date_part('DAY', bx.dfecven)||'/'||date_part('MONTH', bx.dfecven)||'/'||date_part('YEAR', bx.dfecven) as dfecven,
											date_part('DAY', a.dfecpag)||'/'||date_part('MONTH', a.dfecpag)||'/'||date_part('YEAR', a.dfecpag) as dfecpag,
											a.cperiod,  case when e.vobserv='IP' then i.vdescri when e.vobserv in ('SC','LP','RC','LC','PJ') and a.cperanio<'2015' then j.vdescri when e.vobserv in ('SC','LP','RC','LC','PJ') and a.cperanio >='2015' then i.vdescri when e.vobserv='RDIP' then  i.vdescri else '' end as dperiod,
											a.cperanio, sum(a.imp_insol) as nimpins
											, sum(round(a.imp_insol * registro.factor_reajuste(current_date, a.cperiod, ctiprec) - a.imp_insol,2)) as nimprea
											, sum(round(registro.factor_interes(a.dfecven::date, current_date),2))  as nimpmor
											, sum(round(a.imp_insol * registro.factor_reajuste(current_date, a.cperiod, ctiprec) - a.imp_insol,2) + round(registro.factor_interes(a.dfecven::date, current_date),2))as recargo
											, sum(a.costo_emis) as ncosemi
											, sum(0) as descuento
											, sum(0) as abonado
											, sum(round( (a.imp_insol+ round(a.imp_insol * registro.factor_reajuste(current_date, a.cperiod, ctiprec) - a.imp_insol,2)  + round(registro.factor_interes(a.dfecven ::date , current_date),2) ),2)) as ntotals
											,a.ctiping as vdescriptiping
											,a.ctiping
											,d.descripcion as vestado
											,0.00 as ntotals2
											,date_part('DAY', current_date)||'/'||date_part('MONTH', current_date)||'/'||date_part('YEAR', current_date) as dimpres
											, sum(round(a.imp_insol * registro.factor_reajuste(current_date, a.cperiod, ctiprec) - a.imp_insol,2)) as nmonrea
											, case when a.ctiping ='0000000273' then 0 when a.ctiping = '0000000278' then 1 else 2 end as Orden
											, case when e.idsigma in ('0000000278','0000000312') then coalesce(e.vdescri,'') when e.vobserv in ('SC','LP','RC','LC','PJ')  then  'ARBITRIOS ' ||
											((coalesce(left(a.cidpred,10),'') || ' - '|| coalesce(pl_function.verusopred(a.cidpers,a.cidpred) ,'') || ' - '||g.vnombre 		|| ' ' || h.vnombre
														|| case when length(trim(f.dnumero)) > 0 then ' Nro. ' || trim(f.dnumero) else '' end
														|| case when length(trim(f.ddepart)) > 0 then ' Dpt. ' || trim(f.ddepart) else '' end
														|| case when length(trim(f.dmanzan)) > 0 then ' Mza. ' || trim(f.dmanzan) else '' end
														|| case when length(trim(f.dnlotes)) > 0 then ' Lte. ' || trim(f.dnlotes) else '' end
														|| case when length(trim(f.dbloque)) > 0 then ' Block. ' || trim(f.dbloque) else '' end
														|| case when length(trim(f.destaci)) > 0 then ' Estac. ' || trim(f.destaci) else '' end) :: character varying(400)) else coalesce(e.vdescri,'')  end as TipoDe,
											max(a.mhresum)mhresum
											,sum((a.imp_reaj - (case when a.ctiping='0000000312' then COALESCE(z.nimprea, 0) else 0 end) ))as tmp_nimprea
											,sum( a.imp_mora)as tmp_nimpmor
											, (case when bx.ntipcuo is null then 1
												else
												(case when COALESCE(d.estado_general, '0') = '0' and bx.ntipcuo<>1 then COALESCE( (case when COALESCE(bx.nipmapl, 0)=0 then COALESCE(bx.nfacrea, 1) else bx.nfacrea * bx.nfacipm end)  * COALESCE(c.nimprea, 1) , 0)
											else a.fact_reaj end) end) as nfacrea
											, COALESCE(bx.nmorapl * (bx.nmontim / 100), 0) * COALESCE(c.nimpmor, 1) as nfacmor
											, COALESCE(case when a.ctiping in('0000000278', '0000000273','0000001260','1000003127','1000003154','0000000312') and a.nestado<>'I' then  --1000003128  --correlativo mayor del rd arbitrios
												case when (cast('$fechaProyectado'  as date) - cast(bx.dfecven as date)) < 0 then 0 
													else 
														(cast('$fechaProyectado' as date) - cast(bx.dfecven as date)) end
													else 
														case when (cast('$fechaProyectado' as date) - cast(a.dfecven as date)) < 0 then 0 
													else (cast('$fechaProyectado' as date) - cast(a.dfecven as date)) end
													end, 0) as tmp_ndiaven
											, (case when a.nestado='3' then '1' else d.estado_general end) as  nestad2
											, a.nestado as nestad3
											,v.varbresum
											,coalesce(sum(round(coalesce(a.imp_insol,0) + 
												coalesce( case when a.cperanio <='2012' then case when a.ctiping = '0000000278' then  (-1)*
															(a.imp_insol * (case when a.cperanio='2012' then 0.2
																				  when a.cperanio='2011' then 0.3
																				  when a.cperanio='2010' then 0.4
																				  when a.cperanio='2009' then 0.5 else 0.9  end) )  end
															end ,0)    ,2))
												+ sum(a.costo_emis)	,0)  as totalanmi1,
											  coalesce(sum(round(coalesce(a.imp_insol,0) + 
												coalesce( case when a.cperanio <='2012' then 
														  case when a.ctiping = '0000000278' then  (-1) * 
															(a.imp_insol *  (case when a.cperanio='2012' then 0.1
																				   when a.cperanio='2011' then 0.15
																				   when a.cperanio='2010' then 0.25
																				   when a.cperanio='2009' then 0.35 else 0.7  end) )  end
														  end ,0)    
														  ,2)) 
														+ sum(a.costo_emis),0)  as totalanmi2,
                                                                                                                coalesce(sum(a1.imp_insol),0.00) as imp_insol_amorti1,
                                                                                                                coalesce(sum(a2.imp_insol),0.00)as imp_insol_amorti2,
                                                                                                                coalesce(sum(a2.nreajuste),0.00) as nreajuste_amorti,
                                                                                                                coalesce(sum(a2.imp_mora),0.00) as imp_mora_amorti,
                                                                                                                coalesce(sum(a2.costo_emis),0.00) as costo_emis_amorti
										from (select * from tesoreria.mestcta) a
                                                 left join (
                                                                                        select  cast( sum(c.imp_insol) as decimal(18, 2) )imp_insol
                                                                                        ,c.cidecta 
                                                                                        from  tesoreria.damortiza c where c.imp_insol>0.00 and c.nestado='1' group by c.cidecta
                                                                                        ) a1 on a.idsigma=a1.cidecta
                                                                                left join (
                                                                                        select  cast( sum(c.imp_insol) as decimal(18, 2) )imp_insol,
                                                                                        cast(sum(c.nreajuste) as decimal(18, 2)) 	nreajuste, 
                                                                                        cast(sum(c.imp_mora) as decimal(18, 2)) 	imp_mora ,
                                                                                        cast(sum(c.costo_emis) as decimal(18, 2)) 	costo_emis
                                                                                        ,c.cidecta from  tesoreria.damortiza c where c.imp_insol=0.00  and c.nestado='1'  group by c.cidecta
                                                                                        ) a2 on a.idsigma=a2.cidecta
                                
										inner join public.vwperson b on a.cidpers=b.cidpers
										left join tesoreria.mexonera c on a.cidpers=c.cidpers
										inner join public.mestados d on  a.nestado=d.estado
										inner join public.mconten e on a.ctiprec=e.idsigma
										left join public.mconten i on i.vobjeto=a.cperiod and i.cidtabl='1000001878'
										left join public.mconten j on j.vobjeto=a.cperiod and j.cidtabl='1000001865'
										/* left join registro.dpredio k on a.cidpred=k.idsigma*/
										left join registro.mpredio f on a.cidpred=f.idsigma
										left join registro.mviadis g on f.mviadis=g.idsigma
										left join registro.mpoblad h 	on f.mpoblad=h.idsigma
										left join (select cidecta, sum(imp_insol) as nimpins , sum(imp_reaj) as nimprea, sum(costo_emis) as ncosemi 
													from tesoreria.mtesore 
													where nestado='1' and cidpers='0000008080' group by cidecta) z
										on (a.idsigma = z.cidecta)
										left join tesoreria.mreajus as bx on ((a.ctiping=bx.ctiping and cast(replace(substring(a.cperiod from 1 for 2), 'A', '') as integer )=bx.cnromes and a.cperanio=bx.cperanio and bx.ntipcuo=0) or (a.ctiping=bx.ctiping and bx.ntipcuo=1))
										left join (select cidpred,cperanio,' LC: ' || coalesce(round(sum(case when ctiprec ='0000000279' 
																then imp_insol end)/sum(case when ctiprec ='0000000279' 
																						then 1 
																						else 0 end),2),'0.00') || '      PJ: ' ||
															coalesce(round(sum(case when ctiprec = '0000000283' 
																then imp_insol end)/sum(case when ctiprec = '0000000283' then  1 else 0 end),2),'0.00') || '      RB: ' ||
															coalesce(round(sum(case when ctiprec = '0000007267' 
																then imp_insol end)/sum(case when ctiprec = '0000007267' then  1 else 0 end),2),'0.00') || '      SC: '||
															coalesce(round(sum(case when ctiprec = '0000008509' 
																then imp_insol end)/sum(case when ctiprec = '0000008509' then  1 else 0 end),2),'0.00') as varbresum
													from tesoreria.mestcta a
													where a.cidpers='".$contrib."' and a.ctiping = '0000000278'
													group by cidpred,cperanio) as v
												on a.cidpred=v.cidpred and a.cperanio=v.cperanio
										where a.cidpers='".$contrib."' 
												and a.cidpred in ( select case when c2x.cd1 !='0000000000' then c2x.cd1 else a.cidpred end as cidpred2
																	from (select public.StringSplit('".$predio."-') as cd1) c2x )
												and (a.cperanio between '".$desde."' AND '".$hasta."' ) 
												AND a.ctiprec in (select serv 
																	from (select public.StringSplit('".$tributo."-') as serv) a
																  union all
																	select idsigma 
																		from public.mconten 
																		where cidtabl='0000000979' and  '0000000979' in (select serv 
																														 from (select public.StringSplit('".$tributo."-') as serv) a)) 
										
												and a.nestado in (select public.StringSplit('".$estado."-')  as algo)
										group by a.cidpers
												, vrazsoc
												, vdirfis
												, TipoDe
												, a.ctiping
												, a.dfecven
												, dfecpag
												, a.cperanio
												, a.cperiod
												, varbresum
												,dperiod
												,vdescriptiping
												,vestado
												,nnumdoc
												,bx.ntipcuo
												,d.estado_general
												,bx.nipmapl
												,bx.nfacrea
												,bx.nfacipm
												,c.nimprea
												,a.fact_reaj
												,bx.nmorapl
												,bx.nmontim
												,c.nimpmor
												,a.nestado
												,bx.dfecven
									)c
							
							group by cidpers
									, vrazsoc
									, vdirfis
									, TipoDe
									, ctiping
									, dfecven
									, dfecpag
									, cperanio
									, cperiod
									, varbresum
									/*,mhresum*/
									,dperiod
									,vdescriptiping
									,vestado
									,dimpres
									,Orden
									,nestad2
									,nestad3
									,varbresum
                                                                        having sum(nimpins-imp_insol_amorti1)>0	
							Order by Orden,tipode,cperanio,cperiod ");
$Rs_grabar->pg_Poner_Esquema("tesoreria");

$Rs_grabar->executeMSQL();


//echo "<br><br>CONSULTA REQ UPDATE <br> ".$Rs_grabar->Escribir_Consulta();			
$row= $Rs_grabar->pg_Get_Row();



$RUC='20131366702';
$numero_recibo='860100100014';
$codigoContrib=$row['cidpers'];
$nombreContrib=$row['vrazsoc'];
$direccionContrib=$row['vdirfis'];
$fechaProyectado1=$row['fecproy']; 
$vadicon = $row['vadicon'];
//$cajero='NDIESTRO';
//$usuario='NDIESTRO';
//$fechaEmision='12-12-2013 15:56';

//echo 'HOLA '.$fechaProyectado ;
//echo 'USUARIO '.$usuario ;



date_default_timezone_set('America/Lima');
$fechaImpresion=strftime('%d/%m/%Y %H:%M:%S'); 
$nroDoc='42215638';
//$fechaProyectado = '13/12/2013';
//$fechaRecargos = '13/12/2013';
if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'Q';

$colCab = array('61','63','51','25','220');
$colSubCab[0] = array('35','8','18');
$colSubCab[1] = array('18','20','25');
$colSubCab[2] = array('18','8','25');

$topIni = 10;
$marIni = 10; 
$topmar = 65;
//$lw=10;

$pdf = new PDF('P','mm','A4');
$pdf->SetDisplayMode('fullpage');
//$pdf->AutoPrint();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);


$var1 = $pdf->GetY()+5;
//$var1 = $topmar+5;
$pdf->SetXY($lw,$lh = $var1);


	$N = 0;
	$numRows = $Rs_grabar->pg_Num_Rows();
	
	$tipoIngreso = "###";
	$anioPeriodo = "####";
	$predioContrib= "####";
	
	$sumImpSol = 0;
	$sumRejuste = 0;
	$sumMontoTotal = 0;
	$sumAbonado = 0;
	$sumSaldo = 0;
	
	$sumImpSol1 = 0;
	$sumRejuste1 = 0;
	$sumMontoTotal1 = 0;
	$sumAbonado1 = 0;
	$sumSaldo1 = 0;
	
	$sumImpSol2 = 0;
	$sumRejuste2 = 0;
	$sumMontoTotal2 = 0;
	$sumAbonado2 = 0;
	$sumSaldo2 = 0;
	
	$sumDescImpSol = 0;
	$sumDescRejuste = 0;
	$sumDescMontoTotal = 0;
	$sumDescAbonado = 0;
	$sumDescSaldo = 0;
	
	$sumDescImpSol1 = 0;
	$sumDescRejuste1 = 0;
	$sumDescMontoTotal1 = 0;
	$sumDescAbonado1 = 0;
	$sumDescSaldo1 = 0;
	
	$sumDescImpSol2 = 0;
	$sumDescRejuste2 = 0;
	$sumDescMontoTotal2 = 0;
	$sumDescAbonado2 = 0;
	$sumDescSaldo2 = 0;
	
	
	
	
	$sumConAmnistiaImpSol = 0;
	$sumConAmnistiaRejuste = 0;
	$sumConAmnistiaMontoTotal = 0;
	$sumConAmnistiaAbonado = 0;
	$sumConAmnistiaSaldo = 0;
	
	$sumConAmnistiaImpSol1 = 0;
	$sumConAmnistiaRejuste1 = 0;
	$sumConAmnistiaMontoTotal1 = 0;
	$sumConAmnistiaAbonado1 = 0;
	$sumConAmnistiaSaldo1 = 0;
	
	$sumConAmnistiaImpSol2 = 0;
	$sumConAmnistiaRejuste2 = 0;
	$sumConAmnistiaMontoTotal2 = 0;
	$sumConAmnistiaAbonado2 = 0;
	$sumConAmnistiaSaldo2 = 0;
	
	$sumSinAmnistiaImpSol = 0;
	$sumSinAmnistiaRejuste = 0;
	$sumSinAmnistiaMontoTotal = 0;
	$sumSinAmnistiaAbonado = 0;
	$sumSinAmnistiaSaldo = 0;
	
	$sumSinAmnistiaImpSol1 = 0;
	$sumSinAmnistiaRejuste1 = 0;
	$sumSinAmnistiaMontoTotal1 = 0;
	$sumSinAmnistiaAbonado1 = 0;
	$sumSinAmnistiaSaldo1 = 0;
	
	$sumSinAmnistiaImpSol2 = 0;
	$sumSinAmnistiaRejuste2 = 0;
	$sumSinSinAmnistiaMontoTotal2 = 0;
	$sumSinAmnistiaAbonado2 = 0;
	$sumSinAmnistiaSaldo2 = 0;






$Rs_tipoPer = new TSPResult($ConeccionRatania,"");
$Rs_tipoPer->Poner_MSQL("select case when ctipper='1000000093' then '1'
			when ctipper='0000000266' then '1' else '0' end tipo,
			case when b.cidbenef='1000003110' and b.nestado='1' then '1'
			when b.cidbenef='1000003111' and b.nestado='1' then '2'
			else '0' end bene
            from mperson a left join tesoreria.mbeneficio  b on a.idsigma=b.mperson
			where a.idsigma='".$contrib."';
    ");
$Rs_tipoPer->pg_Poner_Esquema("public");

$Rs_tipoPer->executeMSQL();

$rowTipoP= $Rs_tipoPer->pg_Get_Row();
$p_tipopersona = $rowTipoP['tipo'];
$p_tienebene = $rowTipoP['bene'];







	$isAmnistia = false;  // SIN AMNISTIA
	while($N < $numRows){
		$row= $Rs_grabar->pg_Get_Row();

		//echo "TIPO INGRESO ".$row['ctiping']." - ".$tipoIngreso."<br>";
		if($tipoIngreso <> $row['ctiping'] or $predioContrib <> $row['tipode']){
				$lz=0;
				$ls=-5;
				$lw=$marIni;
				$pdf->SetFont('arial','B',10);
				$pdf->SetXY($lw+$ls,$lh);
				$pdf->MultiCell($colCab[4]-20,4,  utf8_decode( trim($row['tipode'])),0,'L');
				
				
				$tipoIngreso = $row['ctiping'];
				$predioContrib = $row['tipode'];
				//echo "TIPO INGRESO 3 ".$row['ctiping']." - ".$tipoIngreso."<br>";
				$anioPeriodo = "####";
				$tipoIngreso = "####";
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
				
				//$row= $Rs_grabar->pg_Get_Row();
				$lh += 9;
				$lz=0;
				$ls=-5;
			}
		
			
			if($anioPeriodo <> $row['cperanio'] ){
				
				$anioPeriodo = $row['cperanio'];
				$tipoIngreso = $row['ctiping'];
				$textoAnio = $row['cperanio'];
				$lz=0;
				$ls=-5;
				if($tipoIngreso=='0000000278'){
					$textoAnio = $textoAnio." ".$row['varbresum'];
				}
				$pdf->SetFont('arial','B',10);
				$pdf->SetXY($lw+$ls,$lh );
				$pdf->MultiCell($colCab[4],3,$textoAnio,0,'L');
				
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
			
				$lz=0;
				$lh += 5;
			}
			
			/*
			if($anioPeriodo <> $row['cperanio'] ){
				
				$lz=0;
				$ls=-5;
				$pdf->SetFont('arial','B',10);
				$pdf->SetXY($lw+$ls,$lh );
				$pdf->MultiCell($colCab[4],3,$row['cperanio'],0,'L');
				
				$anioPeriodo = $row['cperanio'];
				$tipoIngreso = $row['ctiping'];
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
			
				$lz=0;
				$lh += 5;
			}
			*/
			
			/*
			for($i=1; $i<=4; $i++){
				
			*/
			
				$ls=-5;
				$pdf->SetFont('arial','',8);
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[0][0],4,$row['dperiod'],0,'J');
				$pdf->SetXY($lw+$ls+26,$lh+$lz);
				$pdf->MultiCell($colSubCab[0][2],4,substr($row['vestado'],0,9),0,'C');
				$pdf->SetXY($lw+$ls+43,$lh+$lz);
				$pdf->MultiCell($colSubCab[0][2],4,$row['dfecven'],0,'C');
				
				$ls = 56;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][0],4,number_format($row['nimpins'],2,',',''),0,'R'); #insoluto
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				
                                $totalRecargos = floatval($row['recargo2']) + floatval($row['ncosemi']) + ($row['total3'] - ( $row['recargo2'] + $row['ncosemi'] + $row['nimpins']) ) ;
				
				$pdf->MultiCell($colSubCab[1][1],4,number_format($totalRecargos,2,',',''),0,'R'); #Recar + GED
				$pdf->SetXY($lw+$ls+38,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][2],4,number_format($row['total3'],2,',',''),0,'R');
				
				$ls = 119;
				
				if(intval($row['nestado'])==1){
					$fechaPago = $row['dfecpago'];
					$caja = $row['caja'];
					$abono = floatval($row['abonado']);
				}else{
					$fechaPago = "";
					$caja = "";
					$abono = 0;
				}
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][0],4,$fechaPago,0,'C');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][1],4,$caja,0,'R');
				$pdf->SetXY($lw+$ls+26,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][2],4,number_format($abono,2,',',''),0,'R');
				
				$ls = 170;
				
				$saldo = floatval($totalRecargos+$row['nimpins']) - $abono;
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[3],4,number_format($saldo,2,',',''),0,'R');
				
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
			
			
				/*$sumImpSol += floatval($row['nimpins']);
				$sumRejuste += $totalRecargos;
				$sumMontoTotal += floatval($row['ntotals']);
				$sumAbonado += $abono;
				$sumSaldo += $saldo ;
				*/
				
				//----- Sin Admistia ----///
				$sumSinAmnistiaImpSol += floatval($row['nimpins']);
				$sumSinAmnistiaRejuste += $totalRecargos;
				$sumSinAmnistiaMontoTotal += floatval($totalRecargos+$row['nimpins']);
				$sumSinAmnistiaAbonado += $abono;
				$sumSinAmnistiaSaldo += $saldo;
				
				$lz += 3;
				//$lh += $lz;
			
			/*
			}
			
			*/
			$Rs_grabar->pg_Move_Next();
			$row2= $Rs_grabar->pg_Get_Row();
			if($row2['cperanio']<>$anioPeriodo 
					or  $tipoIngreso <> $row2['ctiping'] 
					or $predioContrib <> $row2['tipode'] 
					or $N == ( $numRows -1 ) ){
				
				//----- Con Admistia ----///
				if($isAmnistia == true or $p_tienebene!='0' ){  //and $codigoContrib != '0000021640'
					
					switch($row['ctiping']){
						case '0000000273':	#Predial
							$sumDescImpSol += 0;
							$sumDescRejuste += $sumSinAmnistiaRejuste;
							$sumDescMontoTotal += $sumDescImpSol+$sumDescRejuste;
							$sumDescAbonado += 0;
							$sumDescSaldo += $sumDescMontoTotal - $sumDescAbonado;
							
							$sumConAmnistiaImpSol += $sumSinAmnistiaImpSol - $sumDescImpSol;
							$sumConAmnistiaRejuste += $sumSinAmnistiaRejuste - $sumDescRejuste;
							$sumConAmnistiaMontoTotal += $sumSinAmnistiaMontoTotal - $sumDescMontoTotal;
							$sumConAmnistiaAbonado += $sumSinAmnistiaAbonado - $sumDescAbonado;
							$sumConAmnistiaSaldo += $sumSinAmnistiaSaldo - $sumDescSaldo;
							
							$Texto = "Total Predial ";
						break;
						case '0000000278':	#Arbitrios

                            if($p_tienebene=='0'){
                                    if(trim($row['cperanio'])<='2009' and $p_tipopersona=='1' ){ #2008
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.9;

                                    }elseif(trim($row['cperanio'])>'2009' and trim($row['cperanio'])<'2014'  and $p_tipopersona=='1' ){ # 2008 - 20014
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.5; # 0.5
                                    }elseif(trim($row['cperanio'])=='2014'  and $p_tipopersona=='1' ){
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.4; # 0.5
                                    }
                                    #DMF Quitar Pronto Pago se comenta esta parte para quitar el descuento de pronto pago

                                    elseif(trim($row['cperanio'])=='2015' and $row['cidpers']!= '0000106795' and $row['cidpers']!= '0000110622' and $row['cidpers']!= '0000008260'  and $row['cidpers']!= '0000108393' and $row['cidpers']!= '0000102492' ){	#2014 //and $row['cidpers']!= '0000021640'  no aplica al usuario
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.3;
                                    }

                                    else{
                                            $sumDescImpSol = 0;
                                    }
                            } elseif($p_tienebene=='1'){
                                    if(trim($row['cperanio'])<'2015'){
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.8; # 0.5
                                    }elseif(trim($row['cperanio'])=='2015'){	#2014
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                    }elseif(trim($row['cperanio'])=='2016'){	#2014
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                    }elseif(trim($row['cperanio'])=='2017'){	#2014
                                        $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                    }else{
                                        $sumDescImpSol = 0;
                                    }

                            }
                                //OM 258 con 260
                            elseif($p_tienebene=='2'){  //si es 2
                                if(trim($row['cperanio'])<='2009' and $p_tipopersona=='1' ){ #2008
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.9;

                                }elseif(trim($row['cperanio'])>'2009' and trim($row['cperanio'])<'2014'  and $p_tipopersona=='1' ){ # 2008 - 20014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.5; # 0.5
                                }elseif(trim($row['cperanio'])=='2014'  and $p_tipopersona=='1' ){
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.4; # 0.5
                                }elseif(trim($row['cperanio'])=='2015'){	#2014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                }elseif(trim($row['cperanio'])=='2016'){	#2014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                }elseif(trim($row['cperanio'])=='2017'){	#2014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                }else{
                                    $sumDescImpSol = 0;
                                }

                            }
                            //Solo OM 258
                           /* elseif($p_tienebene=='2'){  //si es 2
                                if(trim($row['cperanio'])<='2009' and $p_tipopersona=='1' ){ #2008
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0;

                                }elseif(trim($row['cperanio'])>'2009' and trim($row['cperanio'])<'2014'  and $p_tipopersona=='1' ){ # 2008 - 20014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0; # 0.5
                                }elseif(trim($row['cperanio'])=='2014'  and $p_tipopersona=='1' ){
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0; # 0.5
                                }elseif(trim($row['cperanio'])=='2015'){	#2014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                }elseif(trim($row['cperanio'])=='2016'){	#2014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                }elseif(trim($row['cperanio'])=='2017'){	#2014
                                    $sumDescImpSol += $sumSinAmnistiaImpSol*0.8;
                                }else{
                                    $sumDescImpSol = 0;
                                }

                            }*/



                                                        
							$sumDescRejuste += $sumSinAmnistiaRejuste;
							$sumDescMontoTotal += $sumDescImpSol+$sumDescRejuste;
							$sumDescAbonado += 0;
							$sumDescSaldo += $sumDescMontoTotal - $sumDescAbonado;
							
							$sumConAmnistiaImpSol = $sumSinAmnistiaImpSol - $sumDescImpSol;
							$sumConAmnistiaRejuste += $sumSinAmnistiaRejuste - $sumDescRejuste;
							$sumConAmnistiaMontoTotal += $sumSinAmnistiaMontoTotal - $sumDescMontoTotal;
							$sumConAmnistiaAbonado += $sumSinAmnistiaAbonado - $sumDescAbonado;
							$sumConAmnistiaSaldo += $sumSinAmnistiaSaldo - $sumDescSaldo;
							
							$Texto = "Total Arbitrios ".substr(trim($row['tipode']),10,10);
							break;
						case '0000001260':	#Multas Tributarias
							$sumDescImpSol += $sumSinAmnistiaImpSol;
							$sumDescRejuste += $sumSinAmnistiaRejuste;
							$sumDescMontoTotal += $sumDescImpSol+$sumDescRejuste;
							$sumDescAbonado += 0;
							$sumDescSaldo += $sumDescMontoTotal - $sumDescAbonado;
							
							
							$sumConAmnistiaImpSol += $sumSinAmnistiaImpSol - $sumDescImpSol;
							$sumConAmnistiaRejuste += $sumSinAmnistiaRejuste - $sumDescRejuste;
							$sumConAmnistiaMontoTotal += $sumSinAmnistiaMontoTotal - $sumDescMontoTotal;
							$sumConAmnistiaAbonado += $sumSinAmnistiaAbonado - $sumDescAbonado;
							$sumConAmnistiaSaldo += $sumSinAmnistiaSaldo - $sumDescSaldo;
							$Texto = "Total Multa Tributaria ";
						break;
						case '0000000312':	#FRACIONAMIENTO TRIBUTARIO
							$sumDescImpSol += 0;
							$sumDescRejuste += 0;
							$sumDescMontoTotal += 0;
							$sumDescAbonado += 0;
							$sumDescSaldo += 0;
							
							
							$sumConAmnistiaImpSol += $sumSinAmnistiaImpSol - $sumDescImpSol;
							$sumConAmnistiaRejuste += $sumSinAmnistiaRejuste - $sumDescRejuste;
							$sumConAmnistiaMontoTotal += $sumSinAmnistiaMontoTotal - $sumDescMontoTotal;
							$sumConAmnistiaAbonado += $sumSinAmnistiaAbonado - $sumDescAbonado;
							$sumConAmnistiaSaldo += $sumSinAmnistiaSaldo - $sumDescSaldo;
							$Texto = "Total Fraccionamiento ";
						break;
                        case '1000003127':	#RD IP
                            $sumDescImpSol += 0;
                            $sumDescRejuste += $sumSinAmnistiaRejuste;
                            $sumDescMontoTotal += $sumDescImpSol+$sumDescRejuste;
                            $sumDescAbonado += 0;
                            $sumDescSaldo += $sumDescMontoTotal - $sumDescAbonado;

                            $sumConAmnistiaImpSol += $sumSinAmnistiaImpSol - $sumDescImpSol;
                            $sumConAmnistiaRejuste += $sumSinAmnistiaRejuste - $sumDescRejuste;
                            $sumConAmnistiaMontoTotal += $sumSinAmnistiaMontoTotal - $sumDescMontoTotal;
                            $sumConAmnistiaAbonado += $sumSinAmnistiaAbonado - $sumDescAbonado;
                            $sumConAmnistiaSaldo += $sumSinAmnistiaSaldo - $sumDescSaldo;

                            $Texto = "Total RD Predial ";
                            break;

						default:
							$sumDescImpSol += 0;
							$sumDescRejuste += 0;
							$sumDescMontoTotal += 0;
							$sumDescAbonado += 0;
							$sumDescSaldo += 0;

							
							$sumConAmnistiaImpSol += $sumSinAmnistiaImpSol - $sumDescImpSol;
							$sumConAmnistiaRejuste += $sumSinAmnistiaRejuste - $sumDescRejuste;
							$sumConAmnistiaMontoTotal += $sumSinAmnistiaMontoTotal - $sumDescMontoTotal;
							$sumConAmnistiaAbonado += $sumSinAmnistiaAbonado - $sumDescAbonado;
							$sumConAmnistiaSaldo += $sumSinAmnistiaSaldo - $sumDescSaldo;
							$Texto = "Total  ";	
						break;
					}
				}
				
				
				//Sub Total Por Año
				$lz += 3;
				$ls=-5;
				$pdf->Line($lw+$colCab[0], $lh + $lz , 205, $lh  + $lz, $style);
				$pdf->SetFont('arial','',8);
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[0],4,'Total '.$row['cperanio'].' Sin Amnistia',1,'L');

				
				$ls = 56;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][0],4,number_format($sumSinAmnistiaImpSol,2,',',''),1,'R');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][1],4,number_format($sumSinAmnistiaRejuste,2,',',''),1,'R');
				$pdf->SetXY($lw+$ls+38,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][2],4,number_format($sumSinAmnistiaMontoTotal,2,',',''),1,'R');
				
				$ls = 119;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
				$pdf->SetXY($lw+$ls+26,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][2],4,number_format($sumSinAmnistiaAbonado,2,',',''),1,'R');
				
				$ls = 170;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[3],4,number_format($sumSinAmnistiaSaldo,2,',',''),1,'R');
				
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
			
				$lz += 4;
				$lh += $lz;
				$lz = 0;
				
				if($isAmnistia == true or $p_tienebene!='0'){ #$isAmnistia == true
						
					$ls=-5;
					
					if($row['cperanio']<'2015'){	#2014
						$textoBenenificio = 'Amnistia - Ordenanza 260-2015/MDPP';	#Ordenanza 230-2013/MDPP
						$textoBenenificio2 = 'Total '.$row['cperanio'].' con Amnistia';
					}
					else{
						$textoBenenificio = 'Beneficio Contribuyente Puntual';
						$textoBenenificio2 = 'Total '.$row['cperanio'].' con Beneficio';
					}
												
					$pdf->SetFont('arial','',6);
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[0],4,$textoBenenificio,1,'L');
	
					
					$ls = 56;
					
						
					$pdf->SetFont('arial','',8);	
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][0],4,number_format(-1*$sumDescImpSol,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][1],4,number_format(-1*$sumDescRejuste,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+38,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][2],4,number_format(-1*($sumDescMontoTotal),2,',',''),1,'R');
					
					
					$ls = 119;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
					$pdf->SetXY($lw+$ls+26,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][2],4,number_format(-1*$sumDescAbonado,2,',',''),1,'R');
					
											
					$ls = 170;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[3],4,number_format(-1*($sumDescSaldo),2,',',''),1,'R');
					
					
					
					$var1 = $pdf->GetY();
					if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
					
					$lz += 4;
					$ls=-5;
					
					$pdf->Line($lw+$colCab[0], $lh + $lz , 205, $lh  + $lz, $style);
					$pdf->SetFont('arial','',8);
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[0],4,$textoBenenificio2,1,'L');
	
					
					$ls = 56;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][0],4,number_format($sumConAmnistiaImpSol,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][1],4,number_format($sumConAmnistiaRejuste,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+38,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][2],4,number_format($sumConAmnistiaMontoTotal,2,',',''),1,'R');
					
					$ls = 119;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
					$pdf->SetXY($lw+$ls+26,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][2],4,number_format($sumConAmnistiaAbonado,2,',',''),1,'R');
					
					$ls = 170;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[3],4,number_format($sumConAmnistiaSaldo,2,',',''),1,'R');
					
					$var1 = $pdf->GetY();
					if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
					
					$lz += 6;
					$lh += $lz;
					$lz = 0;
						
										
				}
				
				
				
				
					
				$sumImpSol1 +=  $sumImpSol;
				$sumRejuste1 += $sumRejuste;
				$sumMontoTotal1 += $sumMontoTotal;
				$sumAbonado1 += $sumAbonado;
				$sumSaldo1 += $sumSaldo;
				
				$sumSinAmnistiaImpSol1 +=  $sumSinAmnistiaImpSol;
				$sumSinAmnistiaRejuste1 += $sumSinAmnistiaRejuste;
				$sumSinAmnistiaMontoTotal1 += $sumSinAmnistiaMontoTotal;
				$sumSinAmnistiaAbonado1 += $sumSinAmnistiaAbonado;
				$sumSinAmnistiaSaldo1 += $sumSinAmnistiaSaldo;
				
				$sumDescImpSol1 +=  $sumDescImpSol;
				$sumDescRejuste1 += $sumDescRejuste;
				$sumDescMontoTotal1 += $sumDescMontoTotal;
				$sumDescAbonado1 += $sumDescAbonado;
				$sumDescSaldo1 += $sumDescSaldo;
				
				$sumConAmnistiaImpSol1 +=  $sumConAmnistiaImpSol;
				$sumConAmnistiaRejuste1 += $sumConAmnistiaRejuste;
				$sumConAmnistiaMontoTotal1 += $sumConAmnistiaMontoTotal;
				$sumConAmnistiaAbonado1 += $sumConAmnistiaAbonado;
				$sumConAmnistiaSaldo1 += $sumConAmnistiaSaldo;
				
				
				$sumImpSol = 0;
				$sumRejuste = 0;
				$sumMontoTotal = 0;
				$sumAbonado = 0;
				$sumSaldo = 0;
				
				$sumSinAmnistiaImpSol = 0;
				$sumSinAmnistiaRejuste = 0;
				$sumSinAmnistiaMontoTotal = 0;
				$sumSinAmnistiaAbonado = 0;
				$sumSinAmnistiaSaldo = 0;
				
				$sumDescImpSol = 0;
				$sumDescRejuste = 0;
				$sumDescMontoTotal = 0;
				$sumDescAbonado = 0;
				$sumDescSaldo = 0;
				
				$sumConAmnistiaImpSol = 0;
				$sumConAmnistiaRejuste = 0;
				$sumConAmnistiaMontoTotal = 0;
				$sumConAmnistiaAbonado = 0;
				$sumConAmnistiaSaldo = 0;
				
				
				
				
			}
			
			if( $tipoIngreso <> $row2['ctiping'] 
									  or $predioContrib <> $row2['tipode'] 
									  or $N == ( $numRows -1 )){
				//Sub Total Por Año
				//Sub Total Por Año
				$lz += 3;
				$ls=-5;
				$pdf->Line($lw+$colCab[0], $lh + $lz , 205, $lh  + $lz, $style);
				$pdf->SetFont('arial','',8);
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[0],4,$Texto.' Sin Amnistia',1,'L');

				
				$ls = 56;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][0],4,number_format($sumSinAmnistiaImpSol1,2,',',''),1,'R');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][1],4,number_format($sumSinAmnistiaRejuste1,2,',',''),1,'R');
				$pdf->SetXY($lw+$ls+38,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][2],4,number_format($sumSinAmnistiaMontoTotal1,2,',',''),1,'R');
				
				$ls = 119;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
				$pdf->SetXY($lw+$ls+26,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][2],4,number_format($sumSinAmnistiaAbonado1,2,',',''),1,'R');
				
				$ls = 170;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[3],4,number_format($sumSinAmnistiaSaldo1,2,',',''),1,'R');
				
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
			
				$lz += 4;
				$lh += $lz;
				$lz = 0;
				
				if($isAmnistia == true or $p_tienebene!='0'){
						
					$ls=-5;
					
					$pdf->SetFont('arial','',8);
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[0],4,'Amnistia/Beneficio',1,'L');
							
					$ls = 56;
						
						
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][0],4,number_format(-1*$sumDescImpSol1,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][1],4,number_format(-1*$sumDescRejuste1,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+38,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][2],4,number_format(-1*($sumDescMontoTotal1),2,',',''),1,'R');
					
					
					$ls = 119;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
					$pdf->SetXY($lw+$ls+26,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][2],4,number_format(-1*$sumDescAbonado1,2,',',''),1,'R');
					
											
					$ls = 170;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[3],4,number_format(-1*($sumDescSaldo1),2,',',''),1,'R');
					
					
					
					$var1 = $pdf->GetY();
					if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
					
					$lz += 4;
					$ls=-5;
					$pdf->SetFont('arial','',6);
					$pdf->Line($lw+$colCab[0], $lh + $lz , 205, $lh  + $lz, $style);
					$pdf->SetFont('arial','',8);
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[0],4,$Texto.' con Amnis / Bene. ',1,'L');
	
					$pdf->SetFont('arial','',8);
					$ls = 56;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][0],4,number_format($sumConAmnistiaImpSol1,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][1],4,number_format($sumConAmnistiaRejuste1,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+38,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][2],4,number_format($sumConAmnistiaMontoTotal1,2,',',''),1,'R');
					
					$ls = 119;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
					$pdf->SetXY($lw+$ls+26,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][2],4,number_format($sumConAmnistiaAbonado1,2,',',''),1,'R');
					
					$ls = 170;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[3],4,number_format($sumConAmnistiaSaldo1,2,',',''),1,'R');
					
					$var1 = $pdf->GetY();
					if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
					
					$lz += 6;
					$lh += $lz;
					$lz = 0;
									

				}
				
				
				$sumImpSol2 +=  $sumImpSol1;
				$sumRejuste2 += $sumRejuste1;
				$sumMontoTotal2 += $sumMontoTotal1;
				$sumAbonado2 += $sumAbonado1;
				$sumSaldo2 += $sumSaldo1;
				
				$sumSinAmnistiaImpSol2 +=  $sumSinAmnistiaImpSol1;
				$sumSinAmnistiaRejuste2 += $sumSinAmnistiaRejuste1;
				$sumSinAmnistiaMontoTotal2 += $sumSinAmnistiaMontoTotal1;
				$sumSinAmnistiaAbonado2 += $sumSinAmnistiaAbonado1;
				$sumSinAmnistiaSaldo2 += $sumSinAmnistiaSaldo1;
				
				$sumDescImpSol2 +=  $sumDescImpSol1;
				$sumDescRejuste2 += $sumDescRejuste;
				$sumDescMontoTotal2 += $sumDescMontoTotal1;
				$sumDescAbonado2 += $sumDescAbonado1;
				$sumDescSaldo2 += $sumDescSaldo1;
				
				$sumConAmnistiaImpSol2 +=  $sumConAmnistiaImpSol1;
				$sumConAmnistiaRejuste2 += $sumConAmnistiaRejuste1;
				$sumConAmnistiaMontoTotal2 += $sumConAmnistiaMontoTotal1;
				$sumConAmnistiaAbonado2 += $sumConAmnistiaAbonado1;
				$sumConAmnistiaSaldo2 += $sumConAmnistiaSaldo1;
				
				
				$sumImpSol1 = 0;
				$sumRejuste1 = 0;
				$sumMontoTotal1 = 0;
				$sumAbonado1 = 0;
				$sumSaldo1 = 0;
				
				$sumSinAmnistiaImpSol1 = 0;
				$sumSinAmnistiaRejuste1 = 0;
				$sumSinAmnistiaMontoTotal1 = 0;
				$sumSinAmnistiaAbonado1 = 0;
				$sumSinAmnistiaSaldo1 = 0;
				
				$sumDescImpSol1 = 0;
				$sumDescRejuste1 = 0;
				$sumDescMontoTotal1 = 0;
				$sumDescAbonado1 = 0;
				$sumDescSaldo1 = 0;
				
				$sumConAmnistiaImpSol1 = 0;
				$sumConAmnistiaRejuste1 = 0;
				$sumConAmnistiaMontoTotal1 = 0;
				$sumConAmnistiaAbonado1 = 0;
				$sumConAmnistiaSaldo1 = 0;
				
			}
			
			
			$lh += $lz;
			$lz =0;
			
			
			$N++;
		}
		
		
		///// TOTAL FINAL

				
				$lz += 3;
				$ls=-5;
				$lw=$marIni;
				$pdf->Line($lw+$colCab[0], $lh + $lz , 205, $lh  + $lz, $style);
				$pdf->SetFont('arial','',8);
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[0],4,'Deuda Total  Sin Amnistia/Beneficio',1,'L');

				$ls = 56;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][0],4,number_format($sumSinAmnistiaImpSol2,2,',',''),1,'R');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][1],4,number_format($sumSinAmnistiaRejuste2,2,',',''),1,'R');
				$pdf->SetXY($lw+$ls+38,$lh+$lz);
				$pdf->MultiCell($colSubCab[1][2],4,number_format($sumSinAmnistiaMontoTotal2,2,',',''),1,'R');
				
				$ls = 119;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
				$pdf->SetXY($lw+$ls+18,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
				$pdf->SetXY($lw+$ls+26,$lh+$lz);
				$pdf->MultiCell($colSubCab[2][2],4,number_format($sumSinAmnistiaAbonado2,2,',',''),1,'R');
				
				$ls = 170;
				
				$pdf->SetXY($lw+$ls,$lh+$lz);
				$pdf->MultiCell($colCab[3],4,number_format($sumSinAmnistiaSaldo2,2,',',''),1,'R');
				
				$var1 = $pdf->GetY();
				if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
			
				$lz += 4;
				$lh += $lz;
				$lz = 0;
				
				if($isAmnistia == true or $p_tienebene!='0'){
						
					$ls=-5;
					
					$pdf->SetFont('arial','',8);
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[0],4,'Amnistia/Beneficio',1,'L');
	
					
					$ls = 56;
					
						
						
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][0],4,number_format(-1*$sumDescImpSol2,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][1],4,number_format(-1*$sumDescRejuste2,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+38,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][2],4,number_format(-1*($sumDescMontoTotal2),2,',',''),1,'R');
					
					
					$ls = 119;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
					$pdf->SetXY($lw+$ls+26,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][2],4,number_format(-1*$sumDescAbonado2,2,',',''),1,'R');
					
											
					$ls = 170;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[3],4,number_format(-1*($sumDescSaldo2),2,',',''),1,'R');
					
					$var1 = $pdf->GetY();
					if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
					
					$lz += 4;
					$ls=-5;
					
					$pdf->Line($lw+$colCab[0], $lh + $lz , 205, $lh  + $lz, $style);
					$pdf->SetFont('arial','',8);
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[0],4,'Deuda Total con Amnistia/Beneficio ',1,'L');
	
					
					$ls = 56;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][0],4,number_format($sumConAmnistiaImpSol2,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][1],4,number_format($sumConAmnistiaRejuste2,2,',',''),1,'R');
					$pdf->SetXY($lw+$ls+38,$lh+$lz);
					$pdf->MultiCell($colSubCab[1][2],4,number_format($sumConAmnistiaMontoTotal2,2,',',''),1,'R');
					
					$ls = 119;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][0],4,'',1,'C');
					$pdf->SetXY($lw+$ls+18,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][1],4,'',1,'R');
					$pdf->SetXY($lw+$ls+26,$lh+$lz);
					$pdf->MultiCell($colSubCab[2][2],4,number_format($sumConAmnistiaAbonado2,2,',',''),1,'R');
					
					$ls = 170;
					
					$pdf->SetXY($lw+$ls,$lh+$lz);
					$pdf->MultiCell($colCab[3],4,number_format($sumConAmnistiaSaldo2,2,',',''),1,'R');
					
					$var1 = $pdf->GetY();
					if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
					
					$lz += 6;
					$lh += $lz;
					$lz = 0;
				}
				
$var1 = $pdf->GetY();				
$pdf->WriteResumen($lw,$var1, $corte,$topmar);
$pdf->Output();


?>