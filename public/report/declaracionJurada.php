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
		global $tituloHR1;
                global $tituloHR2;
                global $tituloHR3;
                global $tituloHR4;
                global $p_nroDoc;
                global $direcreferencia;
                global $disdirfiscal;
                global $documento;
                global $vusuario; 
                global $fechareg; 
                global $sectorPredio;
                global $codPredio;
                global $grupoUrbano;
                global $nombreVia;
                global $direccion;
                global $nautova;
                global $nporcen;
                global $tipovia;
                global $tipogrupohab;
                global $regimen;
                global $estadocons;
                global $condicion;
                global $usogen;
                global $dirPredio;
                global $dirRefPredio;
                global $tipopredio;
                global $ctippre;
                global $arearustico;
                global $TipoCabecera;
               
                
		
	if($TipoCabecera=='1'){
		$lw = $marIni;
		$ln = $topIni;
		$ls = 3;
		$lh = 0;
		//MArca de Agua
		$this->Image('../img/marca/escudomarcaagua4.png',$marcaAguaL1-20,$marcaAguaL2-60,$marcaAguaL3+50,$marcaAguaL4+50,'PNG');
		
		//Logo
		$this->SetFont('narrow','',8);
		//$this->SetFillColor(206,232,212); //verde agua
		$this->SetFillColor(188,217,190); //verde agua
		$this->SetXY($lw-10,$lh);
		$this->Cell(220,6,"",0,1,'L',true);
		$this->SetFillColor(243);
		$this->SetFont('arial','B',9);
		$this->SetXY($lw-10,$lh);
		$this->MultiCell(50,6,'Plaza de Armas S/N',1,'C');
		$this->SetXY($lw+40,$lh);
		$this->MultiCell(50,6,'Huaral - Lima - Peru',1,'C');
		$this->SetXY($lw+90,$lh);
		$this->MultiCell(50,6,'www.munihuaral.gob.pe',1,'C');
		$this->SetXY($lw+140,$lh);
		$this->MultiCell(60,6,'Telefono 264-3617 ',1,'C');
		//$this->RoundedRect(-10, 0, 220, 8, 1,'S');
		$this->Image('../img/marca/munipuentepiedra.png',$lw,$ln-3,52,15,'PNG');
		
		//$this->SetFillColor(145,185,154); //verde agua
		$this->SetFillColor(255);
		$lh = $lh+6;
		$this->SetXY($lw+70,$lh);
		$this->Cell(160,12,"",0,1,'L',true);
		$this->SetFont('arial','B',12);
		$this->SetXY($lw+35,$lh);
		$this->MultiCell(160,10,$tituloHR1,0,'C');
                /*$this->SetXY($lw+65,$lh+4);
		$this->MultiCell(100,10,$tituloHR2,0,'C');*/
                $this->SetXY($lw+35,$lh+4);
                $this->SetFont('narrow','',8);
		$this->MultiCell(160,10,$tituloHR3,0,'C');
                $this->SetXY($lw+35,$lh+8);
                $this->SetFont('arial','B',12);
		$this->MultiCell(160,10,$tituloHR4,0,'C');
		
                $this->SetXY($lw+150,$lh);
                $this->SetFont('arial','B',20);
		$this->MultiCell(60,10,'HR',0,'C');
                $this->SetXY($lw+150,$lh+4);
                $this->SetFont('arial','B',6);
		$this->MultiCell(60,10,'(Hoja Resumen)',0,'C');
                $this->SetXY($lw+150,$lh+7);
                $this->SetFont('arial','B',11);
		$this->MultiCell(60,10,$p_nroDoc,0,'C');
		
                
		//definimos  ancho y color de rectangulo
		$lh = $lh+17;
		
		$nombreContrib1=utf8_decode($nombreContrib);
		$this->SetLineWidth(0.3);
		$this->SetFillColor(255);
		$this->RoundedRect($lw-5, $lh, 110, 20, 1, '');
		$this->SetFont('narrow','',12);
		$this->SetXY($lw-5,$lh+2);
		$this->MultiCell(110,5,trim(utf8_decode($nombreContrib1)),0,'L');
		$this->SetFont('narrow','',8);
		$this->SetXY($lw-5,$lh+7);
		$this->MultiCell(110,4,trim(utf8_decode($documento)),0,'L');
                $this->SetXY($lw-5,$lh+11);
		$this->MultiCell(110,4,trim(utf8_decode($direccionContrib.' - '.$disdirfiscal)),0,'L');
                $this->SetFont('narrow','',8);
		$this->SetXY($lw-5,$lh+15);
		$this->MultiCell(110,4,trim(utf8_decode($direcreferencia)),0,'L');
                
		
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+110,$lh);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh);
		$this->MultiCell(40,4,'Codigo Contribuyente',0,'J');
		$this->SetXY($lw+152,$lh);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$codigoContrib,0,'R');
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+4);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+4);
		$this->MultiCell(40,4,'Fecha Registro',0,'J');
		$this->SetXY($lw+152,$lh+4);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+4);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$fechareg,0,'R');
		
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+8);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+8);
		$this->MultiCell(40,4,'Usuario Registro',0,'J');
		$this->SetXY($lw+152,$lh+8);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+8);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$vusuario,0,'R');
                
                $this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+12);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+12);
		$this->MultiCell(40,4,'Usuario Impresion',0,'J');
		$this->SetXY($lw+152,$lh+12);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+12);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$usuario,0,'R');
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+16);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+16);
		$this->MultiCell(40,4,'Fecha Impresion',0,'J');
		$this->SetXY($lw+152,$lh+16);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+16);
		$this->SetFont('f25bankprinter','',8);
		$this->MultiCell(43,4,$fechaImpresion,0,'R');
		$lh = $lh +22;
		
		$ls=-5;
		$this->SetFont('narrow','',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[0],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[0],4,'CODIGO PREDIO',1,'C');
		
		
		$ls = $colCab[0]-5;
		$this->SetFont('narrow','',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[1],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[1],4,'UBICACION DEL PREDIO',1,'C');

		$ls = $colCab[0]+$colCab[1]-5;
		$this->SetFont('narrow','',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[2],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[2],4,'% COND',1,'C');
	
		
		$ls = $colCab[0]+$colCab[1]+$colCab[2]-5;
		$this->SetFont('narrow','',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[3],5,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[3],4,'AUTOVALUO',1,'C');
		
        }elseif($TipoCabecera=='2'){
            $lw = $marIni;
		$ln = $topIni;
		$ls = 3;
		$lh = 0;
		//MArca de Agua
		$this->Image('../img/marca/escudomarcaagua4.png',$marcaAguaL1-20,$marcaAguaL2-60,$marcaAguaL3+50,$marcaAguaL4+50,'PNG');
		
		//Logo
		$this->SetFont('narrow','',8);
		//$this->SetFillColor(206,232,212); //verde agua
		$this->SetFillColor(188,217,190); //verde agua
		$this->SetXY($lw-10,$lh);
		$this->Cell(220,6,"",0,1,'L',true);
		$this->SetFillColor(243);
		$this->SetFont('arial','B',9);
		$this->SetXY($lw-10,$lh);
		$this->MultiCell(50,6,'Plaza de Armas S/N',1,'C');
		$this->SetXY($lw+40,$lh);
		$this->MultiCell(50,6,'Huaral - Lima - Peru',1,'C');
		$this->SetXY($lw+90,$lh);
		$this->MultiCell(50,6,'www.munihuaral.gob.pe',1,'C');
		$this->SetXY($lw+140,$lh);
		$this->MultiCell(60,6,'Telefono 264-3617',1,'C');
		//$this->RoundedRect(-10, 0, 220, 8, 1,'S');
		$this->Image('../img/marca/munipuentepiedra.png',$lw,$ln-3,52,15,'PNG');
		
		//$this->SetFillColor(145,185,154); //verde agua
		$this->SetFillColor(255);
		$lh = $lh+6;
		$this->SetXY($lw+70,$lh);
		$this->Cell(160,12,"",0,1,'L',true);
		$this->SetFont('arial','B',12);
		$this->SetXY($lw+35,$lh);
		$this->MultiCell(160,10,$tituloHR1,0,'C');
                /*$this->SetXY($lw+65,$lh+4);
		$this->MultiCell(100,10,$tituloHR2,0,'C');*/
                $this->SetXY($lw+35,$lh+4);
                $this->SetFont('narrow','',8);
		$this->MultiCell(160,10,$tituloHR3,0,'C');
                $this->SetXY($lw+35,$lh+8);
                $this->SetFont('arial','B',12);
		$this->MultiCell(160,10,$tituloHR4,0,'C');
		
                
                
                if($ctippre == '1000000090'){
                    $Texto = 'PU';
                    $Texto2 = '(Predio Urbano)';
                }else{
                    $Texto = 'PR';
                    $Texto2 = '(Predio Rustico)';
                }
                
                
                $this->SetXY($lw+150,$lh);
                $this->SetFont('arial','B',20);
		$this->MultiCell(60,10,$Texto,0,'C');
                $this->SetXY($lw+150,$lh+4);
                $this->SetFont('arial','B',6);
		$this->MultiCell(60,10,$Texto2,0,'C');
                $this->SetXY($lw+150,$lh+7);
                $this->SetFont('arial','B',11);
		$this->MultiCell(60,10,$p_nroDoc,0,'C');
		
                
		//definimos  ancho y color de rectangulo
		$lh = $lh+17;
		
		$nombreContrib1=utf8_decode($nombreContrib);
		$this->SetLineWidth(0.3);
		$this->SetFillColor(255);
		$this->RoundedRect($lw-5, $lh, 110, 20, 1, '');
		$this->SetFont('narrow','',12);
		$this->SetXY($lw-5,$lh+2);
		$this->MultiCell(110,5,trim(utf8_decode($nombreContrib1)),0,'L');
		$this->SetFont('narrow','',8);
		$this->SetXY($lw-5,$lh+7);
		$this->MultiCell(110,4,trim(utf8_decode($documento)),0,'L');
                $this->SetXY($lw-5,$lh+11);
		$this->MultiCell(110,4,trim(utf8_decode($direccionContrib.' - '.$disdirfiscal)),0,'L');
                $this->SetFont('narrow','',8);
		$this->SetXY($lw-5,$lh+15);
		$this->MultiCell(110,4,trim(utf8_decode($direcreferencia)),0,'L');
		
		//$qrcode = new QRcode(utf8_encode('Hola'), $err);
		//$qrcode->disableBorder();
		
		//aqui va el codigo de barras
		//$qrcode->displayHTML();
		//$this->SetXY($lw+10,$lh+12);
		//$qrcode->displayPNG(50);
		//$this->Image(,$lw,$ln,62,18,'PNG');
		
		//$this->SetXY($lw,$lh + 17 );
		//$this->Image('../img/marca/barraBoucher_2dat.gif',$lw-3,$lh+6,13,13,'GIF','');
		
		
                //$this->RoundedRect($lw+110, $lh, 85, 20, 1, '');
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+110,$lh);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh);
		$this->MultiCell(40,4,'Codigo Contribuyente',0,'J');
		$this->SetXY($lw+152,$lh);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$codigoContrib,0,'R');
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+4);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+4);
		$this->MultiCell(40,4,'Fecha Registro',0,'J');
		$this->SetXY($lw+152,$lh+4);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+4);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$fechareg,0,'R');
		
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+8);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+8);
		$this->MultiCell(40,4,'Usuario Registro',0,'J');
		$this->SetXY($lw+152,$lh+8);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+8);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$vusuario,0,'R');
                
                $this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+12);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+12);
		$this->MultiCell(40,4,'Usuario Impresion',0,'J');
		$this->SetXY($lw+152,$lh+12);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+12);
		$this->SetFont('f25bankprinter','',10);
		$this->MultiCell(43,4,$usuario,0,'R');
		
		$this->SetFont('arial','',8);
		$this->SetXY($lw+110,$lh+16);
		$this->Cell(42,4,"",1,1,'L',true);
		$this->SetXY($lw+112,$lh+16);
		$this->MultiCell(40,4,'Fecha Impresion',0,'J');
		$this->SetXY($lw+152,$lh+16);
		$this->Cell(43,4,"",1,1,'L',true);
		$this->SetXY($lw+152,$lh+16);
		$this->SetFont('f25bankprinter','',8);
		$this->MultiCell(43,4,$fechaImpresion,0,'R');
		$lh = $lh +22;
		
                
		$ls=-5;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[6],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[6],4,'Sector',1,'C');
                
                $ls = $colCab[6]-5;
                        
               $this->SetFont('narrow','',8);
                $this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[5],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[5],4,$sectorPredio,1,'L');
		
               
                $ls=$colCab[6]+$colCab[5]-5;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[0],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[0],4,'Grupo Hab',1,'C');
                
                $ls = $colCab[6]+$colCab[5]+$colCab[0]-5;
                        
               $this->SetFont('narrow','',8);
                $this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[7],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[7],4,$grupoUrbano,1,'L');
		
                             
		
		$ls = $colCab[0]+$colCab[1]-5;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[2],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[2],4,'Via',1,'C');
		
		$ls = $colCab[0]+$colCab[1]+$colCab[2]-5;
		$this->SetFont('narrow','',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[3],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[3],4,substr($tipovia.' '.$nombreVia,0,25),1,'L');
		
                $lh+=4;
                $ls=-5;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[6],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[6],4,'Predio',1,'C');
                
                $ls = $colCab[6]-5;
                        
               $this->SetFont('narrow','',8);
               

                $this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[8],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[8],4,$codPredio.' - '.$dirPredio,1,'L');
                
                $lh+=4;
                $ls=-5;
		$this->SetFont('arial','B',8);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[6],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[6],4,'Ref',1,'C');
                
                $ls = $colCab[6]-5;
                        
               
                $this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[8],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[8],4,$dirRefPredio,1,'L');
                
                 
                
                $lh+=4;
                $ls=-5;
		$this->SetFont('arial','B',7);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,'COND. PROPIEDAD',1,'C');
                
                $ls = $colCab[9]-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,'EST. CONTRUCCION',1,'C');
                
                 $ls = $colCab[9]*2-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[10],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[10],3,'TIPO PREDIO',1,'C');
                
                 $ls = $colCab[9]*2+$colCab[10]-5;
                
               
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,'USO',1,'C');
                
                 $ls = $colCab[9]*3+$colCab[10]-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,'REGIMEN',1,'C');
                
                 $ls = $colCab[9]*4+$colCab[10]-5;
                
               
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,'% CONDOMINANTE',1,'C');
                
                $lh+=3;
                $ls=-5;
		$this->SetFont('narrow','',7);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,substr($condicion,0,25),1,'C');
                
                $ls = $colCab[9]-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,substr($estadocons,0,25),1,'C');
                
                 $ls = $colCab[9]*2-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[10],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[10],3,substr($tipopredio,0,25) ,1,'C');
                
                 $ls = $colCab[9]*2+$colCab[10]-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,substr($usogen,0,25),1,'C');
                
                 $ls = $colCab[9]*3+$colCab[10]-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,  substr($regimen,0,25),1,'C');
                
                 $ls = $colCab[9]*4+$colCab[10]-5;
                
                
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[9],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[9],3,$nporcen,1,'R');
                
                $lh+=3;
                $ls=-5;
		$this->SetFont('arial','B',7);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[11],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[11],3,'DATOS DE LA CONSTRUCCION',1,'C'); 
                
                $lh+=3;
                $ls=-5;
		$this->SetFont('narrow','',7);
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[12],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[12],6,'PISO',1,'C');
                
                $ls=$colCab[12]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[12],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[12],3,  utf8_decode('ANTIG AÑOS'),1,'C');
                
                $ls=$colCab[12]*2-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[12],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[12],6,  utf8_decode('CLAS'),1,'C');
                
                $ls=$colCab[12]*3-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[12],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[12],6,  utf8_decode('MAT'),1,'C');
                
                $ls=$colCab[12]*4-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[12],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[12],6,  utf8_decode('EST'),1,'C');
                
                $ls=$colCab[12]*5-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[13],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[13],6,  utf8_decode('CATEGORIAS'),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[17],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[17],3,  utf8_decode('VALOR INITARIO POR M2'),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*1+$colCab[17]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[13],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[13],3,  utf8_decode('INCREMENTO   5% (*)'),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*2+$colCab[17]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[15],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[15],3,  utf8_decode('DEPRECIACION'),1,'C');
                
                $this->SetXY($lw+$ls,$lh+3);
		//$this->Cell($colCab[16],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh+3);
		$this->MultiCell($colCab[16],3,  utf8_decode('%'),1,'C');
                $this->SetXY($lw+$ls+$colCab[16],$lh+3);
		//$this->Cell($colCab[13],3,"",1,1,'L',true);
		$this->SetXY($lw+$ls+$colCab[16],$lh+3);
		$this->MultiCell($colCab[13],3,  utf8_decode('S/.'),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*2+$colCab[17]+$colCab[15]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[13],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[13],3,  utf8_decode('VALOR UNITARIO DEPRECIADO'),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*3+$colCab[17]+$colCab[15]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[13],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[13],3,  utf8_decode('AREA CONSTRUIDA'),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*4+$colCab[17]+$colCab[15]-5;
		
		$this->SetXY($lw+$ls,$lh);
		//$this->Cell($colCab[13],6,"",1,1,'L',true);
		$this->SetXY($lw+$ls,$lh);
		$this->MultiCell($colCab[13],3,  utf8_decode('VALOR DE CONSTRUCCION'),1,'C');

		
        }
        
		
	}
	
	function Footer(){
		global $marini;
		global $nro_nec;
		global $ano_eje;
		global $nombreGispAdmincon;
                global $style;
                global $lp;
                global $p_nautova;
                global $p_nbaseim;
                global $p_nimpanu;
                global $p_ncosemi;
                global $p_totalPagar;
                global $nvalpre;
                global $nvalter;        
                global $nvalpis;
                global $nvalins;
                global $nporafe;
                global $areaterreno;
                global $areacomun;
                global $arancel;
                global $dfecadq;
                global $dfecdes;
                global $dafecta;
                global $areaconstruc ;
                global $nvalpistotal;
                global $nvalinstotal;
                global $ctippre;
                global $arearustico;
                global $TipoCabecera;
               
                
		
	if($TipoCabecera=='1'){
                
                
                $h1 = -35;
                $h2 = -35;
                $x = $marini+5;
                $this->SetXY($x,$h1 );
		//$var1 = $this->GetY();
		//if($corte = $this->CheckPageBreak($var1)) $h = $lp;
		$this->SetFont('Arial','',8);
		$hh = $lp+20;
                $this->RoundedRect($x+90, $hh+6, 20, 25, 1, '');
		$Texto = "(*) Predio Descargado";
		$this->SetXY($x,$h1);
		$this->MultiCell(60,4,$Texto,0,'J');
                
                $hh = $lp+36;
                $Texto = "Firma del Propietario o Representante Legal";
		$this->SetXY($x,$h1+=18);
                $this->Line($x, $hh-1 , 85, $hh-1, $style);
		$this->MultiCell(60,4,$Texto,0,'J');
                
                $Texto = "Nombre : ";
		$this->SetXY($x,$h1+=5);
                $this->Line($x+16, $hh+7 , 85, $hh+7, $style);
		$this->MultiCell(15,3,$Texto,0,'R');
                $Texto = "DNI : ";
		$this->SetXY($x,$h1+=5);
                $this->Line($x+16, $hh+12 , 85, $hh+12, $style);
		$this->MultiCell(15,3,$Texto,0,'R');
                
                $h2 = $h2;
                $Texto = "Total de Autovaluo : ";
		$this->SetXY($x+140,$h2);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+170,$h2);
		$this->MultiCell(30,4,  number_format($p_nautova,'2',',',' '),1,'R');
                $Texto = "Base Imponible : ";
		$this->SetXY($x+140,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+170,$h2);
		$this->MultiCell(30,4,number_format($p_nbaseim,'2',',',' '),1,'R');
                $Texto = "Impuesto Predial : ";
		$this->SetXY($x+140,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+170,$h2);
		$this->MultiCell(30,4,number_format($p_nimpanu,'2',',',' '),1,'R');
                $Texto = "Gastos de Emision : ";
		$this->SetXY($x+140,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+170,$h2);
		$this->MultiCell(30,4,number_format($p_ncosemi,'2',',',' '),1,'R');
                $Texto = "Total a Pagar : ";
		$this->SetXY($x+140,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+170,$h2);
		$this->MultiCell(30,4,number_format($p_totalPagar,'2',',',' '),1,'R');
        }elseif($TipoCabecera=='2'){
            
                $h1 = -40;
                $h2 = -40;
                $x = $marini;
                $this->SetXY($x,$h1 );
		//$var1 = $this->GetY();
		//if($corte = $this->CheckPageBreak($var1)) $h = $lp;
		
                $hh = $lp+5;
                $xx = $x+5;
                $this->SetFont('narrow','',5);
                //$this->RoundedRect($xx, $hh+6, 20, 4, 1, '');
                
		$Texto = "CLASIFICACION";
		$this->SetXY($xx,$h1-1);
		$this->Cell(25,3,'',1,'J');
                $this->SetXY($xx,$h1-1);
		$this->MultiCell(25,3,$Texto,0,'J');
                
                $this->SetFont('narrow','',6);
		$this->SetXY($xx,$h1+3-1);
		$this->Cell(25,21,'',1,'J');
                $Texto = "1 CASA HABITACION";
                $this->SetXY($xx,$h1+3-1);
		$this->MultiCell(25,3,$Texto,0,'L');
                $Texto = "2 TIENDA DEPOSITO ALMACEN";
                $this->SetXY($xx,$h1+6-1);
		$this->MultiCell(25,3,$Texto,0,'L');
                $Texto = "3 EDIFICIO (PREDIO EN EDIFICIO)";
                $this->SetXY($xx,$h1+12-1);
		$this->MultiCell(25,3,$Texto,0,'L');
                $Texto = "4 CLINICA, HOSPITAL, CINE, INDUSTRIA, TALLER";
                $this->SetXY($xx,$h1+18-1);
		$this->MultiCell(25,3,$Texto,0,'L');
                
                $this->SetFont('narrow','',5);
                $Texto = "MATERIAL";
		$this->SetXY($xx+25,$h1-1);
		$this->Cell(15,3,'',1,'J');
                $this->SetXY($xx+25,$h1-1);
		$this->MultiCell(15,3,$Texto,0,'J');
                
                $this->SetFont('narrow','',6);
                $this->SetXY($xx+25,$h1+3-1);
		$this->Cell(15,21,'',1,'J');
                $Texto = "1 CONCRETO";
                $this->SetXY($xx+25,$h1+3-1);
		$this->MultiCell(15,3,$Texto,0,'L');
                $Texto = "3 LADRILLO";
                $this->SetXY($xx+25,$h1+6-1);
		$this->MultiCell(15,3,$Texto,0,'L');
                $Texto = "3 ADOBE";
                $this->SetXY($xx+25,$h1+9-1);
		$this->MultiCell(15,3,$Texto,0,'L');
                
                $this->SetFont('narrow','',5);
                $Texto = "EST CONSTRUCCION";
		$this->SetXY($xx+40,$h1-1);
		$this->Cell(19,3,'',1,'J');
                $this->SetXY($xx+40,$h1-1);
		$this->MultiCell(19,3,$Texto,0,'J');
                
                $this->SetFont('narrow','',6);
                $this->SetXY($xx+40,$h1+3-1);
		$this->Cell(19,21,'',1,'J');
                $Texto = "1 MUY BUENO";
                $this->SetXY($xx+40,$h1+3-1);
		$this->MultiCell(19,3,$Texto,0,'L');
                $Texto = "2 BUENO";
                $this->SetXY($xx+40,$h1+6-1);
		$this->MultiCell(19,3,$Texto,0,'L');
                $Texto = "3 REGULAR";
                $this->SetXY($xx+40,$h1+9-1);
		$this->MultiCell(19,3,$Texto,0,'L');
                $Texto = "4 MALO";
                $this->SetXY($xx+40,$h1+12-1);
		$this->MultiCell(19,3,$Texto,0,'L');
                
                $xx = $marini+5;
                $this->SetFont('narrow','',8);
                $Texto = "(*) Construcciones a partir del 5to Piso";
                $this->SetXY($xx,$h1+25-1);
		$this->MultiCell(60,3,$Texto,0,'L');
                $Texto = "(**) Valores Estimados";
                $this->SetXY($xx,$h1+28-1);
		$this->MultiCell(60,3,$Texto,0,'L');
                
                $this->SetXY($xx,$h1+32-1);
		$this->Cell(60,6,'',1,'J');
                $Texto = "Valores Unitarios Oficiales de Edificacion del Ministerio de Vivienda";
                $this->SetXY($xx,$h1+32-1);
		$this->MultiCell(60,3,$Texto,0,'L');
                
                
                $x = $marini+35;
                 
                
                $hh = $lp+5;
                $xx = $x+30;
                $this->SetFont('narrow','',8);
                $this->RoundedRect($xx, $hh+6, 55, 4, 1, '');
                
		$Texto = "Total Area Contruida";
		$this->SetXY($xx,$h1-1);
		$this->MultiCell(60,3,$Texto,0,'J');
                
                $this->SetXY($xx+30,$h1-1);
		$this->MultiCell(60,3,$areaconstruc,0,'J');
                /*
                $Texto = "DATOS DEL TERRENO";
		$this->SetXY($xx,$h1+4-1);
		$this->MultiCell(55,3,$Texto,0,'C'); */
                
                 $this->RoundedRect($xx, $hh+11, 55, 9, 1, '');
                
                 
               
                
                if($ctippre == '1000000090'){
                    $Texto1 = 'Area (m2)';
                    $Texto2 = 'Arancel (m2)';
                    $Texto3 = $areaterreno;
                    
                }else{
                    $Texto1 = 'Hectareas (ha)';
                    $Texto2 = 'Arancel (ha)';
                    $Texto3 = $arearustico;
                    
                }

                $this->SetFont('narrow','',6);
                //$Texto = "Area (m2)";
		$this->SetXY($xx,$h1+5-1);
		$this->MultiCell(18,3,$Texto1,0,'C');
                $Texto = "Area Comun";
		$this->SetXY($xx+18,$h1+5-1);
		$this->MultiCell(18,3,$Texto,0,'C');
                //$Texto = "Arancel (m2)";
		$this->SetXY($xx+36,$h1+5-1);
		$this->MultiCell(18,3,$Texto2,0,'C');
                
                $this->SetFont('narrow','',7);
                $Texto = "(";
		$this->SetXY($xx,$h1+9-1);
		$this->MultiCell(2,3,$Texto,0,'L');
                $Texto = $areaterreno;
		$this->SetXY($xx,$h1+9-1);
		$this->MultiCell(18,3,$Texto3,0,'C');
                $this->SetFont('narrow','',7);
                $Texto = "+";
		$this->SetXY($xx+16,$h1+9-1);
		$this->MultiCell(2,3,$Texto,0,'L');
                $Texto = $areacomun;
		$this->SetXY($xx+18,$h1+9-1);
		$this->MultiCell(18,3,$Texto,0,'C');
                $Texto = ") X ";
		$this->SetXY($xx+34,$h1+9-1);
		$this->MultiCell(5,3,$Texto,0,'L');
                $Texto = $arancel;
		$this->SetXY($xx+36,$h1+9-1);
		$this->MultiCell(18,3,$Texto,0,'C');
                
                
                
                
                $this->SetFont('Arial','',8);
		$hh = $lp+20;
                $this->RoundedRect($x+90, $hh+6, 20, 25, 1, '');
                
                $hh = $lp+36;
                $Texto = "Firma del Propietario o Representante Legal";
		$this->SetXY($x+30,$h1+=23);
                $this->Line($x+30, $hh-1 , 124, $hh-1, $style);
		$this->MultiCell(60,4,$Texto,0,'J');
                
                $Texto = "Nombre : ";
		$this->SetXY($x+30,$h1+=5);
                $this->Line($x+46, $hh+7 , 124, $hh+7, $style);
		$this->MultiCell(15,3,$Texto,0,'R');
                $Texto = "DNI : ";
		$this->SetXY($x+30,$h1+=5);
                $this->Line($x+46, $hh+12 , 124, $hh+12, $style);
		$this->MultiCell(15,3,$Texto,0,'R');
                
                $h2 = $h2;
                $x = $marini;
                $this->SetFont('narrow','',7); 
                $Texto = "Valor Total Contruccion : ";
		$this->SetXY($x+145,$h2);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$nvalpistotal ,1,'R');
                $Texto = "Area Comun Construida : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$areacomun,1,'R');
                $Texto = "(**) Otras Instalaciones : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$nvalinstotal,1,'R');
                $Texto = "Valor del Terreno : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$nvalter,1,'R');
                $Texto = "Autovaluo : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$nvalpre,1,'R');
                $Texto = "% del Autovaluo : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$nporafe,1,'R');
                $Texto = "Fecha Adquisicion : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$dfecadq,1,'R');
                $Texto = "Fecha Descargo : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$dfecdes,1,'R');
                $Texto = "Fecha Cal/Recal : ";
		$this->SetXY($x+145,$h2+=4);
		$this->MultiCell(30,4,$Texto,0,'R');
                $this->SetXY($x+175,$h2);
		$this->MultiCell(30,4,$dafecta,1,'R');
        }
        
}
	
	function WriteResumen($x,$h1, $corte,$topmar){
		//datos SQL
		global $Coneccion;
                global $lp;

		//datos DOC
		global $marini;
		global $topmar;
		global $repositorioFirmas;
		
                
		
	}
	
	function CheckPageBreak($h){
            global $TipoCabecera;
               
                
		
            if($TipoCabecera=='1'){
		if($h >= 105){
			$this->AddPage();	
			return 1;
		}else{
			$this->SetY($h);
			return 0;
		}
            }elseif($TipoCabecera=='2'){
                if($h >= 95){
			$this->AddPage();	
			return 1;
		}else{
			$this->SetY($h);
			return 0;
		}
            }
                /*if($h >= 250){
			$this->AddPage('P','mm','A4');
			return true;
		}*/
	}
	
	
}

