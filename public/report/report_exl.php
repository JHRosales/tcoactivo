<?php
require_once('../../application/models/DataAdapter.php');
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Listado_Eficacia.xls");
$P_RENI = $_REQUEST["p_ren"];
$P_DESD = $_REQUEST["p_des"];
$P_HAST = $_REQUEST["p_ast"];
$P_TIPO = $_REQUEST["p_tip"];
$P_ESTO = $_REQUEST["p_stk"];
$P_DESA = $_REQUEST["p_desa"];



$params[0] =  $P_DESD;
$params[1] =  $P_HAST;


$cn = new Model_DataAdapter();
$grupoasunto = $cn->ejec_store_procedura_sql('coactivo.obtener_dataexl', $params);
$cdetalleDdocument = count($grupoasunto);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte </title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="6"><CENTER><strong>Eficacia de la Cobranza</strong></CENTER></td>
    </tr>
    <tr>
        <td><strong>Numero</strong></td>
        <td><strong>Nro Orden pago</strong></td>
        <td><strong>Fecha de Emision</strong></td>
        <td><strong>Monto Emitido</strong></td>
        <td><strong>Fecha de Pago</strong></td>
        <td><strong>Monto Pagado</strong></td>
        <td><strong>Porcentaje</strong></td>
    </tr>

    <?PHP
$cantem=0;
    $cantpag=0;
    for ($i = 0; $i < $cdetalleDdocument; ++$i) {
        ?>
        <tr>
            <td><?php echo $grupoasunto[$i][0]; ?></td>
            <td><?php echo $grupoasunto[$i][2]; ?></td>
            <td><?php echo $grupoasunto[$i][3]; ?></td>
            <td><?php echo $grupoasunto[$i][4]; ?></td>
            <td><?php echo $grupoasunto[$i][6]; ?></td>
            <td><?php echo $grupoasunto[$i][5]; ?></td>
            <td><?php echo ($grupoasunto[$i][5]/$grupoasunto[$i][4])*100; ?></td>

        </tr>
    <?php
        $cantem=$cantem+ $grupoasunto[$i][4];
        $cantpag=$cantpag+ $grupoasunto[$i][5];
    }
    ?>
    <tr>
        <td></td>
        <td></td>
        <td><b>Total Emitido</b></td>
        <td><b><?php echo $cantem; ?></b></td>
        <td><b>Total Pagado</b></td>
        <td><b><?php echo $cantpag ?></b></td>
    </tr>
</table>
</body>
</html>