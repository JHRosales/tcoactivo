newAuxiliar=function(){
    openDialogDataFunction1("coactivo/manteauxiliarescoactivos",{idsigma: '' },"550", "250", "Agregar Auxiliar Coactivo");
}

editAuxiliar=function(_parameters){
    rowid= jQuery("#tblResultauxiliares").jqGrid('getGridParam','selrow');
    if (rowid == undefined){
        openDialogInfo("Seleccione una fila",300);
        return false;
    }
    row = $("#tblResultauxiliares").jqGrid('getRowData', rowid);
    openDialogDataFunction1("coactivo/manteauxiliarescoactivos",{usuario:row.ciduser,idsigma:row.idsigma,idsigmaperson:row.idsigmaperson},"550", "250", "Mantener Auxiliar Coactivo");
}


limpiarobjetos = function(){
    $('input').removeAttr('readonly');
    $('#idsigma').val("");
    $('#usuario').val("");
    $('#ciduser').val("");
    $('#nomusuario').val("");
    $('#idsigmamcaja').val("");
    $('#cnrocaja').val("");
    $('#dlocal').val("");
    $('#cidlocal').val("");
    $('#ccodcos').val("");
    //$('#dfecini').val("");
    //$('#dfecfin').val("");
    $('#nestado').attr('checked', true);
}

xmanteUpdateauxiliares = function(numberrow,_row){

    if(numberrow == undefined || numberrow == null) {
        row = _row
    }else{
        row = $(this).getRowData(numberrow);
    }
    row.action='update';
    editAuxiliar(row);
}



bindkeysauxiliares = {"onEnter": xmanteUpdateauxiliares};
buscarauxiliar = function() {
    parameters = {
        "name": "tblResultauxiliares",
        "procedure": "coactivo.lst_auxiliarcoactivo",
        "parameters": '{' +
        '"p_idsigma":"",' +
        '"p_ciduser":""' +
        '}'
    };

    proceso = function(requestData){
        $("#panelResultauxiliares").html(requestData);
        records = $("#ctblResultauxiliares").val();
        //if(records > 1) {
        actualizarGrid("tblResultauxiliares", optionauxiliares, bindkeysauxiliares);
        navPanelCentroP();
    };

    procesarConsultaSubProceso('registrar', parameters, proceso);

};
btnAgregaCajaP = {
    caption: '&nbsp; Agregar &nbsp;',
    title: 'Agregar Cajero',
    id:'btnnuevo',
    buttonicon: 'ui-icon-plusthick',
    onClickButton: function () {
        newAuxiliar();
    }
};


btnEditarCajaP = {
    caption: '&nbsp; Editar &nbsp;',
    title: 'Editar Cajero',
    buttonicon: 'ui-icon-pencil',
    onClickButton: function () {
        editAuxiliar();
    }
};

var navPanelCentroP = function() {
    $("#tblResultauxiliares").jqGrid('navGrid', '#ptblResultauxiliares', {edit: false, add: false, del: false, search: false, refresh: false});
    $("#tblResultauxiliares").jqGrid('navButtonAdd', '#ptblResultauxiliares', btnAgregaCajaP);
    $("#tblResultauxiliares").jqGrid('navSeparatorAdd', '#ptblResultauxiliares');
    $("#tblResultauxiliares").jqGrid('navButtonAdd', '#ptblResultauxiliares', btnEditarCajaP);
    $("#tblResultauxiliares").jqGrid('filterToolbar', {searchOnEnter: false, stringResult: true, ignoreCase: true, searchOperators: false, defaultSearch: 'cn'});
}
optionauxiliares = {
    height: 300,
    width: 700,
    editurl: "auxiliaressave",
    colNames: ["C\u00F3digo", "ciduser","Usuario","Codigopersona", "Nombre Usuario", "nestado","Estado","user", "host", "fecha","tipo","Firma"],
    colModel: [
        {name:'idsigma', index:'idsigma', width:90,editable: true, align: 'center', frozen: false,search: true,editoptions:{readonly:true,size:10}},
        {name:'ciduser', index:'ciduser', width:100,editable: true, align: 'center',hidden:true},
        {name:'usuario', index:'usuario', width:100,editable: true,hidden:false},
        {name:'idsigmaperson', index:'idsigmaperson', width:100,editable: true,hidden:true},
        {name:'nomusuario', index:'nomusuario', width:300,editable: true, align: 'left'},
        {name:'estado', index:'estado', width:90,editable: true,hidden:true},
        {name:'destado', index:'destado', width:100,editable: true,align:"center"},
        {name:'vusernm', index:'cnrocaja', width:80,editable: true, align: 'center',hidden:true},
        {name:'vhostnm', index:'cidlocal', width:90,editable: true, align: 'center',hidden:true},
        {name:'vdatetm', index:'dlocal', width:150,editable: true, align: 'left',hidden:true},
        {name:'tipo', index:'ccodcos', width:210,editable: true, align: 'center',hidden:true},
        {name:'firma', index:'dfecini', width:90,editable: true,search: false,align: 'center',hidden:true}
    ],
    caption: "&nbsp;&nbsp;&nbsp;Resultados de la busqueda de Auxiliares Coactivos",
    ignoreCase:true,
    rowNum:9999999,
    ondblClickRow: xmanteUpdateauxiliares,
    afterInsertRow: function(rowid, aData){
        switch (aData.estado) {
            case '1':
                break;
            case '0':
                jQuery("#tblResultauxiliares").jqGrid('setCell',rowid,'idsigma','',{color:'red'});
                jQuery("#tblResultauxiliares").jqGrid('setCell',rowid,'ciduser','',{color:'red'});
                jQuery("#tblResultauxiliares").jqGrid('setCell',rowid,'usuario','',{color:'red'});
                jQuery("#tblResultauxiliares").jqGrid('setCell',rowid,'nestado','',{color:'red'});
                jQuery("#tblResultauxiliares").jqGrid('setCell',rowid,'nomusuario','',{color:'red'});
                jQuery("#tblResultauxiliares").jqGrid('setCell',rowid,'destado','',{color:'red'});
                break;
        }
    }
};


$(function(){
   // rows = [["1","ACTIVO"],["0","INACTIVO"]];
  //  $('#cboestado').html(contenidocombo(rows));
    buscarauxiliar();
});