$pdf = new PDF('L','mm','A5');
$pdf->SetDisplayMode('fullpage');

$usuario = $_GET['usuario'];
$contrib =  $_GET['p_mperson'];
$p_mhresum =  $_GET['p_mhresum'];
$predio =  $_GET['p_ccodpre'];

$nro_contrib = $_GET['p_mperson'];
$p_cperiod = $_GET['p_cperiod'];
//$p_cperiod = '2014';


$periodos = explode('-',$p_cperiod);
$periodos= array_unique($periodos);
$p_cperiod = "";
$NPeriodos = count($periodos);
//echo "PERIODOS ".$NPeriodos." PERIDDITO ".  var_dump($periodos);
//die();
$filtroConsulta = "";
if($p_mhresum<>'-1'){
    $filtroConsulta = " AND t1.idsigma='".$p_mhresum."'";
}



/*
 * AQUI EMPIEZA EL HR HE IMPRIME SEGUN PARAMETROS
 */
        
$Rs_grabarCAB = new TSPResult($ConeccionRatania,"");
$Rs_grabarCAB->Poner_MSQL("select t1.idsigma as mhresum_actual, 
t1.ctipdat as tipo, 
( select  public.mconten.vdescri from public.mconten WHERE idsigma=t1.cmotivo) as motivo, 
t1.vnrodoc as dj,
(t2.vpatern||' '||t2.vmatern||' '||t2.vnombre) as nombrecontrib,
(t2.cdenomi||' '||t2.vdirecc||' '||t2.vnumero||' '||t2.vmanzan||' '||t2.vlote) dirfiscal,
t2.vreferen as refdirfiscal,
(select vnombres from registro.mubigeo WHERE idsigma=t2.mubigeo) as disdirfiscal,
( COALESCE((select public.mconten.vdescri from public.mconten WHERE idsigma=t2.ctipdoc),' DNI ') )||' '||COALESCE(t2.vnrodoc,'') as documento,
t2.idsigma as cidpers,
(COALESCE((select u.usuario from seguridad.usuario as u  where u.cidusuario=t1.vusernm),t1.vusernm) ) as vusuario,
t1.ddatetm as fechareg
from registro.mhresum as t1,
public.mperson as t2
where 
t1.mperson=t2.idsigma
AND t1.mperson='".$nro_contrib."'
$filtroConsulta
--AND t1.nestado='1' 
");
$Rs_grabarCAB->pg_Poner_Esquema("tesoreria");

$Rs_grabarCAB->executeMSQL();


//echo "<br><br>CONSULTA REQ UPDATE <br> ".$Rs_grabarCAB->Escribir_Consulta();

$NCAB = 0;
$numRowsCAB = $Rs_grabarCAB->pg_Num_Rows();
//echo $numRowsCAB;
while($NCAB < $numRowsCAB){

    
//echo "HOLAAAAAAAAAAAAAAAA";
//echo "<br><br>CONSULTA REQ UPDATE <br> ".$Rs_grabar->Escribir_Consulta();			
$rowCAB= $Rs_grabarCAB->pg_Get_Row();

$RUC='20131366702';
$numero_recibo='860100100014';
$codigoContrib=$rowCAB['cidpers'];
$mhresum_actual=$rowCAB['mhresum_actual'];
$nombreContrib=$rowCAB['nombrecontrib'];
$direccionContrib=$rowCAB['dirfiscal'];
$direcreferencia=$rowCAB['refdirfiscal'];
$disdirfiscal=$rowCAB['disdirfiscal'];
$documento=$rowCAB['documento']; 
$vusuario=$rowCAB['vusuario']; 
$fechareg=$rowCAB['fechareg']; 
$c_motivo = $rowCAB['motivo'];
$c_nrodj = $rowCAB['dj'];


$TipoCabecera='1';
//$cajero='NDIESTRO';
//$usuario='NDIESTRO';
//$fechaEmision='12-12-2013 15:56';

//echo 'HOLA '.$fechaProyectado ;
//echo 'USUARIO '.$usuario ;
for($i=0; $i<$NPeriodos; $i++){
    $p_cperiod = $periodos[$i];
    
$tituloHR1 = "DECLARACION JURADA IMPUESTO PREDIAL ".$p_cperiod;
$tituloHR2 = "IMPUESTO PREDIAL ".$p_cperiod;
$tituloHR3 = "Decreto Legislativo 776 Hoja de Actualizacion de Valores";
$tituloHR4 = "MOTIVO ".$c_motivo;
$p_nroDoc = "2014001452";
$p_nroDoc = "Nro ".$c_nrodj;


$Rs_grabar = new TSPResult($ConeccionRatania,"");
$Rs_grabar->Poner_MSQL("SELECT t1.nautova,t1.nbaseim,t1.nimpanu,t1.ncosemi
FROM registro.dresumn as t1
WHERE t1.cperiod='".$p_cperiod."'
AND t1.mhresum='".$mhresum_actual."' 
    ");
$Rs_grabar->pg_Poner_Esquema("tesoreria");

$Rs_grabar->executeMSQL();
//echo $Rs_grabar->Escribir_Consulta();
$row= $Rs_grabar->pg_Get_Row();
$p_nautova = $row['nautova'];
$p_nbaseim = $row['nbaseim'];
$p_nimpanu = $row['nimpanu'];
$p_ncosemi = $row['ncosemi'];
$p_totalPagar = $row['nimpanu'];


$Rs_grabar = new TSPResult($ConeccionRatania,"");
$Rs_grabar->Poner_MSQL("SELECT t1.idsigma as codpredio,
('Sector ') as sectorpredio,
(select vnombre from registro.mpoblad where idsigma = t1.mpoblad ) as grupourbano,
(select vnombre from registro.mviadis where idsigma = t1.mviadis ) as nombrevia,
TRIM((CASE WHEN COALESCE(TRIM(t1.dnumero),'')='' THEN '' ELSE 'Nro '||t1.dnumero  END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dbloque),'')='' THEN ''  ELSE 'Blq. '||t1.dbloque END )||' '||
(CASE WHEN COALESCE(TRIM(t1.ddepart),'')='' THEN ''  ELSE 'Dpto. '||t1.ddepart END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dinteri),'')='' THEN ''  ELSE 'Int. '||t1.dinteri END )||' '||
(CASE WHEN COALESCE(TRIM(t1.destaci),'')='' THEN ''  ELSE 'Est. '||t1.destaci END )||' '||
(CASE WHEN COALESCE(TRIM(t1.ddeposi),'')='' THEN ''  ELSE 'Depos. '||t1.ddeposi END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dmanzan),'')='' THEN ''  ELSE 'Mz. '||t1.dmanzan END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dnlotes),'')='' THEN ''  ELSE 'Lte. '||t1.dnlotes END ) ) as direccion,
t1.vdirpre,
t2.nporcen,
t3.nvalpre,
t2.nestado,
t1.mviadis,
(select con.vobserv from registro.mviadis as via,
public.mconten as con where via.idsigma = t1.mviadis
AND via.ctipvia=con.idsigma 
GROUP BY con.vobserv) as tipovia,
(select con.vobserv from registro.mpoblad as pob,
public.mconten as con where pob.idsigma = t1.mpoblad
AND pob.ctipcen=con.idsigma 
GROUP BY con.vobserv) as tipogrupohab
FROM registro.mpredio as t1,
registro.dpredio as t2,
registro.vpredio as t3
WHERE t1.idsigma=t2.mpredio
AND t2.idsigma=t3.dpredio
AND t3.cperiod='".$p_cperiod."'
AND t2.mhresum='".$mhresum_actual."' 
ORDER BY t2.nestado DESC
    ");
