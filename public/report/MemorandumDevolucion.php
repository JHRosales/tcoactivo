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
		global $topIni;
		global $marIni;
		global $marcaAguaL1;
		global $marcaAguaL2;
		global $marcaAguaL3;
		global $marcaAguaL4;
		global $codigoContrib;
		global $nombreContrib;
		global $nroDoc;
		global $direccionContrib;
		global $fechaImpresion;
		global $fechaProyectado1;
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
		$this->SetXY($lw+60,$lh+10);
		$this->MultiCell(90,10,'Memorandum de Devolucion',0,'C');
		
		
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
		$this->RoundedRect($lw-5, $lh, 170+$tamcabecera,32, 1, '');
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
		//$this->Image('../img/marca/barraBoucher_2dat.gif',$lw-3,$lh+12,13,13,'GIF','');
		
		//$this->RoundedRect($lw+110, $lh, 85, 27, 1, '');
                //$posInfoCuadro
                //+$tamInfoCuadro


        $this->SetXY($lw,$lh+25);
		

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
		$Texto = "* Todos los Montos con Amnistia Tributaria fueron calculados en Base a la Ordenanza 230-2013/MDPP, por tanto el Contribuyente debe de cumplir con las condiciones especificadas en dicha Ordenanza para acceder a los BENEFICIOS";
		$this->SetXY($x,$h);
		$this->MultiCell(200,4,$Texto,0,'J');
		
		
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
                
                
                $this->SetXY($x,$h +=8);
		$var1 = $this->GetY();
		if($corte = $this->CheckPageBreak($var1)) $h = $topmar+7;
                
		$this->SetFont('Arial','',10);
		//Número de página
		//$Texto = "* Todos los Montos con Beneficio Contribuyente Puntual 2014 fueron calculados en Base a la Ordenanza 229-2013/MDPP, que otorga el descuento del 30%  en arbitrios 2014 si se paga TODO el Impuesto Predial 2014, por tanto el Contribuyente debe de cumplir con las condiciones especificadas en dicha Ordenanza para acceder a los BENEFICIOS";
		//$this->SetXY($x,$h);
	//	$this->MultiCell(200,4,$Texto,0,'J');
		
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
$Rs_grabar->Poner_MSQL("select 'sdf' as contrib ");
$Rs_grabar->pg_Poner_Esquema("tesoreria");

$Rs_grabar->executeMSQL();


$row= $Rs_grabar->pg_Get_Row();
$RUC='20131366702';
$numero_recibo='860100100014';
$codigoContrib=$row['contrib'];
$nombreContrib=$row['vrazsoc'];
$direccionContrib=$row['vdirfis'];
$fechaProyectado1=$row['fecproy']; 
$vadicon = $row['vadicon'];

//echo 'USUARIO '.$usuario ;

date_default_timezone_set('America/Lima');
$fechaImpresion=strftime('%d/%m/%Y %H:%M:%S'); 
$nroDoc='42215638';
if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'Q';

$colCab = array('61','63','51','25','220');
$colSubCab[0] = array('35','8','18');
$colSubCab[1] = array('18','20','25');
$colSubCab[2] = array('18','8','25');

$topIni = 10;
$marIni = 10; 
$topmar = 65;

$pdf = new PDF('P','mm','A4');
$pdf->SetDisplayMode('fullpage');
//$pdf->AutoPrint();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);


$var1 = $pdf->GetY()+5;
$pdf->SetXY($lw,$lh = $var1);

$lw=$topIni;
$lh=$var1+5;

$pdf->SetFont('Arial','',12);
//Número de página
$Texto = "* Todos los Montos con Beneficio Contribuyente Puntual 2014 fueron calculados en Base a la Ordenanza 229-2013/MDPP, que otorga el descuento del 30%  en arbitrios 2014 si se paga TODO el Impuesto Predial 2014, por tanto el Contribuyente debe de cumplir con las condiciones especificadas en dicha Ordenanza para acceder a los BENEFICIOS";
$pdf->SetXY($lw,$lh);
$pdf->MultiCell(190,4,$Texto,0,'J');



	$N = 0;
	$numRows = $Rs_grabar->pg_Num_Rows();
	
	$tipoIngreso = "###";
	$anioPeriodo = "####";
	$predioContrib= "####";
	




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

				$lz += 3;
				$lw=$marIni;
				$lh += $lz;
				$lz = 0;

				
$var1 = $pdf->GetY();				
$pdf->WriteResumen($lw,$var1, $corte,$topmar);
$pdf->Output();


?>