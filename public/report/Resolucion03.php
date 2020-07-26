<?php
define('FPDF_FONTPATH','font/');
require_once('fpdf.php');
require_once('gisp_admincon.php');
require_once('qrcode.class.php');
require_once("Connections/coneccionReporte.php");
require_once("Connections/funciones_pg.php");
include "lib_fecha_texto.php";
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

    function WriteHTML($html)
    {
        // Intérprete de HTML
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
            }
            else
            {
                // Etiqueta
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extraer atributos
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        // Etiqueta de apertura
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        // Etiqueta de cierre
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
        // Modificar estilo y escoger la fuente correspondiente
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
        // Escribir un hiper-enlace
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
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
        global $colCab;
        global $colSubCab;
        global $vadicon;
        global $TotalGeneral;
        global $CONCEPTO;
        global $nombreContrib;
        global $Direccion;
        global $Expediente;
        global $fecha;



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
		$this->SetXY($lw+50,$lh+5);
		$this->MultiCell(100,10,'EJECUCION COACTIVA',0,'C');
		
		
		//definimos  ancho y color de rectangulo
		$lh = $lh+20;
		





		$this->SetLineWidth(0.3);
		$this->SetFillColor(255);
		$this->RoundedRect($lw-5, $lh, 170+$tamcabecera, 32, 1, '');
		$this->SetFont('narrow','',11);

		//Linea 1
		$this->SetXY($lw,$lh+2);
		$this->MultiCell(100,5,'EXPEDIENTE Nro.',0,'J');
		$this->SetXY($lw+30,$lh+2);
		$this->MultiCell(160,5,': '.utf8_decode($Expediente),0,'J');

		//Linea 2
		$this->SetXY($lw+0,$lh+6);
		$this->MultiCell(80,5,utf8_decode('EJECUTANTE'),0,'J');


		$this->SetXY($lw+30,$lh+6);
		$this->MultiCell(160,5,': '.utf8_decode('MUNICIPALIDAD PROVINCIAL DE HUARAL'),0,'J');

		//Linea 3
        $this->SetFont('narrow','',11);
		$this->SetXY($lw+0,$lh+10);
		$this->MultiCell(80,5,utf8_decode('EJECUTADO'),0,'J');

        $nombreContrib1=utf8_decode($nombreContrib);
        if(strlen(trim($nombreContrib1))>45 and strlen(trim($nombreContrib1))<=60){
            $this->SetFont('narrow','',12);
        }elseif(strlen(trim($nombreContrib1))>60 and strlen(trim($nombreContrib1))<=140 ){
            $this->SetFont('narrow','',10);

        }elseif(strlen(trim($nombreContrib1))>140){
            $this->SetFont('narrow','',8);

        }else{
            $this->SetFont('narrow','',12);
        }
		$this->SetXY($lw+30,$lh+10);
		$this->MultiCell(160,5,': '.utf8_decode($nombreContrib1),0,'J');
        $this->SetFont('narrow','',11);
		//Linea 4
		$this->SetXY($lw+0,$lh+14);
		$this->MultiCell(80,5,utf8_decode('DOMICILIO'),0,'J');
		$this->SetXY($lw+30,$lh+14);
		$this->MultiCell(160,5,': '.utf8_decode($Direccion),0,'J');

		//Linea 5
		$this->SetXY($lw+0,$lh+18);
		$this->MultiCell(80,5,utf8_decode('MATERIA'),0,'J');
		$this->SetXY($lw+30,$lh+18);
		$this->MultiCell(160,5,': '.utf8_decode($CONCEPTO),0,'J');

		//Linea 6
		$this->SetXY($lw+0,$lh+22);
		$this->MultiCell(80,5,utf8_decode('CODIGO Nro.'),0,'J');
		$this->SetXY($lw+30,$lh+22);
		$this->MultiCell(163,5,': '.utf8_decode($codigoContrib),0,'J');

		//Linea 7
		$this->SetXY($lw+0,$lh+26);
		$this->MultiCell(80,5,utf8_decode('DEUDA'),0,'J');
		$this->SetXY($lw+30,$lh+26);
		$this->MultiCell(163,4,': S/.'.utf8_decode(''.$TotalGeneral.' ('.numtoletrasSOLES($TotalGeneral).' NUEVOS SOLES)'),0,'J');

		//Linea 8
		$this->SetFont('arial','B',12);
		$this->SetXY($lw+0,$lh+35);
		$this->MultiCell(80,5,utf8_decode('RESOLUCION Nº 03'),0,'J');
        $this->SetFont('narrow','',11);
		//Linea 9
		$this->SetXY($lw+30,$lh+42);
		$this->MultiCell(150,5,'Huaral, '.utf8_decode(fechaATexto($fecha)),0,'R');

	/*	$this->SetFont('narrow','',12);
        $this->SetXY($lw+0,$lh+52);
        $this->MultiCell(185,5,utf8_decode('VISTOS.- Y atendiendo el Memorandum nº 188-2015-MPH-GRAT-SGRTR Remito por la Sub Gerencia de Registro Tributario y Recaudacion, remitió a esta sub Gerencia de Ejecutoria Coactiva cuya copia se adjuntan conjuntamente con su constancia de exigibilidad:'),0,'J');
*/







		$lh = $lh +70;



	}
	
	function Footer(){
        global $marini;
		//Posición: a 1,5 cm del final
		$this->SetXY($marini,-6);
		$this->SetFont('Arial','',8);
		$this->Cell(210,5,'Plaza de Armas s/n - Huaral',0.1,0.1,'C',0);
		$this->SetFont('Arial','',8);
		$this->SetXY($marini,-6);
		$this->Cell(0,5,utf8_decode('Pagina N° ').$this->PageNo().' de {nb}',0,0,'R');
	}
	
	function WriteResumen($x,$h, $corte,$topmar){
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

//PARAMETRO OBTENIDO DEL URL
$iddocemitido =  $_GET['iddocemitido'];

//CONSULTA PARA OBTENER LOS DATOS DEL CONTRIBUEYNTE
$Rs_datP = new TSPResult($ConeccionRatania,"");
$Rs_datP->Poner_MSQL("select a.mperson,TO_CHAR(a.dfecdocu, 'dd/MM/yyyy') fecha, a.vnrodocu,
 replace(replace(rtrim(
			(case when c.vpatern='' then '' else rtrim(c.vpatern) || ' ' end) ||
			(case when c.vmatern='' then '' else rtrim(c.vmatern) || ', ' end) ||
			(case when c.vnombre='' then '' else rtrim(c.vnombre) end)), '', ''), ',', '') as crazsoc,
			replace(replace(ltrim(rtrim(c.cdenomi)) || '-' || ltrim(rtrim(c.vdirecc))
			|| (case when ltrim(rtrim(c.vnumero))='' then '' else ' NRO. ' || ltrim(rtrim(c.vnumero)) end)
			|| (case when ltrim(rtrim(c.vdpto))='' then '' else ' DPT. ' || ltrim(rtrim(c.vdpto)) end)
			|| (case when ltrim(rtrim(c.vmanzan))='' then '' else ' MZA. ' || ltrim(rtrim(c.vmanzan)) end)
			|| (case when ltrim(rtrim(c.vlote))='' then '' else ' LTE. ' || ltrim(rtrim(c.vlote)) end), '', ''), ',', '') as direccf
 from  coactivo.mdocumento  a
 inner join public.mperson c on a.mperson=c.idsigma
where a.idsigma='".$iddocemitido."'");
$Rs_datP->pg_Poner_Esquema("public");
$Rs_datP->executeMSQL();
$rowDatop= $Rs_datP->pg_Get_Row();

//CONSULTA PARA OBTENER LOS DATOS PARA EL CONTENIDO DEL DOCUEMTNO
$Rs_grabar = new TSPResult($ConeccionRatania,"");
$Rs_grabar->Poner_MSQL("select concepto,cperanio,sum(monto) monto,fecha from
(select case when concepto='0000000273' then concepto else '0000000278' end concepto ,cperanio,sum(insoluto) monto,to_char(ddatetm,'dd/MM/yyyy') fecha
from coactivo.doc_emitidodet where iddocemitido='".$iddocemitido."'
group by concepto,cperanio ,ddatetm
HAVING sum(insoluto) > 10 )a group by  concepto,cperanio,fecha order by 1,2 ");
$Rs_grabar->pg_Poner_Esquema("COACTIVO");
$Rs_grabar->executeMSQL();
$row= $Rs_grabar->pg_Get_Row();

//CONSULTA PARA OBTENER LOS TOTLES DE COSTAS PROCESALES Y GASTOS ADMINISTRATIVOS
$Rs_Totales = new TSPResult($ConeccionRatania,"");
$Rs_Totales->Poner_MSQL("select * from (
select sum(monto) costas from coactivo.mdocumento a
inner join coactivo.expedientescostas c on a.idsigma=c.mdocumento
inner join coactivo.dcostasprocesales  d on c.iddcostas=d.idsigma
where a.idsigma='".$iddocemitido."'
) a,
(select coalesce(sum(imp_insol),0) gastos from tesoreria.mestcta b
inner join  coactivo.mdocumento  a on a.mperson=b.cidpers
where a.idsigma='".$iddocemitido."' and ctiping='1000000566') b ");
$Rs_Totales->pg_Poner_Esquema("COACTIVO");
$Rs_Totales->executeMSQL();
$rowtot= $Rs_Totales->pg_Get_Row();



$cantidadl=0;
$periodp='';
$periodar='';
$numRows = $Rs_grabar->pg_Num_Rows();
$totalpred=0;
$totarbit=0;
$datosarray=array();
$N = 0;
while($N < $numRows){

    $row= $Rs_grabar->pg_Get_Row();
    if($row['concepto']=='0000000273'){
        if($N==0){
            $periodp=$row['cperanio'];
        }else{
            $periodp=$periodp.", ".$row['cperanio'];
        }
        $totalpred=$totalpred+$row['monto'];
    }
    if($row['concepto']=='0000000278'){
        if($periodar==''){
            $periodar=$row['cperanio'];
        }else{
            $periodar=$periodar.", ".$row['cperanio'];
        }
        $totarbit=$totarbit+$row['monto'];
    }
    $datosarray[]=$row;
    //echo "TIPO INGRESO ".$row['monto']."<br>";
    $sumImpSol=$sumImpSol+$row['monto'];
    $Rs_grabar->pg_Move_Next();
    $N++;
}
//INICIALIZANDO PARAMTROS
$fecha= $row['fecha'];
$nombreContrib=$rowDatop['crazsoc'];
$TotalGeneral=$sumImpSol+$rowtot['costas']+$rowtot['gastos'];
if($periodp!=''){
    $perip='Imp. Pred. '.$periodp;
}
if($periodar!=''){
    $periar=' Imp. Arb. '.$periodar;
}
$CONCEPTO=$perip.$periar;
$firma='0000000344_images.jpg';
$firmaaux='0000000197_Firma (sin fondo).png';
$codigoContrib= $rowDatop['mperson'];
$Expediente = $rowDatop['vnrodocu'];
$Direccion= $rowDatop['direccf'];
$NroRecibos='0000001,0000002,000003,000005';
//FIN PARAMETROS


date_default_timezone_set('America/Lima');
$fechaImpresion=strftime('%d/%m/%Y %H:%M:%S'); 
$nroDoc='42215638';
if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'Q';

$colCab = array('61','63','51','25','220','35');
$colSubCab[0] = array('35','8','18');
$colSubCab[1] = array('18','20','25');
$colSubCab[2] = array('18','8','25');

$topIni = 10;
$marIni = 10; 
$topmar = 65;
//$lw=10;

$pdf = new PDF('P','mm','A4');
$pdf->SetDisplayMode('fullpage');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

$var1 = $pdf->GetY()+6;
//$var1 = $topmar+5;
$pdf->SetXY($lw,$lh = $var1);

//$html = '<b>VISTOS.-</b> Y atendiendo el Memorandum nº 188-2015-MPH-GRAT-SGRTR Remito por la Sub Gerencia de Registro Tributario y Recaudacion, remitió a esta sub Gerencia de Ejecutoria Coactiva cuya copia se adjuntan conjuntamente con su constancia de exigibilidad:';
$pdf->SetFont('Arial','',12);

$lz += 0;
$ls=0;
$lw=$marIni;

$var1 = $pdf->GetY();



$html = '        VISTOS y COSIDERACIONES: PRIMERO: Que, de la revisión del sistema integrado de administración tributaria se verifica que el ejecutado'.
    ' '.$nombreContrib.' ha cancelado su obligación mediante recibos de ingresos Nro '.$NroRecibos.' que obran copias en autos, por lo'.
    ' que de conformidad con lo dispuesto en el literal a) del inciso 1 del artículo 16° del DECRETO SUPREMO 18-2008-JUS, que Aprueba el texto Único y'.
    ' Ordenado de la ley N° 26979 "Ley de Procedimiento de Ejecución Coactiva", que textualmente prescribe "Ninguna autoridad administrativa o política podrá '.
    'suspender el procedimiento, con excepción del ejecutor que deberá hacerlo, bajo responsabilidad, cuando: a) la deuda haya quedado extinguida  la obligación'.
    ' haya sido cumplida". por lo expuesto; SE RESUELVE: PRIMERO; SUSPENDASE el procedimiento de ejecucion coactiva seguido contra el ejecutado'.
    ' '.$nombreContrib.' y levántese la medida de embargo instaurado al ejecutado; de conformidad con lo dispuesto en el literal a) del insiso I del articulo '.
    '16° Decreto Supremo N° 018-2008-JUS, que aprueba el T.U.O de la ley 26979 "Ley de procedimeinto de Ejecucion Coactiva" SEGUNDO: NOTIFIQUESE de acuerdo a Ley. '.
    'Avocandose al presete procedimiento el Abog. Carlos Eduardo Colán Chavinpalpa-Ejecutor Coactivo, designado mediante Resolución de Alcaldía N°302-2015MPH de '.
    'fecha 27/11/2015 Fdo. ABOG. CARLOS EDUARDO COLÁN CHAVINPALPA y el Abog. Luis Felipe Castillo Tuesta - Auxiliar Coactivo.';
$pdf->SetFont('Arial','',11);
$pdf->SetXY($lw+$ls+5,$lh+$lz);
$pdf->MultiCell(175,6,utf8_decode($html),0,'J');
$var1 = $pdf->GetY();
if($corte == $pdf->CheckPageBreak($var1)) $lh = $topmar+7;
$pdf->SetXY($lw+20,$ln+$var1+30);
//$this->RoundedRect(-10, 0, 220, 8, 1,'S');
$pdf->Image('../uploadDdocuments/'.$firma,$lw+20,$ln+$var1+10,52,18,'JPG');
$pdf->Image('../uploadDdocuments/'.$firmaaux,$lw+120,$ln+$var1+10,52,18,'PNG');
$pdf->MultiCell($colCab[0],0,'Ejecutor Coactivo ',0,'C');
$pdf->MultiCell($colCab[0]+230,0,'Auxiliar Coactivo ',0,'C');
//$pdf->WriteResumen($lw,$var1, $corte,$topmar);
//$pdf->WriteHTML(utf8_decode($html));
$pdf->Output();


?>