$Rs_grabar->pg_Poner_Esquema("tesoreria");
//echo $Rs_grabar->Escribir_Consulta();
$Rs_grabar->executeMSQL();



date_default_timezone_set('America/Lima');
$fechaImpresion=strftime('%d/%m/%Y %H:%M:%S'); 
$nroDoc='42215638';
//$fechaProyectado = '13/12/2013';
//$fechaRecargos = '13/12/2013';
if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'Q';

$colCab = array('22','134','14','30','220');
$colSubCab[0] = array('35','8','18');
$colSubCab[1] = array('18','20','25');
$colSubCab[2] = array('18','8','25');

$topIni = 10;
$marIni = 10; 
$topmar = 49;
$lp = 96;
//$lw=10;


//$pdf->AutoPrint();

$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->SetBottonMargin(140);
$pdf->SetFont('Arial','B',16);

$lw = $marIni;
$var1 = $topmar;
//$var1 = $topmar+5;
$pdf->SetXY($lw,$lh = $var1);


	$N = 0;
	$numRows = $Rs_grabar->pg_Num_Rows();
        
	while($N < $numRows){
		
		$row= $Rs_grabar->pg_Get_Row();
                
                $descargo = '';
                if(trim($row['nestado'])=='0'){
                    $descargo = '*';
                }
                
                $sectorPredio = trim($row['sectorpredio']);
                $codPredio = trim($row['codpredio']);
                $grupoUrbano = $row['grupourbano'];
                $nombreVia = $row['nombrevia'];
                $direccion = $row['direccion'];
                $nautova = $row['nvalpre'];
                $nporcen = $row['nporcen'];
                $tipovia = $row['tipovia'];
                $tipogrupohab = $row['tipogrupohab'];
                $ls = -5;
                
                $pdf->SetFont('narrow','',10);
                $pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[0],5,$descargo.' '.$codPredio,1,'R');
                $ls += $colCab[0];
                $pdf->SetFont('narrow','',8);
                $pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[1],5,$sectorPredio.' '.
                        $tipogrupohab.' '.
                        $grupoUrbano.' '.
                        $tipovia.' '.
                        $nombreVia.' '.
                        $direccion,1,'L');
                $ls += $colCab[1];
                $pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[2],5,  number_format($nporcen,'2',',',' '),1,'R');
                $ls += $colCab[2];
                $pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[3],5,number_format($nautova,'2',',',' '),1,'R');
                
                $var1 = $pdf->GetY();
		if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar-5;
                
                $lh = $lh+5;
			$Rs_grabar->pg_Move_Next();
			$N++;
		}
		
    }//fin del array  de periodos
                
                
        $Rs_grabarCAB->pg_Move_Next();
	$NCAB++;
}


