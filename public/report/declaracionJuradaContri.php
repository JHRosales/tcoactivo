<?php
define('FPDF_FONTPATH','font/');
require_once('fpdf.php');
require_once("Connections/coneccionReporte.php");
require_once("Connections/funciones_pg.php");
	
	
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

		global $per_codigo;
		global $per_tipo;
		global $per_tipdoc;
		global $per_numdoc;
		global $per_nombre;
		global $per_sexo;
		global $per_estciv;
		global $per_fecnac;
		global $per_nfijo;
		global $per_nmovil;
		global $per_correo;
		global $per_dir_dist;			
		global $per_dir_npobla;
		global $per_dir_nro;
		global $per_dir_dpto;
		global $per_dir_mz;
		global $per_dir_lote;
		global $per_dir_interior;
		global $per_dir_letra;
		global $per_dir_esta;
		global $per_dir_depo;
		global $per_dir_bloque;
		global $per_dir_seccion;
		global $per_dir_unidinmob;
		global $per_dir_refe;

		global $topIni;
		global $marIni;

		global $logoSupP1;
		global $logoSupP2;
		global $logoSupP3;
		global $logoSupP4;
		global $marcaAguaL1;
		global $marcaAguaL2;
		global $marcaAguaL3;
		global $marcaAguaL4;		
		global $marcaDeAgua;
		
		global $tituloHR1;
        global $tituloHR2;
        global $tituloHR3;
        global $tituloHR4;
                
		
		$tamInfoCuadro = -16;
        $posInfoCuadro = 26;
        $tamInfoCuadro2 = -9;
        $posInfoCuadro2 = 9;
        $tamcabecera=30;
                
		$lw = $marIni;
		$ln = $topIni;
		$ls = 3;
		$lh = 0;

		//Marca de Agua
		$this->Image('../img/marca/escudomarcaagua4.png',$marcaAguaL1+1,$marcaAguaL2+20,$marcaAguaL3+150,$marcaAguaL4+150,'PNG');
		
		//Logo
		$this->SetFont('narrow','',8);
		//$this->SetFillColor(206,232,212); //verde agua
		$this->SetFillColor(188,217,190); //verde agua
		$this->SetXY($lw-10,$lh);
		$this->Cell(220,6,"",0,1,'L',true);
		$this->SetFillColor(243);
		$this->SetFont('arial','B',8);
		$this->SetXY($lw-10,$lh);
		$this->MultiCell(35,6,'Plaza de Armas S/N',1,'C');
		$this->SetXY($lw+25,$lh);
		$this->MultiCell(35,6,'Huaral - Lima',1,'C');
		$this->SetXY($lw+60,$lh);
		$this->MultiCell(45,6,'www.munihuaral.gob.pe',1,'C');
		$this->SetXY($lw+105,$lh);
		$this->MultiCell(34,6,'Telefono 264-3617',1,'C');
		//$this->RoundedRect(-10, 0, 220, 8, 1,'S');
		$this->Image('../img/marca/munipuentepiedra.png',$lw-5,$ln-2,52,15,'PNG');
		
		//$this->SetFillColor(145,185,154); //verde agua
		$this->SetFillColor(255);
		$lh = $lh+6;

				$this->SetFont('arial','B',10);
				$this->SetXY($lw+10,$lh+1);
		$this->MultiCell(160,10,$tituloHR1,0,'C');

				$this->SetFont('narrow','',10);
                $this->SetXY($lw+12,$lh+6);                
		$this->MultiCell(160,10,utf8_decode($tituloHR2),0,'C');


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

        global $bottom_fecreg;
		global $bottom_usureg;
		global $bottom_fecimp;
		global $bottom_usuimp;
                               
                $h1 = -40;
                $h2 = -40;
                $x = $marini+15;
                

        $this->SetFont('Arial','',8);

				$hh = $lp+78;        
        $this->RoundedRect($x+80, $hh+4, 20, 25, 1, '');
		                
                $hh = $lp+31;
                $Texto = "Firma del Contribuyente o Representante Legal";
				$this->SetXY($x,$h1+=18);
                $this->Line($x, $hh+60 , 85, $hh+60, $style);
		$this->MultiCell(70,4,$Texto,0,'J');
                
                $Texto = "Nombre : ";
		$this->SetXY($x,$h1+=5);
                $this->Line($x+16, $hh+70 , 85, $hh+70, $style);
		$this->MultiCell(15,3,$Texto,0,'R');

                $Texto = "DNI : ";
		$this->SetXY($x,$h1+=5);
                $this->Line($x+16, $hh+75 , 85, $hh+75, $style);
		$this->MultiCell(15,3,$Texto,0,'R');

				$Bottom = 205;

				$this->SetFillColor(188,217,190);
				$this->SetXY(0,$Bottom);
				$this->Cell(220,6,"",0,1,'L',true);

				$this->SetXY(0,$Bottom);
		$this->MultiCell(15,6,'Fec.Reg.:',1,'L');
				$this->SetXY(15,$Bottom);
		$this->MultiCell(18,6,$bottom_fecreg,1,'C');
				$this->SetXY(33,$Bottom);
		$this->MultiCell(15,6,'Usu.Reg.:',1,'L');
				$this->SetXY(48,$Bottom);
		$this->MultiCell(25,6,trim($bottom_usureg),1,'L');
				$this->SetXY(73,$Bottom);
		$this->MultiCell(15,6,'Usu.Imp.:',1,'L');
				$this->SetXY(88,$Bottom);
		$this->MultiCell(25,6,trim($bottom_usuimp),1,'L');
				$this->SetXY(113,$Bottom);
		$this->MultiCell(17,6,'Fec.Imp.:',1,'L');
				$this->SetXY(130,$Bottom);
		$this->MultiCell(20,6,trim($bottom_fecimp),1,'C');
                                
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
		if($h >= 105){
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


$pdf = new PDF('P','mm','A5');
$pdf->SetDisplayMode('fullpage');

$tituloHR1 = "DECLARACION JURADA DE CONTRIBUYENTE";

$topIni = 10;
$marIni = 10; 
$lp = 96;

//Recupera datos
$Rs_buscarContri = new TSPResult($ConeccionRatania,"");

$cidpers = $_GET['cidpers'];
$cidtipo = $_GET['cidtipo'];
if(!isset($_GET['cidtipo'])){
	$cidtipo = '1';
}

if($cidtipo=='1'){
    $Rs_buscarContri->Poner_MSQL("
        select distinct
            c.idsigma as cidpers
            , replace(replace(rtrim(
                (case when c.vpatern='' then '' else rtrim(c.vpatern) || ' ' end) ||
                (case when c.vmatern='' then '' else rtrim(c.vmatern) || ', ' end) ||
                (case when c.vnombre='' then '' else rtrim(c.vnombre) end)), '\"', ''), ',', '') as crazsoc
            , replace(replace(ltrim(rtrim(c.cdenomi))||'-'||ltrim(rtrim(c.vdirecc))
                || (case when ltrim(rtrim(c.vnumero))='' then '' else ' NRO. ' || ltrim(rtrim(c.vnumero)) end)
                || (case when ltrim(rtrim(c.vdpto))='' then '' else ' DPT. ' || ltrim(rtrim(c.vdpto)) end)
                || (case when ltrim(rtrim(c.vmanzan))='' then '' else ' MZA. ' || ltrim(rtrim(c.vmanzan)) end)
                || (case when ltrim(rtrim(c.vlote))='' then '' else ' LTE. ' || ltrim(rtrim(c.vlote)) end), '\"', ''), ',', '') as direccf
            , coalesce(ltrim(rtrim(e.vdescri)), '') as vtipdoc
            , coalesce(ltrim(rtrim(c.vnrodoc)), '') as vnrodoc
            , c.vpatern, c.vmatern, c.vnombre, c.ctipper
            , coalesce(ltrim(rtrim(d.vdescri)), '') vtipper
            , c.nestado, c.vhostnm, c.vusernm, c.ddatetm
            , c.ntipers, c.ntipper, coalesce(ltrim(rtrim(g.vnombres)), '') cubigeo
            , c.cdenomi, c.vdirecc, c.vnumero, c.vlote
            , c.vmanzan, c.vdpto, c.vreferen, c.ctipdoc
            , coalesce(ltrim(rtrim(e.vdescri)), '') vtipdoc
            , c.vnrodoc, to_char(c.dfecnac, 'dd/MM/yyyy') dfecnac
            , c.csexo, c.dfecinic, coalesce(ltrim(rtrim(f.vdescri)), '') cestciv, c.ctelfij, c.ctelmov
            , c.vcorreo, c.vobserv
            , c.mpredio, c.mviadis, c.mpoblad, c.vinterior
            , c.vletra, c.vestacionto, c.vdeposito, c.vbloque
            , c.vseccion, c.vunidinmob
            ,
            1 nivel,
            'ACTUAL' idtrans,
            '' dtrans,
            c.vdeclaracion
        from public.mperson c
            left join public.mconten d on c.ctipper=d.idsigma
            left join public.mconten e on c.ctipdoc=e.idsigma
            left join public.mconten f on c.cestciv=f.idsigma
            left join registro.mubigeo g on c.mubigeo=g.idsigma
        where c.idsigma = '$cidpers'
        order by c.idsigma;
    ");
} else {
    $cidpers = intval($cidpers);
    $Rs_buscarContri->Poner_MSQL("
        select distinct
            c.idsigma as cidpers
            , replace(replace(rtrim(
                (case when c.vpatern='' then '' else rtrim(c.vpatern) || ' ' end) ||
                (case when c.vmatern='' then '' else rtrim(c.vmatern) || ', ' end) ||
                (case when c.vnombre='' then '' else rtrim(c.vnombre) end)), '\"', ''), ',', '') as crazsoc
            , replace(replace(ltrim(rtrim(c.cdenomi))||'-'||ltrim(rtrim(c.vdirecc))
                || (case when ltrim(rtrim(c.vnumero))='' then '' else ' NRO. ' || ltrim(rtrim(c.vnumero)) end)
                || (case when ltrim(rtrim(c.vdpto))='' then '' else ' DPT. ' || ltrim(rtrim(c.vdpto)) end)
                || (case when ltrim(rtrim(c.vmanzan))='' then '' else ' MZA. ' || ltrim(rtrim(c.vmanzan)) end)
                || (case when ltrim(rtrim(c.vlote))='' then '' else ' LTE. ' || ltrim(rtrim(c.vlote)) end), '\"', ''), ',', '') as direccf
            , coalesce(ltrim(rtrim(e.vdescri)), '') as vtipdoc
            , coalesce(ltrim(rtrim(c.vnrodoc)), '') as vnrodoc
            , c.vpatern, c.vmatern, c.vnombre, c.ctipper
            , coalesce(ltrim(rtrim(d.vdescri)), '') vtipper
            , c.nestado, c.vhostnm, c.vusernm, c.ddatetm
            , c.ntipers, c.ntipper, coalesce(ltrim(rtrim(g.vnombres)), '') cubigeo
            , c.cdenomi, c.vdirecc, c.vnumero, c.vlote
            , c.vmanzan, c.vdpto, c.vreferen, c.ctipdoc
            , coalesce(ltrim(rtrim(e.vdescri)), '') vtipdoc
            , c.vnrodoc, to_char(c.dfecnac, 'dd/MM/yyyy') dfecnac
            , c.csexo, c.dfecinic, coalesce(ltrim(rtrim(f.vdescri)), '') cestciv, c.ctelfij, c.ctelmov
            , c.vcorreo, c.vobserv
            , c.mpredio, c.mviadis, c.mpoblad, c.vinterior
            , c.vletra, c.vestacionto, c.vdeposito, c.vbloque
            , c.vseccion, c.vunidinmob
            ,
            2 nivel,
            right('0000000000' || cast(c.idtrans as character varying(10)),10 )  idtrans,
            c.detrans dtrans,
            c.vdeclaracion
        from auditoria.mperson c
            left join public.mconten d on c.ctipper=d.idsigma
            left join public.mconten e on c.ctipdoc=e.idsigma
            left join public.mconten f on c.cestciv=f.idsigma
            left join registro.mubigeo g on c.mubigeo=g.idsigma
        where c.idtrans = $cidpers
        order by c.idsigma;
    ");
}

$Rs_buscarContri->pg_Poner_Esquema("public");

$Rs_buscarContri->executeMSQL();

$NumRowsContri = $Rs_buscarContri->pg_Num_Rows();

//echo "<pre>"; print_r($rowContri); echo "</pre>";

if($NumRowsContri>0){

	$rowContri = $Rs_buscarContri->pg_Get_Row();

	$per_codigo = $rowContri['cidpers'];
    $per_declara = $rowContri['vdeclaracion'];

	$per_tipo  = $rowContri['vtipper'];
	$per_tipdoc  = $rowContri['vtipdoc'];
	$per_numdoc  = $rowContri['vnrodoc'];
	$per_nombre  = $rowContri['vnombre'];
	
	if($rowContri['csexo']=='1')
		$per_sexo  = "MASCULINO";
	if($rowContri['csexo']=='2')
		$per_sexo  = "FEMENINO";

	$per_estciv = $rowContri['cestciv'];
	$per_fecnac = $rowContri['dfecnac'];
	$per_nfijo = $rowContri['ctelfij'];
	$per_nmovil = $rowContri['ctelmov'];
	$per_correo = $rowContri['vcorreo'];
	
	$per_dir_dist = $rowContri['cubigeo'];
	$per_dir_npobla = $rowContri['cdenomi'];
	$per_dir_nro = $rowContri['vnumero'];
	$per_dir_dpto = $rowContri['vdpto'];
	$per_dir_mz = $rowContri['vmanzan'];
	$per_dir_lote = $rowContri['vlote'];
	$per_dir_interior = $rowContri['vinterior'];
	$per_dir_letra = $rowContri['vletra'];
	$per_dir_esta = $rowContri['vestacionto'];
	$per_dir_depo = $rowContri['vdeposito'];
	$per_dir_bloque = $rowContri['vbloque'];
	$per_dir_seccion = $rowContri['vseccion'];
	$per_dir_unidinmob = $rowContri['vunidinmob'];
	$per_dir_refe = $rowContri['vreferen'];


	$bottom_fecreg = $rowContri['ddatetm'];
	$bottom_usureg = $rowContri['vusernm'];
	$bottom_usuimp = $_GET['usuario'];
	$bottom_fecimp = date('Y-m-d');
}

$tituloHR2 = "CÓDIGO: ".$per_codigo. "-" . $per_declara;

$lw = $marIni;
$lh = $topIni;
//$pdf->AutoPrint();

$pdf->AliasNbPages();

$pdf->AddPage();

		$pdf->SetFillColor(188,217,190); //verde agua

		//Identificación de Contribuyente
		$lh = $lh+17;
		$pdf->SetFont('arial','B',8);
		$pdf->SetXY($lw,$lh+3);
		$pdf->Cell($colCab[0],60,"",1,'C');
		$pdf->SetXY($lw,$lh+3);
		$pdf->MultiCell($colCab[0],4,utf8_decode('IDENTIFICACIÓN DEL CONTRIBUYENTE'),1,'C','F',true);
		
		$pdf->SetFont('narrow','',8);
                
                $pdf->SetXY($lw+2,$lh+9);
		$pdf->MultiCell(30,4,"Tipo de Persona:",0,'L');
				$pdf->SetXY($lw+4,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_tipo),0,'L');
		$pdf->RoundedRect($lw+3, $lh+14, 30, 6, 1, '');

				$pdf->SetXY($lw+40,$lh+9);
		$pdf->MultiCell(30,4,"Tipo de Documento:",0,'L');
				$pdf->SetXY($lw+42,$lh+15);
		$pdf->MultiCell(40,4,utf8_decode($per_tipdoc),0,'L');
		$pdf->RoundedRect($lw+41, $lh+14, 42, 6, 1, '');

				$pdf->SetXY($lw+90,$lh+9);
		$pdf->MultiCell(30,4,"Nro. de Documento:",0,'L');
				$pdf->SetXY($lw+92,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_numdoc),0,'L');
		$pdf->RoundedRect($lw+91, $lh+14, 34, 6, 1, '');

		$lh += 12;

		$pdf->SetXY($lw+2,$lh+9);
		$pdf->MultiCell(80,4,utf8_decode("Nombres y/o Razón Social:"),0,'L');
				$pdf->SetXY($lw+4,$lh+15);
		$pdf->MultiCell(120,4,utf8_decode($per_nombre),0,'L');
		$pdf->RoundedRect($lw+3, $lh+14, 122, 6, 1, '');

		$lh += 12;

		$pdf->SetXY($lw+2,$lh+9);
		$pdf->MultiCell(30,4,"Sexo:",0,'L');
				$pdf->SetXY($lw+4,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_sexo),0,'L');
		$pdf->RoundedRect($lw+3, $lh+14, 30, 6, 1, '');

				$pdf->SetXY($lw+40,$lh+9);
		$pdf->MultiCell(30,4,"Estado Civil:",0,'L');
				$pdf->SetXY($lw+42,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_estciv),0,'L');
		$pdf->RoundedRect($lw+41, $lh+14, 42, 6, 1, '');

				$pdf->SetXY($lw+90,$lh+9);
		$pdf->MultiCell(30,4,"F. Nacimiento:",0,'L');
				$pdf->SetXY($lw+92,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_fecnac),0,'L');
		$pdf->RoundedRect($lw+91, $lh+14, 34, 6, 1, '');

		$lh += 12;

		$pdf->SetXY($lw+2,$lh+9);
		$pdf->MultiCell(30,4,utf8_decode("Nro. Teléfono:"),0,'L');
				$pdf->SetXY($lw+4,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_nfijo),0,'L');
		$pdf->RoundedRect($lw+3, $lh+14, 30, 6, 1, '');

				$pdf->SetXY($lw+40,$lh+9);
		$pdf->MultiCell(30,4,utf8_decode("Nro. Móvil:"),0,'L');
				$pdf->SetXY($lw+42,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_nmovil),0,'L');
		$pdf->RoundedRect($lw+41, $lh+14, 42, 6, 1, '');

				$pdf->SetXY($lw+90,$lh+9);
		$pdf->MultiCell(30,4,utf8_decode("Correo Electrónico:"),0,'L');
				$pdf->SetXY($lw+92,$lh+15);
		$pdf->MultiCell(30,4,utf8_decode($per_correo),0,'L');
		$pdf->RoundedRect($lw+91, $lh+14, 34, 6, 1, '');


		//Dirección Fiscal
		$lh = $lh+30;
		$pdf->SetFont('arial','B',8);

				$pdf->SetXY($lw,$lh+3);
		$pdf->Cell($colCab[0],75,"",1,'C');
		$pdf->SetXY($lw,$lh+3);
		$pdf->MultiCell($colCab[0],4,utf8_decode('DIRECCIÓN FISCAL'),1,'C','F',true);

		$lh += 8;
		$pdf->SetFont('narrow','',8);

				$pdf->SetXY($lw+2,$lh);
		$pdf->MultiCell(80,4,"Distrito:",0,'L');
				$pdf->SetXY($lw+4,$lh+6);
		$pdf->MultiCell(120,4,utf8_decode($per_dir_dist),0,'L');
		$pdf->RoundedRect($lw+3, $lh+5, 122, 6, 1, '');

		$lh += 12;
				$pdf->SetXY($lw+2,$lh);
		$pdf->MultiCell(80,4,utf8_decode("Urbanización, Asentamiento Humano, etc.:"),0,'L');
				$pdf->SetXY($lw+4,$lh+6);
		$pdf->MultiCell(120,4,utf8_decode($per_dir_npobla),0,'L');
		$pdf->RoundedRect($lw+3, $lh+5, 122, 10, 1, '');
				
		$lh += 16;
				$pdf->SetXY($lw+2,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Nro."),0,'L');
				$pdf->SetXY($lw+4,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_nro),0,'L');
		$pdf->RoundedRect($lw+3, $lh+5, 15, 6, 1, '');

				$pdf->SetXY($lw+22,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Dpto."),0,'L');
				$pdf->SetXY($lw+23,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_dpto),0,'L');
		$pdf->RoundedRect($lw+23, $lh+5, 15, 6, 1, '');

				$pdf->SetXY($lw+42,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Mz."),0,'L');
				$pdf->SetXY($lw+44,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_mz),0,'L');
		$pdf->RoundedRect($lw+43, $lh+5, 15, 6, 1, '');

				$pdf->SetXY($lw+62,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Lote"),0,'L');
				$pdf->SetXY($lw+64,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_lote),0,'L');
		$pdf->RoundedRect($lw+63, $lh+5, 15, 6, 1, '');

				$pdf->SetXY($lw+82,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Interior"),0,'L');
				$pdf->SetXY($lw+84,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_interior),0,'L');
		$pdf->RoundedRect($lw+83, $lh+5, 15, 6, 1, '');

				$pdf->SetXY($lw+102,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Letra"),0,'L');
				$pdf->SetXY($lw+104,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_letra),0,'L');
		$pdf->RoundedRect($lw+103, $lh+5, 22, 6, 1, '');

		$lh += 12;
				$pdf->SetXY($lw+2,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Estacionamiento"),0,'L');
				$pdf->SetXY($lw+4,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_esta),0,'L');
		$pdf->RoundedRect($lw+3, $lh+5, 20, 6, 1, '');

				$pdf->SetXY($lw+27,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Depósito"),0,'L');
				$pdf->SetXY($lw+28,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_depo),0,'L');
		$pdf->RoundedRect($lw+28, $lh+5, 20, 6, 1, '');

				$pdf->SetXY($lw+52,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Bloque"),0,'L');
				$pdf->SetXY($lw+53,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_bloque),0,'L');
		$pdf->RoundedRect($lw+53, $lh+5, 15, 6, 1, '');

				$pdf->SetXY($lw+72,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Sección"),0,'L');
				$pdf->SetXY($lw+73,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_seccion),0,'L');
		$pdf->RoundedRect($lw+73, $lh+5, 24, 6, 1, '');

				$pdf->SetXY($lw+100,$lh);
		$pdf->MultiCell(30,4,utf8_decode("Und. Inmobiliaria"),0,'L');
				$pdf->SetXY($lw+101,$lh+6);
		$pdf->MultiCell(30,4,utf8_decode($per_dir_unidinmob),0,'L');
		$pdf->RoundedRect($lw+101, $lh+5, 24, 6, 1, '');

		$lh += 12;
				$pdf->SetXY($lw+2,$lh);
		$pdf->MultiCell(80,4,utf8_decode("Referencia:"),0,'L');
				$pdf->SetXY($lw+4,$lh+6);
		$pdf->MultiCell(120,4,utf8_decode($per_dir_refe),0,'L');
		$pdf->RoundedRect($lw+3, $lh+5, 122, 10, 1, '');


$lw=$marIni;		
//$var1 = $pdf->GetY();				
//$pdf->WriteResumen($lw,$var1, $corte,$topmar);

$pdf->Output();


?>