$lw=$marIni;		
//$var1 = $pdf->GetY();				
//$pdf->WriteResumen($lw,$var1, $corte,$topmar);


/*
 * AQUI EMPIEZA HA IMPRIMIR LOS PU Y PR
 */

/*
$usuario = $_GET['usuario'];
$fechaProyectado =  $_GET['fechaProyectado'];
$contrib =  $_GET['contrib'];
$predio =  $_GET['predio'];
$estado =  $_GET['estado'];
$desde =  $_GET['desde'];
$hasta =  $_GET['hasta'];
$tributo =  $_GET['tributo'];
$nro_contrib = $_GET['nro_contrib'];
$Nuevo_cperiod = $_GET['p_cperiod'];
*/
        
$Rs_grabarCAB = new TSPResult($ConeccionRatania,"");
$Rs_grabarCAB->Poner_MSQL("select t1.idsigma as mhresum_actual, 
t1.ctipdat as tipo, 
( select  public.mconten.vdescri from public.mconten WHERE idsigma=t1.cmotivo) as motivo, 
t1.vnrodoc as dj,
(t2.vpatern||' '||t2.vmatern||' '||t2.vnombre) as nombrecontrib,
(t2.cdenomi||' '||t2.vdirecc||' '||t2.vnumero||' '||t2.vmanzan||' '||t2.vlote) dirfiscal,
t2.vreferen as refdirfiscal,
(select vnombres from registro.mubigeo WHERE idsigma=t2.mubigeo) as disdirfiscal,
( COALESCE((select public.mconten.vdescri from public.mconten WHERE idsigma=t2.ctipdoc),' DNI ') )||' '||COALESCE(t2.vnrodoc,'') as documento,
t2.idsigma as cidpers,
(COALESCE((select u.usuario from seguridad.usuario as u  where u.cidusuario=t1.vusernm),t1.vusernm) ) as vusuario,
t1.ddatetm as fechareg
from registro.mhresum as t1,
public.mperson as t2
where 
t1.mperson=t2.idsigma
AND t1.mperson='".$nro_contrib."'
$filtroConsulta


");
$Rs_grabarCAB->pg_Poner_Esquema("tesoreria");

$Rs_grabarCAB->executeMSQL();


//echo "<br><br>CONSULTA REQ UPDATE <br> ".$Rs_grabarCAB->Escribir_Consulta();

$NCAB = 0;
$numRowsCAB = $Rs_grabarCAB->pg_Num_Rows();
//echo $numRowsCAB;

while($NCAB < $numRowsCAB){

    
$TipoCabecera='2';
//echo "<br><br>CONSULTA REQ UPDATE <br> ".$Rs_grabar->Escribir_Consulta();			
$rowCAB= $Rs_grabarCAB->pg_Get_Row();


for($i=0; $i<$NPeriodos; $i++){
    $p_cperiod = $periodos[$i];
    
$RUC='20131366702';
$numero_recibo='860100100014';
$codigoContrib=$rowCAB['cidpers'];
$mhresum_actual=$rowCAB['mhresum_actual'];
$nombreContrib=$rowCAB['nombrecontrib'];
$direccionContrib=$rowCAB['dirfiscal'];
$direcreferencia=$rowCAB['refdirfiscal'];
$disdirfiscal=$rowCAB['disdirfiscal'];
$documento=$rowCAB['documento']; 
$vusuario=$rowCAB['vusuario']; 
$fechareg=$rowCAB['fechareg']; 
$c_motivo = $rowCAB['motivo'];
$c_nrodj = $rowCAB['dj'];
//$p_cperiod = '2014';

$Rs_grabarPU = new TSPResult($ConeccionRatania,"");
$Rs_grabarPU->Poner_MSQL("SELECT t1.idsigma as codpredio,
(select vnombre from registro.mpoblad where idsigma = t1.mpoblad ) as grupourbano,
(select vnombre from registro.mviadis where idsigma = t1.mviadis ) as nombrevia,
TRIM((CASE WHEN COALESCE(TRIM(t1.dnumero),'')='' THEN '' ELSE 'Nro '||t1.dnumero  END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dbloque),'')='' THEN ''  ELSE 'Blq. '||t1.dbloque END )||' '||
(CASE WHEN COALESCE(TRIM(t1.ddepart),'')='' THEN ''  ELSE 'Dpto. '||t1.ddepart END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dinteri),'')='' THEN ''  ELSE 'Int. '||t1.dinteri END )||' '||
(CASE WHEN COALESCE(TRIM(t1.destaci),'')='' THEN ''  ELSE 'Est. '||t1.destaci END )||' '||
(CASE WHEN COALESCE(TRIM(t1.ddeposi),'')='' THEN ''  ELSE 'Depos. '||t1.ddeposi END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dmanzan),'')='' THEN ''  ELSE 'Mz. '||t1.dmanzan END )||' '||
(CASE WHEN COALESCE(TRIM(t1.dnlotes),'')='' THEN ''  ELSE 'Lte. '||t1.dnlotes END ) ) as direccion,
t1.vdirpre,
t2.nporcen,
t3.nvalpre,
t2.nestado,
t1.mviadis,
(select con.vobserv from registro.mviadis as via,
public.mconten as con where via.idsigma = t1.mviadis
AND via.ctipvia=con.idsigma 
GROUP BY con.vobserv) as tipovia,
(select con.vobserv from registro.mpoblad as pob,
public.mconten as con where pob.idsigma = t1.mpoblad
AND pob.ctipcen=con.idsigma 
GROUP BY con.vobserv) as tipogrupohab,
(select con.vdescri 
from public.mconten as con where con.idsigma = t2.cusogen )  as usogen ,
(select con.vdescri 
from public.mconten as con where con.idsigma = t2.ccondic ) as condicion,
(select con.vdescri 
from public.mconten as con where con.idsigma = t2.cestado ) as estadocons,
(select con.vdescri 
from public.mconten as con where con.idsigma = t2.csubtip )  as tipopredio,
COALESCE ((select con.vdescri 
from public.mconten as con,
registro.minafec as ina where 
con.idsigma = ina.ctipina
AND ina.mpredio=t1.idsigma
AND ina.mperson='".$codigoContrib."') ,'AFECTO') as regimen,
( SELECT   
        sum(t6.narecon) as narecon
        FROM registro.mconstr as t6,
         registro.dconstr as t5
        WHERE 
        t6.idsigma=t5.mconstr
        AND t6.dpredio=t2.idsigma
        AND t5.cperiod=t3.cperiod
        AND t6.nestado='1') as areaconstruc,
( SELECT sum(tvalpis.nvalpis)
 FROM registro.vpredio as tvalpis WHERE tvalpis.dpredio=t2.idsigma 
AND tvalpis.cperiod=t3.cperiod AND tvalpis.nestado='1') as nvalpistotal, 
( SELECT sum(tvalpis.nvalins)
 FROM registro.vpredio as tvalpis WHERE tvalpis.dpredio=t2.idsigma 
AND tvalpis.cperiod=t3.cperiod AND tvalpis.nestado='1') as nvalinstotal,
t3.nvalter,
t3.nvalpis,
t3.nvalins,
t3.nporafe,
t2.nterren as areaterreno,
t2.ncomtot as areacomun,
t3.narance as arancel,
t2.dfecadq,
t2.dfecdes,
t2.dafecta,
( SELECT t5.nhectar as nhectar FROM registro.mrustic as t6, registro.drustic as t5
 WHERE t6.idsigma=t5.mrustic AND t6.dpredio=t2.idsigma AND t5.cperiod=t3.cperiod 
AND t6.nestado='1' GROUP BY t5.nhectar) as arearustico,
t1.ctippre
FROM registro.mpredio as t1,
registro.dpredio as t2,
registro.vpredio as t3,
registro.mhresum as t4
WHERE t1.idsigma=t2.mpredio
AND t2.mhresum=t4.idsigma
AND t2.idsigma=t3.dpredio
AND t3.cperiod='".$p_cperiod."'
AND t4.idsigma='".$mhresum_actual."' 
ORDER BY t2.nestado DESC

    ");
$Rs_grabarPU->pg_Poner_Esquema("tesoreria");

$Rs_grabarPU->executeMSQL();
//echo $Rs_grabarPU->Escribir_Consulta();

$NTotalRowsPU = $Rs_grabarPU->pg_Num_Rows();

$NRowsPU = 0;

if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'Q';

$colCab = array('22','134','14','30','220','40','15','79','185','33','35','200','9','20','15','30','10','25');
$colSubCab[0] = array('35','8','18');
$colSubCab[1] = array('18','20','25');
$colSubCab[2] = array('18','8','25');


/*
$pdf = new PDF('L','mm','A5');
$pdf->SetDisplayMode('fullpage');
 * 
 * 
 */

//$pdf->AutoPrint();

$topIni = 10;
$marIni = 10; 
$topmar = 72;
$lp = 96;
//$lw=10;

//$pdf->AddPage();


$npredioControl = "####";
while ($NRowsPU<$NTotalRowsPU){


$rowPU= $Rs_grabarPU->pg_Get_Row();

$descargo = '';
/*if(trim($rowPU['nestado'])=='0'){
    $descargo = '*';
}
*/
$ctippre = $rowPU['ctippre'];
$arearustico = $rowPU['arearustico'];
$sectorPredio = trim($rowPU['sectorpredio']);
$codPredio = trim($rowPU['codpredio']);
$grupoUrbano = $rowPU['grupourbano'];
$nombreVia = $rowPU['nombrevia'];
$direccion = $rowPU['direccion'];
$nautova = $rowPU['nvalpre'];
$nporcen = $rowPU['nporcen'];
$tipovia = $rowPU['tipovia'];
$tipogrupohab = $rowPU['tipogrupohab'];
$regimen = $rowPU['regimen'];
$estadocons = $rowPU['estadocons'];
$condicion = $rowPU['condicion'];
$usogen = $rowPU['usogen'];
$tipopredio = $rowPU['tipopredio'];
$dirPredio = $sectorPredio.' '.
                        $tipogrupohab.' '.
                        $grupoUrbano.' '.
                        $tipovia.' '.
                        $nombreVia.' '.
                        $direccion;
$dirRefPredio = $rowPU['vdirpre'];




$areaconstruc = number_format($rowPU['areaconstruc'],'2',',',' ');
$nvalpre = number_format( $rowPU['nvalpre'],'2',',',' ');
$nvalter = number_format($rowPU['nvalter'],'2',',',' ');        
$nvalpis = number_format($rowPU['nvalpis'],'2',',',' ');
$nvalpistotal = number_format($rowPU['nvalpistotal'],'2',',',' ');
$nvalinstotal = number_format($rowPU['nvalinstotal'],'2',',',' ');
$nvalins = number_format($rowPU['nvalins'],'2',',',' ');
$nporafe = number_format($rowPU['nporafe'],'2',',',' ');
$areaterreno = number_format($rowPU['areaterreno'],'2',',',' ');
$areacomun = number_format($rowPU['areacomun'],'2',',',' ');
$arancel = number_format($rowPU['arancel'],'2',',',' ');
$dfecadq = $rowPU['dfecadq'];

$dafecta = $rowPU['dafecta'];
if(trim($rowPU['nestado'])=='0'){
    $dfecdes=$rowPU['dfecdes']; 
}else{
    $dfecdes = ''; 
}



        

$c_motivo = 'MASIVA';
$tituloHR1 = "DECLARACION JURADA"."IMPUESTO PREDIAL ".$p_cperiod;
$tituloHR2 = "IMPUESTO PREDIAL ".$p_cperiod;
$tituloHR3 = "Decreto Legislativo 776 Hoja de Actualizacion de Valores";
$tituloHR4 = "MOTIVO ".$c_motivo;
$p_nroDoc = "Nro ".$p_nroDoc;
$p_nroDoc = "Nro ".$c_nrodj;
//$cajero='NDIESTRO';
//$usuario='NDIESTRO';
//$fechaEmision='12-12-2013 15:56';

//echo 'HOLA '.$fechaProyectado ;
//echo 'USUARIO '.$usuario ;

$Rs_grabar = new TSPResult($ConeccionRatania,"");
$Rs_grabar->Poner_MSQL("SELECT 
((select vobserv from public.mconten where idsigma=t2.cmurcol)||
(select vobserv from public.mconten where idsigma=t2.ctechos)||
(select vobserv from public.mconten where idsigma=t2.cmpisos)||
(select vobserv from public.mconten where idsigma=t2.cpueven)||
(select vobserv from public.mconten where idsigma=t2.crevest)||
(select vobserv from public.mconten where idsigma=t2.cbanios)||
(select vobserv from public.mconten where idsigma=t2.celectr) ) as carater,
(select vobserv from public.mconten where idsigma=t2.cconser) as estconser,
(select vobserv from public.mconten where idsigma=t2.cmateri) as materpredo,
(select vobserv from public.mconten where idsigma=t1.cclasif) as clasif,
t3.nantigu, t2.narecon,
t3.nvaluni,
t3.nincrem,
t3.npordep,
t3.ndepred,
(t3.nvaluni-t3.ndepred) as valunideprep,
t3.nvalpis,
t2.cnumpis
FROM registro.mconstr as t2,
registro.dpredio as t1,
registro.dconstr as t3
WHERE t2.dpredio=t1.idsigma
AND t3.mconstr = t2.idsigma
AND t1.mhresum='".$mhresum_actual."' 
AND t1.mpredio='".$codPredio."' 
AND t3.cperiod='".$p_cperiod."'    
ORDER BY t2.cnumpis ASC,t3.nantigu DESC
    ");
$Rs_grabar->pg_Poner_Esquema("tesoreria");

$Rs_grabar->executeMSQL();
//echo $Rs_grabar->Escribir_Consulta();
$row= $Rs_grabar->pg_Get_Row();

$topIni = 10;
$marIni = 10; 
$topmar = 72;
$lp = 96;
//$lw=10;




date_default_timezone_set('America/Lima');
$fechaImpresion=strftime('%d/%m/%Y %H:%M:%S'); 
$nroDoc='42215638';
//$fechaProyectado = '13/12/2013';
//$fechaRecargos = '13/12/2013';

//$pdf->SetBottonMargin(140);
if($npredioControl<>$codPredio){
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $npredioControl = $codPredio;
}

$pdf->SetFont('Arial','B',16);


$lw = $marIni;
$var1 = $topmar;
$lh = $var1;
//$var1 = $topmar+5;
$pdf->SetXY($lw,$lh);


	$N = 0;
	$numRows = $Rs_grabar->pg_Num_Rows();
	
	
	
	while($N < $numRows){
		
		$row= $Rs_grabar->pg_Get_Row();
                
                /*$var1 = $pdf->GetY();
                if($corte = $pdf->CheckPageBreak($var1)) $lh = $topmar-5;*/
                        
                $carater = trim($row['carater']);
                $estconser = trim($row['estconser']);
                $materpredo = $row['materpredo'];
                $clasif = $row['clasif'];
                $nantigu = $row['nantigu'];
                $narecon = number_format($row['narecon'],'2',',',' ');
                $nvaluni = number_format($row['nvaluni'],'2',',',' ');
                $nincrem = number_format($row['nincrem'],'2',',',' ');
                $npordep = number_format($row['npordep'],'2',',',' ');
                $valunideprep= number_format($row['valunideprep'],'2',',',' ');
                $ndepred = number_format($row['ndepred'],'2',',',' ');
                $nvalpis = number_format($row['nvalpis'],'2',',',' ');
                $cnumpis = $row['cnumpis'];
   
                
		$ls=-5;
                $pdf->SetFont('narrow','',7);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[12],4,$cnumpis,1,'C');
                
                $ls=$colCab[12]-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[12],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[12],4,  $nantigu,1,'C');
                
                $ls=$colCab[12]*2-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[12],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[12],4,  $clasif,1,'C');
                
                $ls=$colCab[12]*3-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[12],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[12],4,  $materpredo,1,'C');
                
                $ls=$colCab[12]*4-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[12],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[12],4,  utf8_decode($estconser),1,'C');
                
                $ls=$colCab[12]*5-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[13],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[13],4,  utf8_decode($carater).$pase,1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[17],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[17],4,  utf8_decode($nvaluni),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*1+$colCab[17]-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[13],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[13],4,  utf8_decode($nincrem),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*2+$colCab[17]-5;
		
	
                
                $pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[16],3,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[16],4,  utf8_decode($npordep),1,'C');
                $pdf->SetXY($lw+$ls+$colCab[16],$lh);
		//$pdf->Cell($colCab[13],3,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls+$colCab[16],$lh);
		$pdf->MultiCell($colCab[13],4,  utf8_decode($ndepred),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*2+$colCab[17]+$colCab[15]-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[13],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[13],4,  utf8_decode($valunideprep),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*3+$colCab[17]+$colCab[15]-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[13],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[13],4,  utf8_decode($narecon),1,'C');
                
                $ls=$colCab[12]*5+$colCab[13]*4+$colCab[17]+$colCab[15]-5;
		
		$pdf->SetXY($lw+$ls,$lh);
		//$pdf->Cell($colCab[13],6,"",1,1,'L',true);
		$pdf->SetXY($lw+$ls,$lh);
		$pdf->MultiCell($colCab[13],4,  utf8_decode($nvalpis),1,'C');
		
                $var1 = $pdf->GetY();
		if($corte = $pdf->CheckPageBreak($var1)) {
                    $lh = $topmar-4; $pase='';
                }
                
                $lh = $lh+4;
			
			$Rs_grabar->pg_Move_Next();
			$N++;
		}
		
    
    $NRowsPU++;
    $Rs_grabarPU->pg_Move_Next();
    }
} //fin del array fr periodos
$lw=$marIni;
  $Rs_grabarCAB->pg_Move_Next();
  $NCAB++;
}





$pdf->Output();


?>